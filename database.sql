-- =====================================================
-- CLAWPIXLABS DATABASE SCHEMA
-- Version: 1.0
-- Compatible: MySQL 5.7+ / MariaDB 10.3+
-- =====================================================
-- 
-- INSTRUKSI INSTALL DI HOSTINGER PHPMYADMIN:
-- 1. Login ke hPanel Hostinger
-- 2. Buka "Databases" → "MySQL Databases"
-- 3. Buat database baru, contoh: clawpix_db
--    Catat: nama database, username, password
-- 4. Klik tombol "phpMyAdmin" di sebelah database
-- 5. Pilih database clawpix_db di sidebar kiri
-- 6. Klik tab "SQL" di atas
-- 7. Copy-paste SELURUH isi file ini, klik "Go"
-- 8. Selesai! Database structure ready
-- =====================================================

-- Pastikan UTF-8 untuk emoji & internasional
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- =====================================================
-- TABLE: users
-- Menyimpan akun (email login + wallet connect)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NULL COMMENT 'Email login (NULL kalau wallet only)',
    `wallet_address` VARCHAR(100) UNIQUE NULL COMMENT 'Wallet address (NULL kalau email only)',
    `wallet_type` ENUM('metamask','walletconnect','coinbase','phantom','trust','rainbow') NULL,
    `username` VARCHAR(50) UNIQUE NULL COMMENT 'Display name @username',
    `display_name` VARCHAR(100) NULL,
    `avatar_url` VARCHAR(500) NULL,
    `bio` TEXT NULL,
    `is_verified` TINYINT(1) DEFAULT 0 COMMENT '1 kalau email confirmed atau wallet signed',
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_wallet` (`wallet_address`),
    INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: magic_links
-- Token email magic link (expire 15 menit)
-- =====================================================
CREATE TABLE IF NOT EXISTS `magic_links` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) UNIQUE NOT NULL COMMENT 'Random secure token',
    `expires_at` TIMESTAMP NOT NULL,
    `used_at` TIMESTAMP NULL COMMENT 'NULL = belum dipakai',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_token` (`token`),
    INDEX `idx_email_expires` (`email`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: wallet_nonces
-- Nonce challenge untuk wallet sign-in (anti-replay)
-- =====================================================
CREATE TABLE IF NOT EXISTS `wallet_nonces` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `wallet_address` VARCHAR(100) NOT NULL,
    `nonce` VARCHAR(64) UNIQUE NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `used_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_wallet_nonce` (`wallet_address`, `nonce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: sessions
-- Session token JWT-style (login persistent)
-- =====================================================
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `session_token` VARCHAR(128) UNIQUE NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_session_token` (`session_token`),
    INDEX `idx_user_expires` (`user_id`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: ai_builds
-- AI yang dibangun user
-- =====================================================
CREATE TABLE IF NOT EXISTS `ai_builds` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `slug` VARCHAR(100) UNIQUE NOT NULL COMMENT 'URL-friendly identifier',
    `name` VARCHAR(150) NOT NULL,
    `type` ENUM('assistant','agent','tool','workflow') NOT NULL,
    `description` TEXT NULL,
    `system_prompt` TEXT NULL COMMENT 'AI instructions',
    `config_json` TEXT NULL COMMENT 'Extra config (model, temp, dll)',
    `is_public` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `total_runs` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_user_type` (`user_id`, `type`),
    INDEX `idx_public_featured` (`is_public`, `is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: ai_runs
-- History setiap kali AI dipanggil (untuk analytics)
-- =====================================================
CREATE TABLE IF NOT EXISTS `ai_runs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ai_build_id` INT NOT NULL,
    `user_id` INT NULL COMMENT 'NULL kalau anonymous run',
    `input` TEXT NULL,
    `output` TEXT NULL,
    `tokens_used` INT DEFAULT 0,
    `duration_ms` INT NULL,
    `status` ENUM('success','error','timeout') DEFAULT 'success',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ai_build_id`) REFERENCES `ai_builds`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_build_created` (`ai_build_id`, `created_at`),
    INDEX `idx_user_created` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: stats_daily
-- Aggregate stats harian (untuk dashboard)
-- =====================================================
CREATE TABLE IF NOT EXISTS `stats_daily` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `date` DATE UNIQUE NOT NULL,
    `total_users` INT DEFAULT 0,
    `new_users_today` INT DEFAULT 0,
    `total_builds` INT DEFAULT 0,
    `new_builds_today` INT DEFAULT 0,
    `total_runs_today` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SEED DATA: Beberapa featured AI sample
-- =====================================================

-- Insert dummy admin user
INSERT INTO `users` (`email`, `username`, `display_name`, `is_verified`, `is_active`)
VALUES ('admin@clawpixlabs.xyz', 'admin', 'Admin', 1, 1);

-- Sample featured builds
INSERT INTO `ai_builds` (`user_id`, `slug`, `name`, `type`, `description`, `is_public`, `is_featured`, `total_runs`)
VALUES 
(1, 'moodboardgpt', 'MoodboardGPT', 'tool', 'Brand brief in, full moodboard out — palettes, fonts, references.', 1, 1, 89),
(1, 'scriptgpt', 'ScriptGPT', 'assistant', 'Writing assistant trained on your channel voice. Drafts video scripts.', 1, 1, 142),
(1, 'trendscout', 'TrendScout', 'agent', 'Scans Twitter for trending design topics every morning.', 1, 1, 67),
(1, 'flowkit', 'FlowKit', 'workflow', 'Multi-step pipeline: scrape, summarize, categorize, email digest.', 1, 1, 23);

-- Sample stats today
INSERT INTO `stats_daily` (`date`, `total_users`, `new_users_today`, `total_builds`, `new_builds_today`, `total_runs_today`)
VALUES (CURDATE(), 1, 1, 4, 4, 0);

-- =====================================================
-- DONE! Database CLAWPIXLABS siap digunakan.
-- =====================================================
