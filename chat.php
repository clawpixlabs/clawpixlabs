<?php
/**
 * CLAWPIXLABS - Claude AI Chat Endpoint (FIXED)
 * 
 * Compatible dengan format dashboard.html:
 *   POST /api/chat.php
 *   Body: { 
 *     mode: 'craft' | 'assistant' | 'agent' | 'tool' | 'workflow',
 *     messages: [{role, content}, ...]
 *   }
 * 
 * AUTHENTICATION REQUIRED — user must be signed in.
 */

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('POST required', 405);
}

// ============= REQUIRE AUTH =============
$user = requireAuth();
$userId = $user['id'];

// ============= GET INPUT =============
$input = getInput();
$mode = trim($input['mode'] ?? 'assistant');
$messages = $input['messages'] ?? [];
$buildId = intval($input['ai_build_id'] ?? 0);

// Validate
if (!is_array($messages) || empty($messages)) {
    jsonError('Messages required');
}

// Get the last user message
$lastUserMsg = '';
foreach (array_reverse($messages) as $m) {
    if (($m['role'] ?? '') === 'user') {
        $lastUserMsg = $m['content'] ?? '';
        break;
    }
}

if (empty($lastUserMsg)) {
    jsonError('No user message found');
}

// ============= SYSTEM PROMPT BY MODE =============
$systemPrompts = [
    'craft' => "You are CLAWPIXLABS Craft Assistant — a creative HTML/CSS code generator.

When the user describes something to build (landing pages, dashboards, calculators, games, etc.), you MUST:
1. Respond with a brief friendly intro (1 sentence)
2. Generate complete, self-contained HTML in a code block (```html ... ```)
3. The HTML should be production-quality, beautiful, and INTERACTIVE
4. Use Tailwind CSS via CDN: <script src=\"https://cdn.tailwindcss.com\"></script>
5. Include any needed JavaScript inline
6. Make it visually stunning with modern design (gradients, animations, micro-interactions)
7. Use lime #D4FF00 and pink #FF1FB3 as accent colors when appropriate

After the code, end with a brief note about what was built (1-2 sentences).",

    'assistant' => "You are a helpful, friendly AI assistant powered by CLAWPIXLABS. 
Answer questions clearly and concisely. Be warm but professional.",

    'agent' => "You are an autonomous AI agent. You execute tasks by breaking them into steps, 
making decisions, and taking actions. Be methodical and explain your reasoning.",

    'tool' => "You are a single-purpose AI tool. Take input, transform it according to your purpose, 
and return clean structured output. Be precise and efficient.",

    'workflow' => "You are a multi-step AI workflow coordinator. Process inputs through stages, 
chain operations, and produce structured results.",
];

$systemPrompt = $systemPrompts[$mode] ?? $systemPrompts['assistant'];

// Override with custom prompt if AI build specified
if ($buildId > 0) {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM ai_builds WHERE id = ?');
    $stmt->execute([$buildId]);
    $build = $stmt->fetch();
    if ($build && !empty($build['system_prompt'])) {
        $systemPrompt = $build['system_prompt'];
    }
}

// ============= BUILD MESSAGES FOR CLAUDE =============
$claudeMessages = [];
foreach ($messages as $msg) {
    $role = $msg['role'] ?? '';
    $content = $msg['content'] ?? '';
    if (in_array($role, ['user', 'assistant']) && !empty($content)) {
        $claudeMessages[] = [
            'role' => $role,
            'content' => substr($content, 0, 8000),
        ];
    }
}

if (empty($claudeMessages)) {
    jsonError('No valid messages');
}

// ============= CALL CLAUDE API =============
$payload = [
    'model' => CLAUDE_MODEL,
    'max_tokens' => CLAUDE_MAX_TOKENS,
    'system' => $systemPrompt,
    'messages' => $claudeMessages,
];

$start = microtime(true);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-api-key: ' . CLAUDE_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
    CURLOPT_TIMEOUT => 90,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

$durationMs = round((microtime(true) - $start) * 1000);

if ($curlErr) {
    error_log('Claude API curl error: ' . $curlErr);
    jsonError('AI service unavailable: ' . $curlErr, 503);
}

$data = json_decode($response, true);

if ($httpCode !== 200) {
    $errMsg = $data['error']['message'] ?? ('HTTP ' . $httpCode);
    error_log('Claude API error: ' . $errMsg . ' | Response: ' . $response);
    jsonError('AI error: ' . $errMsg, 500);
}

// ============= EXTRACT REPLY =============
$rawReply = '';
foreach ($data['content'] ?? [] as $block) {
    if (($block['type'] ?? '') === 'text') {
        $rawReply .= $block['text'];
    }
}

// ============= EXTRACT HTML (for Craft mode) =============
$generatedHtml = null;
$cleanMessage = $rawReply;

if ($mode === 'craft') {
    if (preg_match('/```html\s*(.+?)\s*```/s', $rawReply, $matches)) {
        $generatedHtml = trim($matches[1]);
        $cleanMessage = trim(preg_replace('/```html\s*.+?\s*```/s', '[HTML generated → see preview]', $rawReply));
    } else {
        if (preg_match('/(<!DOCTYPE.+?<\/html>)/is', $rawReply, $matches)) {
            $generatedHtml = trim($matches[1]);
            $cleanMessage = trim(str_replace($matches[1], '[HTML generated → see preview]', $rawReply));
        }
    }
}

// ============= LOG RUN =============
if ($buildId > 0 || $mode === 'craft') {
    $db = getDB();
    $tokens = ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0);
    
    try {
        $stmt = $db->prepare('
            INSERT INTO ai_runs (ai_build_id, user_id, input, output, tokens_used, duration_ms, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $buildId > 0 ? $buildId : null,
            $userId,
            substr($lastUserMsg, 0, 1000),
            substr($rawReply, 0, 2000),
            $tokens,
            $durationMs,
            'success'
        ]);
        
        if ($buildId > 0) {
            $db->prepare('UPDATE ai_builds SET total_runs = total_runs + 1 WHERE id = ?')->execute([$buildId]);
        }
    } catch (Exception $e) {
        error_log('Log run failed: ' . $e->getMessage());
    }
}

// ============= RESPONSE =============
jsonSuccess([
    'message' => $cleanMessage,
    'raw' => $rawReply,
    'html' => $generatedHtml,
    'usage' => $data['usage'] ?? null,
    'duration_ms' => $durationMs,
]);
