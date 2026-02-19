-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 19 Şub 2026, 19:03:58
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `php_exam_db`
--
CREATE DATABASE IF NOT EXISTS `php_exam_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `php_exam_db`;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_link` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `articles`
--

INSERT INTO `articles` (`id`, `name`, `description`, `price`, `image_link`, `author_id`, `created_at`) VALUES
(17, 'Naruto Uzumaki', 'Figurine Naruto Shippuden 18cm.', 39.90, '/php_exam/uploads/naruto.jpg', 3, '2026-02-19 14:39:41'),
(18, 'Sasuke Uchiha', 'Figurine détaillée édition spéciale.', 42.50, '/php_exam/uploads/sasuke.jpg', 3, '2026-02-19 14:39:41'),
(19, 'Monkey D. Luffy', 'One Piece figurine premium.', 44.90, '/php_exam/uploads/monkeydluffy.jpg', 3, '2026-02-19 14:39:41'),
(20, 'Levi Ackerman', 'Attack on Titan édition limitée.', 35.50, '/php_exam/uploads/leviackerman.jpg', 3, '2026-02-19 14:39:41'),
(21, 'Tanjiro Kamado', 'Demon Slayer collection anime.', 39.00, '/php_exam/uploads/tanjiro.jpg', 3, '2026-02-19 14:39:41'),
(22, 'Gojo Satoru', 'Jujutsu Kaisen édition spéciale.', 43.00, '/php_exam/uploads/gojo.jpg', 3, '2026-02-19 14:39:41'),
(23, 'Deku Midoriya', 'My Hero Academia édition héro.', 36.80, '/php_exam/uploads/deku.jpg', 3, '2026-02-19 14:39:41'),
(24, 'Kratos - God of War', 'Figurine God of War Ragnarok.', 50.00, '/php_exam/uploads/kratos.jpg', 3, '2026-02-19 14:39:41'),
(25, 'Geralt of Rivia', 'The Witcher édition collector.', 46.00, '/php_exam/uploads/geroltofrivia.jpg', 3, '2026-02-19 14:39:41'),
(26, 'Link - Zelda', 'The Legend of Zelda premium.', 37.50, '/php_exam/uploads/link-zelda.jpg', 3, '2026-02-19 14:39:41'),
(27, 'Master Chief - Halo', 'Figurine Halo série spéciale.', 41.00, '/php_exam/uploads/masterchief.jpg', 3, '2026-02-19 14:39:41'),
(28, 'Spider-Man Marvel', 'Marvel Legends série.', 45.00, '/php_exam/uploads/spiderman.jpg', 3, '2026-02-19 14:39:41'),
(29, 'Batman Dark Knight', 'DC Comics édition spéciale.', 48.00, '/php_exam/uploads/batman.jpg', 3, '2026-02-19 14:39:41'),
(30, 'Iron Man Mark 50', 'Avengers Infinity War.', 52.00, '/php_exam/uploads/ironman.jpg', 3, '2026-02-19 14:39:41'),
(31, 'The Mandalorian', 'Star Wars collection premium.', 47.00, '/php_exam/uploads/mandalorian.jpg', 3, '2026-02-19 14:39:41');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart`
--

CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `invoice`
--

CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `billing_address` varchar(255) NOT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_postal_code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `invoice`
--

INSERT INTO `invoice` (`id`, `user_id`, `total_amount`, `billing_address`, `billing_city`, `billing_postal_code`, `created_at`) VALUES
(1, 3, 37.50, 'Adresse démo', 'Paris', '75000', '2026-02-19 15:13:16'),
(2, 3, 78.00, 'Adresse démo', 'Paris', '75000', '2026-02-19 15:16:08'),
(3, 3, 43.00, 'Adresse démo', 'Paris', '75000', '2026-02-19 16:59:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `stock`
--

INSERT INTO `stock` (`id`, `article_id`, `quantity`) VALUES
(2, 31, 10),
(3, 30, 10),
(4, 29, 10),
(5, 28, 10),
(6, 27, 10),
(7, 26, 9),
(8, 25, 10),
(9, 24, 10),
(10, 23, 10),
(11, 22, 9),
(12, 21, 8),
(13, 20, 10),
(14, 19, 10),
(15, 18, 10),
(16, 17, 10);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `balance`, `profile_picture`, `role`, `created_at`) VALUES
(3, 'Yarkin05', 'yarkinoner@hotmail.com', '$2y$10$wFAhMRHqrzVYUIMcDEp0WuOQCGsTNRuc8.693Y1XCKjBfIr/ZZa/2', 902.50, NULL, 'user', '2026-02-16 18:26:10');

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
