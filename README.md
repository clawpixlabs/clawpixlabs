<div align="center">

![CLAWPIXLABS Banner](.github/banner.png)

# 🦀 CLAWPIXLABS

### Free AI Playground for Everyone — Build Your Own AI in 60 Seconds

[![Website](https://img.shields.io/badge/🌐_Website-clawpixlabs.xyz-D4FF00?style=for-the-badge)](https://clawpixlabs.xyz)
[![Twitter](https://img.shields.io/badge/X_/_Twitter-@clawpixlabs-FF1FB3?style=for-the-badge&logo=x)](https://x.com/clawpixlabs)
[![Telegram](https://img.shields.io/badge/Telegram-Join_Us-D4FF00?style=for-the-badge&logo=telegram)](https://t.me/clawpixlabs)

[![Solana](https://img.shields.io/badge/Built_on-Solana-9945FF?style=flat-square&logo=solana)](https://solana.com)
[![License](https://img.shields.io/badge/License-MIT-FF1FB3?style=flat-square)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-D4FF00?style=flat-square)](CONTRIBUTING.md)

**$CLPX drops April 30, 2026 · 8 PM UTC · [pump.fun](https://pump.fun)**

</div>

---

## 🚀 What is CLAWPIXLABS?

**CLAWPIXLABS** is a community-driven platform that makes building AI tools accessible to **everyone** — not just developers. Type what you want in plain English, and we generate a working AI for you in 60 seconds.

```
You: "Build an AI that drafts Twitter threads in my voice"
AI:  ✓ Done. Here's your assistant. Try it now.
```

### 💡 Why CLAWPIXLABS?

AI shouldn't cost $20-200/month. AI shouldn't be locked behind paywalls or require coding skills. We're building a **free playground** where anyone can ship their own AI and own the results.

| Platform | Cost | Coding Required |
|----------|------|----------------|
| ChatGPT Plus | $20/mo | No |
| Claude Pro | $20/mo | No |
| Custom GPT Builder | $200/mo | No |
| OpenAI API | Pay-per-use | Yes |
| **CLAWPIXLABS** | **$0 forever** | **No** |

---

## ✨ Features

- 🎨 **Build any AI in plain English** — Assistants, Agents, Tools, or Workflows
- 🆓 **Free forever** — No usage caps, no credit card, no API keys
- 🔐 **OTP login** — 6-digit email code, no passwords to remember
- 👛 **Wallet support** — Phantom, MetaMask, Coinbase, Trust, Rainbow
- 💾 **Own your data** — Export anytime, delete anytime, no training on your data
- 🌐 **Embed anywhere** — Share via link or embed in your site
- 🦀 **Pixel-art aesthetic** — Built different

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | Vanilla JS, Tailwind CSS, Pixelify Sans + Silkscreen fonts |
| Backend | PHP 8, MySQL 5.7+ |
| AI | Claude (Anthropic), GPT-4 (OpenAI), Llama (Meta) |
| Hosting | Hostinger VPS + Cloudflare CDN |
| Token | Solana SPL — `$CLPX` |
| Launch | [pump.fun](https://pump.fun) — fair launch, no presale |

---

## 🏁 Quick Start (Self-host)

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Hostinger / VPS / shared hosting
- Anthropic API key ([get one](https://console.anthropic.com/settings/keys))
- SMTP-capable email account

### 1. Clone the repo

```bash
git clone https://github.com/YOUR_USERNAME/clawpixlabs.git
cd clawpixlabs
```

### 2. Setup database

```bash
# Login to your phpMyAdmin / MySQL CLI
# Run database.sql to create 7 tables + seed data
```

### 3. Configure backend

Edit `api/config.php` with your credentials:

```php
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('CLAUDE_API_KEY', 'sk-ant-api03-...');
define('SMTP_PASS', 'your_email_password');
```

### 4. Upload to server

```bash
# Upload all files to public_html/
# Make sure .htaccess works
```

### 5. Test

Visit `https://yourdomain.com/api/auth.php?action=me`

Expected: `{"error":"Unauthorized"}` ✅ Database connected!

📖 **Full deployment guide:** [DEPLOY_GUIDE.md](DEPLOY_GUIDE.md)

---

## 📂 Project Structure

```
clawpixlabs/
├── index.html              Homepage with login modal
├── dashboard.html          AI builder (auth-gated)
├── faq.html                FAQ + AI chatbot
├── docs.html               Documentation
├── whitepaper.html         Project whitepaper
├── governance.html         DAO governance
├── privacy.html            Privacy Policy
├── terms.html              Terms of Service
├── crab.png                Mascot logo
├── database.sql            MySQL schema (7 tables)
└── api/
    ├── config.php          Configuration (credentials)
    ├── auth.php            Email OTP + wallet sign-in
    ├── chat.php            Authenticated AI chat
    ├── chat-public.php     Public chatbot (FAQ page)
    ├── builds.php          AI build CRUD
    └── .htaccess           Security headers
```

---

## 🎯 How It Works

```
User signs in (email OTP / wallet)
         ↓
Describes AI in plain English
         ↓
Backend calls Claude API with system prompt
         ↓
Returns generated AI logic + UI
         ↓
User deploys, shares, embeds
```

**API Endpoints:**

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth.php?action=request_email` | Request OTP code |
| POST | `/api/auth.php?action=verify_otp` | Verify OTP, get session token |
| POST | `/api/auth.php?action=wallet_nonce` | Get wallet sign challenge |
| POST | `/api/auth.php?action=wallet_verify` | Verify wallet signature |
| GET | `/api/auth.php?action=me` | Get current user |
| POST | `/api/auth.php?action=logout` | End session |
| POST | `/api/chat.php` | Authenticated AI chat |
| POST | `/api/chat-public.php` | Public FAQ chatbot |
| GET/POST/PUT/DELETE | `/api/builds.php` | CRUD AI builds |

---

## 🔐 Security

- ✅ **Prepared statements everywhere** (anti SQL injection)
- ✅ **OTP single-use, 15-minute expiry**
- ✅ **Cryptographically secure tokens** (`random_bytes(32)`)
- ✅ **Session expiration** (7 days, auto-cleanup)
- ✅ **Rate limiting** (3 OTP requests / 15 min per email)
- ✅ **Wallet nonce anti-replay** (single-use, 10 min expiry)
- ✅ **CASCADE DELETE** for data integrity
- ✅ **CORS restricted** to clawpixlabs.xyz only
- ✅ **`.htaccess` blocks** direct config access
- ✅ **No password storage** (OTP-only auth)

📋 **Report security issues:** security@clawpixlabs.xyz

---

## 🪙 $CLPX Token

> **The platform stays free FOREVER, with or without $CLPX.**
> Token is for community ownership and governance only — NOT for accessing the platform.

| Property | Value |
|----------|-------|
| Network | Solana |
| Type | SPL Token |
| Launch | April 30, 2026 · 8 PM UTC |
| Platform | [pump.fun](https://pump.fun) |
| Contract | _Announced at launch_ |

⚠️ **Beware fake tokens.** Always verify CA from official channels.

---

## 🤝 Contributing

We welcome contributions! Here's how:

1. Fork this repo
2. Create your feature branch (`git checkout -b feat/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feat/amazing-feature`)
5. Open a Pull Request

📋 **Full guidelines:** [CONTRIBUTING.md](CONTRIBUTING.md)

### 🐛 Found a bug?

Open an [issue](https://github.com/YOUR_USERNAME/clawpixlabs/issues) with:
- Steps to reproduce
- Expected vs actual behavior
- Browser/OS info
- Screenshots if applicable

---

## 🗺️ Roadmap

### ✅ Q2 2026 (Current)
- [x] Core platform (auth, AI builder, chat)
- [x] OTP email login
- [x] Wallet sign-in (Phantom, MetaMask)
- [x] FAQ AI chatbot
- [x] $CLPX token launch
- [ ] Mobile app (PWA)

### 🔄 Q3 2026
- [ ] Multi-modal AI (vision, audio)
- [ ] Custom domain hosting for AIs
- [ ] DAO governance live
- [ ] Mobile native apps (iOS/Android)
- [ ] Bring Your Own API Key (BYOK)

### 🔮 Q4 2026 & Beyond
- [ ] Decentralized infrastructure pilot
- [ ] AI marketplace (community-built)
- [ ] Plugin system
- [ ] Embedded SDK for 3rd parties

---

## 📜 License

MIT License — see [LICENSE](LICENSE) for details.

Free to use, modify, distribute. Built with love by the CLAWPIXLABS community.

---

## 🌐 Links

- 🌐 **Website:** [clawpixlabs.xyz](https://clawpixlabs.xyz)
- 🐦 **X / Twitter:** [@clawpixlabs](https://x.com/clawpixlabs)
- 💬 **Telegram:** [t.me/clawpixlabs](https://t.me/clawpixlabs)
- 📖 **Docs:** [clawpixlabs.xyz/docs.html](https://clawpixlabs.xyz/docs.html)
- 📝 **Whitepaper:** [clawpixlabs.xyz/whitepaper.html](https://clawpixlabs.xyz/whitepaper.html)
- ❓ **FAQ:** [clawpixlabs.xyz/faq.html](https://clawpixlabs.xyz/faq.html)

---

## 💚 Support

If CLAWPIXLABS helped you:
- ⭐ **Star this repo**
- 🐦 **Follow on X**
- 💬 **Join Telegram** to chat with the community
- 🦀 **Hold $CLPX** to support development

---

<div align="center">

**Built with 🦀 by the CLAWPIXLABS community**

*Free AI for everyone, forever.*

</div>
