CREATE TABLE IF NOT EXISTS student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(15) NOT NULL UNIQUE COMMENT 'รหัสนักศึกษา เช่น 6604101306',
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    real_pass VARCHAR(255) DEFAULT NULL COMMENT 'ถ้าจำเป็นต้องเก็บ เข้ารหัสด้วย AES',
    status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive (สถานะบัญชีใช้งาน)',
    status_student ENUM('no_project', 'doing','passed', 'missing') NOT NULL DEFAULT 'no_project' 
        COMMENT 'สถานะนักศึกษา: no_project=ยังไม่มีโครงงาน, doing=กำลังทำ,passed=ผ่านแล้ว, missing=ขาดการติดต่อ/หายไปนาน',
    name VARCHAR(255) NOT NULL,
    nickname VARCHAR(100) DEFAULT NULL COMMENT 'ชื่อเล่น',
    email VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    remark TEXT DEFAULT NULL COMMENT '  ',
    profile_picture VARCHAR(500) DEFAULT NULL,
    last_login_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;