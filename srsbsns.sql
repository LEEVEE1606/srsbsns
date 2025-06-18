-- SRSBSNS Test App - Production Database Schema
-- Created by Lee van Rensburg
-- Symfony 7.3 Application with Contact Management, User Authentication, and REST API

-- =====================================================
-- 1. CREATE DATABASE (Optional - create manually if needed)
-- =====================================================
-- CREATE DATABASE IF NOT EXISTS `srsbsns` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `srsbsns`;

-- =====================================================
-- 2. CORE APPLICATION TABLES
-- =====================================================

-- Users table for admin authentication and API access
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `roles` JSON NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY(`id`),
    UNIQUE INDEX `UNIQ_1483A5E9E7927C74` (`email`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Contacts table for contact form submissions
CREATE TABLE `contacts` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `telephone` VARCHAR(20) NOT NULL,
    `message` LONGTEXT NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY(`id`),
    INDEX `IDX_33401573B23DB7B8` (`created_at`),
    INDEX `IDX_33401573E7927C74` (`email`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Admin configuration table for application settings
CREATE TABLE `admin_config` (
    `id` INT AUTO_INCREMENT NOT NULL,
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` LONGTEXT NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE INDEX `UNIQ_89421E8595D1CAA6` (`config_key`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Migration versions table for Doctrine migrations
CREATE TABLE `doctrine_migration_versions` (
    `version` VARCHAR(191) NOT NULL,
    `executed_at` DATETIME DEFAULT NULL,
    `execution_time` INT DEFAULT NULL,
    PRIMARY KEY(`version`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =====================================================
-- 3. INSERT INITIAL DATA
-- =====================================================

-- Create default admin user
-- Email: admin@srsbsns.com, Password: admin123
INSERT INTO `users` (`email`, `roles`, `password`, `first_name`, `last_name`, `is_active`, `created_at`) VALUES 
('admin@srsbsns.com', '["ROLE_ADMIN"]', '$2y$13$6.lOe0KtZl.3k7oM7lY.D.xY1M4xVrNkJMPdEUcLcJYfVwZz3xK2u', 'Admin', 'User', 1, NOW());

-- Create default API user
-- Email: apiuser@srsbsns.com, Password: api123
INSERT INTO `users` (`email`, `roles`, `password`, `first_name`, `last_name`, `is_active`, `created_at`) VALUES 
('apiuser@srsbsns.com', '["ROLE_API_USER"]', '$2y$13$6.lOe0KtZl.3k7oM7lY.D.xY1M4xVrNkJMPdEUcLcJYfVwZz3xK2u', 'API', 'User', 1, NOW());

-- Insert default configuration settings
INSERT INTO `admin_config` (`config_key`, `config_value`, `updated_at`) VALUES 
('admin_email', 'admin@srsbsns.com', NOW()),
('cc_email', '', NOW()),
('recaptcha_site_key', '', NOW()),
('recaptcha_secret_key', '', NOW());

-- =====================================================
-- 4. SAMPLE DATA (Optional - for demonstration)
-- =====================================================

-- Insert sample contact form submission
INSERT INTO `contacts` (`name`, `email`, `telephone`, `message`, `created_at`) VALUES 
('John Doe', 'john.doe@example.com', '+1234567890', 'This is a sample contact form submission for testing purposes.', NOW()),
('Jane Smith', 'jane.smith@example.com', '+0987654321', 'Hello! I am interested in learning more about your services. Please get back to me at your earliest convenience.', NOW());

-- =====================================================
-- 5. IMPORTANT NOTES FOR PRODUCTION DEPLOYMENT
-- =====================================================

-- SECURITY CHECKLIST:
-- 1. Change default passwords immediately after deployment
-- 2. Update admin_email to your actual admin email address
-- 3. Configure reCAPTCHA keys for spam protection
-- 4. Set up proper email SMTP configuration in .env
-- 5. Generate new JWT keys for API authentication
-- 6. Review and update all environment variables

-- PERFORMANCE OPTIMIZATION:
-- 1. Add appropriate indexes for frequently queried columns
-- 2. Consider partitioning contacts table if expecting high volume
-- 3. Set up proper MySQL configuration for your server specs

-- BACKUP STRATEGY:
-- 1. Schedule regular database backups
-- 2. Test backup restoration procedures
-- 3. Keep backups in secure, offsite location

-- ACCESS CREDENTIALS (CHANGE IMMEDIATELY):
-- Admin Panel: /admin/login
--   Email: admin@srsbsns.com
--   Password: admin123
-- 
-- API Access: POST /api/login_check
--   Email: apiuser@srsbsns.com  
--   Password: api123

-- For additional security, run these commands after deployment:
-- php bin/console security:hash-password [new_password]
-- php bin/console app:create-users (to recreate users with new passwords)

-- =====================================================
-- END OF SRSBSNS DATABASE SCHEMA
-- =====================================================
