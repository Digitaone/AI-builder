-- Database schema for Digital Product Store

-- Categories Table: Stores product categories
CREATE TABLE `categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(255) NOT NULL UNIQUE,
  `category_description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users Table: Stores user information
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL, -- Store hashed passwords, not plain text
  `first_name` VARCHAR(100),
  `last_name` VARCHAR(100),
  `role` ENUM('customer', 'admin') DEFAULT 'customer', -- Example roles
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products Table: Stores digital product details
CREATE TABLE `products` (
  `product_id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `category_id` INT,
  `file_path` VARCHAR(255) NOT NULL, -- Path to the actual digital product file (secure storage recommended)
  `preview_path` VARCHAR(255), -- Path to a preview version (e.g., watermarked image, demo)
  `cover_image_path` VARCHAR(255), -- Path to the product's cover image
  `stock_available` INT DEFAULT NULL, -- NULL for unlimited, 0 for out of stock, >0 for limited stock
  `average_rating` DECIMAL(3, 2) DEFAULT 0.00,
  `total_ratings` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL -- Set to NULL if category is deleted
);

-- Orders Table: Stores customer order information
CREATE TABLE `orders` (
  `order_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `total_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `order_status` ENUM('pending', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
  `payment_gateway_transaction_id` VARCHAR(255) NULL, -- Store transaction ID from payment gateway
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL -- Keep order history even if user is deleted
);

-- Order Items Table: Stores individual items within an order
CREATE TABLE `order_items` (
  `order_item_id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT,
  `quantity` INT NOT NULL DEFAULT 1,
  `price_at_purchase` DECIMAL(10, 2) NOT NULL, -- Price of the product at the time of purchase
  `download_link` VARCHAR(255) NULL, -- Secure, time-limited download link (if applicable)
  `download_expires_at` TIMESTAMP NULL, -- Expiry date for the download link
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE, -- If order is deleted, delete its items
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE SET NULL -- Keep item in order history even if product is removed
);

-- Wishlists Table: Allows users to save products they are interested in
CREATE TABLE `wishlists` (
  `wishlist_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
  UNIQUE `user_product_unique` (`user_id`, `product_id`) -- Ensures a product can only be added once per user wishlist
);

-- Product Ratings Table: Stores user ratings and reviews for products
CREATE TABLE `product_ratings` (
  `rating_id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` TINYINT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5), -- Rating from 1 to 5
  `review_text` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  UNIQUE `user_product_rating_unique` (`user_id`, `product_id`) -- Ensures a user can rate/review a product only once
);

-- Example: Add an admin user (ensure to change the password)
-- INSERT INTO `users` (`username`, `email`, `password_hash`, `role`)
-- VALUES ('admin', 'admin@example.com', '$2y$10$your_secure_password_hash_here', 'admin');

-- Example: Add a category
-- INSERT INTO `categories` (`category_name`, `category_description`)
-- VALUES ('eBooks', 'Various digital books and publications.');
