-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 16, 2025 at 03:16 AM
-- Server version: 10.6.21-MariaDB-cll-lve
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `analysis`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `access_token` varchar(100) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` text DEFAULT NULL,
  `account_active` smallint(6) NOT NULL DEFAULT 1,
  `role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `phone_number`, `password`, `access_token`, `otp`, `otp_expiry`, `account_active`, `role`) VALUES
(3, 'Victor', 'irunguvictor80@gmail.com', '0708660880', '$2y$10$0UFWju8Pce.dW1tDQxURu..UYIifV5ietKgjJ6Nx.vXlbyRF3BAfm', '8a3af05f3bf3455de24d6ec7712a430712a91a824a6fab8613bf10d4053d200c', NULL, NULL, 1, 1),
(9, 'Stephanie', 'stephaniewwachira@gmail.com', '0712197945', '$2y$10$bj3haAOEt.gwy83gBlo2Xe45kCA.qukX9sHxcYbJ.hxymgmtuVJty', '6c8da55cf84b8603356a41b9988ed01f', NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `file_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `created_at`, `file_name`) VALUES
(1, '2025-01-30 18:50:53', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/exported_data_1738288253.json'),
(2, '2025-01-30 18:54:04', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/exported_data_1738288444.json'),
(3, '2025-01-30 22:38:52', 'uploads/expenseData_1738301932.json'),
(4, '2025-01-31 03:39:00', 'uploads/expenseData_1738319940.json'),
(5, '2025-01-31 03:45:38', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/exported_data_1738320338.json');

-- --------------------------------------------------------

--
-- Table structure for table `nrevenue`
--

CREATE TABLE `nrevenue` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `file_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `nrevenue`
--

INSERT INTO `nrevenue` (`id`, `created_at`, `file_name`) VALUES
(1, '2025-02-01 00:38:16', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/transaction_data1_1738395496.json');

-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

CREATE TABLE `revenue` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `file_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `revenue`
--

INSERT INTO `revenue` (`id`, `created_at`, `file_name`) VALUES
(1, '0000-00-00 00:00:00', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/transaction_data_1738389612.json'),
(2, '0000-00-00 00:00:00', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/transaction_data_1738390000.json'),
(3, '2025-02-01 00:55:35', '/home/xta8f0w7cfw0/analysis.helahub.co/uploads/transaction_data_1738396535.json');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `accessibility` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `accessibility`) VALUES
(1, 'Super Admin', '{\"super_admin\":true}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_ties` (`role`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nrevenue`
--
ALTER TABLE `nrevenue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `revenue`
--
ALTER TABLE `revenue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nrevenue`
--
ALTER TABLE `nrevenue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `revenue`
--
ALTER TABLE `revenue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
