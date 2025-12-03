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
DROP TABLE IF EXISTS `final_users`;
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
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure for `final_categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_categories`;
CREATE TABLE IF NOT EXISTS `final_categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`),
  UNIQUE KEY `uniq_category_name` (`name`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table Structure for `final_brands`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_brands`;
CREATE TABLE IF NOT EXISTS `final_brands` (
  `brand_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `uniq_brand_name` (`name`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_seller_storefront`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_seller_storefront`;
CREATE TABLE IF NOT EXISTS `final_seller_storefront` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id` INT(11) NOT NULL,
  `primary_category` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_seller_id` (`seller_id`),
  CONSTRAINT `fk_storefront_seller`
    FOREIGN KEY (`seller_id`)
    REFERENCES `final_users`(`user_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_storefront_category`
    FOREIGN KEY (`primary_category`)
    REFERENCES `final_categories`(`cat_id`)
    ON DELETE SET NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_storefront_categories`
-- (optional: links storefronts to multiple categories)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_storefront_categories`;
CREATE TABLE IF NOT EXISTS `final_storefront_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `storefront_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_storefront_category` (`storefront_id`, `category_id`),
  CONSTRAINT `fk_sc_storefront`
    FOREIGN KEY (`storefront_id`)
    REFERENCES `final_seller_storefront`(`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_sc_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `final_categories`(`cat_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_products`
-- (moved after categories & brands so FKs work)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_products`;
CREATE TABLE IF NOT EXISTS `final_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id` INT(11) NOT NULL,
  `product_code` VARCHAR(50) NOT NULL,
  `product_name` VARCHAR(100) NOT NULL,
  `product_description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock_quantity` INT(11) NOT NULL,
  `storefront_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `brand_id` INT(11) NOT NULL,

  `availability_status` ENUM('out_of_stock', 'incoming_batch', 'in stock')
    NOT NULL DEFAULT 'out_of_stock',

  `expected_restock_date` DATE DEFAULT NULL,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_product_code` (`product_code`),

  CONSTRAINT `fk_products_seller`
    FOREIGN KEY (`seller_id`)
    REFERENCES `final_users`(`user_id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_products_storefront`
    FOREIGN KEY (`storefront_id`)
    REFERENCES `final_seller_storefront`(`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `final_categories`(`cat_id`)
    ON DELETE RESTRICT,

  CONSTRAINT `fk_products_brand`
    FOREIGN KEY (`brand_id`)
    REFERENCES `final_brands`(`brand_id`)
    ON DELETE RESTRICT

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_orders`;
CREATE TABLE IF NOT EXISTS `final_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` INT(11) NOT NULL,
  `supplier_id` INT(11) NOT NULL,
  `storefront_id` INT(11) NOT NULL,
  `invoice_number` VARCHAR(50) NOT NULL,
  `status` ENUM('pending','completed','canceled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_invoice_number` (`invoice_number`),
  CONSTRAINT `fk_orders_buyer`
    FOREIGN KEY (`buyer_id`)
    REFERENCES `final_users`(`user_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_orders_supplier`
    FOREIGN KEY (`supplier_id`)
    REFERENCES `final_users`(`user_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_orders_storefront`
    FOREIGN KEY (`storefront_id`)
    REFERENCES `final_seller_storefront`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `final_order_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_order_items`;
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
DROP TABLE IF EXISTS `final_invoices`;
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
-- (includes Paystack integration fields)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_payments`;
CREATE TABLE IF NOT EXISTS `final_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('credit_card','paypal','bank_transfer','paystack') NOT NULL,
  `payment_reference` VARCHAR(100) DEFAULT NULL,
  `payment_status` ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_payment_reference` (`payment_reference`),
  CONSTRAINT `fk_payments_invoice`
    FOREIGN KEY (`invoice_id`)
    REFERENCES `final_invoices`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Junction table: storefront <-> categories
-- --------------------------------------------------------
DROP TABLE IF EXISTS `final_storefront_categories`;
CREATE TABLE IF NOT EXISTS `final_storefront_categories` (
  `storefront_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  PRIMARY KEY (`storefront_id`, `category_id`),
  CONSTRAINT `fk_sc_storefront`
    FOREIGN KEY (`storefront_id`)
    REFERENCES `final_seller_storefront`(`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_sc_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `final_categories`(`cat_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Triggers: calculate total_amount in invoices table
-- --------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_calculate_total_amount`;
DROP TRIGGER IF EXISTS `trg_update_total_amount`;

DELIMITER //

-- Trigger for INSERT on order items
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

-- Trigger for UPDATE on order items
CREATE TRIGGER `trg_update_total_amount`
AFTER UPDATE ON `final_order_items`
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
