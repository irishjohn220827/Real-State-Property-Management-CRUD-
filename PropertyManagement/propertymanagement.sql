-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2025 at 04:53 AM
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
-- Database: `propertymanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `leases`
--

CREATE TABLE `leases` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leases`
--

INSERT INTO `leases` (`id`, `property_id`, `tenant_id`, `start_date`, `end_date`, `monthly_rent`, `created_at`) VALUES
(102, 32, 13, '2025-08-19', '2025-09-16', 300.00, '2025-08-18 16:13:49');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `rent` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `address`, `type`, `bedrooms`, `bathrooms`, `rent`, `status`, `created_at`) VALUES
(10, 'Paco Roman Street', 'house', 2, 1, 3000.00, 'available', '2025-08-17 10:16:35'),
(11, 'Maragol Fighter 1st', 'house', 3, 2, 5000.00, 'available', '2025-08-17 11:46:14'),
(13, 'Poblacion North', 'house', 3, 2, 4000.00, 'available', '2025-08-17 11:47:27'),
(14, 'CLSU', 'apartment', 1, 1, 500.00, 'available', '2025-08-17 11:47:45'),
(15, 'SJC', 'apartment', 1, 1, 5000.00, 'available', '2025-08-17 11:48:17'),
(30, 'Baloc, Talavera', 'condo', 2, 1, 10000.00, 'available', '2025-08-18 15:52:31'),
(32, 'bembang, Munoz(Second flr)', 'condo', 1, 1, 300.00, 'rented', '2025-08-18 16:12:46'),
(33, 'Catalanacan purok uno', 'house', 3, 1, 4000.00, 'available', '2025-08-19 02:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `repair_requests`
--

CREATE TABLE `repair_requests` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `issue_description` text NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `request_date` datetime NOT NULL,
  `completion_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_requests`
--

INSERT INTO `repair_requests` (`id`, `property_id`, `tenant_id`, `issue_description`, `status`, `request_date`, `completion_date`, `notes`, `created_at`) VALUES
(37, 14, 9, 'k', 'in_progress', '2025-08-17 18:23:02', NULL, '', '2025-08-17 16:23:02'),
(38, 14, 9, 'k', 'pending', '2025-08-17 18:23:02', NULL, NULL, '2025-08-17 16:23:02'),
(39, 14, 9, 'k', 'pending', '2025-08-17 18:23:02', NULL, NULL, '2025-08-17 16:23:02'),
(40, 10, 13, 'lag', 'completed', '2025-08-17 18:31:26', NULL, '', '2025-08-17 16:31:26'),
(41, 15, 10, 'fg', 'in_progress', '2025-08-17 18:43:17', NULL, 'qsdasd', '2025-08-17 16:43:17'),
(42, 15, 10, 'asdf', 'pending', '2025-08-17 18:52:29', NULL, NULL, '2025-08-17 16:52:29'),
(43, 10, 8, '123', 'completed', '2025-08-17 18:58:53', NULL, NULL, '2025-08-17 16:58:53'),
(44, 10, 8, '123', 'pending', '2025-08-17 19:20:05', NULL, NULL, '2025-08-17 17:20:05'),
(45, 15, 8, 'awd', 'completed', '2025-08-17 19:32:32', NULL, '', '2025-08-17 17:32:32'),
(46, 15, 12, 'asdfassda', 'pending', '2025-08-18 10:08:11', NULL, NULL, '2025-08-18 08:08:11'),
(47, 15, 13, 'n', 'completed', '2025-08-18 10:37:27', NULL, '', '2025-08-18 08:37:27'),
(48, 15, 8, '2312', 'in_progress', '2025-08-18 17:33:11', NULL, '', '2025-08-18 15:33:11'),
(49, 13, 17, 'fdgsg', 'in_progress', '2025-08-18 17:49:01', NULL, '', '2025-08-18 15:49:01'),
(50, 30, 33, 'Door is Broken', 'in_progress', '2025-08-18 17:54:14', NULL, '', '2025-08-18 15:54:14'),
(51, 11, 9, 'qwdq', 'completed', '2025-08-18 17:57:01', NULL, '', '2025-08-18 15:57:01'),
(52, 32, 13, 'asd', 'completed', '2025-08-18 18:14:20', NULL, '', '2025-08-18 16:14:20'),
(53, 33, 36, 'skaofd', 'pending', '2025-08-19 04:42:49', NULL, '', '2025-08-19 02:42:49');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `name`, `email`, `phone`, `created_at`) VALUES
(8, 'irish john Jacinto', 'john@gmail.com', '09486036516', '2025-08-17 10:17:03'),
(9, 'Luxa Mae Colo Ocgaria', 'luxa@gmail.com', '09486036516', '2025-08-17 11:49:07'),
(10, 'irish john', 'john@gmail.com', '09486036516', '2025-08-17 11:49:19'),
(11, 'dave Pararuan', 'dave@gmail.com', '09486036516', '2025-08-17 11:49:26'),
(12, 'John Doe', 'johndoe@gmail.com', '09486036516', '2025-08-17 11:50:04'),
(13, 'Mark Leo', 'leo@gmail.com', '09486036516', '2025-08-17 11:50:30'),
(17, 'irish john', 'john@gmail.com', '09486036516', '2025-08-17 15:00:15'),
(33, 'Mary Grace C. Jacinto', 'jacintomg077@gmail.com', '09486036516', '2025-08-18 15:52:52'),
(36, 'mark baliguat', 'mark@gamil.com', '09486036516', '2025-08-19 02:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$6SoVfqVsr5/NrD68BNYRP.xfrx2.z0kEmwasFhsErVi5.9swhEAu2', '2025-08-18 14:52:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leases`
--
ALTER TABLE `leases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leases`
--
ALTER TABLE `leases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `repair_requests`
--
ALTER TABLE `repair_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leases`
--
ALTER TABLE `leases`
  ADD CONSTRAINT `leases_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `leases_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`);

--
-- Constraints for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD CONSTRAINT `repair_requests_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `repair_requests_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
