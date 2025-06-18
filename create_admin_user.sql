-- Create admin user for SRSBSNS Test App
-- First, make sure the users table exists with correct structure

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Remove existing admin user if exists
DELETE FROM `users` WHERE `email` = 'admin@srsbsns.com';

-- Insert admin user with bcrypt password hash for 'admin123'
INSERT INTO `users` (`email`, `roles`, `password`, `first_name`, `last_name`, `is_active`) VALUES 
('admin@srsbsns.com', '["ROLE_ADMIN"]', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 1);

-- Note: This password hash is for 'admin123' using bcrypt
-- You can generate a new hash using: php bin/console security:hash-password 