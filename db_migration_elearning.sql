-- Migration: Add video URL support for YouTube tutorials and trade URL for browse course links
-- Run this SQL in phpMyAdmin or MySQL CLI

ALTER TABLE `tbl_books`
  ADD COLUMN IF NOT EXISTS `file_size` bigint(20) DEFAULT NULL AFTER `file_type`,
  ADD COLUMN IF NOT EXISTS `video_url` varchar(500) DEFAULT NULL AFTER `file_size`,
  MODIFY COLUMN `file_type` varchar(50) DEFAULT 'pdf',
  MODIFY COLUMN `category` varchar(50) DEFAULT NULL;

ALTER TABLE `tbl_trades`
  ADD COLUMN IF NOT EXISTS `url` varchar(500) DEFAULT NULL AFTER `image`;
