-- Initial Schema
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
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
('current_db_version', '1.0.0');
