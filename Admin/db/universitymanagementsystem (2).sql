-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2025 at 04:30 PM
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
-- Database: `universitymanagementsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `addbook`
--

CREATE TABLE `addbook` (
  `id` int(11) NOT NULL,
  `bookname` varchar(250) NOT NULL,
  `author` varchar(250) NOT NULL,
  `pubYear` year(4) NOT NULL,
  `type` varchar(100) NOT NULL,
  `ebook` varchar(250) NOT NULL,
  `status` varchar(50) NOT NULL,
  `descText` varchar(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addbook`
--

INSERT INTO `addbook` (`id`, `bookname`, `author`, `pubYear`, `type`, `ebook`, `status`, `descText`, `created_at`) VALUES
(1, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', '', '0', '2025-09-07 23:34:06'),
(2, 'Web Technology', 'Purnia', '2019', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', '', '0', '2025-09-07 23:35:31'),
(5, 'Web Technology', 'Purnia', '2019', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', '', '0', '2025-09-07 23:39:25'),
(6, 'Web Technology', 'Purnia', '2019', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', '', '0', '2025-09-07 23:42:41'),
(7, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'dfg', '2025-09-09 16:47:27'),
(8, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'b,kjh', '2025-09-09 16:47:44'),
(9, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'b,kjh', '2025-09-09 16:55:47'),
(10, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'b,kjh', '2025-09-09 16:57:28'),
(11, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'b,kjh', '2025-09-09 16:58:36'),
(12, 'kajdhf', 'DF', '2003', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'b,kjh', '2025-09-09 17:02:08'),
(13, 'Introduction to Data Base', 'Aeysha', '2015', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'Best book', '2025-09-09 17:03:00'),
(14, 'Introduction to Data Base', 'Aeysha', '2015', 'ebook', 'uploads/PHP Zero to Hero Full Cheat Sheet.pdf', 'Available', 'Best book', '2025-09-09 17:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `add_drop_deadline`
--

CREATE TABLE `add_drop_deadline` (
  `id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` varchar(200) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_drop_deadline`
--

INSERT INTO `add_drop_deadline` (`id`, `department`, `course`, `start_date`, `end_date`, `status`) VALUES
(1, 'cse201', 'cse201', '2025-09-12 13:14:00', '2025-09-25 13:14:00', ''),
(6, 'cse101', 'cse301', '2025-09-18 13:20:00', '2025-09-25 13:20:00', ''),
(7, 'cse101', 'cse301', '2025-09-05 13:22:00', '2025-09-11 13:22:00', ''),
(9, 'cse301', 'cse301', '2025-09-10 13:25:00', '2025-09-24 13:25:00', ''),
(10, 'cse301', 'cse301', '2025-09-10 13:25:00', '2025-09-24 13:25:00', ''),
(11, 'cse301', 'cse301', '2025-09-10 13:25:00', '2025-09-12 08:30:00', ''),
(12, 'cse301', 'cse301', '2025-09-17 13:26:00', '2025-09-16 13:26:00', ''),
(13, 'cse301', 'cse301', '2025-09-17 13:26:00', '2025-09-16 13:26:00', 'Expired'),
(14, 'EEE', 'EEE501', '2025-09-17 13:33:00', '2025-09-17 13:33:00', ''),
(15, 'Math', 'EEE501', '2025-09-17 13:33:00', '2025-09-17 13:33:00', 'Expired'),
(17, 'CSE', 'CSE301', '2025-09-11 19:48:00', '2025-09-12 09:48:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent','late') DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `bookid` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date NOT NULL,
  `return_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`id`, `bookid`, `student_id`, `borrow_date`, `due_date`, `return_date`) VALUES
(1, 2, 16, '2025-09-11 18:00:00', '2025-09-26', '2025-09-11 18:00:00'),
(2, 5, 16, '2025-09-11 18:00:00', '2025-09-26', '2025-09-11 18:00:00'),
(3, 2, 16, '2025-09-11 18:00:00', '2025-09-26', '2025-09-11 18:00:00'),
(4, 5, 16, '2025-09-11 18:00:00', '2025-09-26', '2025-09-11 18:00:00'),
(5, 2, 16, '2025-09-11 18:00:00', '2025-09-26', '2025-09-11 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `consulting_hours`
--

CREATE TABLE `consulting_hours` (
  `consult_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `day_of_week` enum('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(20) DEFAULT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `credit` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_deadlines`
--

CREATE TABLE `course_deadlines` (
  `deadline_id` int(11) NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `drop_deadline` date DEFAULT NULL,
  `add_deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_registrations`
--

CREATE TABLE `course_registrations` (
  `reg_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `status` enum('registered','dropped','completed') DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

CREATE TABLE `library` (
  `book_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `available_copies` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `library_issues`
--

CREATE TABLE `library_issues` (
  `issue_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('issued','returned') DEFAULT 'issued'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offered_course`
--

CREATE TABLE `offered_course` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `course_title` varchar(250) NOT NULL,
  `student_capacity` int(11) NOT NULL,
  `student_count` int(11) NOT NULL DEFAULT 0,
  `class_time` time NOT NULL,
  `class_date` varchar(100) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `course_fee` int(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offered_course`
--

INSERT INTO `offered_course` (`id`, `department`, `course_title`, `student_capacity`, `student_count`, `class_time`, `class_date`, `duration`, `course_fee`) VALUES
(1, 'CSE', 'CSE101', 30, 0, '08:00:00', '2025-09-14', '1', 0),
(3, 'CSE', 'Database Systems', 30, 0, '08:00:00', '0000-00-00', '2', 0),
(4, 'BBA', 'Business', 50, 0, '10:00:00', 'Monday', '1', 0),
(5, 'CSE', 'Compiler Design', 30, 0, '08:00:00', 'Wednesday', '3', 0),
(6, 'Architecture ', 'Introduction to Architecture', 50, 0, '04:00:00', 'Tuesday', '1', 10000),
(8, 'EEE', 'Device', 40, 0, '10:00:00', 'Monday', '2', 5000),
(11, 'EEE', 'Device', 60, 0, '08:00:00', 'Thursday', '2', 14000),
(12, 'CSE', 'Microprocessor', 30, 0, '12:00:00', 'Wednesday', '2', 20000),
(13, 'EEE', 'Microprocessor', 50, 0, '02:00:00', 'Thursday, Monday', '2', 5000),
(14, 'BBA', 'Business', 50, 0, '04:00:00', 'Tuesday', '2', 20500);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` enum('bkash','nagad','rocket','bank') DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `practiceregister`
--

CREATE TABLE `practiceregister` (
  `userID` int(50) NOT NULL,
  `UserName` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_history`
--

CREATE TABLE `salary_history` (
  `salary_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `method` enum('bank_transfer','cash') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_number` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `admission_year` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `student_number`, `department`, `program`, `semester`, `admission_year`) VALUES
(2, 4, 'STU4', 'Unknown', 'Unknown', 1, '2025');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_name` varchar(100) DEFAULT NULL,
  `setting_value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `teacher_number` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `teacher_number`, `department`, `designation`, `hire_date`, `salary`) VALUES
(1, 4, 'TCH4', 'Unknown', 'Lecturer', '2025-09-10', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`id`, `title`, `created_at`) VALUES
(1, 'Course updated: ', '2025-09-12 18:07:08'),
(2, 'Course updated: ', '2025-09-12 18:15:31'),
(3, 'Course updated: ', '2025-09-12 18:15:31'),
(4, 'Course updated: ', '2025-09-12 18:15:32'),
(5, 'Course updated: ', '2025-09-12 18:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('not set','student','teacher') DEFAULT 'not set',
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'disabled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `dob`, `contact_number`, `email`, `gender`, `department`, `password`, `role`, `status`, `created_at`) VALUES
(4, 'aaax', 'abbbx', '0000-00-00', '01771685693', 'ahmexxdjubayertamim@gmai.com', 'Male', '', '$2y$10$pp790PSsjQzcxQ0HbMPWc.3QRTha6436XnMGHUYo18Pep5Rmb1flq', 'student', 'enabled', '2025-09-09 13:28:49'),
(5, 'Aeysha', 'Akter', '2025-10-08', '01771685693', 'aa@gmail.com', 'Male', '', '$2y$10$ZEHhf2rGE/wG.M.kzs4cVenQUBKd2AN8flYSbQ4IsVYYRATwrsIXy', 'teacher', 'enabled', '2025-09-09 14:56:24'),
(8, 'Joy', 'Sarker', '2025-09-05', '01789032456', 'aad@gamil.com', 'Male', '', '$2y$10$2pm.hcRysnTQzhUttDkvferJk/2Gogc9WPZY1XZ/TjlQQQSKBSO1S', 'student', 'disabled', '2025-09-10 18:14:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addbook`
--
ALTER TABLE `addbook`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `add_drop_deadline`
--
ALTER TABLE `add_drop_deadline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookid` (`bookid`);

--
-- Indexes for table `consulting_hours`
--
ALTER TABLE `consulting_hours`
  ADD PRIMARY KEY (`consult_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `course_deadlines`
--
ALTER TABLE `course_deadlines`
  ADD PRIMARY KEY (`deadline_id`);

--
-- Indexes for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD PRIMARY KEY (`reg_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indexes for table `library_issues`
--
ALTER TABLE `library_issues`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `offered_course`
--
ALTER TABLE `offered_course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `practiceregister`
--
ALTER TABLE `practiceregister`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contact_number` (`contact_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `student_number` (`student_number`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `teacher_number` (`teacher_number`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
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
-- AUTO_INCREMENT for table `addbook`
--
ALTER TABLE `addbook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `add_drop_deadline`
--
ALTER TABLE `add_drop_deadline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consulting_hours`
--
ALTER TABLE `consulting_hours`
  MODIFY `consult_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_deadlines`
--
ALTER TABLE `course_deadlines`
  MODIFY `deadline_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_registrations`
--
ALTER TABLE `course_registrations`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library`
--
ALTER TABLE `library`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library_issues`
--
ALTER TABLE `library_issues`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offered_course`
--
ALTER TABLE `offered_course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `practiceregister`
--
ALTER TABLE `practiceregister`
  MODIFY `userID` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_history`
--
ALTER TABLE `salary_history`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `consulting_hours`
--
ALTER TABLE `consulting_hours`
  ADD CONSTRAINT `consulting_hours_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_registrations`
--
ALTER TABLE `course_registrations`
  ADD CONSTRAINT `course_registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_registrations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `library_issues`
--
ALTER TABLE `library_issues`
  ADD CONSTRAINT `library_issues_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `library_issues_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `library` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `salary_history`
--
ALTER TABLE `salary_history`
  ADD CONSTRAINT `salary_history_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
