-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 03:06 PM
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
-- Database: `disability_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `app_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_status` enum('pending_admin','eligible','hired','rejected_by_admin','rejected_by_company') DEFAULT 'pending_admin',
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`app_id`, `job_id`, `user_id`, `app_status`, `applied_date`) VALUES
(904, 509, 205, 'hired', '2026-02-08 04:16:13'),
(908, 509, 216, 'eligible', '2026-02-25 07:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `job_description` text NOT NULL,
  `required_skills` text NOT NULL,
  `target_disability_type` enum('locomotor','visual','hearing','speech','any') NOT NULL,
  `job_status` enum('pending','open','closure_requested','closed') DEFAULT 'pending',
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `company_id`, `job_title`, `job_description`, `required_skills`, `target_disability_type`, `job_status`, `posted_date`) VALUES
(509, 208, 'locomotive job', 'this is a job vacancy for a person with locomotive disability', 'php, UI, Table Tennis', 'locomotor', 'open', '2026-02-08 04:13:44'),
(512, 208, 'manil job', 'this is for manjil', 'html,css', 'speech', 'open', '2026-02-08 08:37:21'),
(513, 208, 'jenish', 'this job is for jenish', 'html,css', 'hearing', 'open', '2026-02-08 08:48:51'),
(514, 208, 'jeevan', 'this job is for jeevan', 'database, nodejs', 'locomotor', 'open', '2026-02-25 08:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','company','beneficiary') NOT NULL,
  `account_status` enum('pending','active','rejected') DEFAULT 'pending',
  `disability_type` enum('locomotor','visual','hearing','speech','none') DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `disability_card_url` varchar(255) DEFAULT NULL,
  `company_location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`, `account_status`, `disability_type`, `skills`, `disability_card_url`, `company_location`, `created_at`) VALUES
(1, 'Super Admin', 'admin@admin.com', 'admin123', 'admin', 'active', NULL, NULL, NULL, NULL, '2026-02-07 16:04:14'),
(205, 'swikrit Neupane', 'neupaneswikrit@gmail.com', '123', 'beneficiary', 'active', 'locomotor', 'php, UI, Table Tennis', 'uploads/1770523740_BCA_Scripting_Language_Full_Marks_Answers_Q9_Q10_Q11.pdf', NULL, '2026-02-08 04:09:00'),
(206, 'Arpit Karn', 'arpitkarn@gmail.com', '123', 'beneficiary', 'pending', 'visual', 'database, nodejs', 'uploads/1770523779_BCA_Scripting_Language_Full_Marks_Answers_Q9_Q10_Q11.pdf', NULL, '2026-02-08 04:09:39'),
(207, 'aib group', 'aib@company.com', '123', 'company', 'pending', NULL, NULL, NULL, 'biratnagar', '2026-02-08 04:11:11'),
(208, 'Swikrit & co.', 'sw@gmail.com', '123', 'company', 'active', NULL, NULL, NULL, 'kathmandu', '2026-02-08 04:12:12'),
(214, 'kushal niraula', 'kushal@gmail.com', '123', 'beneficiary', 'active', 'locomotor', 'html,css', 'uploads/1770545899_image.png.jpg', NULL, '2026-02-08 10:18:19'),
(215, 'prashant', 'prashant@gmail.com', '123', 'beneficiary', 'active', 'hearing', 'html,css', 'uploads/1771911765_NM lab 5 output.png', NULL, '2026-02-24 05:42:45'),
(216, 'jeevan niraula', 'jeevan@giamil.com', '123', 'beneficiary', 'active', 'locomotor', 'database, nodejs', 'uploads/1772005805_NM lab 5 output.png', NULL, '2026-02-25 07:50:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=909;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=515;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
