-- v3.0.0 User Features

-- Update Users Table for Advanced Auth
ALTER TABLE users 
ADD COLUMN email VARCHAR(255) NOT NULL UNIQUE AFTER username,
ADD COLUMN avatar VARCHAR(255) DEFAULT NULL,
ADD COLUMN google_id VARCHAR(255) DEFAULT NULL,
ADD COLUMN is_verified TINYINT(1) DEFAULT 0,
ADD COLUMN verify_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN bio TEXT DEFAULT NULL;

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
    UNIQUE KEY unique_history (user_id, tmdb_id, type) -- Update timestamp on rewatch
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
