CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id VARCHAR(255) NOT NULL,
    device_udid VARCHAR(255) NOT NULL,
    license_token VARCHAR(255) UNIQUE NOT NULL,
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL
);
