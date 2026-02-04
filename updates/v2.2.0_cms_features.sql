-- v2.2.0 CMS Features
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert some default pages if empty
INSERT IGNORE INTO pages (title, slug, content) VALUES 
('About Us', 'about', '<h1>About Us</h1><p>Welcome to our streaming platform.</p>'),
('Privacy Policy', 'privacy', '<h1>Privacy Policy</h1><p>Your privacy matters.</p>');
