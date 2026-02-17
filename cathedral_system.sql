-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 12, 2026 at 02:22 PM
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
-- Database: `cathedral_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `affected_table` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chapels`
--

CREATE TABLE `chapels` (
  `chapel_id` int(11) NOT NULL,
  `chapel_name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapels`
--

INSERT INTO `chapels` (`chapel_id`, `chapel_name`, `location`) VALUES
(1, 'Cathedral of St. Michael Archangel', NULL),
(2, 'San Miguel Arcangel Tabing Ilog', NULL),
(3, 'San Miguel Arcangel Habay Chapel', NULL),
(4, 'Chapel of the Risen Lord Piñahan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `chapel_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `donor_name` varchar(100) DEFAULT 'Anonymous',
  `date_received` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `event_type` enum('Mass','Wedding','Baptism','Funeral') DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `chapel_location` varchar(50) DEFAULT NULL,
  `pastor_assigned` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Confirmed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `event_type`, `start_time`, `chapel_location`, `pastor_assigned`, `status`) VALUES
(1, 'Sunday Mass', 'Mass', '2026-02-15 09:00:00', 'Main Cathedral', 'Fr. John Doe', '');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `recipient_name` varchar(100) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `message_type` enum('Reminder','Confirmation','Announcement') DEFAULT NULL,
  `status` enum('Pending','Sent','Failed') DEFAULT 'Pending',
  `sent_at` datetime DEFAULT NULL,
  `chapel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sacramental_records`
--

CREATE TABLE `sacramental_records` (
  `record_id` int(11) NOT NULL,
  `chapel_id` int(11) DEFAULT NULL,
  `record_type` enum('Baptism','Wedding','Funeral') NOT NULL,
  `person_name` varchar(150) NOT NULL,
  `event_date` date NOT NULL,
  `book_number` varchar(20) DEFAULT NULL,
  `page_number` varchar(20) DEFAULT NULL,
  `encoded_by` int(11) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `chapel_id` int(11) DEFAULT NULL,
  `pastor_id` int(11) DEFAULT NULL,
  `event_title` varchar(100) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `chapel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user`, `password`, `role`, `chapel_id`) VALUES
(1, 'admin', '$2y$10$vrI2j6z82FQSU7Jkgea/yevKJiXC7o1/T/21XHoCCSffSRi1jxrkO', 'Super Admin', NULL),
(2, 'staff', '$2y$10$Sdqr3YokLU/HTv4Y59Aq9.U.2.BTw4PCUq93mSUqXqBHQwOloQy.W', 'Staff', NULL),
(3, 'ninja', '$2y$10$5zdKG9JIH4f9CAWmphoFS.ckQpjz1v9JFFz1.zAwq00EaGZ7JfLnG', 'Pastor', NULL),
(4, 'jon snow', '$2y$10$ajDV935YXAYowTwcyzcA7OsdmpzYc7M5InQANG6Dlg5UmGGB6OZyS', 'Cathedral Admin', NULL),
(5, 'george', '$2y$10$ZhSGYPOlnUn8sNDFgWaCP.vLlZef1B.aM.Kfss9y6gVId1qghK90.', 'Chapel Admin', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chapels`
--
ALTER TABLE `chapels`
  ADD PRIMARY KEY (`chapel_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `chapel_id` (`chapel_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `chapel_id` (`chapel_id`);

--
-- Indexes for table `sacramental_records`
--
ALTER TABLE `sacramental_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `chapel_id` (`chapel_id`),
  ADD KEY `encoded_by` (`encoded_by`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `chapel_id` (`chapel_id`),
  ADD KEY `pastor_id` (`pastor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_chapel` (`chapel_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chapels`
--
ALTER TABLE `chapels`
  MODIFY `chapel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sacramental_records`
--
ALTER TABLE `sacramental_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`chapel_id`) REFERENCES `chapels` (`chapel_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`chapel_id`) REFERENCES `chapels` (`chapel_id`);

--
-- Constraints for table `sacramental_records`
--
ALTER TABLE `sacramental_records`
  ADD CONSTRAINT `sacramental_records_ibfk_1` FOREIGN KEY (`chapel_id`) REFERENCES `chapels` (`chapel_id`),
  ADD CONSTRAINT `sacramental_records_ibfk_2` FOREIGN KEY (`encoded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`chapel_id`) REFERENCES `chapels` (`chapel_id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`pastor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_chapel` FOREIGN KEY (`chapel_id`) REFERENCES `chapels` (`chapel_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
