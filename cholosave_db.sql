-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 14, 2025 at 11:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cholosave_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expert_team`
--

CREATE TABLE `expert_team` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `expertise` varchar(255) NOT NULL,
  `bio` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_membership`
--

CREATE TABLE `group_membership` (
  `membership_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending',
  `is_admin` tinyint(1) DEFAULT 0,
  `leave_request` enum('pending','approved','declined','no') DEFAULT 'no',
  `join_date` date DEFAULT NULL,
  `join_request_date` date DEFAULT NULL,
  `time_period_remaining` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_membership`
--

INSERT INTO `group_membership` (`membership_id`, `group_id`, `user_id`, `status`, `is_admin`, `leave_request`, `join_date`, `join_request_date`, `time_period_remaining`) VALUES
(1, 1, 1, 'approved', 1, 'no', '2025-01-12', NULL, 2),
(2, 2, 7, 'approved', 1, 'no', '2025-01-12', NULL, NULL),
(3, 1, 7, 'approved', 0, 'no', '2025-01-12', '2025-01-12', 9),
(4, 3, 8, 'approved', 1, 'no', '2025-01-13', NULL, 24),
(5, 1, 9, 'pending', 0, 'no', NULL, '2025-01-13', NULL),
(6, 1, 8, 'pending', 0, 'no', '2025-01-13', '2025-01-13', 12),
(7, 1, 10, 'declined', 0, 'approved', NULL, NULL, NULL),
(8, 2, 10, 'pending', 0, 'no', NULL, '2025-01-13', NULL),
(9, 3, 10, 'pending', 0, 'no', NULL, '2025-01-13', NULL),
(10, 2, 1, 'pending', 0, 'no', NULL, '2025-01-14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `group_points`
--

CREATE TABLE `group_points` (
  `group_id` int(11) NOT NULL,
  `points` int(11) DEFAULT 10,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `investment_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `investment_type` varchar(255) DEFAULT NULL,
  `ex_profit` double DEFAULT NULL,
  `ex_return_date` date DEFAULT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `group_id`, `amount`, `investment_type`, `ex_profit`, `ex_return_date`, `status`, `created_at`) VALUES
(1, 1, 100.00, 'House', 150, '0000-00-00', 'completed', '2025-01-12 22:30:37'),
(3, 1, 335.00, 'Test', 1313, '2025-01-20', 'pending', '2025-01-12 22:35:58');

-- --------------------------------------------------------

--
-- Table structure for table `investment_returns`
--

CREATE TABLE `investment_returns` (
  `return_id` int(11) NOT NULL,
  `investment_id` int(11) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investment_returns`
--

INSERT INTO `investment_returns` (`return_id`, `investment_id`, `amount`, `return_date`, `description`) VALUES
(3, 1, 670.00, '2025-01-12 22:50:51', 'ABCD');

-- --------------------------------------------------------

--
-- Table structure for table `loan_repayments`
--

CREATE TABLE `loan_repayments` (
  `repayment_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_request`
--

CREATE TABLE `loan_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `status` enum('pending','approved','declined','repaid') DEFAULT 'pending',
  `return_time` date DEFAULT NULL,
  `approve_date` date DEFAULT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `repayment_date` datetime DEFAULT NULL,
  `repayment_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('bkash','Rocket','Nagad') DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_request`
--

INSERT INTO `loan_request` (`id`, `user_id`, `group_id`, `reason`, `amount`, `status`, `return_time`, `approve_date`, `request_time`, `repayment_date`, `repayment_amount`, `payment_method`, `transaction_id`, `payment_time`) VALUES
(1, 7, 1, 'Testing ABC', 100.00, 'repaid', '2025-01-15', '2025-01-12', '2025-01-12 17:14:43', '2025-01-13 00:00:00', 100.00, 'bkash', 'CHS93c56AVE', '2025-01-13 01:16:16'),
(2, 7, 1, 'gfaeuiabf', 500.00, 'repaid', '2025-01-30', '2025-01-13', '2025-01-12 19:16:49', '2025-01-13 00:00:00', 500.00, 'Nagad', 'CHS73bc5AVE', '2025-01-13 21:55:11'),
(3, 10, 1, 'Need Urgent', 100.00, 'approved', '2025-01-20', '2025-01-13', '2025-01-12 21:03:32', NULL, 0.00, NULL, NULL, NULL),
(4, 1, 1, 'qwefwe', 200.00, 'repaid', '2025-01-21', '2025-01-13', '2025-01-12 21:26:24', '2025-01-13 00:00:00', 200.00, 'Rocket', 'CHS6D052AVE', '2025-01-13 22:09:14');

-- --------------------------------------------------------

--
-- Table structure for table `my_group`
--

CREATE TABLE `my_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `members` int(11) NOT NULL,
  `group_admin_id` int(11) DEFAULT NULL,
  `dps_type` enum('weekly','monthly') DEFAULT NULL,
  `time_period` int(11) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `goal_amount` int(11) DEFAULT NULL,
  `warning_time` int(11) DEFAULT NULL,
  `emergency_fund` decimal(8,2) DEFAULT NULL,
  `bKash` varchar(255) DEFAULT NULL,
  `Rocket` varchar(255) DEFAULT NULL,
  `Nagad` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `my_group`
--

INSERT INTO `my_group` (`group_id`, `group_name`, `members`, `group_admin_id`, `dps_type`, `time_period`, `amount`, `start_date`, `goal_amount`, `warning_time`, `emergency_fund`, `bKash`, `Rocket`, `Nagad`, `created_at`, `description`) VALUES
(1, 'CHOLOSAVE', 10, 1, 'monthly', 12, 500.00, '2025-01-15', 50000, 2, 5000.00, '01634132218', '01634132218', '0157589393', '2025-01-12 17:03:23', 'This a group where you can save money with transparent'),
(2, 'Irfan&#39;s Group', 10, 7, 'weekly', 52, 200.00, '2025-01-25', 80000, 3, 8000.00, '1648248681', '01648248681', '01648248681', '2025-01-12 17:12:56', 'Testing Purpose'),
(3, 'Test Time', 10, 8, 'monthly', 24, 700.00, '2025-01-20', 70000, 3, 7000.00, '1648248681', '01648248681', '01648248681', '2025-01-12 19:50:37', 'ofjwfnwnwsrowsrkmgkwerpm');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `poll_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `poll_question` text DEFAULT NULL,
  `status` enum('active','closed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`poll_id`, `group_id`, `poll_question`, `status`, `created_at`) VALUES
(2, 1, 'Irfan has requested a loan of BDT 500. Do you approve ?', 'active', '2025-01-12 17:14:43'),
(3, 1, 'Irfanuzzaman Montasir wants to join the group. Do you approve?', 'active', '2025-01-12 20:47:48'),
(4, 1, 'Drake wants to join the group. Do you approve?', 'active', '2025-01-12 20:48:08'),
(5, 1, 'Drake X wants to join the group. Do you approve?', 'active', '2025-01-12 20:48:24'),
(6, 2, 'Drake X wants to join the group. Do you approve?', 'active', '2025-01-12 20:48:25'),
(15, 2, 'Montasir wants to join the group. Do you approve?', 'active', '2025-01-13 19:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `polls_vote`
--

CREATE TABLE `polls_vote` (
  `vote_id` int(11) NOT NULL,
  `poll_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vote_option` enum('yes','no') NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls_vote`
--

INSERT INTO `polls_vote` (`vote_id`, `poll_id`, `user_id`, `vote_option`, `voted_at`) VALUES
(2, 4, 1, 'no', '2025-01-12 21:15:19'),
(3, 2, 1, 'yes', '2025-01-13 11:38:41');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `reply_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings`
--

CREATE TABLE `savings` (
  `savings_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savings`
--

INSERT INTO `savings` (`savings_id`, `user_id`, `group_id`, `amount`, `created_at`) VALUES
(1, 7, 1, 500.00, '2025-01-12 17:15:09'),
(2, 10, 1, 500.00, '2025-01-12 20:56:49'),
(3, 1, 1, 500.00, '2025-01-12 22:04:04'),
(4, 1, 1, 500.00, '2025-01-12 22:07:48'),
(5, 1, 1, 500.00, '2025-01-13 05:27:48'),
(6, 1, 1, 500.00, '2025-01-13 12:40:30'),
(7, 1, 1, 500.00, '2025-01-13 12:44:22'),
(8, 1, 1, 500.00, '2025-01-13 12:52:54'),
(9, 1, 1, 500.00, '2025-01-13 15:46:45'),
(10, 1, 1, 500.00, '2025-01-13 17:28:43'),
(11, 1, 1, 500.00, '2025-01-13 17:29:23'),
(12, 1, 1, 500.00, '2025-01-13 17:30:55'),
(16, 7, 1, 500.00, '2025-01-13 17:52:08'),
(17, 7, 1, 500.00, '2025-01-13 17:53:12'),
(18, 7, 1, 500.00, '2025-01-13 17:53:55');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_info`
--

CREATE TABLE `transaction_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `payment_method` enum('bKash','Rocket','Nagad') NOT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_info`
--

INSERT INTO `transaction_info` (`id`, `user_id`, `group_id`, `amount`, `transaction_id`, `payment_method`, `payment_time`) VALUES
(1, 7, 1, 500.00, 'CHS8D486AVE', 'bKash', '2025-01-12 17:15:09'),
(2, 10, 1, 500.00, 'CHS47928AVE', 'bKash', '2025-01-12 20:56:49'),
(3, 1, 1, 500.00, 'CHSC0205AVE', 'Nagad', '2025-01-12 22:04:04'),
(4, 1, 1, 500.00, 'CHS24987AVE', 'Rocket', '2025-01-12 22:07:48'),
(5, 1, 1, 500.00, 'CHS5A843AVE', 'bKash', '2025-01-13 05:27:48'),
(6, 1, 1, 500.00, 'CHS10d57AVE', 'bKash', '2025-01-13 12:40:30'),
(7, 1, 1, 500.00, 'CHS1D951AVE', 'Rocket', '2025-01-13 12:44:22'),
(8, 1, 1, 500.00, 'CHSC3526AVE', 'Nagad', '2025-01-13 12:52:54'),
(9, 1, 1, 500.00, 'CHSFC455AVE', 'bKash', '2025-01-13 15:46:45'),
(10, 1, 1, 500.00, 'CHSAA7f4AVE', 'bKash', '2025-01-13 17:28:43'),
(11, 1, 1, 500.00, 'CHSDF748AVE', 'Rocket', '2025-01-13 17:29:23'),
(12, 1, 1, 500.00, 'CHS7E6f9AVE', 'Nagad', '2025-01-13 17:30:55'),
(16, 7, 1, 500.00, 'CHS9F455AVE', 'Nagad', '2025-01-13 17:52:08'),
(17, 7, 1, 500.00, 'CHS58c37AVE', 'Rocket', '2025-01-13 17:53:12'),
(18, 7, 1, 500.00, 'CHSF6671AVE', 'Nagad', '2025-01-13 17:53:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','group_admin','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone_number`, `password`, `role`, `created_at`) VALUES
(1, 'Montasir', 'a@gmail.com', '01634132218', '$2y$10$1fimRzkCanoGqIez105o7OcxIPMesm0TBfSUkAlHa1YFors5vI/2.', 'user', '2025-01-12 15:49:27'),
(7, 'Irfan', 'b@gmail.com', '01634132218', '$2y$10$Cdm9VGGGKKT7PGuFIpB.n.9Kf/HI29.XCOCz./qSMKTf94lcbf1km', 'user', '2025-01-12 17:06:19'),
(8, 'Drake', 'd@gmail.com', '0192378487', '$2y$10$wUqlf.tkSJtptXN22eun6OtkMdIJLC4u0/zad0yR/xss7lzYtz1t.', 'user', '2025-01-12 19:49:36'),
(9, 'Irfanuzzaman Montasir', 'c@gmail.com', '01634132218', '$2y$10$92UlcshqFr5UcGAHPk/uiuGxwCkwXIgN7/AYrO/y9X9ft9GuGSU8W', 'user', '2025-01-12 20:42:43'),
(10, 'Drake X', 'e@gmail.com', '0192378487', '$2y$10$.VYwQsI9qlBP42CFIYQoIuTLe44HSUa6B5k9DaaKPJc6LlEOtShdC', 'user', '2025-01-12 20:46:42'),
(11, 'Fat', 'f@gmail.com', '0192378487', '$2y$10$x9HaOkw.HWVmUGIRiAYX.OijWRq2sPyC4cAd3PxbcVZRsWDXZVOL2', 'user', '2025-01-12 20:47:01'),
(12, 'George', 'g@gmail.com', '01634132218', '$2y$10$XtlAbodK4bSuuKLRK64i0eWWUHjNt3x2ridb53ULcuyoEG4QmUEne', 'user', '2025-01-12 20:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal`
--

CREATE TABLE `withdrawal` (
  `withdrawal_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `payment_number` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `approve_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawal`
--

INSERT INTO `withdrawal` (`withdrawal_id`, `user_id`, `group_id`, `amount`, `payment_number`, `payment_method`, `status`, `request_date`, `approve_date`) VALUES
(1, 7, 1, 500.00, '01634132217', 'Bkash', 'pending', '2025-01-13 06:35:35', NULL),
(2, 1, 1, 200.00, '01601245256', 'Nagad', 'pending', '2025-01-13 06:40:03', NULL),
(3, 7, 1, 100.00, '01634132217', 'Nagad', 'pending', '2025-01-13 15:56:49', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expert_team`
--
ALTER TABLE `expert_team`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `group_membership`
--
ALTER TABLE `group_membership`
  ADD PRIMARY KEY (`membership_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_points`
--
ALTER TABLE `group_points`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`investment_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `investment_returns`
--
ALTER TABLE `investment_returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `investment_id` (`investment_id`);

--
-- Indexes for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD PRIMARY KEY (`repayment_id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `loan_request`
--
ALTER TABLE `loan_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `my_group`
--
ALTER TABLE `my_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `group_admin_id` (`group_admin_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`poll_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `polls_vote`
--
ALTER TABLE `polls_vote`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `unique_vote` (`poll_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `savings`
--
ALTER TABLE `savings`
  ADD PRIMARY KEY (`savings_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `transaction_info`
--
ALTER TABLE `transaction_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawal`
--
ALTER TABLE `withdrawal`
  ADD PRIMARY KEY (`withdrawal_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expert_team`
--
ALTER TABLE `expert_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_membership`
--
ALTER TABLE `group_membership`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `investment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `investment_returns`
--
ALTER TABLE `investment_returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  MODIFY `repayment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_request`
--
ALTER TABLE `loan_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `my_group`
--
ALTER TABLE `my_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `poll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `polls_vote`
--
ALTER TABLE `polls_vote`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `savings`
--
ALTER TABLE `savings`
  MODIFY `savings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transaction_info`
--
ALTER TABLE `transaction_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `withdrawal`
--
ALTER TABLE `withdrawal`
  MODIFY `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `group_membership`
--
ALTER TABLE `group_membership`
  ADD CONSTRAINT `group_membership_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`),
  ADD CONSTRAINT `group_membership_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `group_points`
--
ALTER TABLE `group_points`
  ADD CONSTRAINT `group_points_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);

--
-- Constraints for table `investments`
--
ALTER TABLE `investments`
  ADD CONSTRAINT `investments_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);

--
-- Constraints for table `investment_returns`
--
ALTER TABLE `investment_returns`
  ADD CONSTRAINT `investment_returns_ibfk_1` FOREIGN KEY (`investment_id`) REFERENCES `investments` (`investment_id`);

--
-- Constraints for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD CONSTRAINT `loan_repayments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loan_request` (`id`);

--
-- Constraints for table `loan_request`
--
ALTER TABLE `loan_request`
  ADD CONSTRAINT `loan_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `loan_request_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);

--
-- Constraints for table `my_group`
--
ALTER TABLE `my_group`
  ADD CONSTRAINT `my_group_ibfk_1` FOREIGN KEY (`group_admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);

--
-- Constraints for table `polls_vote`
--
ALTER TABLE `polls_vote`
  ADD CONSTRAINT `polls_vote_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`),
  ADD CONSTRAINT `polls_vote_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `savings`
--
ALTER TABLE `savings`
  ADD CONSTRAINT `savings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `savings_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);

--
-- Constraints for table `transaction_info`
--
ALTER TABLE `transaction_info`
  ADD CONSTRAINT `transaction_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transaction_info_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);

--
-- Constraints for table `withdrawal`
--
ALTER TABLE `withdrawal`
  ADD CONSTRAINT `withdrawal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `withdrawal_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `my_group` (`group_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
