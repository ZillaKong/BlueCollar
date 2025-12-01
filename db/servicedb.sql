-- SET Sql_mode = "NO_AUTO_VALUE_ON_ZERO";
-- Start transaction;
-- SET time_zone = "+00:00";
-- /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
-- /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
-- /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
-- /*!40101 SET NAMES utf8mb4 */;

-- Create DATABASE IF NOT EXISTS `BlueCollar` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `BlueCollar`;

-- -- --------------------------------------------------------
-- -- Table structure for table `users`
-- -- --------------------------------------------------------
-- CREATE TABLE IF NOT EXISTS `final_users` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `first_name` varchar(50) NOT NULL,
--   `last_name` varchar(50) NOT NULL,
--   `company_name` varchar(50) NOT NULL,
--   `email` varchar(100) NOT NULL,
--   `password` varchar(255) NOT NULL,
--   `phone` varchar(50) NOT NULL,
--   `role` enum('admin','seller', 'buyer') NOT NULL DEFAULT 'buyer',
--   if (`role`= 'seller', `store_name` varchar(100) NOT NULL, `store_description` varchar(10000) DEFAULT NULL),
--   if (`role`= 'buyer', `type` enum(`mechanic`, `electrician`, `plumber`, `carpenter`, `painter`, `DIY`, `other`) NOT NULL, `type` enum(`mechanic`, `electrician`, `plumber`, `carpenter`, `painter`, `DIY`, `other`) DEFAULT 'other'),
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `username` (`username`),
--   UNIQUE KEY `email` (`email`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --------------------------------------------------------
-- -- Table structure for table `seller_Storefront`
-- -- --------------------------------------------------------

-- CREATE TABLE IF NOT EXISTS `final_seller_Storefront` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `seller_id` int(11) NOT NULL,
--   `store_name` varchar(100) NOT NULL,
--   `store_description` text,
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `seller_id` (`seller_id`),
--   FOREIGN KEY (`id`) REFERENCES `users`(`id`) ON DELETE CASCADE
--   FOREIGN KEY (`store_name`) REFERENCES `users`(`store_name`) ON DELETE CASCADE
--   FOREIGN KEY (`store_description`) REFERENCES `users`(`store_description`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --------------------------------------------------------
-- -- Table structure for table `products`
-- -- --------------------------------------------------------

-- CREATE TABLE IF NOT EXISTS `final_products` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `seller_id` int(11) NOT NULL,
--   `product_code` varchar(50) NOT NULL UNIQUE,
--   `product_name` varchar(100) NOT NULL,
--   `product_description` text,
--   `price` decimal(10,2) NOT NULL,
--   `stock_quantity` int(11) NOT NULL,
--   if (`stock_quantity`= 0, `availability_status` enum('out_of_stock', 'incoming_batch') NOT NULL DEFAULT 'out_of_stock', enum('out_of_stock', 'incoming_batch') NOT NULL DEFAULT 'out_of_stock'),
--   if (`availability_status`= 'incoming_batch', `expected_restock_date` date DEFAULT NULL, `expected_restock_date` date DEFAULT NULL),
--   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --------------------------------------------------------
-- -- Table structure for table `orders`       
-- -- --------------------------------------------------------

-- CREATE TABLE IF NOT EXISTS `final_orders` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `buyer_id` int(11) NOT NULL,
--   `invoice_number` varchar(50) NOT NULL UNIQUE,
--   `status` enum('pending', 'completed', 'canceled') NOT NULL DEFAULT 'pending',
--   PRIMARY KEY (`id`),
--   FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
--   FOREIGN KEY (`invoice_number`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --------------------------------------------------------
-- -- Table structure for table `order_items`
-- -- --------------------------------------------------------

-- CREATE TABLE IF NOT EXISTS `final_order_items` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `order_id` int(11) NOT NULL,
--   `product_id` int(11) NOT NULL,
--   `quantity` int(11) NOT NULL,
--   `price_at_order` decimal(10,2) NOT NULL,
--   PRIMARY KEY (`id`),
--   FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
--   FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --------------------------------------------------------
-- -- Table structure for table `invoices` 
-- -- --------------------------------------------------------

-- CREATE TABLE IF NOT EXISTS `final_invoices` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `order_id` int(11) NOT NULL,
--   `invoice_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `total_amount` decimal(10,2) NOT NULL,
--   PRIMARY KEY (`id`),
--   FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --------------------------------------------------------
-- -- Table structure for table `payments`
-- -- --------------------------------------------------------

-- CREATE TABLE IF NOT EXISTS `final_payments` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `invoice_id` int(11) NOT NULL,
--   `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `amount` decimal(10,2) NOT NULL,
--   `payment_method` enum('credit_card', 'paypal', 'bank_transfer') NOT NULL,
--   PRIMARY KEY (`id`),
--   FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
--   FOREIGN KEY (`amount`) REFERENCES `invoices`(`total_amount`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- Caluculate total_amount in invoices table
-- DELIMITER //
-- CREATE TRIGGER calculate_total_amount
-- AFTER INSERT ON order_items
-- FOR EACH ROW    
-- BEGIN
--     DECLARE total DECIMAL(10,2);
--     SELECT SUM(price_at_order * quantity) INTO total
--     FROM order_items
--     WHERE order_id = NEW.order_id;
    
--     UPDATE invoices
--     SET total_amount = total
--     WHERE order_id = NEW.order_id;
-- END;//  
-- DELIMITER ;
-- COMMIT;
-- /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
-- /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
-- /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- -- Dump completed on 2024-06-17  3:21:26

SET sql_mode = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `BlueCollar`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `BlueCollar`;

-- --------------------------------------------------------
-- Table structure for table `final_users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `company_name` VARCHAR(50) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `role` ENUM('admin','supplier','buyer') NOT NULL DEFAULT 'buyer',

  -- Seller-specific fields (nullable if not seller)
  `store_name` VARCHAR(100) DEFAULT NULL,
  `store_description` VARCHAR(10000) DEFAULT NULL,

  -- Buyer-specific field (nullable if not buyer)
  `buyer_type` ENUM('mechanic','electrician','plumber','carpenter','painter','DIY','other')
    DEFAULT 'other',

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_seller_storefront`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_seller_storefront` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id` INT(11) NOT NULL,
  `store_name` VARCHAR(100) NOT NULL,
  `store_description` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_seller_id` (`seller_id`),
  CONSTRAINT `fk_storefront_seller`
    FOREIGN KEY (`seller_id`)
    REFERENCES `final_users`(`id`)
    DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id` INT(11) NOT NULL,
  `product_code` VARCHAR(50) NOT NULL,
  `product_name` VARCHAR(100) NOT NULL,
  `product_description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock_quantity` INT(11) NOT NULL,

  `availability_status` ENUM('out_of_stock', 'incoming_batch')
    NOT NULL DEFAULT 'out_of_stock',

  `expected_restock_date` DATE DEFAULT NULL,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_product_code` (`product_code`),
  CONSTRAINT `fk_products_seller`
    FOREIGN KEY (`seller_id`)
    REFERENCES `final_users`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_orders`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` INT(11) NOT NULL,
  `invoice_number` VARCHAR(50) NOT NULL,
  `status` ENUM('pending','completed','canceled') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_invoice_number` (`invoice_number`),
  CONSTRAINT `fk_orders_buyer`
    FOREIGN KEY (`buyer_id`)
    REFERENCES `final_users`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_order_items`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price_at_order` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_items_order`
    FOREIGN KEY (`order_id`)
    REFERENCES `final_orders`(`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product`
    FOREIGN KEY (`product_id`)
    REFERENCES `final_products`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_invoices`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_invoices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `invoice_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_invoice_order` (`order_id`),
  CONSTRAINT `fk_invoices_order`
    FOREIGN KEY (`order_id`)
    REFERENCES `final_orders`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_payments`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('credit_card','paypal','bank_transfer') NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_payments_invoice`
    FOREIGN KEY (`invoice_id`)
    REFERENCES `final_invoices`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure for `Category`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Trigger: calculate total_amount in invoices table
-- --------------------------------------------------------
DELIMITER //

CREATE TRIGGER `trg_calculate_total_amount`
AFTER INSERT ON `final_order_items`
FOR EACH ROW
BEGIN
    DECLARE total DECIMAL(10,2);
    SELECT IFNULL(SUM(price_at_order * quantity), 0) INTO total
    FROM `final_order_items`
    WHERE `order_id` = NEW.`order_id`;

    UPDATE `final_invoices`
    SET `total_amount` = total
    WHERE `order_id` = NEW.`order_id`;
END//

DELIMITER ;

COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Dump completed on 2024-06-17  3:21:26
