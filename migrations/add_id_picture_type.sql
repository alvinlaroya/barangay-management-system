-- Add id_picture_type column to residents table
ALTER TABLE residents ADD COLUMN id_picture_type VARCHAR(50) NULL AFTER id_picture;