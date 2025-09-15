-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 03:41 AM
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
-- Database: `loan_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_panel`
--

CREATE TABLE `admin_panel` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin1','admin2','admin','staff','manager','auditor','accountant','relationship_officer') NOT NULL DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_panel`
--

INSERT INTO `admin_panel` (`id`, `username`, `email`, `phone`, `password`, `created_at`, `role`) VALUES
(1, 'Emmanuel', 'emmanuel@gmail.com', '08061299131', '1234', '2025-06-24 12:51:10', 'staff'),
(2, 'triumph', 'triumph@gmail.com', '08061299131', '1234', '2025-06-24 12:52:10', 'manager'),
(3, 'theophilus', 'theophilus@gmail.com', '08061299131', '1234', '2025-06-24 12:53:26', 'auditor'),
(4, 'testy', 'testy@gmail.com', '08061299131', '1234', '2025-06-24 12:54:32', 'accountant'),
(5, 'bose', 'bose@gmail.com', '08061299131', '1234', '2025-06-24 12:55:46', 'admin2'),
(6, 'theodorah', 'theodorah@gmail.com', '08061299131', '1234', '2025-06-24 12:57:06', 'admin1'),
(7, 'admin', 'admin@gmail.com', '08061299131', '1234', '2025-06-28 09:12:45', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `approver_id` int(11) NOT NULL,
  `level` enum('relationship_officer','accountant','manager') DEFAULT NULL,
  `status` enum('reviewed','checked','approved','rejected') DEFAULT 'reviewed',
  `comment` text DEFAULT NULL,
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_type` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `member_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `due_dates`
--

CREATE TABLE `due_dates` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `amount_due` decimal(12,2) DEFAULT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_deposits`
--

CREATE TABLE `fixed_deposits` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount_deposited` decimal(12,2) NOT NULL,
  `tenure_months` int(11) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `start_date` date DEFAULT curdate(),
  `status` enum('pending','admin1_confirmed','admin2_confirmed','approved','rejected','matured','withdrawn') DEFAULT 'pending',
  `certificate_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `maturity_date` date DEFAULT NULL,
  `action_taken` enum('none','withdraw','rollover') DEFAULT 'none',
  `matured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `loan_type` varchar(100) DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT NULL,
  `tenure_months` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `id_doc` varchar(255) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `payslip` varchar(255) DEFAULT NULL,
  `guarantor` varchar(255) DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT 5.00,
  `status` enum('submitted','reviewed','checked','approved','rejected','disbursed') DEFAULT 'submitted',
  `reviewed_by` int(11) DEFAULT NULL,
  `forwarded_to_admin_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `member_id`, `action`, `ip_address`, `user_agent`, `created_at`, `admin_id`) VALUES
(3, 5, 'Applied for loan', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-28 02:11:23', NULL),
(4, 3, 'Auditor Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-28 07:21:02', NULL),
(5, 3, 'Auditor Login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-28 07:21:29', NULL),
(6, 4, 'Auditor login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-28 07:56:42', NULL),
(7, 4, 'Auditor login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-28 08:34:23', NULL),
(8, 4, 'Auditor login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', '2025-06-28 08:36:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `other_names` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `state_of_origin` varchar(100) DEFAULT NULL,
  `lga_of_origin` varchar(100) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `residential_address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_type` enum('Current','Savings','Other') DEFAULT NULL,
  `id_document` varchar(255) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `place_of_work` varchar(255) DEFAULT NULL,
  `type_of_business_work` varchar(255) DEFAULT NULL,
  `monthly_earning` enum('100k_below','100k_200k','200k_250k','250k_300k','300k_350k','400k_above') DEFAULT NULL,
  `annual_income` enum('below_500k','500k_1m','1m_2m','2m_3m','3m_4m','4m_5m','above_5m') DEFAULT NULL,
  `expected_monthly_contribution_amount` decimal(12,2) DEFAULT NULL,
  `fixed_deposit_amount` decimal(12,2) DEFAULT 0.00,
  `fixed_deposit_years` int(11) DEFAULT 0,
  `contribution_start_date` date DEFAULT NULL,
  `full_name_of_next_of_kin` varchar(255) DEFAULT NULL,
  `address_of_next_of_kin` text DEFAULT NULL,
  `phone_number_of_next_of_kin` varchar(20) DEFAULT NULL,
  `relationship_with_next_of_kin` varchar(100) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_verified` tinyint(1) DEFAULT 0,
  `role` enum('member','staff','relationship_officer','accountant','manager','admin','auditor') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `status` enum('pending','approved','suspended') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `surname`, `first_name`, `other_names`, `username`, `password`, `state_of_origin`, `lga_of_origin`, `permanent_address`, `residential_address`, `phone_number`, `email_address`, `bank_name`, `account_number`, `account_name`, `account_type`, `id_document`, `passport`, `place_of_work`, `type_of_business_work`, `monthly_earning`, `annual_income`, `expected_monthly_contribution_amount`, `fixed_deposit_amount`, `fixed_deposit_years`, `contribution_start_date`, `full_name_of_next_of_kin`, `address_of_next_of_kin`, `phone_number_of_next_of_kin`, `relationship_with_next_of_kin`, `payment_reference`, `payment_verified`, `role`, `created_at`, `updated_at`, `otp_code`, `otp_expires`, `status`) VALUES
