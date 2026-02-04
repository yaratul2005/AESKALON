-- v2.0.0 SaaS Features
-- IP Bans Table
CREATE TABLE IF NOT EXISTS ip_bans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    reason VARCHAR(255),
    banned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Extended Settings for SMTP
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_from_email', 'noreply@great10.xyz'),
('smtp_from_name', 'Great10 Support');

-- Extended Settings for Google OAuth
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('google_client_id', ''),
('google_client_secret', ''),
('google_redirect_uri', 'http://localhost:8000/auth/google/callback');

-- Banner/Ads Settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('ad_popunder', ''),
('ad_banner_top', '');
