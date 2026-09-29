/**
 * ==============================================================================
 * สคริปต์ควบคุม Modal แก้ไขข้อมูลกลุ่มโครงงาน และ ข้อมูลนักศึกษา (ฉบับแยกโมดูลกลาง)
 * ตำแหน่งไฟล์: public/js/student/modal_edit_studentandproject.js
 * ==============================================================================
 * ออกแบบเพื่อรองรับการใช้งานซ้ำ (Reusability):
 * 1. หน้าเพิ่มข้อมูล/พรีวิว CSV (page_student_add.blade.php): แก้ไขข้อมูลใน Memory ก่อนบันทึก
 * 2. หน้ารายการนักศึกษาหลัก (page_student.blade.php): แก้ไขข้อมูลและบันทึกลงฐานข้อมูลจริง
 * ==============================================================================
 */

(function (window, $) {
    'use strict';

    // ออบเจกต์เก็บ Callback ฟังก์ชันสำหรับแต่ละหน้าที่เรียกใช้งาน
    window.ModalEditCallbacks = window.ModalEditCallbacks || {
        onSaveProject: null,
        onSaveStudent: null
    };

    // ==============================================================================
    // 1. ฟังก์ชันซิงค์และเชื่อมโยงอาจารย์ในระบบกับ Custom Select Smooth
    // ==============================================================================

    /**
     * ค้นหาและจับคู่อาจารย์/เจ้าหน้าที่ในระบบอย่างแม่นยำ
     * รองรับทั้งค้นหาด้วย ID, ค้นหาด้วยชื่อตรงเป๊ะ, ค้นหาแบบตัดคำนำหน้า (ผศ.ดร./อ./ฯลฯ)
     * ทำงานได้ทันทีโดยไม่ต้องพึ่งพา StudentVerify และใช้ได้กับทุกหน้าในระบบ
     * @param {string} name - ชื่ออาจารย์/เจ้าหน้าที่
     * @param {string|Array|null} roleFilter - กรองบทบาท ('teacher', 'officer', หรือ null)
     * @param {number|string|null} targetId - รหัส ID จากฐานข้อมูล
     * @param {jQuery|null} $select - Dropdown select element (เพื่อค้นหา option ที่มีอยู่จริง)
     * @returns {Object|null} { id, name, role, text }
     */
    function findTeacherMatch(name, roleFilter, targetId, $select) {
        // 1. ตรวจสอบด้วย targetId ก่อนเสมอถ้ามี (แม่นยำที่สุด)
        if (targetId !== undefined && targetId !== null && String(targetId).trim() !== '') {
            const strId = String(targetId).trim();
            if ($select && $select.length > 0) {
                const $optById = $select.find(`option[data-id="${strId}"]`);
                if ($optById.length > 0) {
                    return {
                        id: strId,
                        name: $optById.val(),
                        role: $optById.data('role') || 'teacher',
                        text: $optById.text()
                    };
                }
            }
            if (window.TEACHERS_LIST && Array.isArray(window.TEACHERS_LIST)) {
                const t = window.TEACHERS_LIST.find(x => String(x.id) === strId);
                if (t) {
                    return { id: t.id, name: t.name, role: t.role, text: t.name };
                }
            }
        }

        // 2. ถ้าไม่มี ID หรือหาด้วย ID ไม่พบ ให้ค้นหาด้วยชื่อ
        if (!name) return null;
        const clean = String(name).replace(/\s+/g, ' ').trim().toLowerCase();
        if (!clean || clean === '-' || clean === 'ไม่มี') return null;

        // ถ้าหน้าไหนโหลด StudentVerify ไว้ ก็สามารถเรียกใช้ร่วมกันได้
        if (window.StudentVerify && typeof window.StudentVerify.findTeacher === 'function') {
            const svFound = window.StudentVerify.findTeacher(name, roleFilter);
            if (svFound) return svFound;
        }

        const stripPrefix = function (str) {
            return String(str || '').replace(/^(ผศ\.ดร\.|รศ\.ดร\.|ศ\.ดร\.|ผศ\.|รศ\.|ศ\.|ดร\.|อ\.|อาจารย์|นาย|นาง|นางสาว|น\.ส\.)\s*/i, '').trim();
        };
        const cleanNoPrefix = stripPrefix(clean);

        // 2.1 ค้นหาจาก options ใน <select> โดยตรง
        if ($select && $select.length > 0) {
            let matched = null;
            // 2.1.1 ตรงกัน 100% กับ option value
            $select.find('option').each(function () {
                if (matched) return;
                const val = $(this).val();
                if (!val || val === '__EXTERNAL__') return;
                const valClean = val.replace(/\s+/g, ' ').trim().toLowerCase();
                if (valClean === clean) {
                    matched = {
                        id: $(this).data('id') || '',
                        name: val,
                        role: $(this).data('role') || 'teacher',
                        text: $(this).text()
                    };
                }
            });
            if (matched) return matched;

            // 2.1.2 ค้นหาแบบตัดคำนำหน้าออก
            if (cleanNoPrefix.length >= 3) {
                $select.find('option').each(function () {
                    if (matched) return;
                    const val = $(this).val();
                    if (!val || val === '__EXTERNAL__') return;
                    const valNoPrefix = stripPrefix(val.toLowerCase());
                    if (valNoPrefix === cleanNoPrefix || valNoPrefix.includes(cleanNoPrefix) || cleanNoPrefix.includes(valNoPrefix)) {
                        matched = {
                            id: $(this).data('id') || '',
                            name: val,
                            role: $(this).data('role') || 'teacher',
                            text: $(this).text()
                        };
                    }
                });
                if (matched) return matched;
            }
        }

        // 2.2 ค้นหาจาก window.TEACHERS_LIST
        if (window.TEACHERS_LIST && Array.isArray(window.TEACHERS_LIST)) {
            const matchesRole = function (t) {
                if (!roleFilter) return true;
                if (Array.isArray(roleFilter)) return roleFilter.includes(t.role);
                return t.role === roleFilter;
            };

            // ตรงกัน 100%
            let tFound = window.TEACHERS_LIST.find(function (t) {
                if (!matchesRole(t)) return false;
                const tName = (t.name || '').replace(/\s+/g, ' ').trim().toLowerCase();
                return tName === clean;
            });
            if (tFound) return { id: tFound.id, name: tFound.name, role: tFound.role, text: tFound.name };

            // ตัดคำนำหน้าออก
            if (cleanNoPrefix.length >= 3) {
                tFound = window.TEACHERS_LIST.find(function (t) {
                    if (!matchesRole(t)) return false;
                    const tNoPrefix = stripPrefix((t.name || '').toLowerCase());
                    return tNoPrefix === cleanNoPrefix || tNoPrefix.includes(cleanNoPrefix) || cleanNoPrefix.includes(tNoPrefix);
                });
                if (tFound) return { id: tFound.id, name: tFound.name, role: tFound.role, text: tFound.name };
            }
        }

        return null;
    }

    /**
     * ซิงค์ค่าระหว่าง Input ข้อความ และ Dropdown อาจารย์ในระบบ
     * @param {string} inputId - ID ช่อง Input ข้อความ
     * @param {string} selectId - ID ของ Dropdown Select
     * @param {string} hiddenId - ID ช่อง Hidden เก็บ ID อาจารย์
     * @param {string} rawName - ชื่ออาจารย์
     * @param {string|Array|null} roleFilter - กรองบทบาทอาจารย์
     * @param {number|string|null} targetId - ID อาจารย์จากฐานข้อมูล
     */
    function syncAdvisorField(inputId, selectId, hiddenId, rawName, roleFilter, targetId) {
        const $input = $(inputId);
        const $select = $(selectId);
        const $hidden = $(hiddenId);

        $input.val(rawName || '');
        $select.val('');
        $hidden.val(targetId || '');

        const found = findTeacherMatch(rawName, roleFilter, targetId, $select);
        if (found) {
            $select.val(found.name);
            $hidden.val(found.id || targetId || '');
            if (!$input.val()) {
                $input.val(found.name);
            }
        }

        // ซิงค์ข้อความแสดงผลใน custom-select-smooth
        if (window.syncCustomSelect && $select.length > 0) {
            window.syncCustomSelect($select[0]);
        }

        // อัปเดตข้อความใน UI wrapper เพิ่มเติม เพื่อความชัวร์ 100%
        const $wrapper = $select.next('.custom-select-wrapper');
        if ($wrapper.length > 0) {
            const $selectedOpt = $select.find('option:selected');
            const displayText = ($selectedOpt.length > 0 && $selectedOpt.val())
                ? $selectedOpt.text()
                : ($select.find('option').first().text() || '-- เลือกอาจารย์ในระบบ --');
            $wrapper.find('.current-text').text(displayText);
            $wrapper.find('.custom-select-option').removeClass('active');
            $wrapper.find('.custom-select-check').addClass('d-none');
            const curVal = $select.val();
            if (curVal) {
                $wrapper.find('.custom-select-option').filter(function () {
                    return $(this).attr('data-val') === curVal;
                }).addClass('active').find('.custom-select-check').removeClass('d-none');
            }
        }
    }

    /**
     * เมื่อพิมพ์ในช่อง Input ข้อความ ให้ค้นหาและซิงค์ตัวเลือกใน Dropdown อัตโนมัติ
     */
    function syncCustomSelectFromInput(selectId, hiddenId, val, roleFilter) {
        const $select = $(selectId);
        const $hidden = $(hiddenId);
        const found = findTeacherMatch(val, roleFilter, null, $select);
        const matchedName = found ? found.name : '';
        const matchedId = found ? found.id : '';

        $select.val(matchedName);
        $hidden.val(matchedId);

        if (window.syncCustomSelect && $select.length > 0) {
            window.syncCustomSelect($select[0]);
        }

        const $wrapper = $select.next('.custom-select-wrapper');
        if ($wrapper.length > 0) {
            const $selectedOpt = $select.find('option:selected');
            const displayText = ($selectedOpt.length > 0 && $selectedOpt.val())
                ? $selectedOpt.text()
                : ($select.find('option').first().text() || '-- เลือกอาจารย์ในระบบ --');
            $wrapper.find('.current-text').text(displayText);
            $wrapper.find('.custom-select-option').removeClass('active');
            $wrapper.find('.custom-select-check').addClass('d-none');
            if (matchedName) {
                $wrapper.find('.custom-select-option').filter(function () {
                    return $(this).attr('data-val') === matchedName;
                }).addClass('active').find('.custom-select-check').removeClass('d-none');
            }
        }
    }

    // ==============================================================================
    // 2. จัดการที่ปรึกษาพิเศษ (Special Advisors Management)
    // ==============================================================================

    /**
     * แสดงข้อความแจ้งเตือนเมื่อยังไม่มีที่ปรึกษาพิเศษ
     */
    function showEmptySpecialAdvisorNotice() {
        if ($('#specialAdvisorsContainer .special-advisor-row').length === 0) {
            if ($('#emptySpecialAdvisorAlert').length === 0) {
                $('#specialAdvisorsContainer').html(`
                    <div id="emptySpecialAdvisorAlert" class="col-12 text-muted small py-2">
                        <i class="fas fa-info-circle me-1"></i>ยังไม่มีที่ปรึกษาพิเศษ (กดปุ่ม <strong class="text-primary">+ เพิ่มที่ปรึกษาพิเศษ</strong> ด้านบนหากต้องการระบุ)
                    </div>
                `);
            }
        }
    }

    /**
     * เพิ่มแถวที่ปรึกษาพิเศษใหม่ (รองรับ อาจารย์, เจ้าหน้าที่, บุคคลภายนอก พร้อม custom-select-smooth)
     * @param {Object} data - { name: '', admin_id: null, role: 'external', is_external: true }
     */
    function addSpecialAdvisorRow(data) {
        data = data || { name: '', admin_id: null, role: 'external', is_external: true };
        const container = document.getElementById('specialAdvisorsContainer');
        if (!container) return;

        $('#emptySpecialAdvisorAlert').remove();

        // ตรวจสอบและค้นหาอาจารย์หรือเจ้าหน้าที่ในระบบอัตโนมัติหากยังไม่มี admin_id
        if (!data.admin_id && data.name) {
            const found = findTeacherMatch(data.name, ['teacher', 'officer']);
            if (found) {
                data.admin_id = found.id;
                data.role = found.role;
                data.is_external = false;
            }
        }

        const rowId = 'sa_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        const teachers = (window.TEACHERS_LIST && Array.isArray(window.TEACHERS_LIST)) ? window.TEACHERS_LIST : [];
        const teacherList = teachers.filter(t => t.role === 'teacher');
        const officerList = teachers.filter(t => t.role === 'officer');

        let teacherOptions = '';
        teacherList.forEach(function (t) {
            const isSel = (data.admin_id && String(data.admin_id) === String(t.id)) || (data.name && data.name === t.name);
            teacherOptions += `<option value="${t.name}" data-id="${t.id}" data-role="teacher" ${isSel ? 'selected' : ''}>${t.name} (อาจารย์)</option>`;
        });

        let officerOptions = '';
        officerList.forEach(function (t) {
            const isSel = (data.admin_id && String(data.admin_id) === String(t.id)) || (data.name && data.name === t.name);
            officerOptions += `<option value="${t.name}" data-id="${t.id}" data-role="officer" ${isSel ? 'selected' : ''}>${t.name} (เจ้าหน้าที่)</option>`;
        });

        const isExternal = (data.role === 'external' || data.is_external || !data.admin_id) && data.name;
        const isSelectedExternal = isExternal && !data.admin_id;

        const rowHtml = `
            <div class="col-12 col-md-6 special-advisor-row" id="${rowId}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-medium mb-0">ที่ปรึกษาพิเศษ</label>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" onclick="removeSpecialAdvisorRow(this)" title="ลบที่ปรึกษาท่านนี้">
                        <i class="fas fa-trash-alt me-1"></i><span class="small">ลบ</span>
                    </button>
                </div>
                <div class="d-flex flex-column gap-1">
                    <select class="custom-select-smooth special-advisor-select" onchange="onSpecialAdvisorSelectChange(this)">
                        <option value="">-- เลือกอาจารย์/เจ้าหน้าที่ในระบบ --</option>
                        <option value="__EXTERNAL__" data-id="" data-role="external" ${isSelectedExternal ? 'selected' : ''}>บุคคลภายนอก (พิมพ์ชื่อระบุเอง)</option>
                        ${teacherOptions}
                        ${officerOptions}
                    </select>
                    <input type="text" class="form-control special-advisor-name" value="${data.name || ''}" placeholder="หรือพิมพ์ชื่อระบุเอง เช่น ผศ.ดร. ... หรือชื่อบุคคลภายนอก" oninput="onSpecialAdvisorNameInput(this)">
                    <input type="hidden" class="special-advisor-id" value="${data.admin_id || ''}">
                    <input type="hidden" class="special-advisor-role" value="${data.role || 'external'}">
                </div>
            </div>
        `;

        $(container).append(rowHtml);

        // แปลง Select เป็น custom-select-smooth
        if (window.initCustomSelectSmooth) {
            window.initCustomSelectSmooth(document.getElementById(rowId));
        }
    }

    /**
     * ลบแถวที่ปรึกษาพิเศษ
     */
    function removeSpecialAdvisorRow(btn) {
        $(btn).closest('.special-advisor-row').fadeOut(150, function () {
            $(this).remove();
            if ($('#specialAdvisorsContainer .special-advisor-row').length === 0) {
                showEmptySpecialAdvisorNotice();
            }
        });
    }

    /**
     * เมื่อผู้ใช้เปลี่ยนตัวเลือกใน Dropdown ที่ปรึกษาพิเศษ
     */
    function onSpecialAdvisorSelectChange(selectEl) {
        const $row = $(selectEl).closest('.special-advisor-row');
        const $opt = $(selectEl).find('option:selected');
        const val = selectEl.value;
        const role = $opt.attr('data-role') || 'external';
        const id = $opt.attr('data-id') || '';
        const $nameInput = $row.find('.special-advisor-name');
        const $idInput = $row.find('.special-advisor-id');
        const $roleInput = $row.find('.special-advisor-role');

        if (val === '__EXTERNAL__') {
            $idInput.val('');
            $roleInput.val('external');
            $nameInput.focus();
        } else if (val && id) {
            $nameInput.val(val);
            $idInput.val(id);
            $roleInput.val(role);
        } else {
            $idInput.val('');
            $roleInput.val('external');
        }

        if (window.syncCustomSelect) {
            window.syncCustomSelect(selectEl);
        }
    }

    /**
     * เมื่อผู้ใช้พิมพ์ชื่อที่ปรึกษาพิเศษเองในช่อง Input
     */
    function onSpecialAdvisorNameInput(inputEl) {
        const $row = $(inputEl).closest('.special-advisor-row');
        const nameVal = inputEl.value.trim();
        const $idInput = $row.find('.special-advisor-id');
        const $roleInput = $row.find('.special-advisor-role');
        const $select = $row.find('.special-advisor-select');

        if (nameVal) {
            const found = findTeacherMatch(nameVal, ['teacher', 'officer'], null, $select);
            if (found) {
                $idInput.val(found.id);
                $roleInput.val(found.role);
                $select.val(found.name);
                if (window.syncCustomSelect) {
                    window.syncCustomSelect($select[0]);
                }
                return;
            }
        }

        if (!$idInput.val() && nameVal) {
            $roleInput.val('external');
            $select.val('__EXTERNAL__');
            if (window.syncCustomSelect) {
                window.syncCustomSelect($select[0]);
            }
        }
    }

    // ==============================================================================
    // 3. ควบคุม Modal แก้ไขข้อมูลกลุ่มโครงงาน (Project Edit Modal)
    // ==============================================================================

    /**
     * เปิด Modal แก้ไขข้อมูลกลุ่มโครงงาน
     * @param {number|Object} target - Index หรือ Object ข้อมูลโครงงาน
     */
    function openEditProjectModal(target) {
        let p = null;
        let idx = null;

        if (typeof target === 'number' || !isNaN(parseInt(target, 10))) {
            idx = parseInt(target, 10);
            if (window.globalParsedData && window.globalParsedData.projects && window.globalParsedData.projects[idx]) {
                p = window.globalParsedData.projects[idx];
            }
        } else if (typeof target === 'object' && target !== null) {
            p = target;
            idx = target.id || target.project_no || 0;
        }

        if (!p) return;

        $('#editProjectIndex').val(idx !== null ? idx : '');
        $('#editProjectType').val(p.type || 'project');
        $('#editProjectNo').val(p.project_no !== undefined && p.project_no !== null ? p.project_no : '');
        $('#editProjectTitleTh').val(p.title_th || '');
        $('#editProjectTitleEn').val(p.title_en || '');
        $('#editProjectCompany').val(p.company_name || '');
        $('#editProjectRemark').val(p.remark || '');

        syncAdvisorField('#editProjectAdvisorPresident', '#editProjectAdvisorPresidentSelect', '#editProjectAdvisorPresidentId', p.advisor_president || p.advisor_president_name, 'teacher', p.advisor_president_id);
        syncAdvisorField('#editProjectAdvisorCommittee1', '#editProjectAdvisorCommittee1Select', '#editProjectAdvisorCommittee1Id', p.advisor_committee_1 || p.advisor_committee_1_name, 'teacher', p.advisor_committee_1_id);
        syncAdvisorField('#editProjectAdvisorCommittee2', '#editProjectAdvisorCommittee2Select', '#editProjectAdvisorCommittee2Id', p.advisor_committee_2 || p.advisor_committee_2_name, 'teacher', p.advisor_committee_2_id);

        // จัดการรายการที่ปรึกษาพิเศษ
        $('#specialAdvisorsContainer').empty();
        let hasSpecialAdvisors = false;

        if (p.special_advisors && Array.isArray(p.special_advisors) && p.special_advisors.length > 0) {
            p.special_advisors.forEach(function (sa) {
                const saName = (typeof sa === 'string' ? sa : (sa && sa.name ? sa.name : '')).trim();
                if (saName && saName !== '-' && saName !== 'ไม่มี') {
                    addSpecialAdvisorRow(typeof sa === 'object' ? sa : { name: saName, admin_id: null, role: 'external', is_external: true });
                    hasSpecialAdvisors = true;
                }
            });
        } else if (p.advisor_special && p.advisor_special.trim() && p.advisor_special.trim() !== '-' && p.advisor_special.trim() !== 'ไม่มี') {
            const rawSpecial = p.advisor_special.trim();
            const found = findTeacherMatch(rawSpecial, ['teacher', 'officer']);
            if (found) {
                addSpecialAdvisorRow({ name: found.name, admin_id: found.id, role: found.role, is_external: false });
            } else {
                addSpecialAdvisorRow({ name: rawSpecial, admin_id: null, role: 'external', is_external: true });
            }
            hasSpecialAdvisors = true;
        }

        if (!hasSpecialAdvisors) {
            showEmptySpecialAdvisorNotice();
        }

        // แสดง/ซ่อนช่องบริษัท
        if (p.type === 'coop') {
            $('#editProjectCompanyContainer').removeClass('d-none');
        } else {
            $('#editProjectCompanyContainer').addClass('d-none');
        }

        // ซิงค์ Custom Select ใน Modal ทั้งหมด
        if (window.initCustomSelectSmooth) {
            window.initCustomSelectSmooth(document.getElementById('modalEditProject'));
        }
        ['#editProjectAdvisorPresidentSelect', '#editProjectAdvisorCommittee1Select', '#editProjectAdvisorCommittee2Select'].forEach(function (selId) {
            const el = $(selId)[0];
            if (window.syncCustomSelect && el) {
                window.syncCustomSelect(el);
            }
        });

        const modalEl = document.getElementById('modalEditProject');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            $('#modalEditProject').modal('show');
        }
    }

    /**
     * บันทึกการแก้ไขข้อมูลกลุ่มโครงงาน
     */
    function saveEditProject() {
        const rawIdx = $('#editProjectIndex').val();
        const idx = parseInt(rawIdx, 10);

        const newType = $('#editProjectType').val();
        const newProjectNo = $('#editProjectNo').val().trim();
        const titleTh = $('#editProjectTitleTh').val().trim();
        const titleEn = $('#editProjectTitleEn').val().trim();
        const companyName = (newType === 'coop') ? $('#editProjectCompany').val().trim() : '';

        const presName = $('#editProjectAdvisorPresident').val().trim();
        const com1Name = $('#editProjectAdvisorCommittee1').val().trim();
        const com2Name = $('#editProjectAdvisorCommittee2').val().trim();

        // รวบรวมข้อมูลที่ปรึกษาพิเศษ
        const specialAdvisors = [];
        $('#specialAdvisorsContainer .special-advisor-row').each(function () {
            const name = $(this).find('.special-advisor-name').val().trim();
            const idVal = $(this).find('.special-advisor-id').val();
            const roleVal = $(this).find('.special-advisor-role').val() || 'external';
            if (name) {
                specialAdvisors.push({
                    name: name,
                    admin_id: idVal ? parseInt(idVal, 10) : null,
                    role: roleVal,
                    is_external: (roleVal === 'external' || !idVal)
                });
            }
        });

        const projectData = {
            id: rawIdx,
            index: idx,
            type: newType,
            project_no: newProjectNo,
            title_th: titleTh,
            title_en: titleEn,
            company_name: companyName,
            advisor_president: presName,
            advisor_president_id: $('#editProjectAdvisorPresidentId').val() || null,
            advisor_committee_1: com1Name,
            advisor_committee_1_id: $('#editProjectAdvisorCommittee1Id').val() || null,
            advisor_committee_2: com2Name,
            advisor_committee_2_id: $('#editProjectAdvisorCommittee2Id').val() || null,
            special_advisors: specialAdvisors,
            advisor_special: specialAdvisors.map(sa => sa.name).join(', '),
            remark: $('#editProjectRemark').val().trim(),
            is_edited: true
        };

        // ตรวจสอบและค้นหา ID อาจารย์ในระบบเพิ่มเติมหากยังว่างอยู่
        if (!projectData.advisor_president_id && presName) {
            const tPres = findTeacherMatch(presName, 'teacher', null, $('#editProjectAdvisorPresidentSelect'));
            if (tPres) projectData.advisor_president_id = tPres.id;
        }
        if (!projectData.advisor_committee_1_id && com1Name) {
            const tCom1 = findTeacherMatch(com1Name, 'teacher', null, $('#editProjectAdvisorCommittee1Select'));
            if (tCom1) projectData.advisor_committee_1_id = tCom1.id;
        }
        if (!projectData.advisor_committee_2_id && com2Name) {
            const tCom2 = findTeacherMatch(com2Name, 'teacher', null, $('#editProjectAdvisorCommittee2Select'));
            if (tCom2) projectData.advisor_committee_2_id = tCom2.id;
        }

        // ปิด Modal
        const modalEl = document.getElementById('modalEditProject');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        } else {
            $('#modalEditProject').modal('hide');
        }

        // ถ้าหน้าเว็บลงทะเบียน Callback ไว้ ให้ส่งไปจัดการต่อ
        if (window.ModalEditCallbacks && typeof window.ModalEditCallbacks.onSaveProject === 'function') {
            window.ModalEditCallbacks.onSaveProject(projectData, idx);
            return;
        }

        // Fallback ค่าเริ่มต้นสำหรับหน้า Preview CSV (Memory Mode)
        if (window.globalParsedData && window.globalParsedData.projects && !isNaN(idx) && window.globalParsedData.projects[idx]) {
            const p = window.globalParsedData.projects[idx];
            Object.assign(p, projectData);

            if (p.student_ids && p.student_ids.length > 0) {
                p.student_ids.forEach(function (sid) {
                    let studentObj = null;
                    if (Array.isArray(window.globalParsedData.students)) {
                        studentObj = window.globalParsedData.students.find(s => String(s.student_id) === String(sid));
                    } else if (window.globalParsedData.students && window.globalParsedData.students[sid]) {
                        studentObj = window.globalParsedData.students[sid];
                    }
                    if (studentObj) {
                        studentObj.type = newType;
                        studentObj.project_no = newProjectNo;
                        studentObj.is_edited = true;
                    }
                });
            }

            if (typeof window.recalculateSummary === 'function') window.recalculateSummary();
            if (typeof window.renderLivePreview === 'function') window.renderLivePreview(window.globalParsedData);
            return;
        }

        // โหมดฐานข้อมูลจริง (สำหรับหน้ารายการข้อมูลหลัก page_student.blade.php)
        const projectId = projectData.id || rawIdx;
        if (projectId) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || window.CSRF_TOKEN || '';
            const btnSave = $('#modalEditProject button[onclick="saveEditProject()"]');
            btnSave.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

            $.ajax({
                url: '/pc-csmju/admin/student/project/update/' + projectId,
                type: 'POST',
                data: JSON.stringify(projectData),
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (res) {
                    btnSave.prop('disabled', false).html('<i class="fas fa-check me-1"></i> บันทึกการแก้ไข');
                    if (typeof iziToast !== 'undefined') {
                        iziToast.success({
                            title: 'สำเร็จ',
                            message: res.message || 'บันทึกการแก้ไขข้อมูลโครงงานเรียบร้อยแล้ว',
                            position: 'topRight'
                        });
                    }
                    setTimeout(function () { location.reload(); }, 700);
                },
                error: function (xhr) {
                    btnSave.prop('disabled', false).html('<i class="fas fa-check me-1"></i> บันทึกการแก้ไข');
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการบันทึกข้อมูลโครงงาน';
                    if (typeof iziToast !== 'undefined') {
                        iziToast.error({ title: 'ไม่สำเร็จ', message: msg, position: 'topRight' });
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    }

    // ==============================================================================
    // 4. ควบคุม Modal แก้ไขข้อมูลนักศึกษา (Student Edit Modal)
    // ==============================================================================

    /**
     * เปิด Modal แก้ไขข้อมูลนักศึกษา
     * @param {string|Object} target - รหัสนักศึกษา หรือ Object ข้อมูลนักศึกษา
     */
    function openEditStudentModal(target) {
        let s = null;

        if (typeof target === 'string' || typeof target === 'number') {
            const studentId = String(target);
            if (window.globalParsedData) {
                if (Array.isArray(window.globalParsedData.students)) {
                    s = window.globalParsedData.students.find(item => String(item.student_id) === studentId);
                } else if (window.globalParsedData.students && window.globalParsedData.students[studentId]) {
                    s = window.globalParsedData.students[studentId];
                } else if (window.globalParsedData.students_without_project) {
                    s = window.globalParsedData.students_without_project.find(item => String(item.student_id) === studentId);
                }
            }
        } else if (typeof target === 'object' && target !== null) {
            s = target;
        }

        if (!s) return;

        $('#editStudentOriginalId').val(s.student_id || '');
        $('#editStudentId').val(s.student_id || '');
        $('#editStudentNickname').val(s.nickname || '');
        $('#editStudentName').val(s.name || '');
        $('#editStudentType').val(s.type || 'project');
        $('#editStudentProjectNo').val(s.project_no !== undefined && s.project_no !== null ? s.project_no : '');
        $('#editStudentStatus').val(s.status_student || 'doing');
        $('#editStudentEmail').val(s.email || '');
        $('#editStudentPhone').val(s.phone || '');
        $('#editStudentRemark').val(s.remark || '');

        // ซิงค์ Custom Select ใน Modal
        if (window.initCustomSelectSmooth) {
            window.initCustomSelectSmooth(document.getElementById('modalEditStudent'));
        }

        const modalEl = document.getElementById('modalEditStudent');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            $('#modalEditStudent').modal('show');
        }
    }

    /**
     * บันทึกการแก้ไขข้อมูลนักศึกษา
     */
    function saveEditStudent() {
        const originalId = $('#editStudentOriginalId').val();
        const newId = $('#editStudentId').val().trim();
        const newNickname = $('#editStudentNickname').val().trim();
        const newName = $('#editStudentName').val().trim();
        const newType = $('#editStudentType').val();
        const newProjectNo = $('#editStudentProjectNo').val().trim();
        const newStatus = $('#editStudentStatus').val();
        const newEmail = $('#editStudentEmail').val().trim();
        const newPhone = $('#editStudentPhone').val().trim();
        const newRemark = $('#editStudentRemark').val().trim();

        if (!newId) {
            alert('กรุณากรอกรหัสนักศึกษา');
            $('#editStudentId').focus();
            return;
        }
        if (!newName) {
            alert('กรุณากรอกชื่อ-นามสกุลนักศึกษา');
            $('#editStudentName').focus();
            return;
        }

        const studentData = {
            original_student_id: originalId,
            student_id: newId,
            nickname: newNickname,
            name: newName,
            type: newType,
            project_no: newProjectNo || null,
            status_student: newStatus,
            email: newEmail,
            phone: newPhone,
            remark: newRemark,
            is_edited: true
        };

        // ปิด Modal
        const modalEl = document.getElementById('modalEditStudent');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        } else {
            $('#modalEditStudent').modal('hide');
        }

        // ถ้าหน้าเว็บลงทะเบียน Callback ไว้ ให้ส่งไปจัดการต่อ
        if (window.ModalEditCallbacks && typeof window.ModalEditCallbacks.onSaveStudent === 'function') {
            window.ModalEditCallbacks.onSaveStudent(studentData, originalId);
            return;
        }

        // Fallback ค่าเริ่มต้นสำหรับหน้า Preview CSV (Memory Mode)
        if (window.globalParsedData) {
            let s = null;
            if (Array.isArray(window.globalParsedData.students)) {
                s = window.globalParsedData.students.find(item => String(item.student_id) === String(originalId));
            } else if (window.globalParsedData.students && window.globalParsedData.students[originalId]) {
                s = window.globalParsedData.students[originalId];
            } else if (window.globalParsedData.students_without_project) {
                s = window.globalParsedData.students_without_project.find(item => String(item.student_id) === String(originalId));
            }

            if (s) {
                Object.assign(s, studentData);

                if (newId !== originalId && !Array.isArray(window.globalParsedData.students) && window.globalParsedData.students[originalId]) {
                    delete window.globalParsedData.students[originalId];
                    window.globalParsedData.students[newId] = s;
                }

                // ซิงค์รหัสและชื่อในกลุ่มโครงงาน
                if (window.globalParsedData.projects && window.globalParsedData.projects.length > 0) {
                    window.globalParsedData.projects.forEach(function (p) {
                        if (p.student_ids && p.student_ids.length > 0) {
                            for (let i = 0; i < p.student_ids.length; i++) {
                                if (String(p.student_ids[i]) === String(originalId)) {
                                    p.student_ids[i] = newId;
                                }
                            }
                        }
                        if (p.members && p.members.length > 0) {
                            p.members.forEach(function (m) {
                                if (String(m.student_id) === String(originalId)) {
                                    m.student_id = newId;
                                    m.name = newName;
                                    m.nickname = newNickname;
                                }
                            });
                        }
                        if (s.status_student === 'no_project') {
                            if (p.student_ids) {
                                p.student_ids = p.student_ids.filter(id => String(id) !== String(newId) && String(id) !== String(originalId));
                            }
                            if (p.members) {
                                p.members = p.members.filter(m => String(m.student_id) !== String(newId) && String(m.student_id) !== String(originalId));
                            }
                        }
                    });
                }

                if (typeof window.recalculateSummary === 'function') window.recalculateSummary();
                if (typeof window.renderLivePreview === 'function') window.renderLivePreview(window.globalParsedData);
                return;
            }
        }

        // โหมดฐานข้อมูลจริง (สำหรับหน้ารายการข้อมูลหลัก page_student.blade.php)
        const studentIdTarget = originalId || studentData.student_id;
        if (studentIdTarget) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || window.CSRF_TOKEN || '';
            const btnSave = $('#modalEditStudent button[onclick="saveEditStudent()"]');
            btnSave.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

            $.ajax({
                url: '/pc-csmju/admin/student/update/' + studentIdTarget,
                type: 'POST',
                data: JSON.stringify(studentData),
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (res) {
                    btnSave.prop('disabled', false).html('<i class="fas fa-check me-1"></i> บันทึกการแก้ไข');
                    if (typeof iziToast !== 'undefined') {
                        iziToast.success({
                            title: 'สำเร็จ',
                            message: res.message || 'บันทึกการแก้ไขข้อมูลนักศึกษาเรียบร้อยแล้ว',
                            position: 'topRight'
                        });
                    }
                    setTimeout(function () { location.reload(); }, 700);
                },
                error: function (xhr) {
                    btnSave.prop('disabled', false).html('<i class="fas fa-check me-1"></i> บันทึกการแก้ไข');
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการบันทึกข้อมูลนักศึกษา';
                    if (typeof iziToast !== 'undefined') {
                        iziToast.error({ title: 'ไม่สำเร็จ', message: msg, position: 'topRight' });
                    } else {
                        alert(msg);
                    }
                }
            });
        }
    }

    // ==============================================================================
    // 5. ลงทะเบียน Event Listeners สำหรับ Dropdown ใน Modal และปุ่มแก้ไขในตาราง
    // ==============================================================================
    $(document).ready(function () {
        // ดักฟังการคลิกปุ่มแก้ไขโครงงานในตาราง (.btn-edit-project)
        $(document).on('click', '.btn-edit-project', function () {
            const $btn = $(this);
            // อ่านค่าจาก data-* ทีละตัวแบบ HTML ธรรมดา ไม่ต้องใช้ json_encode
            let projectData = $btn.data('project');
            if (!projectData || typeof projectData !== 'object') {
                const rawSpecial = $btn.attr('data-special-advisors') || '';
                const specialList = rawSpecial ? rawSpecial.split(',').map(s => s.trim()).filter(s => s.length > 0) : [];
                projectData = {
                    id: $btn.attr('data-id'),
                    type: $btn.attr('data-type') || 'project',
                    title_th: $btn.attr('data-title-th') || '',
                    title_en: $btn.attr('data-title-en') || '',
                    company_name: $btn.attr('data-company') || '',
                    remark: $btn.attr('data-remark') || '',
                    advisor_president: $btn.attr('data-advisor-president') || '',
                    advisor_president_id: $btn.attr('data-advisor-president-id') || null,
                    advisor_committee_1: $btn.attr('data-advisor-committee1') || '',
                    advisor_committee_1_id: $btn.attr('data-advisor-committee1-id') || null,
                    advisor_committee_2: $btn.attr('data-advisor-committee2') || '',
                    advisor_committee_2_id: $btn.attr('data-advisor-committee2-id') || null,
                    special_advisors: specialList
                };
            }
            openEditProjectModal(projectData);
        });

        // ดักฟังการคลิกปุ่มแก้ไขนักศึกษาในตาราง (.btn-edit-student)
        $(document).on('click', '.btn-edit-student', function () {
            const $btn = $(this);
            // อ่านค่าจาก data-* ทีละตัวแบบ HTML ธรรมดา ไม่ต้องใช้ json_encode
            let studentData = $btn.data('student');
            if (!studentData || typeof studentData !== 'object') {
                studentData = {
                    id: $btn.attr('data-id'),
                    student_id: $btn.attr('data-student-id'),
                    name: $btn.attr('data-name') || '',
                    nickname: $btn.attr('data-nickname') || '',
                    type: $btn.attr('data-type') || 'project',
                    status_student: $btn.attr('data-status') || 'doing',
                    email: $btn.attr('data-email') || '',
                    phone: $btn.attr('data-phone') || '',
                    remark: $btn.attr('data-remark') || ''
                };
            }
            openEditStudentModal(studentData);
        });
        $(document).on('change', '#editProjectAdvisorPresidentSelect', function () {
            const val = $(this).val();
            const opt = $(this).find('option:selected');
            const id = opt.data('id') || '';
            if (val) {
                $('#editProjectAdvisorPresident').val(val);
                $('#editProjectAdvisorPresidentId').val(id);
            } else {
                $('#editProjectAdvisorPresidentId').val('');
            }
        });

        $(document).on('change', '#editProjectAdvisorCommittee1Select', function () {
            const val = $(this).val();
            const opt = $(this).find('option:selected');
            const id = opt.data('id') || '';
            if (val) {
                $('#editProjectAdvisorCommittee1').val(val);
                $('#editProjectAdvisorCommittee1Id').val(id);
            } else {
                $('#editProjectAdvisorCommittee1Id').val('');
            }
        });

        $(document).on('change', '#editProjectAdvisorCommittee2Select', function () {
            const val = $(this).val();
            const opt = $(this).find('option:selected');
            const id = opt.data('id') || '';
            if (val) {
                $('#editProjectAdvisorCommittee2').val(val);
                $('#editProjectAdvisorCommittee2Id').val(id);
            } else {
                $('#editProjectAdvisorCommittee2Id').val('');
            }
        });

        $(document).on('change', '#editProjectAdvisorSpecialSelect', function () {
            const val = $(this).val();
            const opt = $(this).find('option:selected');
            const id = opt.data('id') || '';
            if (val) {
                $('#editProjectAdvisorSpecial').val(val);
                $('#editProjectAdvisorSpecialId').val(id);
            }
        });

        $(document).on('input', '#editProjectAdvisorPresident', function () {
            syncCustomSelectFromInput('#editProjectAdvisorPresidentSelect', '#editProjectAdvisorPresidentId', $(this).val(), 'teacher');
        });
        $(document).on('input', '#editProjectAdvisorCommittee1', function () {
            syncCustomSelectFromInput('#editProjectAdvisorCommittee1Select', '#editProjectAdvisorCommittee1Id', $(this).val(), 'teacher');
        });
        $(document).on('input', '#editProjectAdvisorCommittee2', function () {
            syncCustomSelectFromInput('#editProjectAdvisorCommittee2Select', '#editProjectAdvisorCommittee2Id', $(this).val(), 'teacher');
        });
        $(document).on('input', '#editProjectAdvisorSpecial', function () {
            syncCustomSelectFromInput('#editProjectAdvisorSpecialSelect', '#editProjectAdvisorSpecialId', $(this).val(), null);
        });

        $(document).on('change', '#editStudentStatus', function () {
            if ($(this).val() === 'no_project') {
                $('#editStudentProjectNo').val('');
            }
        });
    });

    // ส่งออกฟังก์ชันสู่ Global Scope เพื่อให้หน้าต่างอื่นเรียกใช้ได้โดยตรง
    window.openEditProjectModal = openEditProjectModal;
    window.saveEditProject = saveEditProject;
    window.openEditStudentModal = openEditStudentModal;
    window.saveEditStudent = saveEditStudent;
    window.addSpecialAdvisorRow = addSpecialAdvisorRow;
    window.removeSpecialAdvisorRow = removeSpecialAdvisorRow;
    window.onSpecialAdvisorSelectChange = onSpecialAdvisorSelectChange;
    window.onSpecialAdvisorNameInput = onSpecialAdvisorNameInput;
    window.showEmptySpecialAdvisorNotice = showEmptySpecialAdvisorNotice;
    window.findTeacherMatch = findTeacherMatch;
    window.syncAdvisorField = syncAdvisorField;
    window.syncCustomSelectFromInput = syncCustomSelectFromInput;

})(window, window.jQuery);
