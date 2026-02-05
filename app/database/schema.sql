-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 19, 2013 at 11:22 PM
-- Server version: 5.5.25a
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `php_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `city` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_idx` (`city`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `users`
--

INSERT INTO users (name, email, city, phone, created_at)
SELECT
  CONCAT('User', n),
  CONCAT('user', n, '@test.com'),
  ELT(1 + (n % 6), 'Paris','London','Berlin','Rome','Madrid','Prague'),
  CONCAT('+33', LPAD(n, 9, '0')),
  TIMESTAMP('2013-01-01') + INTERVAL (n % 365) DAY
FROM (
  SELECT @n := @n + 1 AS n
  FROM information_schema.tables, (SELECT @n := 0) t
  LIMIT 10000
) x;

INSERT INTO `users` (`name`, `email`, `city`, `phone`, `created_at`) VALUES
('Andrej', 'andrej@mail.test', 'Glasgow', NULL, '2013-09-19 22:20:19'),
('Juraj', 'juraj@mail.test', 'Praha', NULL, '2013-09-19 22:20:34'),
('Jožko', 'jozko@mail.test', 'Bratislava', NULL, '2013-09-19 22:21:04'),
('Peter', 'peter@mail.test', 'Brno', NULL, '2013-09-19 22:21:17'),
('Jon', 'jon@mail.test', 'New York', NULL, '2013-09-19 22:21:41');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
