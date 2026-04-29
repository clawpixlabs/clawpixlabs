# 🦀 Contributing to CLAWPIXLABS

First off, thanks for considering contributing! CLAWPIXLABS is built by the community, for the community. Every contribution matters.

## 🎯 Ways to Contribute

- 🐛 **Report bugs** — Open an [issue](../../issues/new?template=bug_report.md)
- 💡 **Suggest features** — Open an [issue](../../issues/new?template=feature_request.md)
- 📝 **Improve docs** — Edit any `.md` file directly on GitHub
- 🔧 **Submit PRs** — Fix bugs, add features, polish UI
- 🌍 **Translate** — Help us go multilingual
- 💬 **Help others** — Answer questions in [Telegram](https://t.me/clawpixlabs)

## 🚀 Development Setup

### Prerequisites

- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Modern browser (Chrome/Firefox/Safari)
- Optional: Hostinger account or local LAMP stack

### Local Setup

```bash
# 1. Clone the repo
git clone https://github.com/YOUR_USERNAME/clawpixlabs.git
cd clawpixlabs

# 2. Set up local database
mysql -u root -p < database.sql

# 3. Copy config example
cp api/config.example.php api/config.php

# 4. Edit api/config.php with your credentials

# 5. Serve with PHP built-in server
php -S localhost:8000

# Visit http://localhost:8000
```

## 📋 Pull Request Process

1. **Fork** the repo
2. **Create a branch** with a descriptive name:
   - `feat/wallet-coinbase-support`
   - `fix/otp-timer-bug`
   - `docs/api-reference-update`
3. **Make your changes** — keep PRs focused on one thing
4. **Test thoroughly** — verify nothing else breaks
5. **Commit with conventional style:**
   - `feat: add Coinbase wallet support`
   - `fix: OTP timer not resetting on resend`
   - `docs: update API endpoint reference`
   - `style: improve dashboard mobile layout`
   - `refactor: extract auth helpers to shared module`
6. **Push** and open a PR with:
   - Clear title
   - Description of what + why
   - Screenshots for UI changes
   - Steps to test

## 🎨 Code Style

### PHP
- PSR-12 compliant
- 4 spaces indentation
- snake_case for variables
- camelCase for functions
- Always use prepared statements
- Always validate input

### JavaScript
- Vanilla JS (no jQuery)
- 4 spaces indentation
- camelCase
- Use `const` over `let`, never `var`
- Arrow functions for callbacks

### CSS / Tailwind
- Use Tailwind utility classes when possible
- Custom CSS only for complex animations or unique styles
- Keep brand colors:
  - Lime: `#D4FF00`
  - Pink: `#FF1FB3`
  - Black: `#0A0A0A`

### HTML
- Semantic HTML5
- Accessibility attributes (alt, aria-*, etc.)
- Mobile-first responsive

## 🐛 Bug Reports

A great bug report includes:

- **Title:** Clear and specific (e.g., "OTP timer doesn't reset after resend")
- **Steps:** Numbered list to reproduce
- **Expected:** What should happen
- **Actual:** What actually happens
- **Environment:** Browser, OS, screen size
- **Screenshots/video:** If applicable
- **Console errors:** Open DevTools → Console tab

## 💡 Feature Requests

A great feature request includes:

- **Problem:** What user need does this solve?
- **Solution:** Your proposed solution
- **Alternatives:** Other approaches you considered
- **Mockups:** Visual designs help a lot
- **Use cases:** Real-world scenarios

## 🛡️ Security

**DO NOT** open public issues for security vulnerabilities!

Email security concerns to: **security@clawpixlabs.xyz**

We aim to respond within 24 hours.

## 📜 Code of Conduct

Be kind. Be patient. Be helpful. We're all here to build cool stuff together.

- ✅ Welcome newcomers
- ✅ Constructive criticism
- ✅ Credit others' work
- ❌ No harassment
- ❌ No spam or self-promotion
- ❌ No financial advice / shilling

## 🦀 Recognition

All contributors get:
- Listed in [CONTRIBUTORS.md](CONTRIBUTORS.md)
- Shoutout on our [X](https://x.com/clawpixlabs)
- $CLPX rewards for major contributions (post-launch)

---

**Questions?** Drop in our [Telegram](https://t.me/clawpixlabs) — we don't bite.

🦀 Free AI for everyone, forever.
