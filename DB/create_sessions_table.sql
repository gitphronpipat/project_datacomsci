-- ==========================================================
-- โครงสร้างตาราง: sessions
-- ฐานข้อมูล: datacomsci66 (MySQL 8.0)
-- ที่ตั้งไฟล์: DB/create_sessions_table.sql
-- คำอธิบาย: ตารางมาตรฐานสำหรับระบบจัดเก็บ Session ของ Laravel (SESSION_DRIVER=database)
-- ==========================================================

CREATE TABLE IF NOT EXISTS `sessions` (
    -- 1. รหัสประจำตัวเซสชัน (Primary Key)
    `id` VARCHAR(255) NOT NULL PRIMARY KEY COMMENT 'รหัส Session ID แบบสุ่มและไม่ซ้ำกัน',

    -- 2. รหัสผู้ใช้งานที่เข้าสู่ระบบ
    `user_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'รหัส User/Admin ที่กำลังล็อกอิน (ถ้ายังไม่ได้ล็อกอินจะเป็น NULL)',

    -- 3. หมายเลข IP Address ของผู้ใช้งาน
    `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'เลข IP ของเครื่องที่เข้ามา (รองรับทั้ง IPv4 และ IPv6 ความยาว 45 ตัวอักษร)',

    -- 4. ข้อมูลอุปกรณ์และเบราว์เซอร์ (User Agent)
    `user_agent` TEXT DEFAULT NULL COMMENT 'ข้อมูลอุปกรณ์ เช่น Chrome, Safari, iPhone, Windows 11',

    -- 5. ข้อมูลทั้งหมดใน Session (Payload)
    `payload` LONGTEXT NOT NULL COMMENT 'ข้อมูลทั้งหมดที่เก็บในเซสชัน เข้ารหัสแบบ Base64 และ Serialized',

    -- 6. เวลาที่มีการใช้งานล่าสุด (Unix Timestamp)
    `last_activity` INT NOT NULL COMMENT 'เวลาที่กดคลิกล่าสุด ใช้คำนวณการหมดอายุ (เช่น 1726189000)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางเก็บสถานะ Session ชั่วคราวของผู้ใช้งาน';

-- ==========================================================
-- ข้อมูลเพิ่มเติม: ฟิลด์เสริมที่สามารถต่อยอดได้ในอนาคต (Optional)
-- หากต้องการเก็บ Log ละเอียด เช่น ประวัติการเข้าสู่ระบบแบบถาวร 
-- แนะนำให้สร้างตารางแยก เช่น `login_histories` เพื่อไม่ให้กระทบประสิทธิภาพของตาราง sessions หลัก
-- ==========================================================
