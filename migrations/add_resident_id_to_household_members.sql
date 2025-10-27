-- Migration: Add resident_id to household_members table
-- Date: 2025-10-27

ALTER TABLE `household_members` 
ADD COLUMN `resident_id` int(11) DEFAULT NULL AFTER `household_id`,
ADD KEY `resident_id` (`resident_id`),
ADD CONSTRAINT `household_members_resident_fk` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE SET NULL;