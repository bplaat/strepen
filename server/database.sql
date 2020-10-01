-- Strepen Systeem Database

-- Create users table
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT,

    `firstname` VARCHAR(32) NOT NULL,
    `lastname` VARCHAR(64) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NULL,
    `admin` BOOLEAN NOT NULL DEFAULT FALSE,
    `active` BOOLEAN NOT NULL DEFAULT TRUE,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE (`email`)
);

-- Insert Bastiaan admin user
INSERT INTO `users` (`firstname`, `lastname`, `email`, `password`, `admin`) VALUES
    ('Bastiaan', 'van der Plaat', 'bastiaan.v.d.plaat@gmail.com', '$2b$10$ayELeGjJHHjsDCCBs7d75u1U.14xi2PIiRQS50ibni.XEiszd3gwi', TRUE);

-- Create sessions table
CREATE TABLE `sessions` (
    `id` INT UNSIGNED AUTO_INCREMENT,

    `user_id` INT UNSIGNED NOT NULL,
    `session` CHAR(32) NOT NULL,
    `ip` VARCHAR(32) NOT NULL,
    `browser` VARCHAR(32) NOT NULL,
    `version` VARCHAR(32) NOT NULL,
    `platform` VARCHAR(32) NOT NULL,
    `expires_at` DATETIME NOT NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE (`session`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- Create products table
CREATE TABLE `products` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,

    `name` VARCHAR(32) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `active` BOOLEAN NOT NULL DEFAULT TRUE,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- Create product stock table
CREATE TABLE `product_stock` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,

    `product_id` INT UNSIGNED NOT NULL,
    `amount` INT UNSIGNED NOT NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- Create user debt table
CREATE TABLE `user_debt` (
    `id` INT UNSIGNED AUTO_INCREMENT,

    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `amount` INT UNSIGNED NOT NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- Create keys table
CREATE TABLE `keys` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,

    `name` VARCHAR(32) NOT NULL,
    `key` CHAR(32) NOT NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    UNIQUE (`key`)
);

-- Create news table
CREATE TABLE `news` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,

    `subject` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);
