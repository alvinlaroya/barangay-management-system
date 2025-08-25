-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 04:16 PM
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
-- Database: `barangay_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangay_officials`
--

CREATE TABLE `barangay_officials` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `term_start` date DEFAULT NULL,
  `term_end` date DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('official','admin') DEFAULT 'official'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_officials`
--

INSERT INTO `barangay_officials` (`id`, `full_name`, `position`, `term_start`, `term_end`, `contact`, `status`, `username`, `password`, `role`) VALUES
(7, 'SK', 'SK Chairman', '2023-10-20', '2026-11-20', '00000000000', 'Active', NULL, NULL, 'official');

-- --------------------------------------------------------

--
-- Table structure for table `blotters`
--

CREATE TABLE `blotters` (
  `id` int(11) NOT NULL,
  `complainant` varchar(100) NOT NULL,
  `respondent` varchar(100) DEFAULT NULL,
  `incident_type` varchar(100) DEFAULT NULL,
  `incident_location` text DEFAULT NULL,
  `incident_datetime` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Settled','Dismissed') DEFAULT 'Pending',
  `schedule_datetime` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotters`
--

INSERT INTO `blotters` (`id`, `complainant`, `respondent`, `incident_type`, `incident_location`, `incident_datetime`, `description`, `status`, `schedule_datetime`, `created_at`) VALUES
(1, 'test', 'test', 'test', 'SELECT c.*, CONCAT(r.first_name, \' \', r.middle_name, \' \', r.last_name) AS full_name, r.address', '2025-06-19 04:09:00', 'SELECT c.*, CONCAT(r.first_name, \' \', r.middle_name, \' \', r.last_name) AS full_name, r.address\r\n', 'Pending', NULL, '2025-06-06 09:09:33'),
(2, 'user user', 'test', 'test', 'test', '2025-07-02 21:40:00', 'test', 'Pending', '2025-07-17 22:00:00', '2025-07-02 14:40:56'),
(3, 'Testing testing', 'hfghfd', 'fhfd', 'fhfdc', '2025-08-20 22:00:00', 'gjgfb', 'Pending', '2025-08-21 22:01:00', '2025-08-20 14:00:58');

-- --------------------------------------------------------

--
-- Table structure for table `clearances`
--

CREATE TABLE `clearances` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `issued_date` date DEFAULT curdate(),
  `official_in_charge` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Pending','Ready for Pickup','Claimed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clearances`
--

INSERT INTO `clearances` (`id`, `resident_id`, `purpose`, `issued_date`, `official_in_charge`, `remarks`, `status`) VALUES
(1, 1, 'testing', '2025-06-06', 'testing', 'testing', 'Ready for Pickup'),
(3, NULL, 'testing', '2025-07-02', '', 'testing', 'Pending'),
(4, NULL, 'testing', '2025-07-02', '', 'testing', 'Pending'),
(5, NULL, 'testing', '2025-07-02', '', 'testing', 'Pending'),
(6, 2, 'testing', '2025-07-02', '', 'testing', 'Claimed'),
(7, 2, 'testinggg', '2025-07-02', '', 'testinggg', 'Ready for Pickup'),
(8, 3, 'hgfhfg', '2025-08-20', '', 'fghfg', 'Ready for Pickup');

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `id` int(11) NOT NULL,
  `household_no` varchar(50) DEFAULT NULL,
  `purok` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `head_of_family` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `date_registered` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`id`, `household_no`, `purok`, `address`, `head_of_family`, `contact_number`, `date_registered`) VALUES
(1, '0000', 'testing', 'testing', 'testing', '00000000000', '2025-06-06');

-- --------------------------------------------------------

--
-- Table structure for table `household_members`
--

CREATE TABLE `household_members` (
  `id` int(11) NOT NULL,
  `household_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `relation_to_head` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `household_members`
--

INSERT INTO `household_members` (`id`, `household_id`, `full_name`, `birthdate`, `gender`, `relation_to_head`, `occupation`) VALUES
(1, 1, 'testing', '2025-06-06', 'Male', 'testing', 'testing');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `purok` varchar(50) DEFAULT NULL,
  `voter_status` enum('Yes','No') DEFAULT NULL,
  `is_4ps` enum('Yes','No') DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_registered` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `gender`, `birthdate`, `age`, `civil_status`, `citizenship`, `religion`, `occupation`, `purok`, `voter_status`, `is_4ps`, `contact`, `email`, `address`, `date_registered`) VALUES
(1, 'testing', 'testing', 'testing', '', 'Male', '2025-06-06', 0, '', '', '', 'testing', 'testing', 'Yes', 'Yes', '00000000000', 'testing@gmail.com', 'testing', '2025-06-06 15:55:01'),
(2, 'user', 'user', 'user', '', 'Male', '2003-10-27', 21, 'Single', 'Filipino', 'Catholic', 'N/A', '3', 'Yes', 'No', '00000000000', 'testing@gmail.com', 'Testing', '2025-07-02 21:25:55'),
(3, 'Testing', 'testing', 'testing', '', 'Male', '2003-10-27', 21, 'single', 'filipino', 'catholic', 'none', 'purok 3', 'Yes', 'Yes', '00000000000', 'testing@gmail.com', 'purok 3', '2025-08-20 20:59:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','resident') NOT NULL DEFAULT 'staff',
  `resident_id` int(11) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `resident_id`, `contact`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$EESoQh5H2bS1pkfvKwfi9OMEIvagvu9z9wooio7a5GiepcnjOE.4S', 'admin', NULL, NULL, '2025-07-02 20:40:45'),
(5, 'user user', 'user', '12dea96fec20593566ab75692c9949596833adc9', 'resident', 2, '00000000000', '2025-07-02 21:25:55'),
(6, 'Testing testing', 'testing', '$2y$10$teeB31hk6lNfYyjuRkCKAOkcIwyFO5elxMTdS2a5z4y8iylltB5hy', 'resident', 3, '00000000000', '2025-08-20 20:59:26'),
(13, 'SK', 'sk', '$2y$10$gF.uFTHv4gF2ibWvugCVbehxezB04knHnoQfj7N8VmlYgwNQpcbAO', 'admin', NULL, '00000000000', '2025-08-20 21:53:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay_officials`
--
ALTER TABLE `barangay_officials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `blotters`
--
ALTER TABLE `blotters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clearances`
--
ALTER TABLE `clearances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `household_members`
--
ALTER TABLE `household_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `household_id` (`household_id`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
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
-- AUTO_INCREMENT for table `barangay_officials`
--
ALTER TABLE `barangay_officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `blotters`
--
ALTER TABLE `blotters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clearances`
--
ALTER TABLE `clearances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `household_members`
--
ALTER TABLE `household_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clearances`
--
ALTER TABLE `clearances`
  ADD CONSTRAINT `clearances_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `household_members`
--
ALTER TABLE `household_members`
  ADD CONSTRAINT `household_members_ibfk_1` FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
