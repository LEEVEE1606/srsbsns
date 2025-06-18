-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 18, 2025 at 04:39 PM
-- Server version: 8.0.42-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `srsbsns`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_config`
--

CREATE TABLE `admin_config` (
  `id` int NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` longtext NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_config`
--

INSERT INTO `admin_config` (`id`, `config_key`, `config_value`, `updated_at`) VALUES
(1, 'admin_email', 'leevee@itsonline.biz', '2025-06-18 15:18:43'),
(2, 'cc_email', '', '2025-06-18 15:18:43'),
(3, 'recaptcha_site_key', '6LccrRsqAAAAABW8J8Qs5rWyPXLamwk2MF0l3ASg', '2025-06-18 15:18:43'),
(4, 'recaptcha_secret_key', '6LccrRsqAAAAACsljVWeTKp2r925MVianOyUfXqd', '2025-06-18 15:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `api_call`
--

CREATE TABLE `api_call` (
  `id` int NOT NULL,
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` int NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `response_time` double DEFAULT NULL,
  `user_identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_call`
--

INSERT INTO `api_call` (`id`, `endpoint`, `method`, `status_code`, `user_agent`, `ip_address`, `created_at`, `response_time`, `user_identifier`) VALUES
(1, '/api/login_check', 'POST', 500, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Trailer/92.3.2902.22', '192.168.8.102', '2025-06-18 15:40:53', 415.87495803833, NULL),
(2, '/api/login_check', 'POST', 500, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Trailer/92.3.2902.22', '192.168.8.102', '2025-06-18 15:49:27', 788.50483894348, NULL),
(3, '/api/login_check', 'POST', 500, 'curl/8.5.0', '::1', '2025-06-18 15:49:47', 669.60692405701, NULL),
(4, '/api/login_check', 'POST', 500, 'curl/8.5.0', '::1', '2025-06-18 15:52:03', 453.13715934753, NULL),
(5, '/api/login_check', 'POST', 500, 'curl/8.5.0', '::1', '2025-06-18 15:54:31', 482.27214813232, NULL),
(6, '/api/login_check', 'POST', 401, 'curl/8.5.0', '::1', '2025-06-18 15:57:04', 706.72202110291, NULL),
(7, '/api/login_check', 'POST', 401, 'curl/8.5.0', '::1', '2025-06-18 15:57:24', 434.34906005859, NULL),
(8, '/api/login_check', 'POST', 401, 'curl/8.5.0', '::1', '2025-06-18 16:00:09', 527.17614173889, NULL),
(9, '/api/login_check', 'POST', 401, 'curl/8.5.0', '::1', '2025-06-18 16:00:45', 422.09887504578, NULL),
(10, '/api/login_check', 'POST', 401, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:01:17', 444.63801383972, NULL),
(11, '/api/login_check', 'POST', 401, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:02:48', 463.60516548157, NULL),
(12, '/api/login_check', 'POST', 401, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:03:49', 49.111843109131, NULL),
(13, '/api/login_check', 'POST', 401, 'curl/8.5.0', '::1', '2025-06-18 16:05:52', 97.159862518311, NULL),
(14, '/api/login_check', 'POST', 500, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:16:51', 798.65717887878, 'apiuser@srsbsns.com'),
(15, '/api/login_check', 'POST', 500, 'curl/8.5.0', '::1', '2025-06-18 16:19:00', 546.57506942749, 'apiuser@srsbsns.com'),
(16, '/api/login_check', 'POST', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:23:07', 493.17097663879, 'apiuser@srsbsns.com'),
(17, '/api/contacts', 'GET', 500, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:23:21', 108.58201980591, 'apiuser@srsbsns.com'),
(18, '/api/contacts', 'POST', 500, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:23:36', 404.3140411377, 'apiuser@srsbsns.com'),
(19, '/api/contacts/1', 'GET', 404, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:23:45', 44.94309425354, 'apiuser@srsbsns.com'),
(20, '/api/login_check', 'POST', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:23:55', 529.16288375854, 'apiuser@srsbsns.com'),
(21, '/api/contacts', 'GET', 500, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:24:48', 32.634973526001, 'apiuser@srsbsns.com'),
(22, '/api/login_check', 'POST', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:30:14', 445.5738067627, 'apiuser@srsbsns.com'),
(23, '/api/contacts', 'GET', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:30:19', 45.035123825073, 'apiuser@srsbsns.com'),
(24, '/api/contacts', 'POST', 422, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:30:24', 92.530012130737, 'apiuser@srsbsns.com'),
(25, '/api/contacts/1', 'GET', 404, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:30:32', 49.750804901123, 'apiuser@srsbsns.com'),
(26, '/api/contacts', 'POST', 422, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:30:57', 39.653062820435, 'apiuser@srsbsns.com'),
(27, '/api/contacts', 'GET', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:31:07', 28.712034225464, 'apiuser@srsbsns.com'),
(28, '/api/contacts', 'POST', 422, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:31:11', 34.00993347168, 'apiuser@srsbsns.com'),
(29, '/api/login_check', 'POST', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:34:33', 455.28602600098, 'apiuser@srsbsns.com'),
(30, '/api/contacts', 'GET', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:34:39', 19.587993621826, 'apiuser@srsbsns.com'),
(31, '/api/contacts', 'POST', 201, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:34:42', 61.291933059692, 'apiuser@srsbsns.com'),
(32, '/api/contacts/1', 'GET', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:34:52', 38.321018218994, 'apiuser@srsbsns.com'),
(33, '/api/contacts', 'GET', 200, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 LikeWise/96.6.6398.60', '192.168.8.102', '2025-06-18 16:34:57', 19.087076187134, 'apiuser@srsbsns.com');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `telephone`, `message`, `created_at`) VALUES
(1, 'Test Contact', 'test@example.com', '+1234567890', 'This is a test message from the API testing interface.', '2025-06-18 16:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250617111934', NULL, NULL),
('DoctrineMigrations\\Version20250618070357', '2025-06-18 07:04:04', 268),
('DoctrineMigrations\\Version20250618153003', NULL, NULL),
('DoctrineMigrations\\Version20250618153037', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin@srsbsns.com', '[\"ROLE_ADMIN\"]', '$2y$13$HzEqFfaFwDtGCGR4MBhBK.yJo7uz7GRwS4XmCjyF7UuGi6/A5PROi', 'Admin', 'User', 1, '2025-06-17 19:14:19', NULL),
(2, 'apiuser@srsbsns.com', '[\"ROLE_API_USER\"]', '$2y$13$6axcgqvNW3VN4CI9Q6tjqe9rJzq5ggvZwg3RIHVoY5wB9z7yvA7NS', 'API', 'User', 1, '2025-06-18 05:02:35', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_config`
--
ALTER TABLE `admin_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_89421E8595D1CAA6` (`config_key`);

--
-- Indexes for table `api_call`
--
ALTER TABLE `api_call`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_33401573B23DB7B8` (`created_at`),
  ADD KEY `IDX_33401573E7927C74` (`email`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_config`
--
ALTER TABLE `admin_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `api_call`
--
ALTER TABLE `api_call`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
