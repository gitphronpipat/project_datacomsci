CREATE TABLE IF NOT EXISTS project_special_advisors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL COMMENT 'รหัสโครงงาน (FK: projects.id)',
    admin_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'รหัสเจ้าหน้าที่/อาจารย์ในระบบถ้ามี (FK: admins.id)',
    name VARCHAR(255) NOT NULL COMMENT 'ชื่อ-นามสกุล ที่ปรึกษาพิเศษ (รองรับทั้งคนในและคนนอก)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
