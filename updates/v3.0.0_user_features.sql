-- v3.0.0 User Features (Safe Migration)

-- 1. Add columns without constraints first
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL AFTER username,
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS verify_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL;

-- 2. Migrate existing users (Give them a unique placeholder email)
UPDATE users SET email = CONCAT('user_', id, '@example.com') WHERE email IS NULL OR email = '';

-- 3. Now verify emails are present and apply Unique constraint
-- Note: We use IGNORE in case constraint already exists to prevent errors on re-run
ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NOT NULL;
ALTER TABLE users ADD UNIQUE INDEX IF NOT EXISTS unique_email (email);

-- Watch Later
CREATE TABLE IF NOT EXISTS watch_later (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tmdb_id INT NOT NULL,
    type ENUM('movie', 'tv') DEFAULT 'movie',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_watch (user_id, tmdb_id, type)
);

-- Watch History
CREATE TABLE IF NOT EXISTS user_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tmdb_id INT NOT NULL,
    type ENUM('movie', 'tv') DEFAULT 'movie',
    watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_history (user_id, tmdb_id, type)
);

-- Comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_id INT NOT NULL, -- TMDB ID
    type ENUM('movie', 'tv') DEFAULT 'movie',
    parent_id INT DEFAULT NULL, -- For Replies
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
