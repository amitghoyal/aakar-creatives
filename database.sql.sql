-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql106.infinityfree.com
-- Generation Time: Jun 26, 2026 at 04:05 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41961744_aakar_creatives`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password_hash` varchar(255) NOT NULL COMMENT 'bcrypt/argon2 hash — never store plain text',
  `role` enum('super_admin','admin','viewer') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(3, 'Amit Admin', 'amitg@gmail.com', '$2y$10$IsQ9KRmf.RVsgglcXuR3fusM2b3XMra5HYeqy1bGoM1HmyE7CqoxS', 'super_admin', 1, '2026-05-23 22:57:58', '2026-05-19 09:36:58', '2026-05-23 22:57:58'),
(4, 'Manish Ghoyal', 'manishgohil3036@gmail.com', '$2y$10$ltKhOC873wN2jQgrKIyKTeeGNr9FMN5YuEfUiOaDGJBNoDpmZt5se', 'admin', 1, NULL, '2026-05-19 09:43:00', '2026-05-19 09:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'e.g. product.create, inquiry.status_change',
  `target_type` varchar(60) DEFAULT NULL COMMENT 'e.g. product, inquiry, order',
  `target_id` int(10) UNSIGNED DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Before/after values or context'
) ;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'product.create', 'product', 7, NULL, '::1', '2026-05-18 16:49:15'),
(2, 1, 'category.create', 'category', 7, NULL, '::1', '2026-05-18 16:50:29'),
(3, 1, 'product.delete', 'product', 5, NULL, '::1', '2026-05-18 17:07:20'),
(4, 1, 'category.edit', 'category', 1, NULL, '::1', '2026-05-18 17:15:32'),
(5, 1, 'product.create', 'product', 8, NULL, '::1', '2026-05-18 17:25:02'),
(6, 1, 'auth.logout', NULL, NULL, NULL, '::1', '2026-05-18 17:26:04'),
(7, 1, 'auth.login', NULL, NULL, NULL, '::1', '2026-05-18 17:27:08'),
(8, 3, 'auth.login', NULL, NULL, NULL, '::1', '2026-05-19 09:37:43'),
(9, 3, 'product.create', 'product', 9, NULL, '::1', '2026-05-19 09:39:28'),
(10, 3, 'auth.logout', NULL, NULL, NULL, '::1', '2026-05-19 10:43:58'),
(11, 3, 'auth.login', NULL, NULL, NULL, '::1', '2026-05-19 10:44:13'),
(12, 3, 'product.create', 'product', 10, NULL, '::1', '2026-05-19 11:04:27'),
(13, 3, 'product.delete', 'product', 8, NULL, '::1', '2026-05-19 11:09:57'),
(14, 3, 'product.delete', 'product', 10, NULL, '::1', '2026-05-19 11:10:01'),
(15, 3, 'product.delete', 'product', 7, NULL, '::1', '2026-05-19 11:10:05'),
(16, 3, 'product.delete', 'product', 1, NULL, '::1', '2026-05-19 11:10:09'),
(17, 3, 'product.delete', 'product', 2, NULL, '::1', '2026-05-19 11:10:12'),
(18, 3, 'product.delete', 'product', 3, NULL, '::1', '2026-05-19 11:10:16'),
(19, 3, 'product.delete', 'product', 4, NULL, '::1', '2026-05-19 11:10:20'),
(20, 3, 'product.delete', 'product', 6, NULL, '::1', '2026-05-19 11:10:23'),
(21, 3, 'auth.logout', NULL, NULL, NULL, '::1', '2026-05-19 11:13:33'),
(22, 3, 'auth.login', NULL, NULL, NULL, '::1', '2026-05-19 11:14:17'),
(23, 3, 'product.create', 'product', 11, NULL, '::1', '2026-05-19 11:52:34'),
(24, 3, 'product.create', 'product', 12, NULL, '::1', '2026-05-19 11:56:45'),
(25, 3, 'product.create', 'product', 13, NULL, '::1', '2026-05-19 12:00:03'),
(26, 3, 'product.create', 'product', 14, NULL, '::1', '2026-05-19 12:01:10'),
(27, 3, 'product.edit', 'product', 14, NULL, '::1', '2026-05-19 12:01:43'),
(28, 3, 'product.edit', 'product', 14, NULL, '::1', '2026-05-19 12:14:50'),
(0, 0, 'category.edit', 'category', 7, NULL, '49.34.114.107', '2026-05-19 03:23:31'),
(0, 0, 'category.edit', 'category', 1, NULL, '49.34.114.107', '2026-05-19 03:23:59'),
(0, 0, 'category.edit', 'category', 2, NULL, '49.34.114.107', '2026-05-19 03:24:27'),
(0, 0, 'category.edit', 'category', 1, NULL, '49.34.114.107', '2026-05-19 03:24:55'),
(0, 0, 'category.edit', 'category', 3, NULL, '49.34.114.107', '2026-05-19 03:25:12'),
(0, 0, 'category.edit', 'category', 5, NULL, '49.34.114.107', '2026-05-19 03:26:48'),
(0, 0, 'category.edit', 'category', 7, NULL, '49.34.114.107', '2026-05-19 03:27:01'),
(0, 0, 'category.edit', 'category', 3, NULL, '49.34.114.107', '2026-05-19 03:27:07'),
(0, 0, 'category.edit', 'category', 5, NULL, '49.34.114.107', '2026-05-19 03:27:14'),
(0, 0, 'category.create', 'category', NULL, NULL, '49.34.114.107', '2026-05-19 03:31:00'),
(0, 0, 'category.edit', 'category', NULL, NULL, '49.34.114.107', '2026-05-19 03:31:09'),
(0, 0, 'product.delete', 'product', 14, NULL, '49.34.114.107', '2026-05-19 03:36:53'),
(0, 0, 'auth.logout', NULL, NULL, NULL, '49.34.114.107', '2026-05-19 04:25:40'),
(0, 3, 'auth.login', NULL, NULL, NULL, '106.213.158.205', '2026-05-19 20:50:06'),
(0, 3, 'product.delete', 'product', 9, NULL, '106.213.158.205', '2026-05-19 21:15:42'),
(0, 3, 'product.create', 'product', NULL, NULL, '106.213.158.205', '2026-05-19 21:43:30'),
(0, 3, 'order.create', 'order', NULL, NULL, '106.213.158.205', '2026-05-19 21:45:12'),
(0, 3, 'auth.login', NULL, NULL, NULL, '49.34.99.228', '2026-05-20 22:43:03'),
(0, 3, 'category.edit', 'category', NULL, NULL, '157.32.82.64', '2026-05-21 18:38:38'),
(0, 3, 'auth.login', NULL, NULL, NULL, '157.32.82.154', '2026-05-21 21:44:49'),
(0, 3, 'product.create', 'product', 1000, NULL, '157.32.82.154', '2026-05-21 21:54:00'),
(0, 3, 'product.edit', 'product', 1000, NULL, '157.32.82.154', '2026-05-21 21:54:15'),
(0, 3, 'product.delete', 'product', 1000, NULL, '157.32.82.154', '2026-05-21 21:54:21'),
(0, 3, 'product.create', 'product', 1001, NULL, '157.32.82.154', '2026-05-21 21:55:29'),
(0, 3, 'product.create', 'product', 1002, NULL, '157.32.82.154', '2026-05-21 21:56:52'),
(0, 3, 'product.create', 'product', 1003, NULL, '157.32.82.154', '2026-05-21 21:59:39'),
(0, 3, 'auth.login', NULL, NULL, NULL, '49.34.187.89', '2026-05-22 21:08:49'),
(0, 3, 'auth.login', NULL, NULL, NULL, '49.34.194.150', '2026-05-22 22:02:21'),
(0, 3, 'product.create', 'product', 1004, NULL, '49.34.218.137', '2026-05-23 03:59:07'),
(0, 3, 'product.create', 'product', 1007, NULL, '49.34.218.137', '2026-05-23 04:11:03'),
(0, 3, 'product.create', 'product', 1008, NULL, '49.34.218.137', '2026-05-23 04:16:04'),
(0, 3, 'product.create', 'product', 1009, NULL, '49.34.218.137', '2026-05-23 04:27:01'),
(0, 3, 'product.delete', 'product', 1009, NULL, '49.34.218.137', '2026-05-23 04:32:23'),
(0, 3, 'product.create', 'product', 1010, NULL, '49.34.218.137', '2026-05-23 04:34:30'),
(0, 3, 'product.create', 'product', 1011, NULL, '49.34.218.137', '2026-05-23 04:43:21'),
(0, 3, 'product.edit', 'product', 1010, NULL, '49.34.218.137', '2026-05-23 04:45:41'),
(0, 3, 'product.delete', 'product', 1011, NULL, '49.34.218.137', '2026-05-23 04:45:58'),
(0, 3, 'product.delete', 'product', 1010, NULL, '49.34.218.137', '2026-05-23 04:46:41'),
(0, 3, 'product.delete', 'product', 1008, NULL, '49.34.218.137', '2026-05-23 04:46:45'),
(0, 3, 'product.delete', 'product', 1007, NULL, '49.34.218.137', '2026-05-23 04:46:49'),
(0, 3, 'product.delete', 'product', 1004, NULL, '49.34.218.137', '2026-05-23 04:46:54'),
(0, 3, 'product.edit', 'product', 1003, NULL, '49.34.218.86', '2026-05-23 05:08:28'),
(0, 3, 'product.edit', 'product', 1002, NULL, '49.34.218.86', '2026-05-23 05:09:16'),
(0, 3, 'product.delete', 'product', 1003, NULL, '49.34.218.86', '2026-05-23 05:16:15'),
(0, 3, 'product.delete', 'product', 1002, NULL, '49.34.218.86', '2026-05-23 05:16:20'),
(0, 3, 'product.delete', 'product', 1001, NULL, '49.34.218.86', '2026-05-23 05:16:23'),
(0, 3, 'product.delete', 'product', 7, NULL, '49.34.218.86', '2026-05-23 05:33:56'),
(0, 3, 'product.create', 'product', 1012, NULL, '49.34.218.86', '2026-05-23 05:40:13'),
(0, 3, 'product.create', 'product', 1013, NULL, '49.34.218.86', '2026-05-23 05:41:31'),
(0, 3, 'product.create', 'product', 1014, NULL, '49.34.218.86', '2026-05-23 05:42:48'),
(0, 3, 'product.create', 'product', 1015, NULL, '49.34.218.86', '2026-05-23 05:44:21'),
(0, 3, 'product.create', 'product', 1016, NULL, '49.34.218.86', '2026-05-23 05:46:17'),
(0, 3, 'product.create', 'product', 1017, NULL, '49.34.218.86', '2026-05-23 05:51:35'),
(0, 3, 'product.create', 'product', 1018, NULL, '49.34.218.86', '2026-05-23 05:53:17'),
(0, 3, 'product.create', 'product', 1019, NULL, '49.34.218.86', '2026-05-23 05:55:08'),
(0, 3, 'product.create', 'product', 1020, NULL, '49.34.218.86', '2026-05-23 05:56:45'),
(0, 3, 'product.create', 'product', 1021, NULL, '49.34.218.86', '2026-05-23 05:58:40'),
(0, 3, 'product.create', 'product', 1022, NULL, '49.34.218.86', '2026-05-23 06:00:45'),
(0, 3, 'auth.login', NULL, NULL, NULL, '106.213.171.149', '2026-05-23 22:57:58'),
(0, 3, 'product.delete', 'product', 1016, NULL, '106.213.171.149', '2026-05-23 22:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_events`
--

CREATE TABLE `analytics_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_type` enum('product_view','whatsapp_click','category_view','homepage_visit') NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_hash` char(64) DEFAULT NULL COMMENT 'SHA-256 of visitor IP — no PII stored',
  `user_agent` varchar(300) DEFAULT NULL,
  `referrer` varchar(300) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `analytics_events`
--

INSERT INTO `analytics_events` (`id`, `event_type`, `product_id`, `category_id`, `ip_hash`, `user_agent`, `referrer`, `created_at`) VALUES
(1, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:41:05'),
(2, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:41:53'),
(3, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:42:23'),
(4, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:42:34'),
(5, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:42:51'),
(6, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:43:03'),
(7, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:43:21'),
(8, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 08:53:22'),
(9, 'whatsapp_click', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 08:53:29'),
(10, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php?occasion=anniversary%2Cbirthday%2Cvalentines-day', '2026-05-19 08:54:52'),
(11, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php?occasion=anniversary%2Cbirthday%2Cvalentines-day', '2026-05-19 08:54:59'),
(12, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:55:07'),
(13, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php?q=Frame', '2026-05-19 08:55:39'),
(14, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:55:51'),
(15, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 08:56:59'),
(16, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 08:58:05'),
(17, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 08:58:54'),
(18, 'product_view', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 09:03:54'),
(19, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 09:04:28'),
(20, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 09:05:14'),
(21, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 09:06:18'),
(22, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 09:06:38'),
(23, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/', '2026-05-19 10:43:16'),
(24, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/', '2026-05-19 11:13:40'),
(25, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 11:14:01'),
(26, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 11:14:06'),
(27, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 11:52:50'),
(28, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:03:56'),
(29, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:05:35'),
(30, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:10:07'),
(31, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:10:50'),
(32, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 12:13:02'),
(33, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 12:13:33'),
(34, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 12:13:45'),
(35, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 12:15:58'),
(36, 'whatsapp_click', 12, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:16:17'),
(37, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:16:56'),
(38, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:18:52'),
(39, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:19:02'),
(40, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:19:12'),
(41, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:20:34'),
(42, 'whatsapp_click', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:20:53'),
(43, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:21:08'),
(44, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:21:55'),
(45, 'product_view', 12, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:23:02'),
(46, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:23:10'),
(47, 'homepage_visit', NULL, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/index.php', '2026-05-19 12:23:16'),
(48, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:24:12'),
(49, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:25:04'),
(50, 'product_view', 11, NULL, 'eff8e7ca506627fe15dda5e0e512fcaad70b6d520f37cc76597fdb4f2d83a1a3', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'http://localhost/aakar-creatives-website/shop.php', '2026-05-19 12:25:57'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:20:37'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:21:33'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:23:05'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:27:22'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:31:16'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:32:29'),
(0, 'whatsapp_click', 12, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 03:35:19'),
(0, 'product_view', 12, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:37:27'),
(0, 'product_view', 12, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:37:33'),
(0, 'product_view', 13, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:37:37'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:37:43'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:38:50'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:49:11'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:53:04'),
(0, 'product_view', 11, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:53:20'),
(0, 'product_view', 12, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:53:31'),
(0, 'product_view', 11, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:53:35'),
(0, 'product_view', 12, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:53:42'),
(0, 'product_view', 11, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:54:56'),
(0, 'product_view', 11, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:55:02'),
(0, 'product_view', 12, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:55:09'),
(0, 'product_view', 11, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:55:15'),
(0, 'product_view', 11, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:55:25'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:55:29'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:55:35'),
(0, 'homepage_visit', NULL, NULL, '9f27027e88f89a996954c8dfa16e4de86cb05fd5d06d61d90aed2b07bb2d40ae', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:55:40'),
(0, 'product_view', 11, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:55:52'),
(0, 'product_view', 12, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:55:57'),
(0, 'product_view', 13, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:56:20'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:56:29'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 03:56:32'),
(0, 'product_view', 12, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:56:55'),
(0, 'product_view', 12, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:57:00'),
(0, 'product_view', 11, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:57:02'),
(0, 'product_view', 12, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 03:57:13'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:57:17'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:57:41'),
(0, 'homepage_visit', NULL, NULL, '8e30ecfb1fa6d38771432f057e203eff1a6fc1ea06395cfffedb4d15344e396c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:58:01'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:59:24'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 03:59:47'),
(0, 'product_view', 11, NULL, 'b8062805f6d03c910aa82e1ec0682cc0579351d9e5c792de303062b4b5f5da9f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 04:12:45'),
(0, 'whatsapp_click', 11, NULL, 'b8062805f6d03c910aa82e1ec0682cc0579351d9e5c792de303062b4b5f5da9f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 04:13:04'),
(0, 'product_view', 11, NULL, 'b8062805f6d03c910aa82e1ec0682cc0579351d9e5c792de303062b4b5f5da9f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 04:13:11'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:14:32'),
(0, 'product_view', 11, NULL, '6a9c9abda56179cd9727e5235829e9e05d69e8d03895574512ce0cfcae15f2ea', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 04:15:21'),
(0, 'product_view', 11, NULL, '6a9c9abda56179cd9727e5235829e9e05d69e8d03895574512ce0cfcae15f2ea', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 04:15:27'),
(0, 'product_view', 11, NULL, '6a9c9abda56179cd9727e5235829e9e05d69e8d03895574512ce0cfcae15f2ea', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 04:15:32'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:15:57'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:16:33'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:16:59'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:17:46'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:18:04'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:18:30'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:19:02'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:21:37'),
(0, 'homepage_visit', NULL, NULL, '726f808eb8bbf3cca5f0fd3b649db5383a3de1396e10eae9c3fe6c6c1eb04aa1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:34:30'),
(0, 'homepage_visit', NULL, NULL, '8a88800d40df9e09cb79f96ababa0c491f891713322da75ae392972e387b47ff', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 04:44:00'),
(0, 'product_view', 12, NULL, '84327d83b4bdacd413a500eecdd2238b035478812bad5900fc47ad5824fdf9dd', 'Mozilla/5.0 (Linux; U; Android 13; en-ar; RMX3461 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.5970.168 Mobile Safari/537.36 HeyTapBrowser/45.14.0.1', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 07:28:43'),
(0, 'homepage_visit', NULL, NULL, '84327d83b4bdacd413a500eecdd2238b035478812bad5900fc47ad5824fdf9dd', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Safari/537.36 HeyTapBrowser/45.14.0.1 Chrome/115.0.5970.168', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 07:29:36'),
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 20:02:52'),
(0, 'product_view', 12, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 20:02:59'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:03:09'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:15:11'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:16:10'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:18:35'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:20:53'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:22:04'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/occasions.php?occasion=birthday', '2026-05-19 20:25:36'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-19 20:37:08'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:39:43'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:41:37'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:43:44'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:43:45'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 20:45:32'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 20:46:06'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-19 21:44:07'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 21:45:50'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-19 21:46:04'),
(0, 'product_view', 13, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 21:46:23'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 21:46:38'),
(0, 'product_view', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 22:05:28'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 22:09:07'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 22:10:30'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/occasions.php?occasion=anniversary&i=1', '2026-05-19 22:15:45'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:16:03'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:17:08'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:17:47'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 22:18:59'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-19 22:32:30'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-19 22:32:35'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-19 22:32:43'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-19 22:32:53'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-19 22:33:10'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 22:33:34'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 22:34:22'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 22:34:54'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 22:37:05'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 22:39:33'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-19 22:40:03'),
(0, 'product_view', 11, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 22:40:14'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 22:40:22'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-19 22:40:35'),
(0, 'product_view', 12, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 22:40:49'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-19 22:41:52'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:42:09'),
(0, 'product_view', 11, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:42:26'),
(0, 'homepage_visit', NULL, NULL, '47e7eb3cca64f9687ed3dc376532c917dd490a025c31bec4abbe9a5eaddae32f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:42:33'),
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:49:47'),
(0, 'homepage_visit', NULL, NULL, '3bd27a33668bd3c810cf85e3f0a1c08433c13c3665eb71fb592f4e3627609f44', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-19 22:55:08'),
(0, 'homepage_visit', NULL, NULL, '1d01cc72f3d2a5f472307a8d19a59f589084ae65b319cd9312d6f7497656825f', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-19 22:55:08'),
(0, 'homepage_visit', NULL, NULL, 'e12a59f91681cf15de483bfea9093a143efac94b6af6f59dfef808a2decb5032', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-19 22:55:08'),
(0, 'homepage_visit', NULL, NULL, '96ae33ae42538dd9426e232772f9e5330b5a3c7e03dc2ceb7242791ca508e7e7', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-19 22:55:09'),
(0, 'homepage_visit', NULL, NULL, 'cc974d83d74cc0d7638395f49d4e7e5c6b48dbcb7b0c7bc59f964b168a168896', 'Mozilla/5.0 (Linux; Android 12; M2003J15SC Build/SP1A.210812.016; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.156 Mobile Safari/537.36 [FBAN/FB4A;FBAV/560.0.0.57.63;FBBV/963497797;FBDM/{density=2.75,width=1080,height=2110};FBLC/bn_IN;FBRV/970220379;FB_FW/2;FBCR/Ooredoo;F', 'https://www.facebook.com/', '2026-05-19 22:55:09'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnNAl2IcPcBei9Ph7Hu2WsrjN13kREvpo9znjBMoOmo1nIuxk1-ptvZSi-XXw_aem_9IGzY413G5ue9a5gGNEv-Q', '2026-05-19 22:55:16');
INSERT INTO `analytics_events` (`id`, `event_type`, `product_id`, `category_id`, `ip_hash`, `user_agent`, `referrer`, `created_at`) VALUES
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:55:44'),
(0, 'homepage_visit', NULL, NULL, '097bbd4bc9ccc664a1a9b3b6181b8be59ff329cdd83affed9971b0d3580d772c', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-19 22:55:45'),
(0, 'whatsapp_click', 12, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 22:55:50'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://l.instagram.com/', '2026-05-19 22:56:11'),
(0, 'product_view', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:04:40'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://l.instagram.com/', '2026-05-19 23:05:41'),
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:05:49'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-19 23:12:21'),
(0, 'homepage_visit', NULL, NULL, '6f0bebd1d2aec5dcb5099e857b752a4131742fff17ce3249bf00fa951cd67d92', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:14:11'),
(0, 'homepage_visit', NULL, NULL, '4c57d60455baa69fa293652f18910074e6cf84858538611d758e917e65733ba8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:14:17'),
(0, 'product_view', 11, NULL, '6f0bebd1d2aec5dcb5099e857b752a4131742fff17ce3249bf00fa951cd67d92', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:14:30'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-19 23:15:24'),
(0, 'product_view', 11, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 23:15:31'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-19 23:16:42'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 23:17:07'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 23:17:11'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 23:17:14'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-19 23:17:43'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 23:20:08'),
(0, 'product_view', 11, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:20:13'),
(0, 'product_view', 11, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:22:10'),
(0, 'product_view', 13, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:22:30'),
(0, 'product_view', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:22:54'),
(0, 'whatsapp_click', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:23:08'),
(0, 'homepage_visit', NULL, NULL, 'f5297a81cc28fd01f707a0a92821a091e2aa3e3619931a1e73098d5def674110', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-19 23:24:10'),
(0, 'homepage_visit', NULL, NULL, '629741d6d80bc2877bea916c49a9acfcd6543cbdb34f52b04c9ed6059199d12b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:34:07'),
(0, 'homepage_visit', NULL, NULL, 'd993bfffd6e7c17b53114868d1929463212653d75077b796ba5f25162aecf5fc', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:35:36'),
(0, 'homepage_visit', NULL, NULL, '832de3d7bd8a83b7d3a84d4556dcf3158a5c908b7487da80eac2a2c888a520fd', 'Mozilla/5.0 (Linux; U; Android 16; I2405 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 OPR/99.1.2254.455', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:36:59'),
(0, 'homepage_visit', NULL, NULL, 'dc833a410b16ae9148095b293360fae00af7c22eb946ff6937dadbf4694b8d83', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:38:58'),
(0, 'homepage_visit', NULL, NULL, '5d9a043fd76bc46362dcdea797968cb928b22c1a3abdf882fe4020798e83a0b8', 'Mozilla/5.0 (Linux; Android 10; Redmi Note 9 Pro Max) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:39:50'),
(0, 'homepage_visit', NULL, NULL, '547a20c0a2cf01e786422a609367a81275522df6a12aa662f2381fbc8ba42a9d', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Mobile/15E148 Safari/604.1', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:39:57'),
(0, 'homepage_visit', NULL, NULL, 'cfc56ffc850d63feb9b42f210982bfc3f4d31334f5cf61fd8b7220551e01ad33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:41:40'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:41:43'),
(0, 'homepage_visit', NULL, NULL, 'cfc56ffc850d63feb9b42f210982bfc3f4d31334f5cf61fd8b7220551e01ad33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/?i=1', '2026-05-19 23:42:00'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-19 23:42:05'),
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:42:14'),
(0, 'product_view', 12, NULL, 'cfc56ffc850d63feb9b42f210982bfc3f4d31334f5cf61fd8b7220551e01ad33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:42:26'),
(0, 'whatsapp_click', 12, NULL, 'cfc56ffc850d63feb9b42f210982bfc3f4d31334f5cf61fd8b7220551e01ad33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:42:37'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://l.instagram.com/', '2026-05-19 23:45:39'),
(0, 'homepage_visit', NULL, NULL, '26860ef7259b6f3e968fa0a53abc51fb686367db8f8a205f4551aae38e837afe', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', 'https://aakar-creatives.infinityfree.me/', '2026-05-19 23:45:46'),
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:45:48'),
(0, 'product_view', 12, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:46:01'),
(0, 'product_view', 11, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:48:59'),
(0, 'product_view', 12, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:49:11'),
(0, 'product_view', 13, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:49:16'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-19 23:49:20'),
(0, 'homepage_visit', NULL, NULL, 'adce00c8d92274c582a0fb9ae7cd04e891819097a45674e43bff245187320a8b', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-19 23:49:44'),
(0, 'homepage_visit', NULL, NULL, '48e1d037243094b13a587971f957fc28469b00bc68f2e43bbbe6e9b855f64e7b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 00:44:44'),
(0, 'product_view', 11, NULL, '48e1d037243094b13a587971f957fc28469b00bc68f2e43bbbe6e9b855f64e7b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 00:45:34'),
(0, 'product_view', 12, NULL, '48e1d037243094b13a587971f957fc28469b00bc68f2e43bbbe6e9b855f64e7b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 00:45:39'),
(0, 'product_view', 13, NULL, '48e1d037243094b13a587971f957fc28469b00bc68f2e43bbbe6e9b855f64e7b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 00:46:01'),
(0, 'homepage_visit', NULL, NULL, '48e1d037243094b13a587971f957fc28469b00bc68f2e43bbbe6e9b855f64e7b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/?i=1', '2026-05-20 00:46:16'),
(0, 'homepage_visit', NULL, NULL, '53001aa20ef97c3cf86a83e5255e913faca3d83eef738e8cc257ff3c0c1cac5a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 01:28:10'),
(0, 'homepage_visit', NULL, NULL, 'b9ff8ca167c59a71c2966773d7a9eb638f85db157209b75942c41a8d2050044a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 04:32:48'),
(0, 'homepage_visit', NULL, NULL, '468486a0917c270dbdfbc34f98b1ef9ecb21f2b460a37092849bf7677dc7c8e9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-20 06:17:28'),
(0, 'homepage_visit', NULL, NULL, '468486a0917c270dbdfbc34f98b1ef9ecb21f2b460a37092849bf7677dc7c8e9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 06:17:35'),
(0, 'product_view', 12, NULL, '468486a0917c270dbdfbc34f98b1ef9ecb21f2b460a37092849bf7677dc7c8e9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 06:17:42'),
(0, 'homepage_visit', NULL, NULL, 'a855d53afdbcefac32d0d93237f58ef3b7af415ac46f24d34902f4c80ed01fe3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 07:47:53'),
(0, 'homepage_visit', NULL, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 20:29:44'),
(0, 'homepage_visit', NULL, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 20:29:45'),
(0, 'homepage_visit', NULL, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 20:30:15'),
(0, 'product_view', 11, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 20:31:59'),
(0, 'product_view', 11, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 20:32:02'),
(0, 'whatsapp_click', 11, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 20:32:02'),
(0, 'homepage_visit', NULL, NULL, '86cfe2467f11c350580d7e1a7197858c154aa964f52dda60865404f7a437d59a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-20 20:32:12'),
(0, 'homepage_visit', NULL, NULL, '43716f1050bab17dce0ff5764decdb5a3cfc5b3f7b0158e767af5e897b8fde55', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 22:39:27'),
(0, 'homepage_visit', NULL, NULL, '823ce57cd6454a57e66925ed63251036b0ce1a31da3aa204365365b6cfd9070a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-20 22:39:34'),
(0, 'homepage_visit', NULL, NULL, '7e045b8fdf889e020d72e329f1492e185c66a38e91630d7f155b6d93b77f81c5', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23C55 Instagram 428.2.0.37.66 (iPhone15,4; iOS 26_2; en_GB; en-GB; scale=3.00; 1179x2556; IABMV/1; 961927775) NW/3 Safari/604.1', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPMTI0MDI0NTc0Mjg3NDE0AAGnfEfvYRjn5rLleJ5G0zJbjp8hjiNn-BogsOfjuiLGTfBJjQE0FohqoRN5voU_aem_CMVvjIcdmCBXbH1JOFcncg', '2026-05-21 06:06:44'),
(0, 'homepage_visit', NULL, NULL, 'c7a07fde1763a3499b1d4bc2e22b6f912c030b64d69d2d79063798a4b4fe26c2', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-21 06:07:00'),
(0, 'homepage_visit', NULL, NULL, 'da660878f495aa9b6ee4a98d25dd73ae2b49bfd76ea8eac9d65261dacfa3f43e', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://www.facebook.com/', '2026-05-21 06:07:03'),
(0, 'homepage_visit', NULL, NULL, 'c44d48f905c8917a82d33096708f603dfaeb1705c859f2bd05ddcc45c6e1f7da', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-21 06:07:38'),
(0, 'homepage_visit', NULL, NULL, 'd1c5a172c82cf634be6183e2f07b9881610e2c9a18bbfecfc52fa91b05c2bea9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-21 06:15:13'),
(0, 'homepage_visit', NULL, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnIIEJTY5sWWC6q4DeaAS9qrhMjEs4zpXQt_wlnw2yKI6pdw7VKQkMzV5pg-I_aem_UzIP7ZNf8I7RHzQE5ttjRQ', '2026-05-21 07:01:42'),
(0, 'homepage_visit', NULL, NULL, '2f89cc93554874b9509ceebd561f1d0baf93a948fce1586ca4cb85adf92de224', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-21 07:01:51'),
(0, 'homepage_visit', NULL, NULL, '003092adb0ef63d08c1af940428d2382f58b15125d1848b36eee575f9de5ef95', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'https://www.facebook.com/', '2026-05-21 07:01:57'),
(0, 'homepage_visit', NULL, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAb21jcAR775lleHRuA2FlbQIxMQBzcnRjBmFwcF9pZA81NjcwNjczNDMzNTI0MjcAAacggQlNjmxZYLqrgN5oBL2quEyMSzjOldC3_CWfDbIojql3DtUpCQzNXmmD4g_aem_UzIP7ZNf8I7RHzQE5ttjRQ&i=1', '2026-05-21 07:01:58'),
(0, 'homepage_visit', NULL, NULL, '3c75840a303768b795382057cfe18376614f7cb4c1a93a9c63cfee61bc0bf4b2', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-21 07:02:35'),
(0, 'homepage_visit', NULL, NULL, '94e94686e72dbb7202956e9d8bfcf2eb47c4005cf145e0dd52df2a551886ff8f', 'Mozilla/5.0 (Linux; Android 16; CPH2447 Build/TP1A.220905.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.172 Mobile Safari/537.36 Instagram 430.0.0.53.80 Android (36/16; 640dpi; 1440x3216; OnePlus; CPH2447; OP594DL1; qcom; en_IN; 974607439; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAb21jcAR775lleHRuA2FlbQIxMQBzcnRjBmFwcF9pZA81NjcwNjczNDMzNTI0MjcAAacggQlNjmxZYLqrgN5oBL2quEyMSzjOldC3_CWfDbIojql3DtUpCQzNXmmD4g_aem_UzIP7ZNf8I7RHzQE5ttjRQ&i=1', '2026-05-21 07:02:38'),
(0, 'homepage_visit', NULL, NULL, '5c2a029e2d68b9ab1af9cbc7a9d2248a7df05b35cf58d5b996cb89fdaa68041e', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-21 07:02:52'),
(0, 'homepage_visit', NULL, NULL, '94e94686e72dbb7202956e9d8bfcf2eb47c4005cf145e0dd52df2a551886ff8f', 'Mozilla/5.0 (Linux; Android 16; CPH2447 Build/TP1A.220905.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.172 Mobile Safari/537.36 Instagram 430.0.0.53.80 Android (36/16; 640dpi; 1440x3216; OnePlus; CPH2447; OP594DL1; qcom; en_IN; 974607439; IABMV/1)', 'https://l.instagram.com/', '2026-05-21 07:03:04'),
(0, 'homepage_visit', NULL, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://l.instagram.com/', '2026-05-21 07:03:18'),
(0, 'product_view', 11, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:03:37'),
(0, 'product_view', 12, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:03:42'),
(0, 'homepage_visit', NULL, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:03:47'),
(0, 'homepage_visit', NULL, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://l.instagram.com/', '2026-05-21 07:04:32'),
(0, 'product_view', 11, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:05:09'),
(0, 'product_view', 11, NULL, '9985deeb8313920eb1075481eb45a83cabd23b1a7e279f06b817e82ea48550d1', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:05:20'),
(0, 'homepage_visit', NULL, NULL, 'ebe48fed14ad88c310e64ca5f2b7887a41ad82dc517c5e3be84609f16c193a76', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnuDnpY22wKDYhC5YLMtyrUhgXOf3us1qBjJUPZ5tP7XwifwHWSXcPJpRyoh0_aem_hXBVgSXFxJdupdV5UUsudw', '2026-05-21 07:37:54'),
(0, 'product_view', 11, NULL, 'ebe48fed14ad88c310e64ca5f2b7887a41ad82dc517c5e3be84609f16c193a76', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:38:12'),
(0, 'homepage_visit', NULL, NULL, 'ebe48fed14ad88c310e64ca5f2b7887a41ad82dc517c5e3be84609f16c193a76', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 07:38:34'),
(0, 'homepage_visit', NULL, NULL, 'ebe48fed14ad88c310e64ca5f2b7887a41ad82dc517c5e3be84609f16c193a76', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.137 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-21 07:38:35'),
(0, 'homepage_visit', NULL, NULL, '9f55449e218fe60576c258db3b8ca185ccca89eba7992208328b7b00c9184bf2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-21 18:10:52'),
(0, 'homepage_visit', NULL, NULL, '9f55449e218fe60576c258db3b8ca185ccca89eba7992208328b7b00c9184bf2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 18:10:59'),
(0, 'homepage_visit', NULL, NULL, 'dd34a52c024cded8c1d24fba1134e03043d220a9a206e8ca138ce623f3024563', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-21 20:58:39'),
(0, 'homepage_visit', NULL, NULL, 'dd34a52c024cded8c1d24fba1134e03043d220a9a206e8ca138ce623f3024563', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-21 21:18:25'),
(0, 'homepage_visit', NULL, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/admin.php?page=products', '2026-05-21 21:47:51'),
(0, 'homepage_visit', NULL, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-21 22:00:00'),
(0, 'homepage_visit', NULL, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-21 22:00:46'),
(0, 'product_view', 1001, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:03:50'),
(0, 'product_view', 1001, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:03:55'),
(0, 'product_view', 1002, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:04:12'),
(0, 'product_view', 1003, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:04:26'),
(0, 'product_view', 1001, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:04:56'),
(0, 'homepage_visit', NULL, NULL, '00ac57d4063187ec3f59996eaa2c76754f6d37d9afbc9609c809928ee407630a', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:05:00'),
(0, 'homepage_visit', NULL, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-21 22:05:27'),
(0, 'product_view', 1001, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:07:40'),
(0, 'product_view', 11, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:07:44'),
(0, 'product_view', 1002, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:15:43'),
(0, 'product_view', 1002, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:16:23'),
(0, 'product_view', 12, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:18:04'),
(0, 'product_view', 1001, NULL, 'ab33ec1cbb597031dbb7b730b2bed0e8df03390dd15dfe9b073dc5403d6a3e93', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:20:08'),
(0, 'product_view', 1001, NULL, 'c9bf834e1a9291a0dde7a539533408b876006cdcac5a42b303fa280a128eae3e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:28:34'),
(0, 'product_view', 1001, NULL, 'c9bf834e1a9291a0dde7a539533408b876006cdcac5a42b303fa280a128eae3e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:28:44'),
(0, 'product_view', 1003, NULL, 'c9bf834e1a9291a0dde7a539533408b876006cdcac5a42b303fa280a128eae3e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:28:52'),
(0, 'product_view', 1002, NULL, 'c9bf834e1a9291a0dde7a539533408b876006cdcac5a42b303fa280a128eae3e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:29:26'),
(0, 'product_view', 1001, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:33:19'),
(0, 'product_view', 1001, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:33:29'),
(0, 'product_view', 11, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:33:35'),
(0, 'product_view', 12, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:33:44'),
(0, 'product_view', 13, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:33:48'),
(0, 'product_view', 1002, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:33:54'),
(0, 'product_view', 1003, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:34:00'),
(0, 'product_view', NULL, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:34:12'),
(0, 'product_view', 1003, NULL, '96e5c4bebcd743949a0c1201632e7c8e801e7900dd8155423482e6d7c83a1c0c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 22:34:22'),
(0, 'homepage_visit', NULL, NULL, '6041cdee6a38cc9e85496ee377724572c66590c473737c0baecf0edfb7c23e27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-21 23:08:40'),
(0, 'product_view', 1001, NULL, '6041cdee6a38cc9e85496ee377724572c66590c473737c0baecf0edfb7c23e27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:21:11'),
(0, 'product_view', 12, NULL, '6041cdee6a38cc9e85496ee377724572c66590c473737c0baecf0edfb7c23e27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:21:24'),
(0, 'product_view', 1001, NULL, '6041cdee6a38cc9e85496ee377724572c66590c473737c0baecf0edfb7c23e27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:33:50'),
(0, 'homepage_visit', NULL, NULL, '6041cdee6a38cc9e85496ee377724572c66590c473737c0baecf0edfb7c23e27', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-21 23:34:22'),
(0, 'product_view', 1001, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:54:12'),
(0, 'product_view', 11, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:54:18'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:55:01'),
(0, 'product_view', 1001, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=threaded-memories-shirt', '2026-05-21 23:55:17'),
(0, 'product_view', 1003, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=threaded-memories-shirt', '2026-05-21 23:55:50'),
(0, 'product_view', 1002, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=threaded-memories-shirt', '2026-05-21 23:56:13'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-21 23:57:51'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:59:09'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-21 23:59:24'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:01:58'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:12:51'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:14:52'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:15:27'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 00:17:05'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:10'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:11'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 00:17:12'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:14'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-22 00:17:17'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:49'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:52'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:54'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:17:56');
INSERT INTO `analytics_events` (`id`, `event_type`, `product_id`, `category_id`, `ip_hash`, `user_agent`, `referrer`, `created_at`) VALUES
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:19:21'),
(0, 'homepage_visit', NULL, NULL, '7ad0a0f37004f2f28161a130c762068477c8fb6272ba2e867322b9566e253495', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:20:00'),
(0, 'homepage_visit', NULL, NULL, '8e7f9f269b20dcc38e6ab872eee9cea0cb7354225562c78f52ebc1f2756b117e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:38:15'),
(0, 'product_view', 1001, NULL, '8e7f9f269b20dcc38e6ab872eee9cea0cb7354225562c78f52ebc1f2756b117e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 00:38:25'),
(0, 'homepage_visit', NULL, NULL, '8e7f9f269b20dcc38e6ab872eee9cea0cb7354225562c78f52ebc1f2756b117e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 00:38:45'),
(0, 'homepage_visit', NULL, NULL, '8e7f9f269b20dcc38e6ab872eee9cea0cb7354225562c78f52ebc1f2756b117e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 00:38:45'),
(0, 'homepage_visit', NULL, NULL, '8e7f9f269b20dcc38e6ab872eee9cea0cb7354225562c78f52ebc1f2756b117e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 00:38:58'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 02:14:09'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnUYnHcjHNjhYMaqyKxW1qVn80EZETBJ-g24btLpxYNBnQ_Ca7K8TQl3aotqg_aem_5chtsKZgJu_S_jy1Fiengw', '2026-05-22 02:14:37'),
(0, 'product_view', 1001, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:15:13'),
(0, 'product_view', 11, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:15:23'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:16:02'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:16:16'),
(0, 'product_view', 1002, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:16:34'),
(0, 'product_view', 1002, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:16:46'),
(0, 'product_view', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:16:51'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:17:57'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:18:09'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-22 02:18:14'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 02:18:50'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:19:20'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 02:19:22'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:19:57'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 02:30:43'),
(0, 'product_view', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-22 02:49:00'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-22 02:54:23'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-22 02:57:05'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:58:39'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-22 02:58:49'),
(0, 'product_view', 1001, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 02:59:08'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:03:12'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:03:16'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:03:31'),
(0, 'product_view', 1001, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:04:07'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:04:25'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:04:46'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:04:55'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 03:05:02'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:05:05'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 03:05:20'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 03:19:39'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 03:20:37'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php?i=1', '2026-05-22 03:20:38'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 03:22:22'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/?i=1', '2026-05-22 03:27:56'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/?i=1', '2026-05-22 03:27:56'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:31:23'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 03:31:33'),
(0, 'homepage_visit', NULL, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 03:39:14'),
(0, 'whatsapp_click', 1001, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:59:21'),
(0, 'product_view', 1001, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 03:59:26'),
(0, 'product_view', 11, NULL, 'e25ca1b856e2ac221ad41f7379ca318fcb46e91327097170764e65879182f5dc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:00:05'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:51:02'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://l.instagram.com/', '2026-05-22 04:52:50'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:53:05'),
(0, 'product_view', 13, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:53:25'),
(0, 'product_view', 13, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:53:59'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:54:06'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 04:55:02'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:55:29'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:55:36'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:55:44'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 04:58:56'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-22 05:01:52'),
(0, 'homepage_visit', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnZqSMEhqYZimBePJiW-2kvASvNIYZADYsMbgSBhKgW_B_kEwpptygSYPb-dM_aem_erFkq02LuiVlghlB1-1rzg', '2026-05-22 05:02:03'),
(0, 'homepage_visit', NULL, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; U; Android 13; en-ar; RMX3461 Build/TP1A.220905.001) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.5970.168 Mobile Safari/537.36 HeyTapBrowser/45.14.0.1', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 05:02:13'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:02:18'),
(0, 'homepage_visit', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 05:03:39'),
(0, 'homepage_visit', NULL, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 05:03:43'),
(0, 'homepage_visit', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 05:04:00'),
(0, 'product_view', 1001, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:04:29'),
(0, 'product_view', 11, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:04:30'),
(0, 'product_view', 13, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:04:31'),
(0, 'homepage_visit', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:04:33'),
(0, 'homepage_visit', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:04:47'),
(0, 'homepage_visit', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:04:58'),
(0, 'product_view', NULL, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:10'),
(0, 'product_view', 1001, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:20'),
(0, 'product_view', 11, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:23'),
(0, 'product_view', 12, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:25'),
(0, 'product_view', 13, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:29'),
(0, 'product_view', 1003, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:31'),
(0, 'whatsapp_click', 1001, NULL, '57c41e1112a6726ad87f6af04159ac76544a1ce371e49305926387d37f45f1b9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:05:35'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-22 05:07:16'),
(0, 'whatsapp_click', 1002, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=threaded-memories-shirt', '2026-05-22 05:08:27'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 05:08:35'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:08:57'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:09:09'),
(0, 'homepage_visit', NULL, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=threaded-memories-shirt', '2026-05-22 05:09:13'),
(0, 'homepage_visit', NULL, NULL, 'ee283ecf1d3c79cc72269181a0493ae3f10c4ce0da0905747f64d482f7557a01', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 05:09:22'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:09:37'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:02'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:07'),
(0, 'product_view', 1002, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:16'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:18'),
(0, 'product_view', 1003, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:26'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:52'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:10:57'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:11:04'),
(0, 'homepage_visit', NULL, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'android-app://com.google.android.googlequicksearchbox/', '2026-05-22 05:11:48'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:11:52'),
(0, 'product_view', 1003, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:12:05'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 05:13:11'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:13:31'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:13:35'),
(0, 'homepage_visit', NULL, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'android-app://com.google.android.googlequicksearchbox/', '2026-05-22 05:14:02'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:14:05'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:14:25'),
(0, 'product_view', 11, NULL, 'df58269e97ee304d43bc9197f0f36193248d8e5a168afc1a666d259f077c82a4', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-22 05:14:34'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:14:54'),
(0, 'product_view', 13, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:31:27'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:31:32'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:31:37'),
(0, 'product_view', 12, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:31:43'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:31:56'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:31:56'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:32:36'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:33:20'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:33:29'),
(0, 'product_view', 12, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:33:35'),
(0, 'product_view', 1002, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:33:52'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:37:56'),
(0, 'product_view', 11, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:38:03'),
(0, 'product_view', 1001, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:49:23'),
(0, 'product_view', 12, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:49:27'),
(0, 'product_view', 1003, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 05:49:34'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 05:51:32'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-22 05:51:51'),
(0, 'homepage_visit', NULL, NULL, 'bb083052efa762609be050f25b41312bdb6332129e937d3c74c48434824f5535', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-22 05:53:03'),
(0, 'homepage_visit', NULL, NULL, '914e8e489b893c4a878807259a58afdb6ce6e7b6f8d722ec556f6520c54d8001', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://l.instagram.com/', '2026-05-22 07:11:25'),
(0, 'product_view', 1003, NULL, '5c2e53fae891b635c15de49f6b4e396bb28017e3de2d9acf06b0ef91b6e50bc9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 10:01:24'),
(0, 'homepage_visit', NULL, NULL, '5c2e53fae891b635c15de49f6b4e396bb28017e3de2d9acf06b0ef91b6e50bc9', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 10:02:00'),
(0, 'homepage_visit', NULL, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 19:33:07'),
(0, 'product_view', 1001, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 19:33:16'),
(0, 'homepage_visit', NULL, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-22 19:33:47'),
(0, 'product_view', 1003, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 19:33:54'),
(0, 'product_view', 11, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 19:42:22'),
(0, 'product_view', 1001, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 19:42:27'),
(0, 'product_view', 11, NULL, '0112adbe15ce4929fdc5e9f01a9ae0c2cc5a996e1bbc4d90cdb2b79f67e30e5f', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?occasion=valentines-day', '2026-05-22 19:44:23'),
(0, 'homepage_visit', NULL, NULL, '7d63b855630c4e1d90a4b84d5cc31fccb7c5d7d7886862d08e1038e94483fa5f', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnrl1F0Y2jVtUa0sdwfWzRfJLpP2h37aS7n9aF6zFdhWhLiqlMKaW3BTvRBZk_aem_Y6VQnfHvvYykQ9l8S9lKnA', '2026-05-22 20:13:12'),
(0, 'homepage_visit', NULL, NULL, '7d63b855630c4e1d90a4b84d5cc31fccb7c5d7d7886862d08e1038e94483fa5f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 20:22:10'),
(0, 'homepage_visit', NULL, NULL, '7d63b855630c4e1d90a4b84d5cc31fccb7c5d7d7886862d08e1038e94483fa5f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt', '2026-05-22 20:24:50'),
(0, 'homepage_visit', NULL, NULL, '7d63b855630c4e1d90a4b84d5cc31fccb7c5d7d7886862d08e1038e94483fa5f', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 20:25:12'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 20:27:46'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-22 20:28:04'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 20:31:52'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=photo-frames', '2026-05-22 20:33:18'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-22 20:34:09'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=photo-magazines', '2026-05-22 20:35:04'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 20:35:42'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=threaded-memories-shirt', '2026-05-22 20:36:52'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 20:37:00'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 20:37:07'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 20:37:15'),
(0, 'homepage_visit', NULL, NULL, '2834eedc9fb4de8b9a74839b9e71c70e282a44c706a1f62d1b040bac9b9b9db7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/occasions.php', '2026-05-22 20:37:22');
INSERT INTO `analytics_events` (`id`, `event_type`, `product_id`, `category_id`, `ip_hash`, `user_agent`, `referrer`, `created_at`) VALUES
(0, 'homepage_visit', NULL, NULL, '8ae2bf016c57648501fd59b08bdad529698615eb652c178f36fb0fc3d69af0fb', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 20:39:25'),
(0, 'homepage_visit', NULL, NULL, '8ae2bf016c57648501fd59b08bdad529698615eb652c178f36fb0fc3d69af0fb', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 20:39:33'),
(0, 'homepage_visit', NULL, NULL, '55e97f940a461805da478d44c5d9617d173a90a55fb527c3619b7b061ab8a175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-22 21:12:49'),
(0, 'whatsapp_click', 1001, NULL, '55e97f940a461805da478d44c5d9617d173a90a55fb527c3619b7b061ab8a175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/product.php?slug=pink-bow-embroidery-oversized-shirt', '2026-05-22 21:13:41'),
(0, 'homepage_visit', NULL, NULL, '04dc8ca4f898eb050aff407a662aba5c810ec4b7140cdb597f7a57144ee6fc10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 21:19:35'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-22 22:09:10'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php?category=rochet-ouquet', '2026-05-22 22:09:39'),
(0, 'homepage_visit', NULL, NULL, 'a9f8784e774fa994a987040235dd080a45557b4ec5ebdce62bf13b08d66415f4', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://l.instagram.com/', '2026-05-22 22:13:55'),
(0, 'homepage_visit', NULL, NULL, 'a9f8784e774fa994a987040235dd080a45557b4ec5ebdce62bf13b08d66415f4', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://l.instagram.com/', '2026-05-22 22:15:49'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 22:21:37'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 22:22:38'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 22:22:43'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 22:22:43'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-22 22:25:10'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 22:29:46'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/about.php', '2026-05-22 22:54:29'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 22:55:43'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-22 22:58:38'),
(0, 'homepage_visit', NULL, NULL, '228ec97c5516f1da2b219788dd005a727482f3bba0712404694c938cfa5cc80d', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 23:07:38'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://l.instagram.com/', '2026-05-22 23:31:46'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/occasions.php?occasion=all', '2026-05-22 23:32:26'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=single-tulip-crochet-bouquet', '2026-05-22 23:32:46'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 23:38:50'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 23:39:01'),
(0, 'homepage_visit', NULL, NULL, '0caaf9651338849bc66cf72051bb095b2db9673f71aa3f8eb3a216079fc02616', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-22 23:39:33'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 23:39:55'),
(0, 'homepage_visit', NULL, NULL, '0caaf9651338849bc66cf72051bb095b2db9673f71aa3f8eb3a216079fc02616', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '', '2026-05-22 23:40:23'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-22 23:40:35'),
(0, 'homepage_visit', NULL, NULL, 'e6c9d598ab405c0203c7b367402c58f76a1067b3305bb9c26faf9709f8a7f50d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-22 23:42:15'),
(0, 'homepage_visit', NULL, NULL, 'af9fe5076dfb004d0670fbd4bb81e234ce2b97fa9d84055fac13152619076c06', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-23 00:11:57'),
(0, 'homepage_visit', NULL, NULL, 'ea602db1c503f01cc4c7bb6b09392d8fcffc5aa78e25f37c6163ea5737e7a11c', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnlaquyKfuRVrlR_Dab2OjxktfaCaso2gHLXKHu_kssd27_uwNpcEO913sQeU_aem_YW3oYcdk2NYCuNj_St4agA', '2026-05-23 01:46:10'),
(0, 'homepage_visit', NULL, NULL, 'ea602db1c503f01cc4c7bb6b09392d8fcffc5aa78e25f37c6163ea5737e7a11c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '', '2026-05-23 01:49:38'),
(0, 'whatsapp_click', 1003, NULL, 'ea602db1c503f01cc4c7bb6b09392d8fcffc5aa78e25f37c6163ea5737e7a11c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt', '2026-05-23 01:50:09'),
(0, 'homepage_visit', NULL, NULL, '0024c70c637c7ac72b9aaaf0473003850e841921c0824b19a492ee1548e1adab', 'Mozilla/5.0 (Linux; Android 15; RMX3785 Build/AP3A.240617.008; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.102 Mobile Safari/537.36 Instagram 428.0.0.47.67 Android (35/15; 480dpi; 1080x2400; realme; RMX3785; RE5C6CL1; mt6835; en_GB; 961145441; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGn4Qngs84oD2vbRRJLyRnfNNQs64o4ibjW3wOHi8TvGE5GinYjeUbxn6whVBQ_aem_INruwDW8NdNG3p0yRbfCKA&i=1', '2026-05-23 02:00:29'),
(0, 'homepage_visit', NULL, NULL, '0024c70c637c7ac72b9aaaf0473003850e841921c0824b19a492ee1548e1adab', 'Mozilla/5.0 (Linux; Android 15; RMX3785 Build/AP3A.240617.008; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.102 Mobile Safari/537.36 Instagram 428.0.0.47.67 Android (35/15; 480dpi; 1080x2400; realme; RMX3785; RE5C6CL1; mt6835; en_GB; 961145441; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-23 02:01:20'),
(0, 'whatsapp_click', 1003, NULL, 'e57a153d652ad360c55dfa82875a62ff55a073d28432441ce63c0ff8425b816b', 'Mozilla/5.0 (Linux; Android 13; 2312DRAABI Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 427.0.0.47.73 Android (33/13; 440dpi; 1080x2400; Xiaomi/Redmi; 2312DRAABI; gold; mt6833; en_IN; 954603167; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnMMV6BUKrkTTM0FjMjbxmkABa146RQrUMgSBtTMgr6xbsZ47teUFznDCfOdo_aem_53nceB9Ex2jQChazo4pEAg&i=1', '2026-05-23 02:26:08'),
(0, 'homepage_visit', NULL, NULL, 'de77e0b2c30d0b6ce1cddb444e2878969f48868e66fdce02592fec1770495f3b', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt&fbclid=PAb21jcAR-UgFleHRuA2FlbQIxMQBzcnRjBmFwcF9pZA81NjcwNjczNDMzNTI0MjcAAaeIYS1Zpb_H2JMvB3jCL590UNDRUSNY0kdgiE48j4ZtzWPnK-JXsTKgAwBW5g_aem_TUmmcN2MDGY6LUwMMPe6cQ', '2026-05-23 02:26:58'),
(0, 'homepage_visit', NULL, NULL, 'de77e0b2c30d0b6ce1cddb444e2878969f48868e66fdce02592fec1770495f3b', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-23 02:26:59'),
(0, 'homepage_visit', NULL, NULL, 'de77e0b2c30d0b6ce1cddb444e2878969f48868e66fdce02592fec1770495f3b', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=pink-bow-embroidery-oversized-shirt&fbclid=PAb21jcAR-UlxleHRuA2FlbQIxMQBzcnRjBmFwcF9pZA81NjcwNjczNDMzNTI0MjcAAaeIYS1Zpb_H2JMvB3jCL590UNDRUSNY0kdgiE48j4ZtzWPnK-JXsTKgAwBW5g_aem_TUmmcN2MDGY6LUwMMPe6cQ', '2026-05-23 02:28:54'),
(0, 'homepage_visit', NULL, NULL, 'de77e0b2c30d0b6ce1cddb444e2878969f48868e66fdce02592fec1770495f3b', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-23 02:31:26'),
(0, 'homepage_visit', NULL, NULL, 'ff607f5c21d72dafd05bf52945663f3fb47e3e03fc9b0083a774d60bea5a566b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.7390.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-23 03:46:17'),
(0, 'homepage_visit', NULL, NULL, '3972521d3e6604c3f198a91a1e8cf7bf554067fb9ee09072dbfc6646afc6b94a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.7444.163 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-23 03:47:04'),
(0, 'homepage_visit', NULL, NULL, '86d5abb76e1c001f7b7b614c22b4bcffb0f2c01c6b877dff8ef1eb19b4595ae6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 04:16:45'),
(0, 'homepage_visit', NULL, NULL, '23e182793188251e75878dd4f7abe73444b4608d3aaa693a7cf22df5b503d5fe', 'Mozilla/5.0 (Linux; Android 16; SM-A356E Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.160 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (36/16; 450dpi; 1080x2340; samsung; SM-A356E; a35x; s5e8835; en_IN; 968419278; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnrJbetMfPDXE_2CTBR7kogSEMBq9cZ5LMWMhiseFziZMOc5FOieoReiZulMg_aem_8i0VqrS8e5EInCpDFNXPLg&i=1', '2026-05-23 04:21:53'),
(0, 'homepage_visit', NULL, NULL, '86d5abb76e1c001f7b7b614c22b4bcffb0f2c01c6b877dff8ef1eb19b4595ae6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 04:22:53'),
(0, 'whatsapp_click', 1004, NULL, '23e182793188251e75878dd4f7abe73444b4608d3aaa693a7cf22df5b503d5fe', 'Mozilla/5.0 (Linux; Android 16; SM-A356E Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.160 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (36/16; 450dpi; 1080x2340; samsung; SM-A356E; a35x; s5e8835; en_IN; 968419278; IABMV/1)', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-23 04:23:34'),
(0, 'homepage_visit', NULL, NULL, '86d5abb76e1c001f7b7b614c22b4bcffb0f2c01c6b877dff8ef1eb19b4595ae6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/product.php?slug=customized-friendship-magazine-scrapbook-personalized-memory-book-gift', '2026-05-23 04:36:39'),
(0, 'homepage_visit', NULL, NULL, '86d5abb76e1c001f7b7b614c22b4bcffb0f2c01c6b877dff8ef1eb19b4595ae6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/product.php?slug=customized-friendship-magazine-scrapbook-personalized-memory-book-gift', '2026-05-23 04:46:12'),
(0, 'homepage_visit', NULL, NULL, '915e8d670be1435d7d386d1c402fec2e918f467eaf0ad9f1c3f5b6221edf41a3', 'Mozilla/5.0 (Linux; Android 15; RMX3686 Build/AP3A.240617.008; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.131 Mobile Safari/537.36 Instagram 427.0.0.47.73 Android (35/15; 480dpi; 1080x2412; realme; RMX3686; RE58A5L1; mt6877; en_GB; 954603209; IABMV/1)', 'https://aakar-creatives.infinityfree.me/product.php?slug=red-heart-embroidery-shirt&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnQvHVzPj70hrn58JwgP5gD3J3PGQMjOjfHG__P5FeOYT-oWD1488RNNJ6XGI_aem_YWdncwAiWWOPa8N8QQSb9hQEvELD&brid=YWdncwGlsl5fT-WekOaI4XKj5RVu&i=1', '2026-05-23 05:07:14'),
(0, 'homepage_visit', NULL, NULL, 'd264a7e926b570cebe64c829dbf2f6d26110b7719b7e751a1d87e25182078839', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 05:14:41'),
(0, 'homepage_visit', NULL, NULL, 'd264a7e926b570cebe64c829dbf2f6d26110b7719b7e751a1d87e25182078839', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 05:24:50'),
(0, 'homepage_visit', NULL, NULL, 'acc570e580f4c4ce6a625020c834e9c2f59aefa171772777467965f8a11f3c50', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnfUjsFglzCheBWuFDBmxwB0PzOdvQcOOqQJPyOPRcGGTUpT4aL-klYcZihXk_aem_IJM51GmvTlIDa9Aa7kFvJw', '2026-05-23 06:01:07'),
(0, 'homepage_visit', NULL, NULL, 'acc570e580f4c4ce6a625020c834e9c2f59aefa171772777467965f8a11f3c50', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 429.1.0.44.70 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 968419236)', 'https://aakar-creatives.infinityfree.me/product.php?slug=customized-friendship-magazine-scrapbook-personalized-memory-book-gift', '2026-05-23 06:03:03'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-23 21:11:46'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnSil0yF1-pymMN7IXGP0v_3jPcQM_t0_xW0sekG2WoiKOxrOQb4gQQVJu7hw_aem_z40ECqvyr9vMmhNGZx7nXA', '2026-05-23 22:53:46'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 22:54:53'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 22:55:00'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://l.instagram.com/', '2026-05-23 22:55:09'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/product.php?slug=customized-anniversary-photo-frame-for-couples-personalized-memory-collage-gift', '2026-05-23 22:55:39'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/product.php?slug=personalized-birthday-magazine-scrapbook', '2026-05-23 22:56:07'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '', '2026-05-23 22:58:15'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-23 22:58:31'),
(0, 'homepage_visit', NULL, NULL, '039d0a50fa640487cb5b133efab560a50c5a5a435699d140824f030b21ea33a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/product.php?slug=sage-floral-embroidery-shirt', '2026-05-23 22:59:05'),
(0, 'homepage_visit', NULL, NULL, '4d74f05ddf12eb8b23916ca14db4b310e5314af06eb54c151e8f8a948999c5dc', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-05-26 08:32:26'),
(0, 'homepage_visit', NULL, NULL, '11283810d415f8466438ada146b446341164f5f363fe7b2600075f9ac348c316', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-27 20:47:15'),
(0, 'homepage_visit', NULL, NULL, '11283810d415f8466438ada146b446341164f5f363fe7b2600075f9ac348c316', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-27 20:47:25'),
(0, 'homepage_visit', NULL, NULL, '11283810d415f8466438ada146b446341164f5f363fe7b2600075f9ac348c316', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/?i=1', '2026-05-27 20:48:24'),
(0, 'homepage_visit', NULL, NULL, '11283810d415f8466438ada146b446341164f5f363fe7b2600075f9ac348c316', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-27 20:56:53'),
(0, 'homepage_visit', NULL, NULL, '11283810d415f8466438ada146b446341164f5f363fe7b2600075f9ac348c316', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-05-27 21:08:06'),
(0, 'homepage_visit', NULL, NULL, '1495a7c82fb3751eea5145434830d33f82bab130bfa574095898580b6d819e5c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-05-27 22:13:26'),
(0, 'homepage_visit', NULL, NULL, '3181f8819fca1213fff0e498a72a9324e02816d4e227fd02fcda9f7341ccce1d', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 430.0.0.53.80 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 974607564)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGn82Ym9bEUvDohm4XO4R4OE2PqLs5KZocXUJzDM6Ni3M9Nv19ZgzyHnoxCZF8_aem_FBzj_p7yJF0NymrQIsbWFw', '2026-05-27 22:26:31'),
(0, 'whatsapp_click', 1018, NULL, '3181f8819fca1213fff0e498a72a9324e02816d4e227fd02fcda9f7341ccce1d', 'Mozilla/5.0 (Linux; Android 12; M2101K6P Build/SKQ1.210908.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.121 Mobile Safari/537.36 Instagram 430.0.0.53.80 Android (31/12; 440dpi; 1080x2400; Xiaomi/Redmi; M2101K6P; sweetin; qcom; en_IN; 974607564)', 'https://aakar-creatives.infinityfree.me/product.php?slug=bow-embroidery-aesthetic-shirt', '2026-05-27 22:26:45'),
(0, 'homepage_visit', NULL, NULL, '5fd2c0153b9df47a929849cfb5decf711e093c4a9209f9b5c0de460aef95f24b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.7444.163 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-27 22:31:12'),
(0, 'homepage_visit', NULL, NULL, 'fc9c85a8230449bc7580367ff6121ea166b04b5be80e5c6e900ccee146a1036b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.7444.162 Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-05-27 22:32:15'),
(0, 'homepage_visit', NULL, NULL, 'fd35b1a7fe5f8262f36b5588a8c628ae0bba1f2d1de16a646636da1e538cbc37', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-27 22:37:31'),
(0, 'homepage_visit', NULL, NULL, 'bf10944784e915e07b445bfe9258db1ad453c50614ba4bd0dad777d2f368ceb5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', '', '2026-05-27 23:16:36'),
(0, 'homepage_visit', NULL, NULL, '8bc803ef90ffcfe9317207bcd66a1fb4f65552ec9c8df034bbc54a4a250f4b10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-27 23:21:03'),
(0, 'homepage_visit', NULL, NULL, 'aa5dd7c63ae983ece76476bd760ef956f7f3982847fdab30824759afee4cdb94', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 02:08:41'),
(0, 'homepage_visit', NULL, NULL, 'fb387f013703a8048e9bfbd2eeb016992bfda5e058818f549b05e6940303f53b', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [LinkedInApp]/9.32.959', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 03:14:18'),
(0, 'homepage_visit', NULL, NULL, '38c0c8a5513baa7047cd86e9085e471afa4be43bd74725075cefe5625ddea59d', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 03:36:33'),
(0, 'homepage_visit', NULL, NULL, '0918793c49b96b1ff52f4ea74b216bb2ca3d98f08f0c6eeb266fcfb27e81cbc3', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnt_cYskwJLMFpaikGGPek1dz3RNgVbfnpJDdj91XcvT-omxPB2R1WCjmj3uk_aem_SHR8pqwV1ut_TBphfuYuCw', '2026-05-28 06:08:17'),
(0, 'whatsapp_click', 1021, NULL, '0918793c49b96b1ff52f4ea74b216bb2ca3d98f08f0c6eeb266fcfb27e81cbc3', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/product.php?slug=handmade-crochet-sunflower-bouquet', '2026-05-28 06:08:52'),
(0, 'homepage_visit', NULL, NULL, '0918793c49b96b1ff52f4ea74b216bb2ca3d98f08f0c6eeb266fcfb27e81cbc3', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/product.php?slug=handmade-crochet-sunflower-bouquet', '2026-05-28 06:09:22'),
(0, 'homepage_visit', NULL, NULL, '0918793c49b96b1ff52f4ea74b216bb2ca3d98f08f0c6eeb266fcfb27e81cbc3', 'Mozilla/5.0 (Linux; Android 14; SM-A135F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.120 Mobile Safari/537.36 Instagram 329.0.0.41.93 Android (34/14; 450dpi; 1080x2208; samsung; SM-A135F; a13; exynos850; en_IN; 593717564)', 'https://aakar-creatives.infinityfree.me/product.php?slug=customized-anniversary-photo-frame-for-couples-personalized-memory-collage-gift', '2026-05-28 06:10:24'),
(0, 'homepage_visit', NULL, NULL, '00f532b281c510260c916959305bf522be8cbefc3df64861658c935136424095', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 06:28:50'),
(0, 'homepage_visit', NULL, NULL, 'c9e0ab04e3992bd1cfe3bdf8620b88833ab75c37f4a517c0a20f8f0f04625c72', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 09:16:31'),
(0, 'homepage_visit', NULL, NULL, '63da11b5b7ab01a7370d71a3be3e9714e8da42af8c1c9d12e3806c31a142d276', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 12:03:10'),
(0, 'homepage_visit', NULL, NULL, '37fd4344fd955120448902cc4ce7ef5be891aaab239e819234b67560efaee9f4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-28 20:36:20'),
(0, 'homepage_visit', NULL, NULL, '5f4795efb1b20d9ddd3c320f32d27cdb08bea58114c63d0d256a4bc1ac48a7c9', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [LinkedInApp]/9.31.7998', 'https://aakar-creatives.infinityfree.me/', '2026-05-29 10:11:54'),
(0, 'homepage_visit', NULL, NULL, '74c8afc8bcc257b75bad0f95e56bb9c5d52e353689602c708559508f848463ce', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-05-31 21:20:38'),
(0, 'homepage_visit', NULL, NULL, 'eb3bf969a1b5454a8f065134f76429d31748eb6cc15fa5b2569e58bf2af3c9e6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-01 22:36:01'),
(0, 'homepage_visit', NULL, NULL, 'eb3bf969a1b5454a8f065134f76429d31748eb6cc15fa5b2569e58bf2af3c9e6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=photo-frames', '2026-06-01 22:38:25'),
(0, 'homepage_visit', NULL, NULL, 'eb3bf969a1b5454a8f065134f76429d31748eb6cc15fa5b2569e58bf2af3c9e6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/index.php', '2026-06-01 22:38:41'),
(0, 'homepage_visit', NULL, NULL, 'eb3bf969a1b5454a8f065134f76429d31748eb6cc15fa5b2569e58bf2af3c9e6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'android-app://com.linkedin.android/', '2026-06-01 22:41:39'),
(0, 'homepage_visit', NULL, NULL, '8a4f36d5eaec745608d98388a63b5c554c130dfc996502631906fc7c3c50c7e1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-02 00:43:49'),
(0, 'homepage_visit', NULL, NULL, 'b04ebd334037cb92520e3d14c814018c234983ad803a4490f8b045545d24b44c', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php?category=photo-frames', '2026-06-02 03:56:51'),
(0, 'homepage_visit', NULL, NULL, '82f31850b7f0c586cada154412a4f9cbd08808dc916b87d540c96f79a36a9422', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-02 10:38:01'),
(0, 'homepage_visit', NULL, NULL, '9378074471284c907ab5935da0caf3f1edea5e9ca84cc05ce04c449e3032b55c', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23F77 Instagram 431.0.0.23.68 (iPhone17,3; iOS 26_5; en_GB; en-GB; scale=3.00; 1179x2556; IABMV/1; 978209931) Safari/604.1', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPMTI0MDI0NTc0Mjg3NDE0AAGnSKCA9KMZp0kINa6IAujQI6wAoPjcWmqhcjBUjdlTTuEjhVpvE-19dqkBrG0_aem_Trp8fZd8mPXCjj54LHTBjA', '2026-06-05 22:03:23'),
(0, 'homepage_visit', NULL, NULL, '68b0eb0340231819a4284913423eb4675744946448ac1053de7ad1f043bee8d1', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-08 19:50:37'),
(0, 'homepage_visit', NULL, NULL, '75a1692d8650c0bfd62d6f7c3e3b8925a4aef80e9a278e75219a79f6f959424b', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-08 19:50:37'),
(0, 'homepage_visit', NULL, NULL, '2957641b349b5bdfa3401912d591d65c9fd7398aca3e6cc3edfc37a7ad703d62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'https://www.facebook.com/', '2026-06-08 19:50:41'),
(0, 'homepage_visit', NULL, NULL, 'a90b47a95b8f79d8e1956447a2903d897d54d955ff66f928127cf88a28aa4071', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-08 19:51:19'),
(0, 'homepage_visit', NULL, NULL, '2a7b61e0ed40ac4a3b8ee5c8852e2a7e0390669f30057b25937b96d45cbc8f3e', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-08 19:51:33'),
(0, 'homepage_visit', NULL, NULL, '3e915019d6d6081dea2c271373e21621f77fb04c1ff7e59933b58a7940b777be', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-12 17:26:09'),
(0, 'whatsapp_click', 1015, NULL, '3e915019d6d6081dea2c271373e21621f77fb04c1ff7e59933b58a7940b777be', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/shop.php', '2026-06-12 17:26:27'),
(0, 'homepage_visit', NULL, NULL, 'd9e1673151666969b4d6b8731f5b3e2ed1280fdc078a64a6cc1e71f2aec415ff', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-16 04:57:37'),
(0, 'homepage_visit', NULL, NULL, 'a0d38d5bc38a4d2574b41d9bd511fdeb3ec279a325fe10987c6007a648d2df53', 'Mozilla/5.0 (Linux; Android 16; SM-E346B Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/149.0.7827.87 Mobile Safari/537.36 Instagram 433.0.0.47.68 Android (36/16; 450dpi; 1080x2340; samsung; SM-E346B; m34x; s5e8825; en_IN; 990700936; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnJc2GVSmbdE3YOpQwxg8rvn59KulTfe9kFo9tHezSH0m1fh5ShDpxflOnTmI_aem_izl8PqxF1kb0__DXukobcQ', '2026-06-17 02:00:57'),
(0, 'homepage_visit', NULL, NULL, 'e085cb79c259d64b03e1f50f7a607fbc66715d35b41027b221cae8c952a5fd18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/?utm_source=chatgpt.com', '2026-06-20 21:47:13'),
(0, 'homepage_visit', NULL, NULL, '446cac262805d56f55cca0597bd1c11c848c707ac1582c078f2b6ed1002387cb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'http://aakar-creatives.infinityfree.me/', '2026-06-20 21:48:03'),
(0, 'homepage_visit', NULL, NULL, 'e085cb79c259d64b03e1f50f7a607fbc66715d35b41027b221cae8c952a5fd18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '', '2026-06-20 22:04:52'),
(0, 'homepage_visit', NULL, NULL, '8ada9baa91154f7fecf0fa67ffe93ed94abe2f944aa049c42f4c414ed8287b15', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-20 22:05:00'),
(0, 'homepage_visit', NULL, NULL, 'e085cb79c259d64b03e1f50f7a607fbc66715d35b41027b221cae8c952a5fd18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-20 22:57:33'),
(0, 'homepage_visit', NULL, NULL, '77bdbc37be6cb782e0e22c92c6eda2a757310c763959cdb9cd58b0ab6df03723', 'Mozilla/5.0 (Linux; Android 15; RMX3686 Build/AP3A.240617.008; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/148.0.7778.217 Mobile Safari/537.36 Instagram 432.1.0.44.80 Android (35/15; 480dpi; 1080x2412; realme; RMX3686; RE58A5L1; mt6877; en_GB; 986923712; IABMV/1)', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPNTY3MDY3MzQzMzUyNDI3AAGnoXpYGRBkZ1cBgr3QVxDSf11cliTPZWK_aOQyk4jqgnRMeA5hO3n9fF0jrx0_aem_YWdncwA-XrW59C8n7rK6eSO3O-xy&brid=YWdncwG7QjLAQ3T7SLzIPmLvZ9dx', '2026-06-21 23:43:26'),
(0, 'homepage_visit', NULL, NULL, 'caa0e064d9d24f9ee1d2cf7319cf91f2bafc8dbbc2ea3a51d4f7e65b9d845654', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-22 11:03:38'),
(0, 'homepage_visit', NULL, NULL, 'fde1a5f7fc4dbcd0b014a895f6eda8f32ae3d3814c26a83aa4e5a6dad0ae17fb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'https://aakar-creatives.infinityfree.me/', '2026-06-23 22:25:27'),
(0, 'homepage_visit', NULL, NULL, '76f01bb437b5615efbe2713ca21689b8ee891d449352bb43604a2323979bb678', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-24 02:36:48'),
(0, 'homepage_visit', NULL, NULL, 'caaf3178917b3eae10caa49ce9304449091a3ef438bb12d7f5cb750e141042c8', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-24 02:36:48'),
(0, 'homepage_visit', NULL, NULL, 'ab6bfa1ab74df0a43ac96a42d332d6eac136212146f9402a88af8a1969c03a6f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'https://www.facebook.com/', '2026-06-24 02:36:50'),
(0, 'homepage_visit', NULL, NULL, '2038b871d6010271ac84963a9e59c45e3c983e148d12506778256349546de20f', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-24 02:37:04'),
(0, 'homepage_visit', NULL, NULL, '6e63372cd22816196415eb61a4425dbd3d5085bc19b808079e33e3de86a00c3b', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-24 02:37:05'),
(0, 'homepage_visit', NULL, NULL, '50d775003c7a2cb48f185492fed3d728f6113fc440fcd04bdded599fb18526cf', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', '', '2026-06-24 02:37:29'),
(0, 'homepage_visit', NULL, NULL, 'fc9f6130dda586c8645dd781548f99f455a71adcd7449d8c93af460ac1224407', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-24 22:17:52'),
(0, 'homepage_visit', NULL, NULL, 'fc9f6130dda586c8645dd781548f99f455a71adcd7449d8c93af460ac1224407', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '', '2026-06-24 22:42:16'),
(0, 'homepage_visit', NULL, NULL, '02077580669b86e6a353e1bb91e34a09f5b4e69ae83fbbb5236c7c94864a3fba', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-24 23:52:45'),
(0, 'homepage_visit', NULL, NULL, '57e48a925b7f5223c9c00d47f69d28318a7e3e80619a32fb1c98873a7d2793b1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'android-app://com.linkedin.android/', '2026-06-24 23:56:51'),
(0, 'homepage_visit', NULL, NULL, '57e48a925b7f5223c9c00d47f69d28318a7e3e80619a32fb1c98873a7d2793b1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'android-app://com.linkedin.android/', '2026-06-24 23:59:32'),
(0, 'homepage_visit', NULL, NULL, '24f2066a9f09a53d6ccd7263b13b1e0c2a278d376c3b3b32bdd024cee6e714cc', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23F77 Instagram 433.0.0.33.57 (iPhone17,3; iOS 26_5; en_GB; en-GB; scale=3.00; 1179x2556; IABMV/1; 989803374) Safari/604.1', 'https://aakar-creatives.infinityfree.me/?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQPMTI0MDI0NTc0Mjg3NDE0AAGn-8xGmQ98FOmqZgLaNQzIRdJFH0EvsBFcYAucQcrdpW_AK1KKSnUXCgJY0YA_aem_yCDB2GTj1FH-rQ-L3lRn7A', '2026-06-25 12:20:43'),
(0, 'homepage_visit', NULL, NULL, '17ed83ebac85f53571210e773c181262a0d713611026f50ec074a5c28861e4f1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'https://aakar-creatives.infinityfree.me/', '2026-06-26 00:34:10');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `color_hex` char(7) NOT NULL DEFAULT '#b85c6e' COMMENT 'e.g. #b85c6e',
  `icon` varchar(50) DEFAULT NULL COMMENT 'icon name or emoji',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `name`, `color_hex`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bestseller', '#b85c6e', 'star', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(2, 'New', '#4a9e6e', 'sparkle', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(3, 'Trending', '#c9a96e', 'flame', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(4, 'Limited Edition', '#7c5cbf', 'clock', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(5, 'Handmade', '#d4789a', 'hand', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(6, 'Most Loved', '#e05050', 'heart', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(7, 'Premium', '#3a3a3a', 'gem', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(8, 'Couple Favorite', '#b85c6e', 'couple', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(130) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(400) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_url`, `is_featured`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Photo Frames', 'photo-frames', 'Timeless moments, beautifully framed', '/uploads/categories/categories_6a0c3a77e78cc6.22012007.png', 1, 1, 1, '2026-05-18 16:22:16', '2026-05-19 03:24:55'),
(2, 'Photo Magazines', 'photo-magazines', 'Your story, magazine-style', '/uploads/categories/categories_6a0c3a5c37d318.68940699.png', 1, 2, 1, '2026-05-18 16:22:16', '2026-05-19 03:24:27'),
(3, 'Gift Boxes', 'gift-boxes', 'Curated boxes of love', '/uploads/categories/categories_6a0c3a89230422.55953399.png', 1, 3, 1, '2026-05-18 16:22:16', '2026-05-19 03:27:07'),
(4, 'Threaded Memories Shirt', 'threaded-memories-shirt', 'Custom embroidered T-shirts / shirt stitched with love and memories.', '/uploads/categories/categories_6a0c3be48ff2a3.99305485.jpeg', 1, 6, 1, '2026-05-19 03:31:00', '2026-05-21 21:40:09'),
(5, 'Personalized Gifts', 'bouquets', 'Thoughtfully crafted gifts made specially for your loved ones and memories.', '/uploads/categories/categories_6a0c3ae8c90bf1.06198937.png', 1, 5, 1, '2026-05-18 16:22:16', '2026-05-19 03:27:14'),
(7, 'Crochet Bouquet', 'rochet-ouquet', 'Thread of Love', '/uploads/categories/categories_6a0c3a23c7c1d3.81488264.png', 1, 1, 1, '2026-05-18 16:50:29', '2026-05-19 03:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `category_attributes`
--

CREATE TABLE `category_attributes` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `attribute_type` enum('size','color','material','custom') NOT NULL,
  `label` varchar(100) NOT NULL COMMENT 'Display name e.g. "Frame Size", "Yarn Color"',
  `is_required` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = customer must select before WhatsApp enquiry',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Defines which attributes are available per product category';

--
-- Dumping data for table `category_attributes`
--

INSERT INTO `category_attributes` (`id`, `category_id`, `attribute_type`, `label`, `is_required`, `sort_order`, `created_at`) VALUES
(1, 1, 'size', 'Frame Size', 1, 1, '2026-05-21 06:24:40'),
(2, 1, 'color', 'Frame Color', 0, 2, '2026-05-21 06:24:40'),
(3, 2, 'size', 'Magazine Size', 1, 1, '2026-05-21 06:24:40'),
(4, 3, 'size', 'Box Size', 1, 1, '2026-05-21 06:24:40'),
(5, 3, 'color', 'Ribbon Color', 0, 2, '2026-05-21 06:24:40'),
(6, 5, 'color', 'Color Preference', 0, 1, '2026-05-21 06:24:40'),
(7, 7, 'size', 'Bouquet Size', 1, 1, '2026-05-21 06:24:40'),
(8, 7, 'color', 'Yarn Color', 1, 2, '2026-05-21 06:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL COMMENT 'Store with country code: +91XXXXXXXXXX',
  `email` varchar(180) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `total_orders` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `password_hash`, `instagram`, `city`, `notes`, `is_active`, `last_login_at`, `total_orders`, `created_at`, `updated_at`) VALUES
(0, 'Anisha Ramani', '9748652145', NULL, NULL, NULL, 'Surat', NULL, 1, NULL, 0, '2026-05-19 21:14:13', '2026-05-19 21:14:13'),
(1, 'Priya Sharma', '+919876543210', NULL, NULL, '@priya.sharma', 'Mumbai', NULL, 1, NULL, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(2, 'Rohit Verma', '+919765432109', NULL, NULL, NULL, 'Delhi', NULL, 1, NULL, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(3, 'Anjali Mehta', '+919654321098', NULL, NULL, '@anjali.m', 'Ahmedabad', NULL, 1, NULL, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(4, 'Karan Patel', '+919543210987', NULL, NULL, NULL, 'Surat', NULL, 1, NULL, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(5, 'Sneha Joshi', '+919432109876', NULL, NULL, '@sneha.joshi', 'Pune', NULL, 1, NULL, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(100, 'Amit Ghoyal', '+919510360227', 'amittghoyal@gmail.com', '$2y$12$lG1hQ81yLa4LgKefFP6aFeCtfSiG.V.xzYNaCoHwrut.qVcWMpqUe', NULL, 'Surat', NULL, 1, '2026-05-22 22:17:24', 0, '2026-05-22 02:47:34', '2026-05-22 22:17:24'),
(101, 'Amit Gohil', '+919723282715', 'amitgohil1105@gmail.com', '$2y$12$BjtdT1KXkA0xdKM0Nse5POgXRi7kJVgfgVQ5ecL3xcBtzptmmt/9K', NULL, 'Surat', NULL, 1, NULL, 0, '2026-05-22 23:42:59', '2026-05-22 23:42:59');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_sections`
--

CREATE TABLE `homepage_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(80) NOT NULL COMMENT 'Machine key: hero_banner, featured_products …',
  `label` varchar(120) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `config_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Section-specific settings (headline, CTA, etc.)'
) ;

--
-- Dumping data for table `homepage_sections`
--

INSERT INTO `homepage_sections` (`id`, `key`, `label`, `description`, `sort_order`, `is_visible`, `config_json`, `updated_at`) VALUES
(1, 'hero_banner', 'Hero Banner', 'Full-width hero image + headline', 1, 1, '{\"headline\":\"Turning Memories Into Forever Gifts\",\"script_line\":\"Handmade with love & emotions.\",\"cta_text\":\"Explore Aakar\",\"cta_url\":\"#collection\"}', '2026-05-19 03:21:27'),
(2, 'featured_products', 'Featured Products', 'Hand-picked showcase products', 2, 1, NULL, '2026-05-18 16:22:17'),
(3, 'new_arrivals', 'New Arrivals', 'Recently added items', 3, 1, NULL, '2026-05-18 16:22:17'),
(4, 'festival_section', 'Festival Section', 'Occasion-based collections', 4, 1, NULL, '2026-05-18 16:22:17'),
(5, 'trending_gifts', 'Trending Gifts', 'Most viewed products', 5, 1, NULL, '2026-05-18 16:22:17'),
(6, 'couple_collection', 'Couple Collection', 'Romantic & couple gifts', 6, 1, NULL, '2026-05-18 16:47:44'),
(7, 'handmade_collection', 'Handmade Collection', 'Crochet & handmade products', 7, 1, NULL, '2026-05-18 16:22:17'),
(8, 'testimonials', 'Testimonials', 'Customer reviews', 8, 1, NULL, '2026-05-18 16:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = general inquiry',
  `source` enum('instagram','whatsapp','facebook','referral','direct','other') NOT NULL DEFAULT 'whatsapp',
  `status` enum('new_inquiry','contacted','confirmed','designing','completed','delivered','cancelled') NOT NULL DEFAULT 'new_inquiry',
  `notes` text DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `wa_clicked_at` datetime DEFAULT NULL COMMENT 'Timestamp of the WhatsApp button click',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `customer_id`, `product_id`, `source`, `status`, `notes`, `followup_date`, `wa_clicked_at`, `created_at`, `updated_at`) VALUES
(3, 3, NULL, 'instagram', 'completed', NULL, NULL, '2026-05-17 16:22:16', '2026-05-18 16:22:16', '2026-05-18 17:18:04'),
(4, 4, NULL, 'referral', 'delivered', NULL, NULL, '2026-05-16 16:22:16', '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(5, 5, NULL, 'facebook', 'contacted', NULL, NULL, '2026-05-15 16:22:16', '2026-05-18 16:22:16', '2026-05-18 16:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `media_library`
--

CREATE TABLE `media_library` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_url` varchar(500) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `file_type` enum('image','video') NOT NULL DEFAULT 'image',
  `mime_type` varchar(80) DEFAULT NULL COMMENT 'e.g. image/webp',
  `file_size_kb` int(10) UNSIGNED DEFAULT NULL,
  `width_px` smallint(6) DEFAULT NULL,
  `height_px` smallint(6) DEFAULT NULL,
  `alt_text` varchar(200) DEFAULT NULL,
  `folder` enum('products','categories','hero','customers','misc') NOT NULL DEFAULT 'products',
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_library`
--

INSERT INTO `media_library` (`id`, `file_url`, `file_name`, `file_type`, `mime_type`, `file_size_kb`, `width_px`, `height_px`, `alt_text`, `folder`, `uploaded_by`, `created_at`) VALUES
(1, '/uploads/products/products_6a0be2cfaed576.06204028.png', 'products_6a0be2cfaed576.06204028.png', 'image', NULL, 2207, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55'),
(2, '/uploads/products/products_6a0be2cfb01225.61063150.png', 'products_6a0be2cfb01225.61063150.png', 'image', NULL, 2409, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55'),
(3, '/uploads/products/products_6a0be2cfb57995.32535158.png', 'products_6a0be2cfb57995.32535158.png', 'image', NULL, 2461, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55'),
(4, '/uploads/products/products_6a0be2cfb63142.91240988.png', 'products_6a0be2cfb63142.91240988.png', 'image', NULL, 2562, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55'),
(5, '/uploads/products/products_6a0be2cfb6cd41.95452764.png', 'products_6a0be2cfb6cd41.95452764.png', 'image', NULL, 2447, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55'),
(6, '/uploads/products/products_6a0be2cfb7a8c0.13671743.png', 'products_6a0be2cfb7a8c0.13671743.png', 'image', NULL, 2615, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55'),
(7, '/uploads/products/products_6a0be2cfb86922.99634515.png', 'products_6a0be2cfb86922.99634515.png', 'image', NULL, 2352, NULL, NULL, NULL, 'products', 3, '2026-05-19 09:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `occasions`
--

CREATE TABLE `occasions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(110) NOT NULL,
  `icon_emoji` varchar(10) DEFAULT NULL,
  `image_url` varchar(400) DEFAULT NULL COMMENT 'Banner/cover image for this occasion',
  `banner_color` varchar(7) DEFAULT '#b85c6e' COMMENT 'Fallback hex colour',
  `description` varchar(300) DEFAULT NULL COMMENT 'Short description shown on storefront',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `occasions`
--

INSERT INTO `occasions` (`id`, `name`, `slug`, `icon_emoji`, `image_url`, `banner_color`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'All Occasions', 'all', NULL, NULL, '#b85c6e', NULL, 1, 0, '2026-05-18 16:22:16', '2026-05-21 06:24:39'),
(2, 'Anniversary', 'anniversary', NULL, '/public/images/banners/annivarsary.jpeg', '#b85c6e', NULL, 1, 1, '2026-05-18 16:22:16', '2026-05-22 23:13:32'),
(3, 'Birthday', 'birthday', NULL, '/public/images/banners/birthday.jpeg', '#b85c6e', NULL, 1, 2, '2026-05-18 16:22:16', '2026-05-22 23:13:47'),
(4, 'Valentine\'s Day', 'valentines-day', NULL, NULL, '#b85c6e', NULL, 0, 3, '2026-05-18 16:22:16', '2026-05-22 23:07:34'),
(5, 'Rakhi', 'rakhi', NULL, NULL, '#b85c6e', NULL, 0, 4, '2026-05-18 16:22:16', '2026-05-22 23:07:22'),
(6, 'Mother\'s Day', 'mothers-day', NULL, NULL, '#b85c6e', NULL, 0, 5, '2026-05-18 16:22:16', '2026-05-22 23:07:14'),
(7, 'Friendship Day', 'friendship-day', NULL, NULL, '#b85c6e', NULL, 0, 6, '2026-05-18 16:22:16', '2026-05-22 23:07:06'),
(8, 'Diwali', 'diwali', NULL, NULL, '#b85c6e', NULL, 0, 7, '2026-05-18 16:22:16', '2026-05-22 23:06:59');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `coupon_code` varchar(40) DEFAULT NULL COMMENT 'NULL = auto-applied; set code for manual entry',
  `discount_type` enum('percentage','flat','free_shipping','combo') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'e.g. 20 for 20%, 200 for ₹200',
  `min_order_value` decimal(10,2) DEFAULT NULL COMMENT 'Minimum cart value to apply',
  `max_uses` int(11) DEFAULT NULL COMMENT 'NULL = unlimited',
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `name`, `coupon_code`, `discount_type`, `discount_value`, `min_order_value`, `max_uses`, `used_count`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'Rakhi Special', 'RAKHI24', 'free_shipping', '0.00', NULL, NULL, 0, '2025-08-01', '2025-08-20', 1, '2026-05-18 16:22:16', '2026-05-18 16:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `inquiry_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Linked inquiry if it started as one',
  `customer_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` tinyint(4) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(10,2) NOT NULL COMMENT '(unit_price × qty) − discount_amount',
  `status` enum('pending','confirmed','in_production','dispatched','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `customisation` text DEFAULT NULL COMMENT 'Customer personalisation notes',
  `delivery_address` text DEFAULT NULL,
  `expected_by` date DEFAULT NULL,
  `delivered_on` date DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `inquiry_id`, `customer_id`, `product_id`, `quantity`, `unit_price`, `discount_amount`, `final_price`, `status`, `customisation`, `delivery_address`, `expected_by`, `delivered_on`, `admin_notes`, `created_at`, `updated_at`) VALUES
(0, NULL, 0, 0, 1, '1600.00', '0.00', '1600.00', 'delivered', NULL, NULL, '2026-05-17', NULL, NULL, '2026-05-19 21:45:12', '2026-05-19 21:45:12');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `badge_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `short_description` varchar(300) DEFAULT NULL COMMENT 'Emotional one-liner shown on cards',
  `full_description` text DEFAULT NULL,
  `product_story` text DEFAULT NULL COMMENT 'Emotional brand story for the product page',
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL COMMENT 'Crossed-out / original price if on sale',
  `delivery_days` varchar(60) DEFAULT '3–5 Working Days',
  `whatsapp_message` text DEFAULT NULL COMMENT 'Pre-filled WA enquiry message for this product',
  `tags` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: romantic,couple,handmade',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT 0,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `is_bestseller` tinyint(1) NOT NULL DEFAULT 0,
  `in_stock` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','draft','archived') NOT NULL DEFAULT 'draft',
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `whatsapp_clicks` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `badge_id`, `name`, `slug`, `short_description`, `full_description`, `product_story`, `price`, `discount_price`, `delivery_days`, `whatsapp_message`, `tags`, `is_featured`, `is_new_arrival`, `is_trending`, `is_bestseller`, `in_stock`, `status`, `views`, `whatsapp_clicks`, `sort_order`, `created_at`, `updated_at`) VALUES
(11, 7, 5, 'Single Tulip Crochet Bouquet', 'single-tulip-crochet-bouquet', 'A tiny handmade bloom wrapped with love and soft elegance.', 'Delicately handcrafted with premium crochet detailing, the Single Tulip Crochet Bouquet is a minimal yet meaningful gift made to preserve emotions forever. Wrapped in elegant pastel paper and finished with a satin ribbon, this bouquet brings warmth, softness, and aesthetic charm to every occasion.\r\n\r\nPerfect for surprise gifting, aesthetic room décor, friendship gifts, anniversaries, and little moments that deserve to be remembered.\r\n\r\nUnlike real flowers, this bloom never fades — making it a timeless keepsake filled with memories.', 'Some flowers bloom for a few days.\r\nSome memories bloom forever.\r\n\r\nThe Single Tulip Crochet Bouquet was created for people who love simple yet heartfelt gestures. Every stitch is handmade carefully to turn a tiny flower into a lasting emotion — a gift that reminds someone they are loved, appreciated, and remembered every single day.', '199.00', '299.00', '3-5 Working Days', 'Hello Aakar Creatives 🌸\r\n\r\nI\'m interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* ₹{price}\r\n\r\nPlease share more details. 😊', 'crochet bouquet, tulip bouquet, handmade gift, aesthetic bouquet, korean bouquet, forever flower, mini bouquet, pastel gift, tulip crochet, cute gift', 0, 0, 0, 0, 1, 'active', 81, 3, 0, '2026-05-19 11:52:34', '2026-06-20 22:02:46'),
(12, 7, 8, 'Single Rose Crochet Bouquet', 'single-rose-crochet-bouquet', 'A timeless handmade rose crafted to hold emotions forever.', 'Elegant, soft, and beautifully handcrafted, the Single Rose Crochet Bouquet is a delicate expression of love and appreciation. Designed with premium crochet artistry and wrapped in minimal pastel packaging, this forever bloom captures the beauty of a real rose without ever fading.\r\n\r\nWhether gifted as a romantic gesture, friendship token, or aesthetic surprise, this bouquet adds warmth and emotion to every special moment.\r\n\r\nA tiny handmade creation that stays beautiful forever.', 'Roses have always been symbols of love.\r\nBut this one was made to last beyond moments.\r\n\r\nThe Single Rose Crochet Bouquet is carefully handcrafted stitch by stitch to create a meaningful keepsake that never fades away. It’s not just a flower — it’s a memory wrapped in elegance, made for the people who deserve something truly heartfelt.', '199.00', '299.00', '3-5 Working Days', 'Hello Aakar Creatives 🌸\r\n\r\nI\'m interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* ₹{price}\r\n\r\nPlease share more details. 😊', 'crochet rose, rose bouquet, handmade gift, forever rose, aesthetic bouquet, romantic gift, crochet flowers, mini bouquet, korean bouquet, cute handmade gift', 0, 0, 1, 0, 1, 'active', 30, 4, 0, '2026-05-19 11:56:45', '2026-05-29 10:12:25'),
(13, 7, 8, 'I Love You Crochet Bouquet', 'i-love-you-crochet-bouquet', 'A handmade bouquet that says “I Love You” in the softest way possible.', 'Beautifully handcrafted with crochet artistry and wrapped in elegant kraft paper, the “I Love You” Crochet Bouquet is a heartfelt gift designed to express love beyond words. Featuring delicate floral details, soft textures, and a charming handmade heart centerpiece, this bouquet is perfect for romantic surprises and memorable celebrations.\r\n\r\nUnlike real flowers, this bouquet lasts forever — turning emotions into a keepsake your loved one can treasure every day.', 'Some feelings deserve more than ordinary flowers.\r\n\r\nThe “I Love You” Crochet Bouquet was created to capture love in a form that never fades. Every stitch is handmade carefully to transform simple yarn into something deeply meaningful — a bouquet that becomes a memory long after the moment passes.', '599.00', '799.00', '3-5 Working Days', 'Hello Aakar Creatives 🌸\r\n\r\nI\'m interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* ₹{price}\r\n\r\nPlease share more details. 😊', 'love bouquet, crochet bouquet, handmade gift, romantic bouquet, forever flowers, valentine gift, aesthetic gift, crochet flowers, cute bouquet, handmade crochet', 0, 1, 0, 0, 1, 'active', 14, 0, 0, '2026-05-19 12:00:03', '2026-05-22 22:56:52'),
(1012, 1, 1, 'Customized Anniversary Photo Frame for Couples | Personalized Memory Collage Gift', 'customized-anniversary-photo-frame-for-couples-personalized-memory-collage-gift', 'A personalized anniversary photo frame featuring your favorite memories in a premium collage design with a highlighted couple cutout.', 'Celebrate your love story with our handcrafted Anniversary Memory Collage Frame. Designed with multiple cherished photographs and a premium central couple highlight, this frame transforms your memories into timeless wall décor. Perfect for anniversaries, weddings, birthdays, Valentine\'s Day, or special surprise gifts.\r\n\r\nEach frame is customized with your photos, names, quotes, and theme preferences to create a truly one-of-a-kind keepsake.', 'Ideal For:\r\n\r\n-Anniversary Gifts\r\n-Wedding Gifts\r\n-Valentine’s Day\r\n-Birthday Surprise\r\n-Couple Gifts\r\nMemory Keepsakes\r\nRomantic Room Décor\r\n\r\nAvailable Sizes\r\n\r\n-A5 Size\r\n-A4 Size\r\n-12x18 Inch\r\n-18x24 Inch\r\n\r\nFrame Colors\r\n-Black\r\n-Dark Brown\r\n-White\r\n-Wooden Finish\r\n-Customization Details\r\n\r\nCustomers can provide:\r\n\r\n-5 to 15 Photos\r\n-Couple Name\r\n-Special Date\r\n-Custom Quote or Message\r\n-Preferred Theme Style', '649.00', '699.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 4, 0, 0, '2026-05-23 05:40:13', '2026-06-24 22:42:55'),
(1013, 1, 2, 'Minimalist Family Memory Frame', 'minimalist-family-memory-frame', 'A beautifully customized family photo collage frame designed to preserve your most precious moments in an elegant minimalist style.', 'Turn your family memories into timeless wall décor with our Minimalist Family Memory Frame. Featuring a clean aesthetic with multiple photo sections and personalized typography, this frame adds warmth and emotional value to any home.\r\n\r\nPerfect for families, parents, newborn memories, anniversaries, housewarming gifts, and special occasions, this elegant frame blends modern design with heartfelt moments.\r\n\r\nCustomize it with your favorite family photographs, names, dates, or meaningful quotes to create a keepsake you’ll cherish forever.', NULL, '699.00', '799.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 1, 0, 0, 1, 'active', 2, 0, 0, '2026-05-23 05:41:31', '2026-06-21 23:44:35'),
(1014, 1, 6, 'Spotify Song Photo Frame', 'spotify-song-photo-frame', 'A personalized Spotify-inspired photo frame featuring your favorite song, memories, and scannable music design for the perfect emotional gift.', 'Celebrate your special moments with our Customized Spotify Song Photo Frame — a unique blend of music and memories. Designed with your favorite photos, song title, Spotify code, and personalized message, this frame becomes a timeless keepsake for birthdays, anniversaries, friendships, and relationships.\r\n\r\nSimply scan the Spotify code to instantly play your chosen song and relive the memories connected to it. The modern minimalist layout and premium frame make it a beautiful addition to any room décor.\r\n\r\nPerfect for gifting someone who means the world to you.', NULL, '349.00', '499.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 1, 0, 0, '2026-05-23 05:42:48', '2026-05-28 20:45:16'),
(1015, 2, 1, 'Customized Friendship Magazine Scrapbook | Personalized Memory Book Gift', 'customized-friendship-magazine-scrapbook-personalized-memory-book-gift', 'A customized aesthetic magazine-style scrapbook filled with memories, photos, heartfelt messages, and emotional storytelling for your favorite person.', 'Turn your memories into a beautiful story with our Personalized Friendship Magazine Scrapbook. Designed like a real magazine with custom pages, aesthetic layouts, emotional notes, photo collages, and meaningful captions, this scrapbook is the perfect gift for your best friend, partner, sibling, or special person.\r\n\r\nEvery page is thoughtfully personalized using your photos, memories, inside jokes, friendship journey, and heartfelt messages to create a truly unforgettable keepsake.\r\n\r\nWhether it’s for birthdays, friendship anniversaries, farewell gifts, or long-distance relationships, this scrapbook captures emotions in the most creative and personal way possible.', NULL, '749.00', '799.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 4, 1, 0, '2026-05-23 05:44:21', '2026-06-12 17:26:27'),
(1017, 4, 8, 'Red Heart Embroidery Shirt', 'red-heart-embroidery-shirt', 'A trendy white embroidered shirt featuring cute red heart embroidery patterns for a soft aesthetic and stylish everyday look.', 'Add charm to your wardrobe with our Heart Embroidery Aesthetic Shirt, designed with elegant red embroidered heart patterns on a premium white shirt. Inspired by Pinterest aesthetic fashion, this shirt combines minimal style with a cute and classy appearance, making it perfect for casual outings, café dates, college wear, and everyday styling.\r\n\r\nCrafted from soft breathable fabric with premium embroidery detailing, this shirt offers both comfort and fashion. The minimalist embroidered hearts create a unique handcrafted look that instantly upgrades your outfit.', 'Key Features:\r\nPremium white cotton shirt with red heart embroidery, soft breathable fabric, aesthetic minimalist design, handmade embroidery detailing, comfortable fit, durable stitching, and trendy casual styling.\r\n\r\nIdeal For:\r\nCasual wear, aesthetic outfits, college fashion, café outings, streetwear styling, gifting, birthday gifts, and Pinterest-inspired fashion lovers.\r\n\r\nAvailable Sizes:\r\nXS, S, M, L, XL, XXL, and Oversized Fit Options.\r\n\r\nColor Options:\r\nWhite Shirt with Red Hearts, White Shirt with Pink Hearts, Black Shirt with Red Hearts, and Custom Color Combinations.\r\n\r\nCustomization Options:\r\nCustomers can customize heart colors, sleeve embroidery, initials, names, small quotes, oversized fit, and embroidery placement.\r\n\r\nMaterials Used:\r\nPremium cotton fabric, high-quality embroidery threads, durable stitching, and fade-resistant embroidery work.\r\n\r\nPackage Includes:\r\n1 Customized Heart Embroidery Shirt with premium packaging.\r\n\r\nSpecial Features:\r\nCute handcrafted heart embroidery, Pinterest-inspired aesthetic design, comfortable oversized styling, minimalist fashion look, and premium embroidery finishing.\r\n\r\nWash Care Instructions:\r\nGentle hand wash or machine wash recommended. Avoid direct ironing on embroidery. Dry in shade for long-lasting quality.\r\n\r\nSEO Title:', '1599.00', '1999.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 1, 1, 0, 1, 'active', 0, 0, 0, '2026-05-23 05:51:35', '2026-05-23 05:51:35'),
(1018, 4, 2, 'Bow Embroidery Aesthetic Shirt', 'bow-embroidery-aesthetic-shirt', 'A cute aesthetic embroidered shirt featuring elegant pink bow embroidery patterns for a soft feminine and trendy fashion look.', 'Upgrade your aesthetic wardrobe with our Bow Embroidery Aesthetic Shirt designed with delicate pink bow embroidery patterns on a premium white shirt. Inspired by Korean fashion and Pinterest aesthetics, this shirt gives a classy, cute, and minimal vibe perfect for everyday styling.\r\n\r\nThe elegant embroidered bows add a playful yet premium touch, making it ideal for casual outings, café dates, vacations, college wear, and soft girl fashion looks. Crafted with breathable fabric and detailed embroidery work, this shirt combines comfort with trendy styling.', NULL, '1699.00', '2199.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', 'Key Features: Premium white cotton shirt with pink bow embroidery, aesthetic minimalist design, soft breathable fabric, handmade embroidery detailing, comfortable loose fit, durable stitching, and trendy Korean-inspired styling.  Ideal For: Casual wear, soft girl fashion, Pinterest outfits, Korean fashion styling, café outings, college wear, birthday gifting, and aesthetic wardrobe collections.  Available Sizes: XS, S, M, L, XL, XXL, and Oversized Fit Options.  Color Options: White with Pink Bow', 0, 0, 0, 0, 1, 'active', 2, 1, 0, '2026-05-23 05:53:17', '2026-05-27 22:26:45'),
(1019, 4, 7, 'Floral Embroidery Minimal Shirt', 'floral-embroidery-minimal-shirt', 'A premium minimalist embroidered shirt featuring elegant floral embroidery detailing for a classy, aesthetic, and timeless fashion look.', 'Bring elegance to your everyday fashion with our Floral Embroidery Minimal Shirt designed with delicate handcrafted floral embroidery on a premium cotton shirt. Inspired by minimalist aesthetic fashion, this shirt combines sophistication with subtle embroidery detailing to create a stylish and luxurious appearance.\r\n\r\nPerfect for casual outings, office styling, brunch looks, vacations, and aesthetic fashion lovers, the floral embroidery adds a soft artistic touch while maintaining a clean and modern design. Crafted with breathable fabric and premium stitching, this shirt offers both comfort and premium styling.', 'Key Features:\r\nPremium cotton shirt with floral embroidery detailing, handcrafted embroidery work, minimalist aesthetic design, soft breathable fabric, durable stitching, comfortable fit, and elegant casual styling.\r\n\r\nIdeal For:\r\nCasual fashion, office wear, brunch outfits, café outings, aesthetic styling, vacation wear, gifting, and minimalist fashion lovers.\r\n\r\nAvailable Sizes:\r\nXS, S, M, L, XL, XXL, and Oversized Fit Options.\r\n\r\nColor Options:\r\nWhite with Multicolor Floral Embroidery, Sage Green with White Floral Embroidery, Beige Floral Design, Black Floral Design, and Custom Color Options.\r\n\r\nCustomization Options:\r\nCustomers can customize flower colors, embroidery placement, initials, sleeve embroidery, pocket embroidery, names, and personalized floral patterns.\r\n\r\nMaterials Used:\r\nPremium cotton fabric, high-quality embroidery threads, soft breathable material, and fade-resistant embroidery work.\r\n\r\nPackage Includes:\r\n1 Customized Floral Embroidery Shirt with premium packaging.\r\n\r\nSpecial Features:\r\nElegant handcrafted floral embroidery, Pinterest-inspired minimal aesthetic styling, premium embroidery finishing, soft comfortable fabric, and timeless fashion design.\r\n\r\nWash Care Instructions:\r\nGentle hand wash or machine wash recommended. Avoid direct ironing on embroidery. Dry in shade for long-lasting embroidery quality.', '1399.00', '1599.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 1, 0, 0, '2026-05-23 05:55:08', '2026-05-23 06:03:47'),
(1020, 4, 4, 'Sage Floral Embroidery Shirt', 'sage-floral-embroidery-shirt', 'A premium sage green embroidered shirt featuring elegant white floral embroidery for a classy, modern, and aesthetic fashion statement.', 'Elevate your wardrobe with our Sage Floral Embroidery Shirt crafted with beautiful white floral embroidery on a premium sage green cotton shirt. Designed for minimal yet stylish fashion lovers, this shirt combines elegance with modern handcrafted embroidery detailing.\r\n\r\nThe artistic floral embroidery placed across the shoulder creates a luxurious designer-inspired appearance while maintaining a clean and versatile look. Perfect for casual outings, festive styling, dinner dates, vacations, and statement fashion outfits, this embroidered shirt adds sophistication to every occasion.', 'Key Features:\r\nPremium sage green cotton shirt with white floral embroidery, handcrafted embroidery detailing, minimalist luxury design, breathable comfortable fabric, durable stitching, premium finishing, and stylish modern fit.\r\n\r\nIdeal For:\r\nCasual styling, party wear, festive outfits, dinner dates, aesthetic fashion, vacation outfits, gifting, and statement streetwear looks.\r\n\r\nAvailable Sizes:\r\nS, M, L, XL, XXL, and Custom Tailored Fit Options.\r\n\r\nColor Options:\r\nSage Green with White Floral Embroidery, Black with White Floral Design, Beige Floral Design, White Floral Design, and Custom Color Combinations.\r\n\r\nCustomization Options:\r\nCustomers can customize embroidery patterns, flower placement, initials, sleeve embroidery, back embroidery, names, and personalized floral artwork.\r\n\r\nMaterials Used:\r\nPremium cotton fabric, high-quality embroidery threads, breathable soft material, and fade-resistant embroidery work.\r\n\r\nPackage Includes:\r\n1 Customized Sage Floral Embroidery Shirt with premium packaging.\r\n\r\nSpecial Features:\r\nLuxury floral embroidery detailing, handcrafted aesthetic design, Pinterest-inspired fashion styling, premium comfort fit, and elegant minimal embroidery artwork.\r\n\r\nWash Care Instructions:\r\nGentle hand wash or machine wash recommended. Avoid direct ironing on embroidery. Dry in shade for long-lasting embroidery quality.', '1699.00', '2299.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 3, 0, 0, '2026-05-23 05:56:45', '2026-05-27 21:25:43'),
(1021, 7, 1, 'Handmade Crochet Sunflower Bouquet', 'handmade-crochet-sunflower-bouquet', 'A beautifully handmade crochet sunflower bouquet crafted with soft yarn and aesthetic wrapping, perfect for gifting and everlasting memories.', 'Brighten someone’s day with our Handmade Crochet Sunflower Bouquet, designed with detailed handcrafted crochet work to create a beautiful everlasting flower arrangement. Inspired by the warmth and happiness of real sunflowers, this bouquet adds a cute, aesthetic, and emotional touch to every special moment.\r\n\r\nUnlike real flowers, these crochet sunflowers never fade, making them a timeless keepsake for birthdays, anniversaries, friendship gifts, graduation gifts, and special surprises. Wrapped in premium rustic fabric with elegant ribbon detailing, this bouquet is perfect for aesthetic gifting and room décor.', 'Key Features:\r\nHandmade crochet sunflower design, premium yarn craftsmanship, soft aesthetic wrapping, reusable everlasting flower bouquet, lightweight design, durable handmade finishing, and cute gift-ready presentation.\r\n\r\nIdeal For:\r\nBirthday gifts, friendship gifts, anniversary surprises, graduation gifts, Valentine’s Day, aesthetic room décor, sunflower lovers, and handmade gift collections.\r\n\r\nAvailable Variants:\r\nSingle Sunflower Bouquet, Double Sunflower Bouquet, Mini Crochet Bouquet, and Customized Crochet Flower Bouquet.\r\n\r\nColor Options:\r\nClassic Yellow Sunflower, Pastel Sunflower, Pink Sunflower, White Sunflower, and Custom Color Options.\r\n\r\nCustomization Options:\r\nCustomers can customize bouquet wrapping, ribbon colors, flower colors, name tags, message cards, bouquet size, and flower combinations.\r\n\r\nMaterials Used:\r\nPremium soft yarn, handcrafted crochet work, durable floral wire stem, aesthetic wrapping fabric, and satin ribbon finishing.\r\n\r\nPackage Includes:\r\n1 Handmade Crochet Sunflower Bouquet with premium wrapping and secure packaging.\r\n\r\nSpecial Features:\r\nEverlasting handmade flowers, aesthetic Pinterest-inspired design, eco-friendly gifting option, soft handcrafted detailing, and reusable decorative bouquet styling.\r\n\r\nCare Instructions:\r\nKeep away from water and dust. Clean gently with a soft dry cloth. Store in a dry place for long-lasting quality.', '349.00', '499.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 5, 1, 0, '2026-05-23 05:58:40', '2026-06-01 22:37:59'),
(1022, 7, 8, 'LED Crochet Tulip Bouquet | Handmade Aesthetic Crochet Flower Gift', 'led-crochet-tulip-bouquet-handmade-aesthetic-crochet-flower-gift', 'A dreamy handmade crochet tulip bouquet decorated with warm fairy lights and premium wrapping, perfect for aesthetic gifting and everlasting memories.', 'Make every moment magical with our LED Crochet Tulip Bouquet, beautifully handcrafted using soft premium yarn and decorated with glowing fairy lights for a luxurious aesthetic look. Designed with elegant crochet tulips and artistic bouquet wrapping, this bouquet creates the perfect balance of handmade charm and modern gifting aesthetics.\r\n\r\nUnlike real flowers, these crochet tulips never fade, making them a timeless keepsake for birthdays, anniversaries, Valentine’s Day, friendship gifts, and special surprises. The soft glowing LED lights add a romantic and cozy touch, making this bouquet perfect for room décor and memorable gifting moments.', 'Key Features:\r\nHandmade crochet tulip flowers, warm LED fairy light decoration, premium aesthetic bouquet wrapping, reusable everlasting flower arrangement, soft yarn craftsmanship, lightweight design, durable handmade finishing, and luxury gift presentation.\r\n\r\nIdeal For:\r\nBirthday gifts, anniversary surprises, Valentine’s Day, friendship gifts, romantic surprises, aesthetic room décor, graduation gifts, and handmade gift collections.\r\n\r\nAvailable Variants:\r\nMini Crochet Tulip Bouquet, LED Tulip Bouquet, Premium Crochet Flower Bouquet, and Customized Crochet Bouquet Arrangement.\r\n\r\nColor Options:\r\nLavender Tulips, Pink Tulips, White Tulips, Yellow Tulips, Multicolor Tulips, and Custom Color Combinations.\r\n\r\nCustomization Options:\r\nCustomers can customize flower colors, bouquet size, wrapping style, fairy light colors, ribbon colors, message cards, and flower combinations.\r\n\r\nMaterials Used:\r\nPremium soft yarn, handcrafted crochet work, durable floral wire stems, warm LED fairy lights, luxury wrapping paper, and satin ribbon finishing.\r\n\r\nPackage Includes:\r\n1 Handmade LED Crochet Tulip Bouquet with fairy lights, premium wrapping, and secure packaging.\r\n\r\nSpecial Features:\r\nEverlasting handmade flowers, glowing LED decoration, Pinterest-inspired aesthetic bouquet styling, eco-friendly gifting option, reusable room décor bouquet, and premium handcrafted detailing.\r\n\r\nCare Instructions:\r\nKeep away from water and excessive dust. Handle fairy lights carefully. Clean gently with a soft dry cloth and store in a dry place.', '1599.00', '1999.00', '3-5 Working Days', 'Hello Aakar Creatives\r\n\r\nI am interested in:\r\n\r\n*Product:* {product_name}\r\n*Price:* {price}\r\n\r\nPlease share more details.', NULL, 0, 0, 1, 0, 1, 'active', 1, 0, 0, '2026-05-23 06:00:45', '2026-06-01 22:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL COMMENT 'e.g. Soft Pink, Sky Blue, Ivory White',
  `slug` varchar(90) NOT NULL COMMENT 'e.g. soft-pink, sky-blue',
  `hex_code` char(7) NOT NULL DEFAULT '#cccccc' COMMENT 'CSS hex for the swatch circle e.g. #F4B8C1',
  `swatch_image` varchar(400) DEFAULT NULL COMMENT 'Optional swatch texture image (overrides hex)',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master color catalogue — hex swatches drive image switching on frontend';

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `name`, `slug`, `hex_code`, `swatch_image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Soft Pink', 'soft-pink', '#F4B8C1', NULL, 1, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(2, 'Red', 'red', '#E53935', NULL, 2, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(3, 'Coral', 'coral', '#FF6B6B', NULL, 3, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(4, 'Peach', 'peach', '#FFCBA4', NULL, 4, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(5, 'Lavender', 'lavender', '#C9B1FF', NULL, 5, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(6, 'Purple', 'purple', '#7B1FA2', NULL, 6, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(7, 'Sky Blue', 'sky-blue', '#87CEEB', NULL, 7, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(8, 'Royal Blue', 'royal-blue', '#1565C0', NULL, 8, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(9, 'Mint Green', 'mint-green', '#A8E6CF', NULL, 9, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(10, 'Forest Green', 'forest-green', '#2E7D32', NULL, 10, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(11, 'Butter Yellow', 'butter-yellow', '#FFF176', NULL, 11, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(12, 'Gold', 'gold', '#FFD700', NULL, 12, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(13, 'White', 'white', '#FFFFFF', NULL, 13, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(14, 'Ivory', 'ivory', '#FFFFF0', NULL, 14, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(15, 'Cream', 'cream', '#FFF8DC', NULL, 15, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(16, 'Beige', 'beige', '#F5F5DC', NULL, 16, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(17, 'Brown', 'brown', '#6D4C41', NULL, 17, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(18, 'Black', 'black', '#1A1A1A', NULL, 18, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(19, 'Charcoal', 'charcoal', '#37474F', NULL, 19, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(20, 'Dusty Rose', 'dusty-rose', '#C2858E', NULL, 20, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(21, 'Pastel Orange', 'pastel-orange', '#FFCC99', NULL, 21, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(22, 'Multi-Color', 'multi-color', '#FF69B4', NULL, 99, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `product_media`
--

CREATE TABLE `product_media` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `file_url` varchar(500) NOT NULL,
  `file_type` enum('image','video') NOT NULL DEFAULT 'image',
  `alt_text` varchar(200) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = main display image',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_media`
--

INSERT INTO `product_media` (`id`, `product_id`, `file_url`, `file_type`, `alt_text`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 9, '/uploads/products/products_6a0be2783e8525.77384201.png', 'image', NULL, 1, 0, '2026-05-19 09:39:28'),
(2, 11, '/uploads/products/products_6a0c01aa834b44.48945082.jpg', 'image', NULL, 1, 0, '2026-05-19 11:52:34'),
(3, 11, '/uploads/products/products_6a0c01aa84f013.55827365.jpg', 'image', NULL, 0, 1, '2026-05-19 11:52:34'),
(4, 12, '/uploads/products/products_6a0c02a5a04953.09692147.jpg', 'image', NULL, 1, 0, '2026-05-19 11:56:45'),
(5, 13, '/uploads/products/products_6a0c036b42a3c0.04802227.jpg', 'image', NULL, 1, 0, '2026-05-19 12:00:03'),
(7, 14, '/uploads/products/products_6a0c06e2d44882.19382035.jpeg', 'image', NULL, 0, 1, '2026-05-19 12:14:50'),
(0, 1010, '/uploads/products/products_6a1193653812d9.26208999.jpg', 'image', NULL, 0, 1, '2026-05-23 04:45:41'),
(0, 1010, '/uploads/products/products_6a119365383582.99878997.jpg', 'image', NULL, 0, 2, '2026-05-23 04:45:41'),
(0, 1010, '/uploads/products/products_6a1193653855e5.98965011.jpg', 'image', NULL, 0, 3, '2026-05-23 04:45:41'),
(0, 1010, '/uploads/products/products_6a1193653876e6.03912307.jpg', 'image', NULL, 0, 4, '2026-05-23 04:45:41'),
(0, 1010, '/uploads/products/products_6a1193653897c6.70777104.jpg', 'image', NULL, 0, 5, '2026-05-23 04:45:41'),
(0, 1010, '/uploads/products/products_6a11936538b6e1.57567654.jpg', 'image', NULL, 0, 6, '2026-05-23 04:45:41'),
(0, 1003, '/uploads/products/products_6a1198bc9a1736.53869155.jpg', 'image', NULL, 0, 1, '2026-05-23 05:08:28'),
(0, 1002, '/uploads/products/products_6a1198ec63b200.01869725.jpeg', 'image', NULL, 0, 1, '2026-05-23 05:09:16'),
(0, 1012, '/uploads/products/products_6a11a02d68af45.11984278.jpeg', 'image', NULL, 1, 0, '2026-05-23 05:40:13'),
(0, 1012, '/uploads/products/products_6a11a02d68e683.64860624.jpeg', 'image', NULL, 0, 1, '2026-05-23 05:40:13'),
(0, 1012, '/uploads/products/products_6a11a02d68fb96.32340115.jpeg', 'image', NULL, 0, 2, '2026-05-23 05:40:13'),
(0, 1013, '/uploads/products/products_6a11a07b8546a6.87392020.jpeg', 'image', NULL, 1, 0, '2026-05-23 05:41:31'),
(0, 1014, '/uploads/products/products_6a11a0c8ab6c67.31526297.jpg', 'image', NULL, 1, 0, '2026-05-23 05:42:48'),
(0, 1015, '/uploads/products/products_6a11a1258e72d1.00524423.jpg', 'image', NULL, 1, 0, '2026-05-23 05:44:21'),
(0, 1015, '/uploads/products/products_6a11a1258e9ae9.13896694.jpg', 'image', NULL, 0, 1, '2026-05-23 05:44:21'),
(0, 1015, '/uploads/products/products_6a11a1258ebda1.96716187.jpg', 'image', NULL, 0, 2, '2026-05-23 05:44:21'),
(0, 1016, '/uploads/products/products_6a11a1990c8935.22612999.jpg', 'image', NULL, 1, 0, '2026-05-23 05:46:17'),
(0, 1016, '/uploads/products/products_6a11a1990cae92.24196280.jpg', 'image', NULL, 0, 1, '2026-05-23 05:46:17'),
(0, 1016, '/uploads/products/products_6a11a1990cccc0.41687099.jpg', 'image', NULL, 0, 2, '2026-05-23 05:46:17'),
(0, 1016, '/uploads/products/products_6a11a1990ced00.69775297.jpg', 'image', NULL, 0, 3, '2026-05-23 05:46:17'),
(0, 1016, '/uploads/products/products_6a11a1990d0e08.76221462.jpg', 'image', NULL, 0, 4, '2026-05-23 05:46:17'),
(0, 1016, '/uploads/products/products_6a11a1990d2dc4.50884337.jpg', 'image', NULL, 0, 5, '2026-05-23 05:46:17'),
(0, 1017, '/uploads/products/products_6a11a2d7758cd9.05617018.jpeg', 'image', NULL, 1, 0, '2026-05-23 05:51:35'),
(0, 1017, '/uploads/products/products_6a11a2d775aa29.22295355.jpeg', 'image', NULL, 0, 1, '2026-05-23 05:51:35'),
(0, 1018, '/uploads/products/products_6a11a33d33f992.64164891.jpeg', 'image', NULL, 1, 0, '2026-05-23 05:53:17'),
(0, 1019, '/uploads/products/products_6a11a3acbebe51.52739288.jpg', 'image', NULL, 1, 0, '2026-05-23 05:55:08'),
(0, 1020, '/uploads/products/products_6a11a40d384c11.45515438.jpeg', 'image', NULL, 1, 0, '2026-05-23 05:56:45'),
(0, 1021, '/uploads/products/products_6a11a480c4ec88.75382472.jpeg', 'image', NULL, 1, 0, '2026-05-23 05:58:40'),
(0, 1022, '/uploads/products/products_6a11a4fd85e849.17185438.jpeg', 'image', NULL, 1, 0, '2026-05-23 06:00:45'),
(0, 1022, '/uploads/products/products_6a11a4fd85ff26.67805059.jpeg', 'image', NULL, 0, 1, '2026-05-23 06:00:45'),
(0, 1022, '/uploads/products/products_6a11a4fd8617e8.78678971.jpeg', 'image', NULL, 0, 2, '2026-05-23 06:00:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_occasions`
--

CREATE TABLE `product_occasions` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `occasion_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_occasions`
--

INSERT INTO `product_occasions` (`product_id`, `occasion_id`) VALUES
(9, 1),
(14, 1),
(1000, 1),
(1001, 1),
(1002, 1),
(1003, 1),
(1007, 1),
(1010, 1),
(1011, 1),
(1012, 1),
(1013, 1),
(1014, 1),
(1015, 1),
(1016, 1),
(1017, 1),
(1018, 1),
(1019, 1),
(1020, 1),
(1021, 1),
(1022, 1),
(11, 2),
(12, 2),
(1004, 2),
(1008, 2),
(1009, 2),
(1010, 2),
(1011, 2),
(1012, 2),
(1015, 2),
(1016, 2),
(1022, 2),
(11, 3),
(12, 3),
(1008, 3),
(1009, 3),
(1010, 3),
(1011, 3),
(1012, 3),
(1014, 3),
(1015, 3),
(1016, 3),
(1022, 3),
(11, 4),
(12, 4),
(13, 4),
(11, 5),
(11, 6),
(12, 6),
(11, 7),
(12, 7);

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(60) NOT NULL COMMENT 'Display label: "A4", "5×7 Inch", "Large", "XL"',
  `slug` varchar(70) NOT NULL COMMENT 'URL-safe: a4, 5x7-inch, large, xl',
  `size_type` enum('clothing','frame','print','bouquet','box','custom') NOT NULL DEFAULT 'custom' COMMENT 'Groups sizes for admin UI filtering',
  `dimension_cm` varchar(60) DEFAULT NULL COMMENT 'e.g. "21×29.7" or "chest:96–101"',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master size catalogue shared across all product types';

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `label`, `slug`, `size_type`, `dimension_cm`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'XS', 'xs', 'clothing', 'chest: 81–86 cm', 1, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(2, 'S', 's', 'clothing', 'chest: 86–91 cm', 2, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(3, 'M', 'm', 'clothing', 'chest: 91–96 cm', 3, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(4, 'L', 'l', 'clothing', 'chest: 96–101 cm', 4, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(5, 'XL', 'xl', 'clothing', 'chest: 101–106 cm', 5, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(6, 'XXL', 'xxl', 'clothing', 'chest: 106–111 cm', 6, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(10, '4×6 Inch', '4x6-inch', 'frame', '10×15 cm', 10, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(11, '5×7 Inch', '5x7-inch', 'frame', '12.7×17.8 cm', 11, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(12, '8×10 Inch', '8x10-inch', 'frame', '20.3×25.4 cm', 12, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(13, '10×12 Inch', '10x12-inch', 'frame', '25.4×30.5 cm', 13, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(14, '12×18 Inch', '12x18-inch', 'frame', '30.5×45.7 cm', 14, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(15, 'A4', 'a4', 'print', '21×29.7 cm', 15, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(16, 'A3', 'a3', 'print', '29.7×42 cm', 16, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(20, 'Mini', 'mini', 'bouquet', 'approx. 15 cm tall', 20, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(21, 'Standard', 'standard', 'bouquet', 'approx. 25 cm tall', 21, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(22, 'Large', 'large', 'bouquet', 'approx. 35 cm tall', 22, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(30, 'Small', 'small', 'box', '15×10×6 cm', 30, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(31, 'Medium', 'medium', 'box', '20×15×8 cm', 31, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(32, 'Large (Box)', 'large-box', 'box', '25×20×10 cm', 32, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(40, 'One Size', 'one-size', 'custom', NULL, 40, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(41, 'Custom', 'custom', 'custom', NULL, 99, 1, '2026-05-21 06:24:40', '2026-05-21 06:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `size_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL if this product has no size variants',
  `color_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL if this product has no color variants',
  `sku` varchar(100) DEFAULT NULL COMMENT 'Optional unique stock-keeping unit code',
  `price_override` decimal(10,2) DEFAULT NULL COMMENT 'NULL = use products.price; set to override for this variant',
  `discount_price_override` decimal(10,2) DEFAULT NULL COMMENT 'NULL = use products.discount_price',
  `stock_qty` smallint(6) NOT NULL DEFAULT -1 COMMENT '-1 = unlimited/made-to-order; 0 = out of stock; >0 = quantity',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Every unique size+color combination for a product with optional price/stock override';

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size_id`, `color_id`, `sku`, `price_override`, `discount_price_override`, `stock_qty`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 11, 20, 1, NULL, '149.00', '229.00', -1, 1, 10, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(2, 11, 20, 7, NULL, '149.00', '229.00', -1, 1, 11, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(3, 11, 20, 9, NULL, '149.00', '229.00', -1, 1, 12, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(4, 11, 20, 11, NULL, '149.00', '229.00', -1, 1, 13, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(5, 11, 20, 5, NULL, '149.00', '229.00', -1, 1, 14, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(6, 11, 21, 1, NULL, NULL, NULL, -1, 1, 20, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(7, 11, 21, 2, NULL, NULL, NULL, -1, 1, 21, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(8, 11, 21, 7, NULL, NULL, NULL, -1, 1, 22, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(9, 11, 21, 9, NULL, NULL, NULL, -1, 1, 23, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(10, 11, 21, 11, NULL, NULL, NULL, -1, 1, 24, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(11, 11, 21, 5, NULL, NULL, NULL, -1, 1, 25, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(12, 11, 21, 20, NULL, NULL, NULL, -1, 1, 26, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(13, 11, 22, 1, NULL, '279.00', '399.00', -1, 1, 30, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(14, 11, 22, 2, NULL, '279.00', '399.00', -1, 1, 31, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(15, 11, 22, 7, NULL, '279.00', '399.00', -1, 1, 32, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(16, 11, 22, 9, NULL, '279.00', '399.00', -1, 1, 33, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(17, 11, 22, 5, NULL, '279.00', '399.00', -1, 1, 34, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(18, 12, 20, 1, NULL, '149.00', '229.00', -1, 1, 10, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(19, 12, 20, 2, NULL, '149.00', '229.00', -1, 1, 11, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(20, 12, 20, 20, NULL, '149.00', '229.00', -1, 1, 12, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(21, 12, 21, 1, NULL, NULL, NULL, -1, 1, 20, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(22, 12, 21, 2, NULL, NULL, NULL, -1, 1, 21, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(23, 12, 21, 20, NULL, NULL, NULL, -1, 1, 22, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(24, 12, 21, 3, NULL, NULL, NULL, -1, 1, 23, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(25, 12, 21, 6, NULL, NULL, NULL, -1, 1, 24, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(26, 12, 22, 2, NULL, '279.00', '399.00', -1, 1, 30, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(27, 12, 22, 1, NULL, '279.00', '399.00', -1, 1, 31, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(28, 12, 22, 20, NULL, '279.00', '399.00', -1, 1, 32, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(29, 13, 21, 1, NULL, NULL, NULL, -1, 1, 10, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(30, 13, 21, 2, NULL, NULL, NULL, -1, 1, 11, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(31, 13, 21, 5, NULL, NULL, NULL, -1, 1, 12, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(32, 13, 21, 7, NULL, NULL, NULL, -1, 1, 13, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(33, 13, 22, 1, NULL, '799.00', '999.00', -1, 1, 20, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(34, 13, 22, 2, NULL, '799.00', '999.00', -1, 1, 21, '2026-05-21 06:24:40', '2026-05-21 06:24:40'),
(35, 13, 22, 5, NULL, '799.00', '999.00', -1, 1, 22, '2026-05-21 06:24:40', '2026-05-21 06:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_images`
--

CREATE TABLE `product_variant_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `color_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = default / no-color image',
  `file_url` varchar(500) NOT NULL,
  `file_type` enum('image','video') NOT NULL DEFAULT 'image',
  `alt_text` varchar(200) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = main swatch preview & first gallery image for this color',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Color-keyed images: frontend swaps gallery when user picks a color swatch';

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Link to customer if known',
  `product_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Product the review is about',
  `name` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Shown name (may differ from customers table)',
  `instagram` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5 COMMENT '1–5',
  `review` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = visible on site',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = homepage spotlight',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `customer_id`, `product_id`, `name`, `instagram`, `rating`, `review`, `is_approved`, `is_featured`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Priya Malhotra', '@priya.m', 5, 'Absolutely beautiful! The quality exceeded my expectations. My boyfriend loved it!', 1, 1, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(2, 2, NULL, 'Ravi Kumar', '@ravi_captures', 5, 'The magazine was so emotional, we both cried! 100% worth every rupee.', 1, 1, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(3, 3, NULL, 'Ananya Singh', '@ananya.creates', 4, 'So cute and well-made. The flowers look so real! Great packaging too.', 1, 0, 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(4, NULL, NULL, 'Amit', '@amittvibes', 5, 'This is a greate gift ever', 1, 0, 0, '2026-05-18 17:19:45', '2026-05-18 17:19:45');

-- --------------------------------------------------------

--
-- Table structure for table `v_top_products`
--

CREATE TABLE `v_top_products` (
  `id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `category` varchar(120) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `views` int(10) UNSIGNED DEFAULT NULL,
  `whatsapp_clicks` int(10) UNSIGNED DEFAULT NULL,
  `click_rate_pct` decimal(15,1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_settings`
--

CREATE TABLE `whatsapp_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `phone_number` varchar(20) NOT NULL COMMENT 'With country code: +91XXXXXXXXXX',
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_settings`
--

INSERT INTO `whatsapp_settings` (`id`, `phone_number`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, '9510360227', 1, '2026-05-18 16:22:16', '2026-05-18 16:46:41');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_templates`
--

CREATE TABLE `whatsapp_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `occasion_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = default template for all',
  `label` varchar(100) NOT NULL,
  `template` text NOT NULL COMMENT 'Variables: {product_name} {price} {category}',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_templates`
--

INSERT INTO `whatsapp_templates` (`id`, `occasion_id`, `label`, `template`, `is_default`, `created_at`, `updated_at`) VALUES
(1, NULL, '', '', 1, '2026-05-18 16:22:16', '2026-05-21 21:01:51'),
(2, 3, 'Valentine\'s Day Template', 'Hi Aakar Creatives 💕\n\nLooking for a Valentine\'s Day gift!\n\n*Product:* {product_name}\n*Price:* ₹{price}\n\nKindly guide me. 🌹', 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16'),
(3, 1, 'Anniversary Template', 'Hello 💍\n\nI want to surprise my partner!\n\n*Product:* {product_name}\n*Price:* ₹{price}\n\nPlease help me customise it. 😊', 0, '2026-05-18 16:22:16', '2026-05-18 16:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_email` (`email`),
  ADD KEY `idx_admins_active` (`is_active`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_badge_name` (`name`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_customer_cart_item` (`customer_id`,`product_id`,`variant_id`),
  ADD KEY `fk_cart_product` (`product_id`),
  ADD KEY `fk_cart_variant` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_category_slug` (`slug`);

--
-- Indexes for table `category_attributes`
--
ALTER TABLE `category_attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ca_category_type` (`category_id`,`attribute_type`),
  ADD KEY `idx_ca_category` (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_customer_phone` (`phone`),
  ADD KEY `idx_customer_email` (`email`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inq_customer` (`customer_id`),
  ADD KEY `idx_inq_product` (`product_id`),
  ADD KEY `idx_inq_status` (`status`);

--
-- Indexes for table `media_library`
--
ALTER TABLE `media_library`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ml_folder` (`folder`),
  ADD KEY `idx_ml_uploaded` (`uploaded_by`);

--
-- Indexes for table `occasions`
--
ALTER TABLE `occasions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_occasion_slug` (`slug`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_offers_active` (`is_active`),
  ADD KEY `idx_offers_dates` (`start_date`,`end_date`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_customer` (`customer_id`),
  ADD KEY `idx_orders_product` (`product_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_product_slug` (`slug`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_badge` (`badge_id`),
  ADD KEY `idx_products_status` (`status`),
  ADD KEY `idx_products_featured` (`is_featured`),
  ADD KEY `idx_products_new_arrival` (`is_new_arrival`),
  ADD KEY `idx_products_trending` (`is_trending`),
  ADD KEY `idx_products_bestseller` (`is_bestseller`),
  ADD KEY `idx_products_sort` (`sort_order`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_color_slug` (`slug`),
  ADD KEY `idx_color_sort` (`sort_order`);

--
-- Indexes for table `product_occasions`
--
ALTER TABLE `product_occasions`
  ADD PRIMARY KEY (`product_id`,`occasion_id`),
  ADD KEY `idx_po_occasion` (`occasion_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_size_slug` (`slug`),
  ADD KEY `idx_size_type` (`size_type`),
  ADD KEY `idx_size_sort` (`sort_order`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_variant_combo` (`product_id`,`size_id`,`color_id`),
  ADD KEY `idx_pv_product` (`product_id`),
  ADD KEY `idx_pv_size` (`size_id`),
  ADD KEY `idx_pv_color` (`color_id`),
  ADD KEY `idx_pv_active` (`is_active`);

--
-- Indexes for table `product_variant_images`
--
ALTER TABLE `product_variant_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pvi_product` (`product_id`),
  ADD KEY `idx_pvi_color` (`color_id`),
  ADD KEY `idx_pvi_product_color` (`product_id`,`color_id`,`sort_order`),
  ADD KEY `idx_pvi_primary` (`product_id`,`color_id`,`is_primary`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_test_customer` (`customer_id`),
  ADD KEY `idx_test_product` (`product_id`),
  ADD KEY `idx_test_approved` (`is_approved`),
  ADD KEY `idx_test_featured` (`is_featured`);

--
-- Indexes for table `whatsapp_settings`
--
ALTER TABLE `whatsapp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wat_occasion` (`occasion_id`),
  ADD KEY `idx_wat_default` (`is_default`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_customer_product_wish` (`customer_id`,`product_id`),
  ADD KEY `fk_wish_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `category_attributes`
--
ALTER TABLE `category_attributes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `media_library`
--
ALTER TABLE `media_library`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT for table `occasions`
--
ALTER TABLE `occasions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1023;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `product_variant_images`
--
ALTER TABLE `product_variant_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `whatsapp_settings`
--
ALTER TABLE `whatsapp_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `whatsapp_templates`
--
ALTER TABLE `whatsapp_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `category_attributes`
--
ALTER TABLE `category_attributes`
  ADD CONSTRAINT `fk_ca_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_pv_color` FOREIGN KEY (`color_id`) REFERENCES `product_colors` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pv_size` FOREIGN KEY (`size_id`) REFERENCES `product_sizes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_variant_images`
--
ALTER TABLE `product_variant_images`
  ADD CONSTRAINT `fk_pvi_color` FOREIGN KEY (`color_id`) REFERENCES `product_colors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pvi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wish_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wish_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
