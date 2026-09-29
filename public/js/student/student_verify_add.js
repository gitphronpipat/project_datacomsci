/**
 * ==============================================================================
 * ไฟล์: student_verify_add.js
 * ที่ตั้ง: public/js/student/student_verify_add.js
 * หน้าที่: โมดูลเครื่องยนต์ตรวจสอบความถูกต้องของข้อมูลนักศึกษาและโครงงาน (Verification Engine)
 *         แยกตรรกะตรวจจับข้อมูลผิดปกติออกจากไฟล์แสดงผล (DOM Renderer)
 *         ตรวจจับทั้ง:
 *           1. รหัสนักศึกษาซ้ำในไฟล์ (Duplicate ID)
 *           2. รหัสไม่ครบ 10 หลัก (Invalid Length)
 *           3. รหัสไม่ใช่สาขา 04101 (Not CS Major)
 *           4. ไม่มีชื่อหัวข้อโครงงาน (Missing Title - ต้องมีไทยหรืออังกฤษอย่างน้อย 1 ภาษา)
 *           5. สหกิจศึกษาไม่ระบุสถานที่ฝึกงาน/บริษัท (Coop Missing Company)
 *           6. ไม่พบชื่ออาจารย์ที่ปรึกษา/กรรมการในระบบ (Unmatched Teacher/Officer)
 *           7. กลุ่มโครงงานที่มีสมาชิกข้อมูลผิดปกติ
 * ==============================================================================
 */

