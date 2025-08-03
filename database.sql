-- --------------------------------------------------------
-- SQL Schema for WanderChicVibes Blog
-- --------------------------------------------------------

--
-- Set SQL mode and time zone
--
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wanderchicvibes`
--
CREATE DATABASE IF NOT EXISTS `wanderchicvibes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wanderchicvibes`;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--
-- This table will store all the blog posts for the website.
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'WanderChicVibes Team',
  `featured_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- (Optional) Table structure for table `comments`
--
-- CREATE TABLE `comments` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `post_id` int(11) NOT NULL,
--   `author_name` varchar(100) NOT NULL,
--   `author_email` varchar(255) NOT NULL,
--   `comment` text NOT NULL,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `is_approved` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`),
--   KEY `post_id` (`post_id`),
--   CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--

--
-- (Optional) Table structure for table `users` (for admin panel)
--
-- CREATE TABLE `users` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `username` varchar(50) NOT NULL,
--   `password` varchar(255) NOT NULL, -- Hashed password
--   `email` varchar(100) NOT NULL,
--   `role` enum('admin','editor','contributor') NOT NULL DEFAULT 'contributor',
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `username` (`username`),
--   UNIQUE KEY `email` (`email`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
