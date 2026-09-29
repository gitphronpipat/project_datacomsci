CREATE TABLE IF NOT EXISTS document_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    version INT NOT NULL,
    filename VARCHAR(255) NOT NULL COMMENT 'ชื่อไฟล์',
    file_path VARCHAR(500) NOT NULL COMMENT 'path หรือ URL',
    file_size INT DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
    mime_type VARCHAR(100) DEFAULT NULL,
    uploaded_by VARCHAR(15) DEFAULT NULL COMMENT 'student_id ผู้ upload',
    note TEXT DEFAULT NULL COMMENT 'หมายเหตุการแก้ไข',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_doc_version (document_id, version),
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES student(student_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;