-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2026 at 04:01 PM
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
-- Database: `giheke_tss_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comm_attachments`
--

CREATE TABLE `comm_attachments` (
  `id` int(11) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_conversations`
--

CREATE TABLE `comm_conversations` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `type` enum('direct','group','discussion') DEFAULT 'direct',
  `created_by` varchar(50) DEFAULT NULL,
  `created_by_role` enum('student','trainer','admin') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_message_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_discussion_channels`
--

CREATE TABLE `comm_discussion_channels` (
  `id` int(11) NOT NULL,
  `channel_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_by_role` enum('student','trainer','admin') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comm_discussion_channels`
--

INSERT INTO `comm_discussion_channels` (`id`, `channel_name`, `description`, `module_name`, `department`, `level`, `created_by`, `created_by_role`, `is_active`, `created_at`) VALUES
(1, 'General Discussion', 'Open discussion for all students and staff', NULL, NULL, NULL, NULL, NULL, 1, '2026-06-10 12:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `comm_discussion_messages`
--

CREATE TABLE `comm_discussion_messages` (
  `id` bigint(20) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `sender_code` varchar(50) NOT NULL,
  `sender_role` enum('student','trainer','admin') NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_group_projects`
--

CREATE TABLE `comm_group_projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_by_role` enum('student','trainer','admin') DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_messages`
--

CREATE TABLE `comm_messages` (
  `id` bigint(20) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_code` varchar(50) NOT NULL,
  `sender_role` enum('student','trainer','admin') NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `message_type` enum('text','image','file','system') DEFAULT 'text',
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_edited` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_participants`
--

CREATE TABLE `comm_participants` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_code` varchar(50) NOT NULL,
  `user_role` enum('student','trainer','admin') NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_project_members`
--

CREATE TABLE `comm_project_members` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_code` varchar(50) NOT NULL,
  `user_role` enum('student','trainer','admin') NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_project_messages`
--

CREATE TABLE `comm_project_messages` (
  `id` bigint(20) NOT NULL,
  `project_id` int(11) NOT NULL,
  `sender_code` varchar(50) NOT NULL,
  `sender_role` enum('student','trainer','admin') DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comm_reported_messages`
--

CREATE TABLE `comm_reported_messages` (
  `id` int(11) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `reported_by` varchar(50) NOT NULL,
  `reported_by_role` enum('student','trainer','admin') DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','reviewed','dismissed','action_taken') DEFAULT 'pending',
  `reviewed_by` varchar(50) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcomments`
--

CREATE TABLE `tblcomments` (
  `postId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `postingDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcomments`
--

INSERT INTO `tblcomments` (`postId`, `name`, `email`, `comment`, `status`, `postingDate`) VALUES
(1, 'Omar MBONABUCYA', 'mbonoma2006@gmail.com', 'Byari sawa\r\n', '1', '2026-06-10 06:01:11');

-- --------------------------------------------------------

--
-- Table structure for table `tblposts`
--

CREATE TABLE `tblposts` (
  `id` int(11) NOT NULL,
  `PostTitle` longtext DEFAULT NULL,
  `CategoryId` int(11) DEFAULT NULL,
  `PostDetails` longtext DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `Is_Active` int(11) DEFAULT NULL,
  `PostUrl` mediumtext DEFAULT NULL,
  `PostImage` varchar(255) DEFAULT NULL,
  `viewCounter` int(11) DEFAULT NULL,
  `postedBy` varchar(255) DEFAULT NULL,
  `lastUpdatedBy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tblposts`
--

INSERT INTO `tblposts` (`id`, `PostTitle`, `CategoryId`, `PostDetails`, `PostingDate`, `UpdationDate`, `Is_Active`, `PostUrl`, `PostImage`, `viewCounter`, `postedBy`, `lastUpdatedBy`) VALUES
(13, 'Giheke TSS Students Capture Moments of Joy After Sunday Prayer', 3, 'On Sunday, students from Giheke Technical Secondary School gathered in a spirit of gratitude and unity after attending a morning prayer session. The atmosphere was filled with joy, reflection, and a strong sense of community as students took time to appreciate one another and their shared journey in the school.\r\n\r\nAfter the prayer service, many students chose to take group and individual photos within the school premises. These photos served as a lasting remembrance of their time together, capturing smiles, friendships, and the positive energy that defines student life at Giheke TSS.\r\n\r\nThe school environment provided a beautiful backdrop, with students standing proudly in their uniforms, representing discipline, hope, and ambition for the future. Teachers and staff also encouraged the moment, emphasizing the importance of building memories while maintaining strong values of unity and respect.\r\n\r\nSuch moments reflect the school’s commitment not only to academic excellence but also to spiritual growth and community bonding. Sundays at Giheke TSS continue to be a special time where students reconnect, reflect, and strengthen their relationships before starting a new academic week.\r\n\r\nThese photographs will remain a meaningful reminder of student life, friendship, and the shared experiences that shape the Giheke TSS journey.', '2026-06-10 13:33:56', '2026-06-10 13:38:39', 1, 'Giheke-TSS-Students-Capture-Moments-of-Joy-After-Sunday-Prayer', '88884d5027c580f63f7cfb85f3267386.jpg', 3, 'GIHEKE TSS SCHOOL', NULL),
(14, 'Giheke TSS Students Capture Moments of Joy After Sunday Prayer', 3, 'On Sunday, students from Giheke Technical Secondary School gathered in a spirit of gratitude and unity after attending a morning prayer session. The atmosphere was filled with joy, reflection, and a strong sense of community as students took time to appreciate one another and their shared journey in the school.\r\n\r\nAfter the prayer service, many students chose to take group and individual photos within the school premises. These photos served as a lasting remembrance of their time together, capturing smiles, friendships, and the positive energy that defines student life at Giheke TSS.\r\n\r\nThe school environment provided a beautiful backdrop, with students standing proudly in their uniforms, representing discipline, hope, and ambition for the future. Teachers and staff also encouraged the moment, emphasizing the importance of building memories while maintaining strong values of unity and respect.\r\n\r\nSuch moments reflect the school’s commitment not only to academic excellence but also to spiritual growth and community bonding. Sundays at Giheke TSS continue to be a special time where students reconnect, reflect, and strengthen their relationships before starting a new academic week.\r\n\r\nThese photographs will remain a meaningful reminder of student life, friendship, and the shared experiences that shape the Giheke TSS journey.', '2026-06-10 13:41:10', '2026-06-10 13:46:05', 1, 'Giheke-TSS-Students-Capture-Moments-of-Joy-After-Sunday-Prayer', 'fba2fdc1dc8175dcb0af8397c20efffa.jpg', 1, 'GIHEKE TSS SCHOOL', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_activity_logs`
--

CREATE TABLE `tbl_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_type` varchar(50) DEFAULT 'admin',
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_activity_logs`
--

INSERT INTO `tbl_activity_logs` (`id`, `user_id`, `user_type`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'admin', 'add_staff', 'KANYANDEGE Joseph Desire', '::1', '2026-06-08 21:07:05'),
(2, 1, 'admin', 'add_staff', 'KANYANDEGE Joseph Desire', '::1', '2026-06-08 21:09:42'),
(3, 1, 'admin', 'update_settings', 'Updated general site settings', '::1', '2026-06-10 12:11:45'),
(4, 1, 'admin', 'update_settings', 'Updated homepage text content', '::1', '2026-06-10 12:48:26'),
(5, 1, 'admin', 'add_trade', 'Software developmeent', '::1', '2026-06-10 13:07:00'),
(6, 1, 'admin', 'update_trade', 'Software developmeent', '::1', '2026-06-10 13:07:54'),
(7, 1, 'admin', 'add_trade', 'Computer Systems and Architecture', '::1', '2026-06-10 13:09:39'),
(8, 1, 'admin', 'add_trade', 'Computer Systems and Architecture', '::1', '2026-06-10 13:15:12'),
(9, 1, 'admin', 'update_trade', 'Software developmeent', '::1', '2026-06-10 13:15:24'),
(10, 1, 'admin', 'add_trade', 'Network and internet technology', '::1', '2026-06-10 13:16:55'),
(11, 1, 'admin', 'add_trade', 'Electronisc and telecoommunications', '::1', '2026-06-10 13:18:00'),
(12, 1, 'admin', 'add_trade', 'Profesional accounting', '::1', '2026-06-10 13:18:51'),
(13, 1, 'admin', 'add_trade', 'Electical technology', '::1', '2026-06-10 13:19:34'),
(14, 1, 'admin', 'add_trade', 'Building Construction', '::1', '2026-06-10 13:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admins`
--

CREATE TABLE `tbl_admins` (
  `id` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(255) NOT NULL,
  `ImageUrl` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admins`
--

INSERT INTO `tbl_admins` (`id`, `FirstName`, `LastName`, `Email`, `Phone`, `ImageUrl`, `Password`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(1, 'NGENZI', 'OMAR', 'mbonoma2007@gmail.com', '+250 795605472', '078c7fd6cf59d1d3c1ff77c80ab71221.png', 'admin', '9f82f6b36b43e10ba6c3c78f71263c7c', '2024-06-09 16:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement`
--

CREATE TABLE `tbl_announcement` (
  `Announcement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_announcement`
--

INSERT INTO `tbl_announcement` (`Announcement`) VALUES
('The website is under upgrades!👏');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_aply_status`
--

CREATE TABLE `tbl_aply_status` (
  `id` int(11) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_aply_status`
--

INSERT INTO `tbl_aply_status` (`id`, `Status`, `Message`) VALUES
(1, 'approved', 'Students Are Allowed To Apply');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_apply_student`
--

CREATE TABLE `tbl_apply_student` (
  `id` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Contact` varchar(255) NOT NULL,
  `SchoolName` varchar(255) NOT NULL,
  `SchoolReport` varchar(255) NOT NULL,
  `PreviousTrade` varchar(255) NOT NULL,
  `PreviousLevel` varchar(255) NOT NULL,
  `SchoolTrade` varchar(255) NOT NULL,
  `SchoolLevel` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `Message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_apply_student`
--

INSERT INTO `tbl_apply_student` (`id`, `FirstName`, `LastName`, `Email`, `Contact`, `SchoolName`, `SchoolReport`, `PreviousTrade`, `PreviousLevel`, `SchoolTrade`, `SchoolLevel`, `status`, `Message`) VALUES
(1, 'Munezero', 'Christian', 'ckelchristian@gmail.com', '0785120223', 'acadeny scholl', '37f066d38b702c1305e207bf69aea665.pdf', 'Computer System', 'Level 3', 'Software Development', 'Level 4', 'approved', 'htg drt tryh try rty rt yert yt ytr yrt rt ert y'),
(4, 'Munezero', 'Christian', 'ckelchristian@gmail.com', '0785120223', 'GIHEKE TSS', '280a9ac11b2598d35426d471a898ef1f.pdf', 'Software Development', 'Level 4', 'Electronics Services', 'Level 5', 'approved', 'this is testing message this is testing message this is testing message '),
(5, 'Omar', 'MBONABUCYA', 'mbonoma2006@gmail.com', '0795605472', 'MATARE TSS', 'e74273c809e3873e3cfa975b4e30ecef.pdf', 'Computer Networks', 'Level 4', 'Computer Networks', 'Level 4', 'pending', 'This is to test the application form'),
(6, 'Omar', 'MBONABUCYA', 'mbonoma2006@gmail.com', '0795605472', 'MATARE TSS', '388ff744fc22b7ea27d37b43222e0715.pdf', 'Software Development', 'Level 3', 'Software Development', 'Level 4', 'pending', 'This is to test');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_books`
--

CREATE TABLE `tbl_books` (
  `id` int(11) NOT NULL,
  `BookTitle` varchar(255) NOT NULL,
  `BookLevel` varchar(255) NOT NULL,
  `BookDepartment` varchar(255) NOT NULL,
  `BookUrl` varchar(255) NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `featured_image_type` varchar(10) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'Book',
  `level` varchar(50) DEFAULT NULL,
  `department` varchar(500) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT 'pdf',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_books`
--

INSERT INTO `tbl_books` (`id`, `BookTitle`, `BookLevel`, `BookDepartment`, `BookUrl`, `featured_image`, `featured_image_type`, `title`, `category`, `level`, `department`, `file_path`, `file_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Electrical Conduct System Lying', 'Level 3', 'Industrial Electricity', '96e22e825661963858f635bb3e6941ff.pdf', NULL, NULL, 'Electrical Conduct System Lying', 'Book', 'Level 3', 'Industrial Electricity', '96e22e825661963858f635bb3e6941ff.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(2, 'Electrical System Protection', 'Level 3', 'Industrial Electricity', '39c16ef76d1a6b39b066113a1d17468a.pdf', NULL, NULL, 'Electrical System Protection', 'Book', 'Level 3', 'Industrial Electricity', '39c16ef76d1a6b39b066113a1d17468a.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(3, 'Electrical Installation Testing', 'Level 3', 'Industrial Electricity', '9c7b4cdd73b48931a2f4f0f7854118d6.pdf', NULL, NULL, 'Electrical Installation Testing', 'Book', 'Level 3', 'Industrial Electricity', '9c7b4cdd73b48931a2f4f0f7854118d6.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(4, 'Domestic Eletrical Drawing', 'Level 3', 'Industrial Electricity', '675cfb5362a48ea4ccda2b2d756034a2.pdf', NULL, NULL, 'Domestic Eletrical Drawing', 'Book', 'Level 3', 'Industrial Electricity', '675cfb5362a48ea4ccda2b2d756034a2.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(26, 'Electrical Mesurements and Instrumental', 'Level 4', 'Industrial Electricity', '0eb120ab691a790e959ca16f7958e024.pdf', NULL, NULL, 'Electrical Mesurements and Instrumental', 'Book', 'Level 4', 'Industrial Electricity', '0eb120ab691a790e959ca16f7958e024.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(27, 'Electrical Motor and Maintainance', 'Level 4', 'Industrial Electricity', '41a4f5f0b8b4b5be38d84b21aa0ef0ce.pdf', NULL, NULL, 'Electrical Motor and Maintainance', 'Book', 'Level 4', 'Industrial Electricity', '41a4f5f0b8b4b5be38d84b21aa0ef0ce.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(42, 'Electrical Transimission and Distrubition Line', 'Level 5', 'Industrial Electricity', '7ec449974a5ad1e7cbf12b71eec4643e.pdf', NULL, NULL, 'Electrical Transimission and Distrubition Line', 'Book', 'Level 5', 'Industrial Electricity', '7ec449974a5ad1e7cbf12b71eec4643e.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(43, 'Electrical Power  Distrubution System Design', 'Level 5', 'Industrial Electricity', 'c20c63ab720e3eaeb26c3744e1156792.pdf', NULL, NULL, 'Electrical Power  Distrubution System Design', 'Book', 'Level 5', 'Industrial Electricity', 'c20c63ab720e3eaeb26c3744e1156792.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(44, 'Industial Electrical Drawing', 'Level 5', 'Industrial Electricity', '5b088bfa1898f10ac9c45e1855ded188.pdf', NULL, NULL, 'Industial Electrical Drawing', 'Book', 'Level 5', 'Industrial Electricity', '5b088bfa1898f10ac9c45e1855ded188.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(45, 'Substation Installation', 'Level 5', 'Industrial Electricity', '115757b130cd00eefd91b94ba8fb3052.pdf', NULL, NULL, 'Substation Installation', 'Book', 'Level 5', 'Industrial Electricity', '115757b130cd00eefd91b94ba8fb3052.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34'),
(46, 'Camera Maintainance', 'Level 5', 'Industrial Electricity', 'fa4702e3a55a4caad4b6fba52d610bbb.pdf', NULL, NULL, 'Camera Maintainance', 'Book', 'Level 5', 'Industrial Electricity', 'fa4702e3a55a4caad4b6fba52d610bbb.pdf', 'pdf', NULL, '2026-06-10 08:39:34', '2026-06-10 08:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_contact_messages`
--

CREATE TABLE `tbl_contact_messages` (
  `id` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `replied` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_core_values`
--

CREATE TABLE `tbl_core_values` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'bi bi-heart',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_facilities`
--

CREATE TABLE `tbl_facilities` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_features`
--

CREATE TABLE `tbl_features` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'bi bi-star',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallery_post`
--

CREATE TABLE `tbl_gallery_post` (
  `id` int(11) NOT NULL,
  `CategoryNameId` int(11) NOT NULL,
  `ImageUrl` varchar(255) NOT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_gallery_post`
--

INSERT INTO `tbl_gallery_post` (`id`, `CategoryNameId`, `ImageUrl`, `CreationDate`) VALUES
(50, 5, 'cca2b0a4803bb2678bb2039bc5f0adfe.jpg', '2026-06-10 05:53:37'),
(51, 3, '9e7b884474df78973ce5add42f23ff58.jpg', '2026-06-10 05:55:03'),
(52, 1, '3ad77024e8d524f9e0dc5413177c5789jpeg', '2026-06-10 13:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_media`
--

CREATE TABLE `tbl_media` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_navigation`
--

CREATE TABLE `tbl_navigation` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `target` varchar(10) DEFAULT '_self',
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_parent_doc`
--

CREATE TABLE `tbl_parent_doc` (
  `id` int(11) NOT NULL,
  `DocUrl` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_parent_doc`
--

INSERT INTO `tbl_parent_doc` (`id`, `DocUrl`) VALUES
(1, '280a9ac11b2598d35426d471a898ef1f.pdf'),
(3, '1734fce95cf1a533b0f63f489e9fa8cb.pdf'),
(4, '1734fce95cf1a533b0f63f489e9fa8cb.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_question_test`
--

CREATE TABLE `tbl_question_test` (
  `id` int(11) NOT NULL,
  `QuestionLevel` varchar(255) NOT NULL,
  `QuestionDepartment` varchar(255) NOT NULL,
  `ModuleName` varchar(255) NOT NULL,
  `Question` varchar(255) NOT NULL,
  `Option1` varchar(255) NOT NULL,
  `Option2` varchar(255) NOT NULL,
  `Option3` varchar(255) NOT NULL,
  `Option4` varchar(255) NOT NULL,
  `Answer` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_question_test`
--

INSERT INTO `tbl_question_test` (`id`, `QuestionLevel`, `QuestionDepartment`, `ModuleName`, `Question`, `Option1`, `Option2`, `Option3`, `Option4`, `Answer`) VALUES
(1, 'Level 5', 'Software Development', 'Website Development', 'What is does html stands for ?', 'Hyper Text Marked List', 'Hyper Text Markup Language', 'Hyper Transfer Markup Language', 'Hyper Text Markup List', 'Hyper Text Markup Language'),
(2, 'Level 5', 'Software Development', 'Website Development', 'What is Website?', 'It is  computer software that allows us to manipulate and integrated system with the existing softwares', 'It is a group of interrelated subjects with along side to software to install and update systems analysis ', 'It is the concept of software and hardare for impentation of pages', 'It is the collection of webpages that are integrated together on the single domain name', 'It is the collection of webpages that are integrated together on the single domain name'),
(3, 'Level 5', 'Software Development', 'Website Development', 'Which type of programming language used for designing html elements  for the layout and user interfaces', 'html', 'php', 'css', 'py', 'css'),
(4, 'Level 5', 'Software Development', 'Website Development', 'Css is a programming language . state its full words of css.', 'Cascading Style Sheet', 'Computer Simple Sheet', 'Cascading Style Shell', 'Cascading Simple Shell', 'Cascading Style Sheet'),
(5, 'Level 5', 'Software Development', 'Website Development', 'Those are examples of text editors for developing webpages and integrating them with the database expect:', 'notepad', 'visual studio ', 'shell', 'sublime text', 'shell'),
(6, 'Level 3', 'Software Development', 'Deployment', 'what does domain mean', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Option 2'),
(7, 'Level 3', 'Software Development', 'Deployment', 'QUESTION ', 'OPTION 1', 'OPTION 2 ', 'OPTION 3', 'OPTION 4', 'OPTION 1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_quiz_modules`
--

CREATE TABLE `tbl_quiz_modules` (
  `id` int(11) NOT NULL,
  `ModuleName` varchar(255) NOT NULL,
  `ModuleLevel` varchar(255) NOT NULL,
  `ModuleDepartment` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_quiz_modules`
--

INSERT INTO `tbl_quiz_modules` (`id`, `ModuleName`, `ModuleLevel`, `ModuleDepartment`) VALUES
(1, 'Develop Backend Application', 'Level 5', 'Software Development'),
(2, 'Database Development', 'Level 5', 'Software Development'),
(3, 'System Analysis Design', 'Level 5', 'Software Development'),
(4, 'Website Development', 'Level 5', 'Software Development'),
(5, 'Intermediate English', 'Level 5', 'Software Development'),
(6, 'Ikinyarwanda cy \' umwuga', 'Level 5', 'Software Development'),
(7, 'Deployment', 'Level 3', 'Software Development');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_school_category`
--

CREATE TABLE `tbl_school_category` (
  `id` int(11) NOT NULL,
  `CategoryName` varchar(255) NOT NULL,
  `CategoryDescription` varchar(255) NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_school_category`
--

INSERT INTO `tbl_school_category` (`id`, `CategoryName`, `CategoryDescription`, `PostingDate`) VALUES
(1, 'Sporting', 'it deals on physical activities that  make health good and health', '2024-04-08 08:51:11'),
(3, 'Entertainment', 'entertainment supports some musoc production', '2023-12-30 09:11:57'),
(5, 'Education', 'Education Provide skills and knowldge', '2023-12-30 09:16:02'),
(12, 'Praticals', 'Practicals deals on student applications and events', '2024-01-30 08:45:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seo_meta`
--

CREATE TABLE `tbl_seo_meta` (
  `id` int(11) NOT NULL,
  `page_path` varchar(500) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_site_settings`
--

CREATE TABLE `tbl_site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_site_settings`
--

INSERT INTO `tbl_site_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'GIHEKE ', '2026-06-08 20:58:26', '2026-06-10 12:11:45'),
(2, 'site_tagline', 'Technical Secondary School', '2026-06-08 20:58:26', '2026-06-08 20:58:26'),
(3, 'site_email', 'giheketss@gmail.com', '2026-06-08 20:58:26', '2026-06-08 20:58:26'),
(4, 'site_phone', '+250 788 885 418', '2026-06-08 20:58:26', '2026-06-08 20:58:26'),
(5, 'site_address', 'Rusizi District, Giheke Sector', '2026-06-08 20:58:26', '2026-06-08 20:58:26'),
(11, 'social_facebook', 'https://www.linkedin.com/in/omar-mbonabucya-608375380/', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(12, 'social_twitter', 'https://www.linkedin.com/in/omar-mbonabucya-608375380/', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(13, 'social_instagram', 'https://www.linkedin.com/in/omar-mbonabucya-608375380/', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(14, 'social_linkedin', 'https://www.linkedin.com/in/omar-mbonabucya-608375380/', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(15, 'footer_copyright', '© 2026 GIHEKE TSS. All Rights Reserved.', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(16, 'maintenance_mode', '0', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(17, 'site_logo', 'assets/uploads/site_1781093505_c73af30c.webp', '2026-06-10 12:11:45', '2026-06-10 12:11:45'),
(18, 'programs_label', 'Our Programs', '2026-06-10 12:48:26', '2026-06-10 12:48:26'),
(19, 'programs_title', 'Specialized Technical Trades', '2026-06-10 12:48:26', '2026-06-10 12:48:26'),
(20, 'programs_subtitle', 'Develop practical skills, industry knowledge, and professional confidence through our career-focused training programs.', '2026-06-10 12:48:26', '2026-06-10 12:48:26'),
(21, 'programs_duration', '3 Years', '2026-06-10 12:48:26', '2026-06-10 12:48:26');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff_members`
--

CREATE TABLE `tbl_staff_members` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `role` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_staff_members`
--

INSERT INTO `tbl_staff_members` (`id`, `name`, `role`, `phone`, `initials`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'KANYANDEGE Joseph Desire', 'Principal', '+250 788885418', 'HS', 'assets/uploads/staff_1780952825_9b5e5915.jpg', 1, 1, '2026-06-08 21:07:05', '2026-06-08 21:07:05'),
(2, 'KANYANDEGE Joseph Desire', 'Principal', '+250 788885418', 'HS', 'assets/uploads/staff_1780952982_97de5958.jpg', 1, 1, '2026-06-08 21:09:42', '2026-06-08 21:09:42');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_stdaccounts`
--

CREATE TABLE `tbl_stdaccounts` (
  `id` int(11) NOT NULL,
  `StudentCode` varchar(20) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_stdaccounts`
--

INSERT INTO `tbl_stdaccounts` (`id`, `StudentCode`, `FullName`, `Password`, `created_at`) VALUES
(1, '5010001', 'Munezero Christian', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-10 12:38:59'),
(2, '5060002', 'Ruby Burris', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-10 12:38:59'),
(3, '3010003', 'Christian test', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-10 12:38:59'),
(4, '5010004', 'test test', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-10 12:38:59'),
(8, 'SDMS2640N005', 'Daniel IZABAYO', '$2y$10$lEfBKFds8ckhKTJWfUnoZeAyM0cxiNTSPQdHb0aOjha.T9zh/.bmi', '2026-06-10 12:39:53'),
(9, '361003190399', 'Daniel IZABAYO', '$2y$10$/bj17wGTnI8wPM1s8fi25e2cM.1EVhL3NqwIqjY8ipXer73AAYGlG', '2026-06-10 13:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_students`
--

CREATE TABLE `tbl_students` (
  `id` int(11) NOT NULL,
  `StudentCode` varchar(20) NOT NULL,
  `LevelCode` int(11) NOT NULL,
  `DepartmentCode` int(11) NOT NULL,
  `StudentLevel` enum('Level 3','Level 4','Level 5','') NOT NULL,
  `StudentDepartment` varchar(100) NOT NULL,
  `FullName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_students`
--

INSERT INTO `tbl_students` (`id`, `StudentCode`, `LevelCode`, `DepartmentCode`, `StudentLevel`, `StudentDepartment`, `FullName`) VALUES
(9, '361003190399', 40, 300, 'Level 4', 'Electronics & Telecom', 'Daniel IZABAYO');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_marks`
--

CREATE TABLE `tbl_student_marks` (
  `id` int(11) NOT NULL,
  `Student` varchar(20) NOT NULL,
  `ModuleName` varchar(250) NOT NULL,
  `ModuleLevel` varchar(250) NOT NULL,
  `ModuleDepartment` varchar(250) NOT NULL,
  `StudentMarks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_student_marks`
--

INSERT INTO `tbl_student_marks` (`id`, `Student`, `ModuleName`, `ModuleLevel`, `ModuleDepartment`, `StudentMarks`) VALUES
(1, '5010001', 'Website Development', 'Level 5', 'Software Development', 40);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_message`
--

CREATE TABLE `tbl_student_message` (
  `id` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `StudentLevel` varchar(255) NOT NULL,
  `StudentDepartment` varchar(255) NOT NULL,
  `StudentMessage` text NOT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trades`
--

CREATE TABLE `tbl_trades` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT '3 Years',
  `icon` varchar(50) DEFAULT 'bi bi-tools',
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trades`
--

INSERT INTO `tbl_trades` (`id`, `name`, `description`, `duration`, `icon`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Software developmeent', 'Learn to design, develop, and maintain modern software solutions using industry-standard technologies and best practices. This program provides hands-on training in programming, web and mobile application development, database management, software engineering, and system analysis. Students build real-world projects that strengthen problem-solving, creativity, teamwork, and technical skills needed for successful careers in the digital economy.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781096820_89c2ba65.webp', 1, 1, '2026-06-10 13:07:00', '2026-06-10 13:15:24'),
(2, 'Computer Systems and Architecture', 'Computer Systems and Architecture\r\n\r\nLearn how computers work from the inside out, including hardware components, system architecture, operating systems, and basic networking. This program builds practical skills in computer assembly, maintenance, troubleshooting, and system support, preparing students to manage and maintain modern IT systems in real-world environments.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781097312_b6394605.jpg', 2, 1, '2026-06-10 13:15:12', '2026-06-10 13:15:12'),
(3, 'Network and internet technology', 'Network and Internet Technology\r\n\r\nLearn how modern computer networks and the internet are designed, configured, and maintained. This program focuses on networking fundamentals, data communication, network setup, troubleshooting, and internet technologies. Students gain practical skills to install, manage, and secure network systems used in schools, businesses, and service providers.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781097415_b1f03cd8.webp', 3, 1, '2026-06-10 13:16:55', '2026-06-10 13:16:55'),
(4, 'Electronisc and telecoommunications', 'Electronics and Telecommunications\r\n\r\nLearn the principles of electronic circuits and communication systems used in modern technology. This program develops practical skills in circuit design, electronic components, signal transmission, telecommunications systems, and equipment maintenance. Students gain hands-on experience building, testing, and troubleshooting electronic and communication devices used in real-world applications.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781097480_e2ed3899.jpg', 4, 1, '2026-06-10 13:18:00', '2026-06-10 13:18:00'),
(5, 'Profesional accounting', 'Professional Accounting\r\n\r\nLearn the fundamentals and practical applications of accounting in business and finance. This program builds skills in financial recording, bookkeeping, budgeting, taxation, auditing, and financial reporting. Students gain hands-on experience using accounting principles and tools to prepare accurate financial statements and support effective business decision-making.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781097531_a1f728ca.jpg', 5, 1, '2026-06-10 13:18:51', '2026-06-10 13:18:51'),
(6, 'Electical technology', 'Electrical Technology\r\n\r\nLearn the principles and practical applications of electrical systems used in homes, industries, and commercial environments. This program develops skills in electrical installation, wiring, circuit design, maintenance, fault diagnosis, and safety practices. Students gain hands-on experience working with electrical equipment and systems to prepare for real-world technical and industrial careers.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781097574_d103203d.jpg', 6, 1, '2026-06-10 13:19:34', '2026-06-10 13:19:34'),
(7, 'Building Construction', 'Building Construction\r\n\r\nLearn the fundamentals of modern building design and construction processes. This program equips students with practical skills in site preparation, masonry, carpentry, drawing interpretation, materials handling, and construction techniques. Students gain hands-on experience in planning, building, and maintaining structures while applying safety standards and industry practices used in real construction projects.', '3 Years', 'bi bi-tools', 'assets/uploads/trade_1781097615_8ec91b4a.jpg', 7, 1, '2026-06-10 13:20:15', '2026-06-10 13:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_trainers`
--

CREATE TABLE `tbl_trainers` (
  `id` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(255) NOT NULL,
  `ImageUrl` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_trainers`
--

INSERT INTO `tbl_trainers` (`id`, `FirstName`, `LastName`, `Email`, `Phone`, `ImageUrl`, `Password`, `CreationDate`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(3, 'Omar ', 'Teacher', 'omar@gmail.com', '+2500785120223', 'DefaultImage.png', 'trainer123', '2026-06-01 10:26:16', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comm_attachments`
--
ALTER TABLE `comm_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `comm_conversations`
--
ALTER TABLE `comm_conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comm_discussion_channels`
--
ALTER TABLE `comm_discussion_channels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comm_discussion_messages`
--
ALTER TABLE `comm_discussion_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel_id` (`channel_id`);

--
-- Indexes for table `comm_group_projects`
--
ALTER TABLE `comm_group_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comm_messages`
--
ALTER TABLE `comm_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation_id` (`conversation_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `comm_participants`
--
ALTER TABLE `comm_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indexes for table `comm_project_members`
--
ALTER TABLE `comm_project_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `comm_project_messages`
--
ALTER TABLE `comm_project_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `comm_reported_messages`
--
ALTER TABLE `comm_reported_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `tblcomments`
--
ALTER TABLE `tblcomments`
  ADD PRIMARY KEY (`postId`);

--
-- Indexes for table `tblposts`
--
ALTER TABLE `tblposts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `CategoryId` (`CategoryId`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`,`user_type`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `tbl_admins`
--
ALTER TABLE `tbl_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `tbl_aply_status`
--
ALTER TABLE `tbl_aply_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_apply_student`
--
ALTER TABLE `tbl_apply_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_books`
--
ALTER TABLE `tbl_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_contact_messages`
--
ALTER TABLE `tbl_contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_core_values`
--
ALTER TABLE `tbl_core_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_facilities`
--
ALTER TABLE `tbl_facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_features`
--
ALTER TABLE `tbl_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gallery_post`
--
ALTER TABLE `tbl_gallery_post`
  ADD KEY `id` (`id`),
  ADD KEY `CategoryNameId` (`CategoryNameId`);

--
-- Indexes for table `tbl_media`
--
ALTER TABLE `tbl_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`file_type`);

--
-- Indexes for table `tbl_navigation`
--
ALTER TABLE `tbl_navigation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_order` (`sort_order`);

--
-- Indexes for table `tbl_parent_doc`
--
ALTER TABLE `tbl_parent_doc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_question_test`
--
ALTER TABLE `tbl_question_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_quiz_modules`
--
ALTER TABLE `tbl_quiz_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_school_category`
--
ALTER TABLE `tbl_school_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_seo_meta`
--
ALTER TABLE `tbl_seo_meta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_path` (`page_path`);

--
-- Indexes for table `tbl_site_settings`
--
ALTER TABLE `tbl_site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tbl_staff_members`
--
ALTER TABLE `tbl_staff_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_stdaccounts`
--
ALTER TABLE `tbl_stdaccounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `StudentCode` (`StudentCode`);

--
-- Indexes for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `StudentCode` (`StudentCode`);

--
-- Indexes for table `tbl_student_marks`
--
ALTER TABLE `tbl_student_marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `StudentCode` (`Student`);

--
-- Indexes for table `tbl_student_message`
--
ALTER TABLE `tbl_student_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_trades`
--
ALTER TABLE `tbl_trades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_trainers`
--
ALTER TABLE `tbl_trainers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reset_token_hash` (`reset_token_hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comm_attachments`
--
ALTER TABLE `comm_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_conversations`
--
ALTER TABLE `comm_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_discussion_channels`
--
ALTER TABLE `comm_discussion_channels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comm_discussion_messages`
--
ALTER TABLE `comm_discussion_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_group_projects`
--
ALTER TABLE `comm_group_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_messages`
--
ALTER TABLE `comm_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_participants`
--
ALTER TABLE `comm_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_project_members`
--
ALTER TABLE `comm_project_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_project_messages`
--
ALTER TABLE `comm_project_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comm_reported_messages`
--
ALTER TABLE `comm_reported_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcomments`
--
ALTER TABLE `tblcomments`
  MODIFY `postId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblposts`
--
ALTER TABLE `tblposts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_activity_logs`
--
ALTER TABLE `tbl_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_admins`
--
ALTER TABLE `tbl_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_apply_student`
--
ALTER TABLE `tbl_apply_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_books`
--
ALTER TABLE `tbl_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `tbl_contact_messages`
--
ALTER TABLE `tbl_contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_core_values`
--
ALTER TABLE `tbl_core_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_facilities`
--
ALTER TABLE `tbl_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_features`
--
ALTER TABLE `tbl_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_gallery_post`
--
ALTER TABLE `tbl_gallery_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `tbl_media`
--
ALTER TABLE `tbl_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_navigation`
--
ALTER TABLE `tbl_navigation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_parent_doc`
--
ALTER TABLE `tbl_parent_doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_question_test`
--
ALTER TABLE `tbl_question_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_quiz_modules`
--
ALTER TABLE `tbl_quiz_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_school_category`
--
ALTER TABLE `tbl_school_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbl_seo_meta`
--
ALTER TABLE `tbl_seo_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_site_settings`
--
ALTER TABLE `tbl_site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_staff_members`
--
ALTER TABLE `tbl_staff_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_stdaccounts`
--
ALTER TABLE `tbl_stdaccounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_students`
--
ALTER TABLE `tbl_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_student_marks`
--
ALTER TABLE `tbl_student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_student_message`
--
ALTER TABLE `tbl_student_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_trades`
--
ALTER TABLE `tbl_trades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_trainers`
--
ALTER TABLE `tbl_trainers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comm_attachments`
--
ALTER TABLE `comm_attachments`
  ADD CONSTRAINT `comm_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `comm_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comm_discussion_messages`
--
ALTER TABLE `comm_discussion_messages`
  ADD CONSTRAINT `comm_discussion_messages_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `comm_discussion_channels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comm_messages`
--
ALTER TABLE `comm_messages`
  ADD CONSTRAINT `comm_messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `comm_conversations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comm_participants`
--
ALTER TABLE `comm_participants`
  ADD CONSTRAINT `comm_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `comm_conversations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comm_project_members`
--
ALTER TABLE `comm_project_members`
  ADD CONSTRAINT `comm_project_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `comm_group_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comm_project_messages`
--
ALTER TABLE `comm_project_messages`
  ADD CONSTRAINT `comm_project_messages_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `comm_group_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comm_reported_messages`
--
ALTER TABLE `comm_reported_messages`
  ADD CONSTRAINT `comm_reported_messages_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `comm_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tblposts`
--
ALTER TABLE `tblposts`
  ADD CONSTRAINT `tblposts_ibfk_1` FOREIGN KEY (`CategoryId`) REFERENCES `tbl_school_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_gallery_post`
--
ALTER TABLE `tbl_gallery_post`
  ADD CONSTRAINT `tbl_gallery_post_ibfk_1` FOREIGN KEY (`CategoryNameId`) REFERENCES `tbl_school_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
