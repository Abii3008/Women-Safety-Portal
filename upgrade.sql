-- Run this ONLY if you already imported the OLD database.sql (keeps your data).
-- Works on XAMPP (MariaDB). Select women_safety_db first, then run in the SQL tab.
USE women_safety_db;
ALTER TABLE users ADD COLUMN IF NOT EXISTS sos_message VARCHAR(300) DEFAULT '';
ALTER TABLE emergency_contacts ADD COLUMN IF NOT EXISTS is_primary TINYINT(1) NOT NULL DEFAULT 0;
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email), INDEX idx_ip (ip)
);
