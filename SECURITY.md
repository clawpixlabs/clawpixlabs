# 🛡️ Security Policy

## Reporting a Vulnerability

We take security seriously at CLAWPIXLABS. If you discover a security vulnerability, please follow this process:

### 🚨 DO NOT open a public GitHub issue

This protects users until a fix is deployed.

### ✅ DO email us directly

Send details to: **security@clawpixlabs.xyz**

Include:
- Description of the vulnerability
- Steps to reproduce
- Affected versions / files
- Potential impact
- Your suggested fix (if any)
- Your name/handle for credit (optional)

### ⏱️ Response Timeline

| Stage | Timeline |
|-------|----------|
| Initial response | Within 24 hours |
| Investigation | 1-3 days |
| Fix deployment | 1-7 days (depends on severity) |
| Public disclosure | After fix is deployed |

### 🏆 Recognition

We credit security researchers in our [Hall of Fame](#hall-of-fame) (with your permission).

For critical findings, we offer:
- Public acknowledgment
- $CLPX rewards (post-launch)
- Direct DM with team

---

## 🔒 Supported Versions

Only the **latest version** on `main` branch receives security updates.

| Version | Supported |
|---------|-----------|
| Latest (main) | ✅ Yes |
| Previous versions | ❌ No |

---

## 🛡️ Our Security Practices

What we do to keep CLAWPIXLABS secure:

- ✅ Prepared statements for all SQL queries
- ✅ Cryptographically secure tokens (`random_bytes`)
- ✅ Single-use OTP codes with 15-min expiry
- ✅ Wallet nonce anti-replay protection
- ✅ Rate limiting (3 OTP / 15 min per email)
- ✅ Session expiration (7 days, auto-cleanup)
- ✅ HTTPS-only (enforced via HSTS)
- ✅ CORS restricted to clawpixlabs.xyz
- ✅ `.htaccess` blocks direct config access
- ✅ Input validation on all endpoints
- ✅ No password storage (OTP-only auth)

---

## 🚫 Out of Scope

The following are NOT considered security issues:
- Issues only affecting outdated browsers
- Self-XSS (e.g., requiring victim to paste code in console)
- Lack of rate limiting on cosmetic features
- Issues in third-party dependencies (report upstream)
- Social engineering attacks
- DoS via excessive request volumes (handled at infra level)

---

## 🏆 Hall of Fame

Security researchers who helped make CLAWPIXLABS safer:

*Be the first! 🦀*

---

**Thanks for keeping CLAWPIXLABS safe for everyone.**

🦀