(function (window) {
    'use strict';

    const StudentVerify = {
        /**
         * ค้นหาอาจารย์/เจ้าหน้าที่จากฐานข้อมูลในระบบ (window.TEACHERS_LIST)
         * @param {string} name - ชื่อที่ต้องการค้นหา
         * @param {string|null} roleFilter - กรองเฉพาะบทบาท เช่น 'teacher', 'officer' หรือ null (เอาทั้งหมด)
         * @returns {Object|null}
         */
        findTeacher: function (name, roleFilter) {
            if (!name || !window.TEACHERS_LIST || !Array.isArray(window.TEACHERS_LIST)) return null;
            const clean = String(name).replace(/\s+/g, ' ').trim().toLowerCase();
            if (!clean || clean === '-' || clean === 'ไม่มี') return null;

            // ตรวจสอบบทบาท (รองรับทั้ง string เดียว หรือ array)
            const matchesRole = function (t) {
                if (!roleFilter) return true;
                if (Array.isArray(roleFilter)) return roleFilter.includes(t.role);
                return t.role === roleFilter;
            };

            // ฟังก์ชันตัดคำนำหน้า เช่น ผศ.ดร., ดร., อาจารย์, นาย, นาง, นางสาว
            const stripPrefix = function (str) {
                return str.replace(/^(ผศ\.ดร\.|รศ\.ดร\.|ศ\.ดร\.|ผศ\.|รศ\.|ศ\.|ดร\.|อ\.|อาจารย์|นาย|นาง|นางสาว|น\.ส\.)\s*/i, '').trim();
            };

            const cleanNoPrefix = stripPrefix(clean);

            // 1. ค้นหาแบบชื่อตรงกัน 100%
            let found = window.TEACHERS_LIST.find(function (t) {
                if (!matchesRole(t)) return false;
                const tName = (t.name || '').replace(/\s+/g, ' ').trim().toLowerCase();
                return tName === clean;
            });
            if (found) return found;

            // 2. ค้นหาแบบตัดคำนำหน้าออก
            if (cleanNoPrefix.length >= 3) {
                found = window.TEACHERS_LIST.find(function (t) {
                    if (!matchesRole(t)) return false;
                    const tNoPrefix = stripPrefix((t.name || '').toLowerCase());
                    return tNoPrefix === cleanNoPrefix || tNoPrefix.includes(cleanNoPrefix) || cleanNoPrefix.includes(tNoPrefix);
                });
                if (found) return found;
            }

            return null;
        },

        /**
         * 1. ตรวจสอบว่าเป็นรหัสนักศึกษาสาขาวิทยาการคอมพิวเตอร์ (รหัสสาขา 04101) หรือไม่
         * โครงสร้างรหัส ม.แม่โจ้ 10 หลัก: [ปี 2 หลัก][รหัสสาขา 5 หลัก '04101'][ลำดับ 3 หลัก]
         * ตัวอย่าง: 6604101356 -> 66 + 04101 + 356
         * @param {string|number} studentId
         * @returns {boolean}
         */
        isCsMajor: function (studentId) {
            if (!studentId) return false;
            const sid = String(studentId).trim();
            if (sid.length !== 10) return false;
            return sid.substring(2, 7) === '04101';
        },

        /**
         * 2. ตรวจสอบความถูกต้องของรูปแบบรหัสนักศึกษา (ต้องเป็นตัวเลข 10 หลัก)
         * @param {string|number} studentId
         * @returns {boolean}
         */
        isValidIdFormat: function (studentId) {
            if (!studentId) return false;
            const sid = String(studentId).trim();
            return /^\d{10}$/.test(sid);
        },

        /**
         * 3. ตรวจสอบความผิดปกติของนักศึกษาเป็นรายบุคคล (Single Student Verification)
         * @param {Object} student - ออบเจ็กต์ข้อมูลนักศึกษา { student_id, name, ... }
         * @returns {Array} รายการปัญหาที่พบ (ถ้าไม่มีปัญหาจะได้อาเรย์ว่าง)
         */
        verifyStudent: function (student) {
            if (!student) return [];
            const sid = String(student.student_id || '').trim();
            const issues = [];

            // 3.1 ตรวจรูปแบบรหัส 10 หลัก (Invalid Length / Non-numeric)
            if (!this.isValidIdFormat(sid)) {
                const len = sid.length;
                issues.push({
                    code: 'invalid_format',
                    badge: '<span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle me-1"></i>รหัสไม่ครบ 10 หลัก</span>',
                    desc: 'รหัสนักศึกษาต้องเป็นตัวเลข 10 หลัก (ตรวจพบ: ' + (len > 0 ? len + ' หลัก "' + sid + '"' : 'ว่างเปล่า') + ')'
                });
            } else if (!this.isCsMajor(sid)) {
                // 3.2 ตรวจรหัสสาขาวิชา 04101
                const detectedCode = sid.length >= 7 ? sid.substring(2, 7) : sid;
                issues.push({
                    code: 'major_mismatch',
                    badge: '<span class="badge bg-danger text-white"><i class="fas fa-exclamation-circle me-1"></i>ไม่ใช่สาขา 04101</span>',
                    desc: 'รหัสสาขาตรวจพบเป็น <strong class="text-danger">"' + detectedCode + '"</strong> แทนที่จะเป็น <strong>"04101"</strong> (วท.บ. วิทยาการคอมพิวเตอร์)'
                });
            }

            return issues;
        },

        /**
         * 4. ตรวจสอบความผิดปกติของกลุ่มโครงงาน (Project Verification)
         * @param {Object} project - ออบเจ็กต์กลุ่มโครงงาน
         * @param {number} idx - Index ลำดับกลุ่ม
         * @returns {Object} { hasAnomaly: boolean, issues: Array }
         */
        verifyProject: function (project, idx) {
            if (!project) return { hasAnomaly: false, issues: [] };
            const self = this;
            const issues = [];
            const isCoop = (project.type === 'coop');

            // 4.1 ตรวจสอบชื่อโครงงาน: ต้องมีภาษาไทย (title_th) หรือภาษาอังกฤษ (title_en) อย่างน้อย 1 ภาษา
            const titleTh = (project.title_th || '').trim();
            const titleEn = (project.title_en || '').trim();
            if (!titleTh && !titleEn) {
                issues.push({
                    code: 'missing_title',
                    badge: '<span class="badge bg-danger text-white"><i class="fas fa-heading me-1"></i>ไม่มีชื่อโครงงาน</span>',
                    desc: 'ยังไม่มีชื่อโครงงาน (อย่างน้อยต้องระบุชื่อภาษาไทยหรือภาษาอังกฤษ 1 ภาษา)'
                });
            }

            // 4.2 ตรวจสอบสหกิจศึกษา: ต้องระบุสถานที่ฝึกงาน/บริษัท
            if (isCoop) {
                const company = (project.company_name || '').trim();
                if (!company) {
                    issues.push({
                        code: 'coop_missing_company',
                        badge: '<span class="badge bg-warning text-dark"><i class="fas fa-building me-1"></i>ไม่ระบุสถานที่ฝึกงาน</span>',
                        desc: 'เป็นวิชาสหกิจศึกษา (วท 497) แต่ยังไม่ได้ระบุชื่อบริษัทหรือสถานที่ฝึกงาน'
                    });
                }
            }

            // 4.3 ตรวจสอบอาจารย์ที่ปรึกษาประธาน (ต้องเป็น role teacher เท่านั้น)
            const presName = (project.advisor_president || '').trim();
            if (!presName || presName === '-') {
                issues.push({
                    code: 'missing_president',
                    badge: '<span class="badge bg-danger text-white"><i class="fas fa-user-times me-1"></i>ไม่มีประธานที่ปรึกษา</span>',
                    desc: 'ยังไม่ได้ระบุประธานอาจารย์ที่ปรึกษาของกลุ่มโครงงานนี้'
                });
            } else {
                // ค้นหาในฐานข้อมูลอาจารย์ (role teacher เท่านั้น)
                const foundPres = self.findTeacher(presName, 'teacher');
                if (!foundPres) {
                    issues.push({
                        code: 'unmatched_president',
                        badge: '<span class="badge bg-danger text-white"><i class="fas fa-user-slash me-1"></i>ไม่พบอาจารย์ประธานในระบบ</span>',
                        desc: 'ชื่อประธานที่ปรึกษา <strong class="text-danger">"' + presName + '"</strong> ไม่ตรงกับรายชื่ออาจารย์ในฐานข้อมูล (กดแก้ไขเพื่อเลือกอาจารย์)'
                    });
                }
            }

            // 4.4 ตรวจสอบกรรมการ 1 (ถ้ามีระบุ ต้องเป็นอาจารย์ role teacher ในระบบ)
            const com1Name = (project.advisor_committee_1 || '').trim();
            if (com1Name && com1Name !== '-') {
                const foundCom1 = self.findTeacher(com1Name, 'teacher');
                if (!foundCom1) {
                    issues.push({
                        code: 'unmatched_committee_1',
                        badge: '<span class="badge bg-warning text-dark"><i class="fas fa-user-slash me-1"></i>ไม่พบกรรมการ 1 ในระบบ</span>',
                        desc: 'ชื่อกรรมการ 1 <strong class="text-danger">"' + com1Name + '"</strong> ไม่ตรงกับรายชื่ออาจารย์ในฐานข้อมูล'
                    });
                }
            }

            // 4.5 ตรวจสอบกรรมการ 2 (ถ้ามีระบุ ต้องเป็นอาจารย์ role teacher ในระบบ)
            const com2Name = (project.advisor_committee_2 || '').trim();
            if (com2Name && com2Name !== '-') {
                const foundCom2 = self.findTeacher(com2Name, 'teacher');
                if (!foundCom2) {
                    issues.push({
                        code: 'unmatched_committee_2',
                        badge: '<span class="badge bg-warning text-dark"><i class="fas fa-user-slash me-1"></i>ไม่พบกรรมการ 2 ในระบบ</span>',
                        desc: 'ชื่อกรรมการ 2 <strong class="text-danger">"' + com2Name + '"</strong> ไม่ตรงกับรายชื่ออาจารย์ในฐานข้อมูล'
                    });
                }
            }

            return {
                hasAnomaly: issues.length > 0,
                issues: issues
            };
        },

        /**
         * 5. สแกนตรวจสอบข้อมูลทั้งหมด (Full Data Scan)
         * สรุปรายการผิดปกติทั้งระดับ [กลุ่มโครงงาน] และ [นักศึกษา]
         * @param {Object} data - ข้อมูลภาพรวม { students, projects, summary, ... }
         * @returns {Object} { anomaliesList: Array, totalCount: number, studentCount: number, projectCount: number }
         */
        scanAll: function (data) {
            const self = this;
            const anomaliesList = [];
            const duplicateMap = {};
            let studentCount = 0;
            let projectCount = 0;

            if (!data) return { anomaliesList: [], totalCount: 0, studentCount: 0, projectCount: 0 };

            // ------------------------------------------------------------------
            // 5.1 สแกนความผิดปกติระดับ "กลุ่มโครงงาน" (Project-level Anomaly)
            // ------------------------------------------------------------------
            if (data.projects && data.projects.length > 0) {
                data.projects.forEach(function (p, pIdx) {
                    const pVerify = self.verifyProject(p, pIdx);
                    if (pVerify.hasAnomaly) {
                        projectCount++;
                        const isCoop = (p.type === 'coop');
                        const projTitle = (p.title_th || p.title_en || '').trim() || '<span class="text-danger fst-italic">ไม่มีชื่อโครงงาน</span>';
                        const projNo = p.project_no !== undefined && p.project_no !== null ? p.project_no : (pIdx + 1);

                        anomaliesList.push({
                            target_type: 'project',
                            project_idx: pIdx,
                            project_no: projNo,
                            project_type: p.type,
                            project_type_label: isCoop ? 'สหกิจศึกษา (497)' : 'โครงงาน (498)',
                            title: projTitle,
                            issues: pVerify.issues
                        });
                    }
                });
            }

            // ------------------------------------------------------------------
            // 5.2 สแกนความผิดปกติระดับ "นักศึกษา" (Student-level Anomaly)
            // ------------------------------------------------------------------
            const studentsList = Array.isArray(data.students)
                ? data.students
                : Object.values(data.students || {});

            studentsList.forEach(function (s) {
                const sid = String(s.student_id || '').trim();
                const studentIssues = self.verifyStudent(s);

                // ตรวจสอบรหัสนักศึกษาซ้ำในไฟล์ (Duplicate Student ID in CSV)
                if (sid) {
                    if (!duplicateMap[sid]) {
                        duplicateMap[sid] = 1;
                    } else {
                        duplicateMap[sid]++;
                        studentIssues.push({
                            code: 'duplicate_id',
                            badge: '<span class="badge bg-warning text-dark"><i class="fas fa-copy me-1"></i>รหัสซ้ำในไฟล์</span>',
                            desc: 'พบรหัสนักศึกษานี้ปรากฏซ้ำมากกว่า 1 ครั้งในไฟล์ CSV'
                        });
                    }
                }

                if (studentIssues.length > 0) {
                    studentCount++;
                    anomaliesList.push({
                        target_type: 'student',
                        student_id: s.student_id,
                        nickname: s.nickname || '',
                        name: s.name || '',
                        group: s.project_no ? ('กลุ่มที่ ' + s.project_no + ' (' + (s.type === 'coop' ? 'สหกิจ' : 'โครงงาน') + ')') : 'ยังไม่มีกลุ่ม',
                        issues: studentIssues
                    });
                }
            });

            return {
                anomaliesList: anomaliesList,
                totalCount: anomaliesList.length,
                studentCount: studentCount,
                projectCount: projectCount
            };
        }
    };

    // ส่งออกเป็น Global Object
    window.StudentVerify = StudentVerify;
    window.isComputerScienceMajor = function (sid) {
        return StudentVerify.isCsMajor(sid);
    };

})(window);
