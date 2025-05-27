-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 11:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `time_in`, `time_out`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-05-27', '13:14:20', '13:20:59', '2025-05-27 05:14:20', '2025-05-27 05:20:59'),
(2, 5, '2025-05-27', '15:36:03', '17:20:47', '2025-05-27 07:36:03', '2025-05-27 09:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `incident_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `incident_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Resolved') DEFAULT 'Pending',
  `resolution_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `user_id`, `incident_type`, `description`, `incident_date`, `status`, `resolution_date`, `created_at`, `updated_at`) VALUES
(1, 4, 'Power Outage', 'It happened around 2pm this afternoon and until now the power is not back yet.', '2025-05-27 15:25:09', 'Resolved', '2025-05-27 15:32:14', '2025-05-27 07:25:09', '2025-05-27 07:32:14'),
(2, 4, 'Important Document Corrupted', 'I checked a very important document and it wasn\'t opening. I found out it was corrupted.', '2025-05-27 15:35:03', 'Resolved', '2025-05-27 17:18:59', '2025-05-27 07:35:03', '2025-05-27 09:18:59'),
(3, 5, 'Automated Email Spam', 'All employees suddenly received a company-wide email every 10 minutes with the subject line “Everything’s fine 🙂.”', '2025-05-27 15:40:41', 'Resolved', '2025-05-27 17:18:50', '2025-05-27 07:40:41', '2025-05-27 09:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','employee') NOT NULL DEFAULT 'employee'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `role`) VALUES
(3, 'Meryl Grace', 'Luntian', '20221298@nbsc.edu.ph', '$2y$10$LVYgYhGjYMfkPw7DkcfqsO6EUhkG0ZYRGvhgeDXMdwRjISfCWgjo2', '2025-05-26 13:58:07', 'admin'),
(4, 'Grace', 'Benigno', 'luntian429.21@gmail.com', '$2y$10$zNKoGuAG2hrrosyoG0k8Ius4YwWgnXehqpSfIqr0TAoyGBTxCjoNK', '2025-05-27 05:11:27', 'employee'),
(5, 'Star', 'Queens', 'starryyoungments@gmail.com', '$2y$10$hhBHW2g.SEZ36NxhqxHHoOI49.Gp3wt0dWyzl6JemRf2zpB1QQ68m', '2025-05-27 07:35:46', 'employee'),
(6, 'Audrey Abigail', 'Hisanza', 'audrey@gmail.com', '$2y$10$toH6YZW2IOiiJkI0WymCuu6/DyIIyp7Reh93/IPvg5jxHHtHkECiS', '2025-05-27 09:20:00', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
