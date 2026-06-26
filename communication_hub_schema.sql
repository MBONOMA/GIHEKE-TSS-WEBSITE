-- ============================================================
-- Communication Hub - Full Database Schema
-- ============================================================

-- -----------------------------------------------------------
-- 1. comm_conversations
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `subject` VARCHAR(255) DEFAULT NULL,
    `type` ENUM('direct', 'group', 'discussion') DEFAULT 'direct',
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_by_role` ENUM('student', 'trainer', 'admin') DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_message_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 2. comm_participants
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_participants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `user_code` VARCHAR(50) NOT NULL,
    `user_role` ENUM('student', 'trainer', 'admin') NOT NULL,
    `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_read_at` DATETIME DEFAULT NULL,
    FOREIGN KEY (`conversation_id`) REFERENCES `comm_conversations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 3. comm_messages
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_messages` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_code` VARCHAR(50) NOT NULL,
    `sender_role` ENUM('student', 'trainer', 'admin') NOT NULL,
    `sender_name` VARCHAR(255) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `message_type` ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    `file_path` VARCHAR(500) DEFAULT NULL,
    `file_name` VARCHAR(255) DEFAULT NULL,
    `file_size` INT DEFAULT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `is_edited` TINYINT(1) DEFAULT 0,
    `is_deleted` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_conversation_id` (`conversation_id`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`conversation_id`) REFERENCES `comm_conversations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 4. comm_reported_messages
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_reported_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `message_id` BIGINT NOT NULL,
    `reported_by` VARCHAR(50) NOT NULL,
    `reported_by_role` ENUM('student', 'trainer', 'admin') DEFAULT NULL,
    `reason` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'reviewed', 'dismissed', 'action_taken') DEFAULT 'pending',
    `reviewed_by` VARCHAR(50) DEFAULT NULL,
    `reviewed_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`message_id`) REFERENCES `comm_messages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 5. comm_attachments
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_attachments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `message_id` BIGINT NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(100) DEFAULT NULL,
    `file_size` INT DEFAULT NULL,
    `uploaded_by` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`message_id`) REFERENCES `comm_messages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 6. comm_discussion_channels
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_discussion_channels` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `channel_name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `module_name` VARCHAR(255) DEFAULT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `level` VARCHAR(50) DEFAULT NULL,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_by_role` ENUM('student', 'trainer', 'admin') DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 7. comm_discussion_messages
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_discussion_messages` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `channel_id` INT NOT NULL,
    `sender_code` VARCHAR(50) NOT NULL,
    `sender_role` ENUM('student', 'trainer', 'admin') NOT NULL,
    `sender_name` VARCHAR(255) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`channel_id`) REFERENCES `comm_discussion_channels`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 8. comm_group_projects
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_group_projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `module_name` VARCHAR(255) DEFAULT NULL,
    `department` VARCHAR(100) DEFAULT NULL,
    `level` VARCHAR(50) DEFAULT NULL,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_by_role` ENUM('student', 'trainer', 'admin') DEFAULT NULL,
    `due_date` DATE DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 9. comm_project_members
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_project_members` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `user_code` VARCHAR(50) NOT NULL,
    `user_role` ENUM('student', 'trainer', 'admin') NOT NULL,
    `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `comm_group_projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 10. comm_project_messages
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comm_project_messages` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `sender_code` VARCHAR(50) NOT NULL,
    `sender_role` ENUM('student', 'trainer', 'admin') DEFAULT NULL,
    `sender_name` VARCHAR(255) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `file_path` VARCHAR(500) DEFAULT NULL,
    `file_name` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`project_id`) REFERENCES `comm_group_projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed Data
-- ============================================================

INSERT INTO `comm_discussion_channels` (`channel_name`, `description`, `department`, `level`) VALUES
('General Discussion', 'Open discussion for all students and staff', NULL, NULL);
