-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 03:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chocolates_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `chocolates`
--

CREATE TABLE `chocolates` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  `expiration_date` date DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `manufacturer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chocolates`
--

INSERT INTO `chocolates` (`id`, `product_name`, `brand`, `category`, `supplier_name`, `quantity`, `price`, `expiration_date`, `date_added`, `manufacturer`) VALUES
(2, 'Diary Milk', 'Cadbury', 'Milk Chocolate', 'Global Food Inc.', 15, 85.00, '2026-05-12', '2026-05-11 00:58:27', 'Modelez Int.'),
(3, 'KITKAT', 'Cadbury', 'White Chocolate', 'Global Food Inc.', 1, 4.00, '2026-05-13', '2026-05-13 02:01:08', 'Modelez Int.'),
(4, 'KITKAT', '', 'Dark Chocolate', 'fsfdsgftr', 15, 85.00, '2030-09-19', '2026-05-18 01:13:42', '');

-- --------------------------------------------------------

--
-- Table structure for table `chocolate_images`
--

CREATE TABLE `chocolate_images` (
  `id` int(11) NOT NULL,
  `chocolate_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chocolate_images`
--

INSERT INTO `chocolate_images` (`id`, `chocolate_id`, `image`) VALUES
(2, 2, '1778461107_6a0129b355879_wp2355285.webp'),
(3, 3, '1778637668_6a03db6494762_Nestlé KitKat.jpg'),
(4, 4, '1779066822_Nestlé KitKat.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chocolates`
--
ALTER TABLE `chocolates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chocolate_images`
--
ALTER TABLE `chocolate_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chocolate_id` (`chocolate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chocolates`
--
ALTER TABLE `chocolates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chocolate_images`
--
ALTER TABLE `chocolate_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chocolate_images`
--
ALTER TABLE `chocolate_images`
  ADD CONSTRAINT `chocolate_images_ibfk_1` FOREIGN KEY (`chocolate_id`) REFERENCES `chocolates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
