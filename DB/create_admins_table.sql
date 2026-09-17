-- ==========================================================
-- โครงสร้างตาราง: admins
-- ฐานข้อมูล: laravel_db (MySQL 8.0)
-- ที่ตั้งไฟล์: DB/create_admins_table.sql
-- ==========================================================

CREATE TABLE IF NOT EXISTS `admins` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสประจำตัว (Primary Key)',
    
    -- ข้อมูลเข้าสู่ระบบ (ตามที่ระบุ)
    `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'ชื่อผู้ใช้งาน (ไม่ซ้ำกัน)',
    `password` VARCHAR(255) NOT NULL COMMENT 'รหัสผ่านที่เข้ารหัสแล้ว (Bcrypt/Argon2 สำหรับระบบยืนยันตัวตน)',
    `real_pass` VARCHAR(255) DEFAULT NULL COMMENT 'รหัสผ่านจริง (แนะนำให้เข้ารหัสหรือใช้เฉพาะช่วงพัฒนา/ทดสอบ)',
    
    -- สิทธิ์และสถานะ (ตามที่ระบุ)
    `role` ENUM('admin', 'teacher', 'officer') NOT NULL DEFAULT 'officer' COMMENT 'บทบาท: ผู้ดูแลระบบ, อาจารย์, เจ้าหน้าที่',
    `status` ENUM('0', '1') NOT NULL DEFAULT '1' COMMENT 'สถานะ: 1 = ใช้งานปกติ, 0 = ปิดใช้งาน',
    
    -- ฟิลด์แนะนำเพิ่มเติมสำหรับระบบงานจริง (พิจารณาใช้งาน):
    `name` VARCHAR(100) DEFAULT NULL COMMENT 'ชื่อ-นามสกุลจริง (สำหรับแสดงผล เช่น ยินดีต้อนรับ คุณสมชาย)',
    `email` VARCHAR(100) DEFAULT NULL UNIQUE COMMENT 'อีเมลสำหรับติดต่อ/แจ้งเตือน/กู้คืนรหัสผ่าน',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์ติดต่อ',
    `profile_picture` VARCHAR(255) DEFAULT NULL COMMENT 'ลิงก์หรือ path รูปภาพบัตรประชาชน',
    `last_login_at` DATETIME DEFAULT NULL COMMENT 'เวลาที่เข้าสู่ระบบล่าสุด (สำหรับตรวจสอบความปลอดภัย)',
    
    -- เวลาบันทึกและแก้ไขข้อมูล (มาตรฐานของ Laravel)
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างข้อมูล',
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขข้อมูลล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บข้อมูลผู้ดูแลระบบ อาจารย์ และเจ้าหน้าที่';