(1, 'Emmanuel', 'Triumph', 'opisa', 'evangelist', '1234', 'kogi', 'okehi', 'GRA Ajegunle', 'GRA Ajegunle', '09065497334', 'triumph@gmail.com', 'Zenith bank', '8061299131', 'Emmanuel Triumph', 'Savings', '', '', 'abuja', 'student', '100k_below', 'below_500k', 50.00, 0.00, 0, '2025-06-16', 'ALAO Emmanuel James', 'GRA Ajegunle', '08061299131', 'parent', '', 0, 'staff', '2025-06-19 10:31:11', '2025-06-21 11:38:37', NULL, NULL, 'pending'),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'member', '2025-06-19 20:22:31', '2025-06-19 20:22:31', NULL, NULL, 'pending'),
(3, 'ALAO', 'Emmanuel', '', 'emmy', '$2y$10$Ev3F.KHgYtB8vjKENqeEkOM9EJlljIfRZhS20PgeahTheD2CgvMB.', 'FCT', 'OKEHI', 'GRA  behind Catholic church', 'Ajegunle Mpape', '08061299131', 'alaoemmanuel1978@gmail.com', 'GUARANTY TRUST BANK', '0048226227', 'ALAO JAMES EMMANUEL ', 'Savings', 'bg1.jpeg', 'homepage.jpeg', 'Freelancer', 'Online Money', '', 'below_500k', 150.00, 0.00, 0, '2025-04-05', 'Emmanuel James ALAO', 'GRA  behind Catholic church\r\nAjegunle Mpape', '07065497334', 'friend', 'pay_685477b930f9c', 0, 'member', '2025-06-19 20:48:57', '2025-06-27 04:20:59', '839022', '2025-06-19 22:58:57', 'approved'),
(7, 'ALAO', 'Emmanuel', '', 'ben', '$2y$10$8XkkfpPF.JZZMGr9oNCjzO6xsCHdshJIln18K9zcT8rlo4vmJzrve', 'FCT', 'OKEHI', 'GRA  behind Catholic church', 'Ajegunle Mpape', '08061299131', 'alaoemmanuel1978@gmail.com', 'GUARANTY TRUST BANK', '0048226227', 'ALAO JAMES EMMANUEL ', 'Savings', 'bg2.jpeg', 'bg1.jpeg', 'Freelancer', 'Online Money', '', 'below_500k', 150.00, 0.00, 0, '2025-04-05', 'Emmanuel James ALAO', 'GRA  behind Catholic church\r\nAjegunle Mpape', '07065497334', 'friend', 'pay_685478290a12d', 0, 'member', '2025-06-19 20:50:49', '2025-06-27 04:20:40', '566573', '2025-06-19 23:00:49', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender`, `receiver`, `message`, `timestamp`) VALUES
(1, 'admin', 'member123', 'Emmanuel', '2025-06-30 00:53:09'),
(2, 'admin', 'member123', 'Good morning', '2025-06-30 00:54:58'),
(3, 'admin', 'member123', 'hi', '2025-06-30 01:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_savings`
--

CREATE TABLE `monthly_savings` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `auto_amount` decimal(10,2) DEFAULT NULL,
  `type` enum('auto','manual') DEFAULT 'auto',
  `month` year(4) DEFAULT NULL,
  `contribution_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `type` enum('repayment','savings') NOT NULL,
  `payment_reference` varchar(100) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `acknowledged_by_admin1` tinyint(1) DEFAULT 0,
  `acknowledged_at` datetime DEFAULT NULL,
  `confirmed_by_admin2` tinyint(1) DEFAULT 0,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repayments`
--

CREATE TABLE `repayments` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings`
--

CREATE TABLE `savings` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` enum('auto','manual') DEFAULT 'auto',
  `remarks` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings_transactions`
--

CREATE TABLE `savings_transactions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_type` enum('monthly','topup','bonus') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_by` int(11) DEFAULT NULL,
  `processed_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_panel`
--
ALTER TABLE `admin_panel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender`),
  ADD KEY `receiver_id` (`receiver`);

--
-- Indexes for table `due_dates`
--
ALTER TABLE `due_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fixed_deposits`
--
ALTER TABLE `fixed_deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `forwarded_to_admin_by` (`forwarded_to_admin_by`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_logs_member` (`member_id`),
  ADD KEY `fk_logs_admin` (`admin_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_savings`
--
ALTER TABLE `monthly_savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_reference` (`payment_reference`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `repayments`
--
ALTER TABLE `repayments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `savings`
--
ALTER TABLE `savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_panel`
--
ALTER TABLE `admin_panel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `due_dates`
--
ALTER TABLE `due_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fixed_deposits`
--
ALTER TABLE `fixed_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `monthly_savings`
--
ALTER TABLE `monthly_savings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repayments`
--
ALTER TABLE `repayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `savings`
--
ALTER TABLE `savings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `members` (`id`);

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`),
  ADD CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver`) REFERENCES `members` (`id`);

--
-- Constraints for table `due_dates`
--
ALTER TABLE `due_dates`
  ADD CONSTRAINT `due_dates_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`);

--
-- Constraints for table `fixed_deposits`
--
ALTER TABLE `fixed_deposits`
  ADD CONSTRAINT `fixed_deposits_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`forwarded_to_admin_by`) REFERENCES `members` (`id`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin_panel` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `monthly_savings`
--
ALTER TABLE `monthly_savings`
  ADD CONSTRAINT `monthly_savings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `repayments`
--
ALTER TABLE `repayments`
  ADD CONSTRAINT `repayments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`);

--
-- Constraints for table `savings`
--
ALTER TABLE `savings`
  ADD CONSTRAINT `savings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  ADD CONSTRAINT `savings_transactions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `withdrawals_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
