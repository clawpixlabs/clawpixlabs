<?php
/**
 * CLAWPIXLABS - Configuration Template
 * 
 * INSTRUCTIONS:
 * 1. Copy this file: cp api/config.example.php api/config.php
 * 2. Fill in YOUR credentials
 * 3. NEVER commit config.php to git (it's in .gitignore)
 */

// ============= DATABASE =============
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// ============= CLAUDE API =============
// Get yours: https://console.anthropic.com/settings/keys
define('CLAUDE_API_KEY', 'sk-ant-api03-XXXXXXXXX');
define('CLAUDE_MODEL', 'claude-sonnet-4-5-20250929');
define('CLAUDE_MAX_TOKENS', 1024);

// ============= SMTP EMAIL =============
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'noreply@yourdomain.com');
define('SMTP_PASS', 'your_email_password');
define('SMTP_FROM_NAME', 'CLAWPIXLABS');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');

// ============= SITE =============
define('SITE_URL', 'https://yourdomain.com');
define('SITE_NAME', 'CLAWPIXLABS');

// Session expire (7 days)
define('SESSION_LIFETIME', 604800);

// OTP expire (15 minutes)
define('MAGIC_LINK_LIFETIME', 900);

// ============= CORS =============
header('Access-Control-Allow-Origin: ' . SITE_URL);
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ============= DB CONNECTION =============
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            jsonError('Database connection failed', 500);
        }
    }
    return $pdo;
}

// ============= HELPERS =============
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function jsonError($message, $code = 400) {
    jsonResponse(['error' => $message], $code);
}

function jsonSuccess($data = []) {
    jsonResponse(array_merge(['success' => true], $data));
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function getInput() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?: [];
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function getCurrentUser() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $token);
    
    if (empty($token)) return null;
    
    $db = getDB();
    $stmt = $db->prepare('
        SELECT u.* FROM users u
        JOIN sessions s ON s.user_id = u.id
        WHERE s.session_token = ? AND s.expires_at > NOW()
    ');
    $stmt->execute([$token]);
    return $stmt->fetch();
}

function requireAuth() {
    $user = getCurrentUser();
    if (!$user) jsonError('Unauthorized', 401);
    return $user;
}
