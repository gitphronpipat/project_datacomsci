CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL COMMENT 'เอกสารต้องอยู่ในโปรเจกต์',
    title VARCHAR(255) NOT NULL COMMENT 'ชื่อเอกสาร',
    description TEXT DEFAULT NULL COMMENT 'คำอธิบาย',
    current_version INT NOT NULL DEFAULT 1 COMMENT 'เวอร์ชันล่าสุด',
    status ENUM('draft','submitted','approved','rejected')
        NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;