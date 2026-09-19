CREATE DATABASE IF NOT EXISTS traffic_tracker_test;

USE traffic_tracker_test;

CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(2048) NOT NULL,
    referrer VARCHAR(2048) DEFAULT NULL,
    visitor_id VARCHAR(64) NOT NULL,
    user_agent VARCHAR(256) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Grant the app user access to the test database too
GRANT ALL PRIVILEGES ON traffic_tracker_test.* TO 'tracker_user'@'%';
FLUSH PRIVILEGES;