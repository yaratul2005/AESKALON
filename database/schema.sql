-- Initial Schema
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    google_id VARCHAR(255) NULL,
    facebook_id VARCHAR(255) NULL,
    avatar VARCHAR(255) NULL,
    is_verified TINYINT(1) DEFAULT 0,
    verify_token VARCHAR(255) NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
);

CREATE TABLE IF NOT EXISTS app_version (
    id INT AUTO_INCREMENT PRIMARY KEY,
    version VARCHAR(50) NOT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin (password: admin123)
-- You should change this immediately
INSERT IGNORE INTO users (username, password_hash) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Default Settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('site_name', 'Great10 Streaming'),
('site_header_code', ''),
('site_footer_code', '<p>&copy; 2024 Great10</p>'),
('seo_description', 'Watch movies online free'),
('google_client_id', ''),
('google_client_secret', ''),
('google_redirect_uri', ''),
('facebook_app_id', ''),
('facebook_app_secret', ''),
('facebook_redirect_uri', ''),
('current_db_version', '1.0.0');
