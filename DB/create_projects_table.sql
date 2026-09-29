CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('project','coop') NOT NULL DEFAULT 'project'
        COMMENT 'project=498, coop=497',
    title_th TEXT COMMENT 'ชื่อโปรเจกต์ภาษาไทย',
    title_en TEXT COMMENT 'ชื่อโปรเจกต์ภาษาอังกฤษ',
    project_year INT NOT NULL COMMENT 'ปีที่ทำโครงงาน',
    advisor_president_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'ประธานอาจารย์ที่ปรึกษา (FK: admins.id)',
    advisor_committee_1_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'กรรมการ 1 (FK: admins.id)',
    advisor_committee_2_id BIGINT UNSIGNED DEFAULT NULL COMMENT 'กรรมการ 2 (FK: admins.id)',
    advisor_special BIGINT UNSIGNED DEFAULT NULL COMMENT 'ที่ปรึกษาพิเศษ (FK: admins.id)',
    status_project ENUM('wait','success','cancel') DEFAULT 'wait' COMMENT 'สถานะ',
    company_name VARCHAR(255) DEFAULT NULL COMMENT 'ชื่อบริษัท (เฉพาะ coop)',
    remark TEXT DEFAULT NULL COMMENT 'หมายเหตุ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (advisor_president_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (advisor_committee_1_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (advisor_committee_2_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (advisor_special) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;