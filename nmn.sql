-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2025 at 07:37 AM
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
-- Database: `smart_ticketing`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Music', 'Concerts, festivals, and music performances', '2025-09-20 16:55:31'),
(2, 'Technology', 'Tech conferences, workshops, and hackathons', '2025-09-20 16:55:31'),
(3, 'Sports', 'Sporting events and competitions', '2025-09-20 16:55:31'),
(4, 'Arts', 'Theater, exhibitions, and cultural events', '2025-09-20 16:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `venue` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `event_image` varchar(255) DEFAULT NULL,
  `total_tickets` int(11) NOT NULL,
  `tickets_sold` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `category_id`, `title`, `description`, `venue`, `date_time`, `event_image`, `total_tickets`, `tickets_sold`, `price`, `status`, `created_at`) VALUES
(1, 2, 1, 'Summer Music Festival', 'Annual music festival featuring popular artists', 'City Park Amphitheater', '2025-07-15 14:00:00', 'public/uploads/event_images/event_68cf207919ac25.97597834.jpg', 500, 0, 75.00, 'approved', '2025-09-20 16:55:31'),
(2, 2, 2, 'Tech Innovation Summit', 'Conference on the latest technology trends', 'Convention Center Hall A', '2025-08-20 09:00:00', 'public/uploads/event_images/event_68cf206100d673.22028767.jpg', 300, 0, 120.00, 'approved', '2025-09-20 16:55:31'),
(3, 2, 4, 'Modern Art Exhibition', 'Exhibition of contemporary art pieces', 'Downtown Art Gallery', '2025-06-10 10:00:00', 'public/uploads/event_images/event_68cf208f607b53.73410404.jpg', 200, 0, 25.00, 'approved', '2025-09-20 16:55:31'),
(4, 2, 2, 'Laracast', 'Where you earn all about php and laravel', 'ZITF Hall 3', '2025-10-04 10:00:00', 'public/uploads/event_images/event_68cf0641cc58c4.15547991.jpg', 100, 2, 10.00, 'approved', '2025-09-20 19:53:37'),
(5, 6, 1, 'Garden Of House', 'come chill mix and mingle', 'ZITF Arena', '2025-10-11 15:00:00', 'public/uploads/event_images/event_68d05dfb888286.67301318.jpg', 500, 1, 5.00, 'approved', '2025-09-21 20:20:11');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `qr_code_hash` varchar(255) NOT NULL,
  `is_scanned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `event_id`, `user_id`, `transaction_id`, `qr_code_hash`, `is_scanned`, `created_at`) VALUES
(4, 1, 3, 1, 'qrcode_1_3_', 0, '2025-09-20 16:56:02'),
(5, 2, 4, 2, 'qrcode_2_4_', 1, '2025-09-20 16:58:53'),
(6, 3, 3, 3, 'qrcode_3_3_', 0, '2025-09-20 16:59:33'),
(7, 4, 3, 4, 'TICKET__d67e9c9454b61848', 0, '2025-09-21 09:19:21'),
(8, 4, 3, 5, 'TICKET_8_bafaa168cd5ca955', 0, '2025-09-21 19:36:14'),
(9, 5, 5, 6, 'TICKET_9_f8efc59430995f5c', 1, '2025-09-21 20:21:32');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_scans`
--

CREATE TABLE `ticket_scans` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `scanned_by` int(11) NOT NULL,
  `scan_result` enum('valid','invalid','duplicate') NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_scans`
--

INSERT INTO `ticket_scans` (`id`, `ticket_id`, `scanned_by`, `scan_result`, `scanned_at`) VALUES
(5, 4, 2, 'valid', '2025-09-20 17:00:57'),
(6, 6, 2, 'valid', '2025-09-20 17:01:25'),
(7, 5, 2, 'valid', '2025-09-20 18:55:00'),
(8, 9, 6, 'valid', '2025-09-21 20:29:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `amount`, `status`, `transaction_date`, `created_at`) VALUES
(1, 3, 75.00, 'completed', '2025-09-20 16:55:31', '2025-09-20 16:55:31'),
(2, 4, 120.00, 'completed', '2025-09-20 16:55:31', '2025-09-20 16:55:31'),
(3, 3, 25.00, 'completed', '2025-09-20 16:55:31', '2025-09-20 16:55:31'),
(4, 3, 10.00, 'completed', '2025-09-21 09:19:21', '2025-09-21 09:19:21'),
(5, 3, 10.00, 'completed', '2025-09-21 19:36:14', '2025-09-21 19:36:14'),
(6, 5, 5.00, 'completed', '2025-09-21 20:21:32', '2025-09-21 20:21:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','organizer','attendee') DEFAULT 'attendee',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','suspended','pending') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `first_name`, `last_name`, `profile_picture`, `created_at`, `status`) VALUES
(1, 'admin', 'admin@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator', NULL, '2025-09-20 16:55:31', 'active'),
(2, 'org1', 'org1@events.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'organizer', 'John', 'EventManager', NULL, '2025-09-20 16:55:31', 'active'),
(3, 'user1', 'user1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'attendee', 'Alice', 'Attendee', NULL, '2025-09-20 16:55:31', 'active'),
(4, 'user2', 'user2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'attendee', 'Bob', 'Smith', NULL, '2025-09-20 16:55:31', 'active'),
(5, 'Shumba', 'brianshumba9@gmail.com', '$2y$10$RsTHxA3XYtRq1sF1kKsTWuAHetmr/vMR9WFhFiIxylIq5Ia4cJJUS', 'attendee', 'Brian', 'Shumba', 'public/uploads/profile_pics/profile_68d05cb4612b30.85423469.png', '2025-09-21 20:14:44', 'active'),
(6, 'organizer', 'organizer@gmail.com', '$2y$10$XMD782R/A5NMLb3ksLBgWuem6v7uBLx.oHCo8j9gh6xNI.LODZ8ZS', 'organizer', 'organizer', 'organizer', 'public/uploads/profile_pics/profile_68d05d3d90a904.34869562.jpg', '2025-09-21 20:17:01', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_code_hash` (`qr_code_hash`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `ticket_scans`
--
ALTER TABLE `ticket_scans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `scanned_by` (`scanned_by`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ticket_scans`
--
ALTER TABLE `ticket_scans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_scans`
--
ALTER TABLE `ticket_scans`
  ADD CONSTRAINT `ticket_scans_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_scans_ibfk_2` FOREIGN KEY (`scanned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
