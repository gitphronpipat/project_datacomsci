/**
 * ==============================================================================
 * ไฟล์สคริปต์: page_preview_info_add_studentandproject.js
 * ที่ตั้ง: public/js/student/page_preview_info_add_studentandproject.js
 * คำอธิบาย: จัดการระบบอัปโหลดไฟล์ CSV นักศึกษา, ตรวจสอบ (Live Preview),
 *           และบันทึกข้อมูลนักศึกษาพร้อมกลุ่มโครงงานเข้าสู่ฐานข้อมูล
 * ==============================================================================
 */

// ==============================================================================
// ก้อนที่ 1: การตั้งค่า (CONFIG) และตัวแปรสถานะระบบ (STATE MANAGEMENT)
// ==============================================================================
// อ่านค่า Config ที่ส่งต่อมาจาก Blade (ถ้าไม่มีให้ใช้ค่า URL มาตรฐานของระบบ)
const studentPreviewConfig = window.STUDENT_PREVIEW_CONFIG || {
    previewUrl: '/pc-csmju/admin/student/import/preview',
    storeUrl: '/pc-csmju/admin/student/import/store',
    redirectUrl: '/pc-csmju/admin/student',
    csrfToken: $('meta[name="csrf-token"]').attr('content') || ''
};

// ตัวแปรเก็บไฟล์และข้อมูลที่อ่านได้จากไฟล์ CSV
let selectedCsvFile = null;      // เก็บ Object File ที่ผู้ใช้เลือก
let globalParsedData = null;     // เก็บก้อน JSON ข้อมูลนักศึกษาและโครงงานที่แปลงแล้ว
window.globalParsedData = null;

// คีย์สำหรับจัดการ Session ใน Browser ป้องกันข้อมูลหายเมื่อกด Refresh (F5)
    const PREVIEW_SESSION_KEY = 'STUDENT_PREVIEW_SESSION_DATA';
    const PREVIEW_TAB_KEY = 'STUDENT_PREVIEW_ACTIVE_TAB';

    /**
     * ฟังก์ชันบันทึกสถานะพรีวิวลง sessionStorage ในเบราว์เซอร์
     * (คงอยู่ตลอดการรีเฟรชหน้าจอ F5 แต่จะถูกล้างเมื่อปิดแท็บ หรือกดเลือกไฟล์ใหม่ หรือคลิกไปหน้าอื่น)
     */
    function persistPreviewSession() {
        const dataToSave = globalParsedData || window.globalParsedData;
        if (dataToSave) {
            try {
                sessionStorage.setItem(PREVIEW_SESSION_KEY, JSON.stringify(dataToSave));
                window.globalParsedData = dataToSave;
            } catch (e) {
                console.warn('Cannot persist preview session to sessionStorage:', e);
            }
        } else {
            sessionStorage.removeItem(PREVIEW_SESSION_KEY);
            window.globalParsedData = null;
        }
    }
    window.persistPreviewSession = persistPreviewSession;

    /**
     * ฟังก์ชันล้างข้อมูลพรีวิวออกจาก Session
     */
    function clearPreviewSession() {
        sessionStorage.removeItem(PREVIEW_SESSION_KEY);
        sessionStorage.removeItem(PREVIEW_TAB_KEY);
        globalParsedData = null;
        window.globalParsedData = null;
        selectedCsvFile = null;
    }
    window.clearPreviewSession = clearPreviewSession;

    /**
     * ตรวจสอบและกู้คืนข้อมูลพรีวิวจาก sessionStorage เมื่อเปิดหรือรีเฟรชหน้าจอ
     */
    function restorePreviewSessionIfExists() {
        const saved = sessionStorage.getItem(PREVIEW_SESSION_KEY);
        if (!saved) return false;

        try {
            const parsed = JSON.parse(saved);
            if (parsed && (parsed.projects || parsed.students)) {
                globalParsedData = parsed;
                window.globalParsedData = parsed;
                const savedTab = sessionStorage.getItem(PREVIEW_TAB_KEY);
                if (savedTab) {
                    window.currentPreviewTab = savedTab;
                }
                renderLivePreview(globalParsedData);
                return true;
            }
        } catch (e) {
            console.warn('Failed to parse saved preview session:', e);
            clearPreviewSession();
        }
        return false;
    }
    window.restorePreviewSessionIfExists = restorePreviewSessionIfExists;

// ==============================================================================
// ก้อนที่ 2: สลับโหมดการเพิ่มข้อมูล (CSV Import VS กรอกข้อมูลทีละคน)
// ==============================================================================
/**
 * ฟังก์ชันสลับโหมดระหว่าง 'csv' (นำเข้าไฟล์) และ 'manual' (กรอกข้อมูลทีละคน)
 * @param {string} mode - 'csv' หรือ 'manual'
 */
function switchAddMode(mode) {
    $('#mainStepTabs').removeClass('d-none');
    $('#btnBackPage').removeClass('d-none');
    if (mode === 'csv') {
        $('#tabModeCsv').addClass('active');
        $('#tabModeManual').removeClass('active');
        $('#modeCsvContainer').removeClass('d-none');
        $('#modeManualContainer').addClass('d-none');
    } else {
        $('#tabModeManual').addClass('active');
        $('#tabModeCsv').removeClass('active');
        $('#modeManualContainer').removeClass('d-none');
        $('#modeCsvContainer').addClass('d-none');
    }
}
window.switchAddMode = switchAddMode; // เผื่อเรียกจาก onclick ใน Blade

// ==============================================================================
// ก้อนที่ 3: จัดการเลือกและลากวางไฟล์ CSV (แบบเรียบง่าย สไตล์เดียวกับอัปโหลดรูปภาพ)
// ==============================================================================
$(document).ready(function () {
    // กำหนดให้โหมดเริ่มต้นเป็น 'csv' (นำเข้าจากไฟล์) ให้มีเส้นใต้ Active สีน้ำเงินเสมอ
    switchAddMode('csv');

    // ตรวจสอบและกู้คืนข้อมูลพรีวิวอัตโนมัติหากมีการรีเฟรชหน้าจอ (F5) ข้อมูลจะไม่สูญหาย
    restorePreviewSessionIfExists();

    // 1. เคลียร์ค่า input file เมื่อถูกคลิก เพื่อให้เลือกไฟล์เดิมซ้ำได้เสมอ
    $(document).on('click', '#csvFileInput', function () {
        this.value = '';
    });

    // คลิกที่พื้นที่ว่างในกล่องเพื่อเปิด File Dialog
    $(document).on('click', '#csvDropzoneBox', function (e) {
        if ($(e.target).closest('label, button, a, input').length > 0) return;
        const input = document.getElementById('csvFileInput');
        if (input) input.click();
    });

    // 2. ดักฟังเมื่อเลือกไฟล์ผ่าน File Dialog ปกติ
    $(document).on('change', '#csvFileInput', function () {
        if (this.files && this.files.length > 0) {
            handleFileSelected(this.files[0]);
        }
    });

    // 3. ระบบ Drag & Drop เรียบง่าย (ต้อง e.preventDefault() ทุก event เพื่อกันเบราว์เซอร์เปิดไฟล์เอง)
    $(document).on('dragover dragenter', '#csvDropzoneBox', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('bg-white border-primary shadow');
    });

    $(document).on('dragleave', '#csvDropzoneBox', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('bg-white border-primary shadow');
    });

    $(document).on('drop', '#csvDropzoneBox', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('bg-white border-primary shadow');

        const dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files.length > 0) {
            handleFileSelected(dt.files[0]);
        }
    });

    /**
     * ตรวจสอบชนิดไฟล์ และอัปเดต UI แสดงชื่อไฟล์พร้อมขนาด
     * @param {File} file 
     */
    function handleFileSelected(file) {
        if (!file) return;
        const fileName = file.name.toLowerCase();
        if (!fileName.endsWith('.csv') && !fileName.endsWith('.txt')) {
            alert('กรุณาเลือกไฟล์ .csv หรือ .txt เท่านั้นครับ');
            const input = document.getElementById('csvFileInput');
            if (input) input.value = '';
            selectedCsvFile = null;
            $('#btnParseCsv').prop('disabled', true);
            return;
        }

        selectedCsvFile = file;
        $('#fileNameText').text(file.name);
        $('#fileSizeText').text((file.size / 1024).toFixed(1) + ' KB');
        $('#dropzonePrompt').addClass('d-none');
        $('#fileSelectedBadge').removeClass('d-none');
        $('#btnParseCsv').prop('disabled', false);
    }

    // ==============================================================================
    // ก้อนที่ 4: ส่งไฟล์ CSV ไปให้ CONTROLLER ทำการ PREVIEW ข้อมูล (AJAX)
    // ==============================================================================
    $(document).on('click', '#btnParseCsv', function () {
        const input = document.getElementById('csvFileInput');
        const fileToSend = selectedCsvFile || 
            (input && input.files && input.files.length > 0 ? input.files[0] : null);

        if (!fileToSend) {
            alert('กรุณาเลือกไฟล์ CSV ก่อนครับ');
            return;
        }

        // เตรียมข้อมูลส่งแบบ Multipart Form Data
        const formData = new FormData();
        formData.append('csv_file', fileToSend);
        formData.append('_token', studentPreviewConfig.csrfToken);

        // แสดงสถานะ Loading ให้ผู้ใช้เห็นชัดเจน
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังอ่านและประมวลผลข้อมูล...');

        $.ajax({
            url: studentPreviewConfig.previewUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $btn.prop('disabled', false).html(originalHtml);
                if (res.status === 'success') {
                    globalParsedData = res;
                    window.globalParsedData = res;
                    renderLivePreview(res);
                } else {
                    alert(res.message || 'เกิดข้อผิดพลาดในการประมวลผลไฟล์');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                const msg = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
                alert(msg);
            }
        });
    });

    // ==============================================================================
    // ก้อนที่ 8: ฟังก์ชันช่วยเหลือในโหมดกรอกข้อมูลทีละคน (MANUAL FORM HELPERS)
    // ==============================================================================
    // 1. ซิงค์รหัสนักศึกษาเป็น Username และ Password ให้อัตโนมัติ (แต่ไม่ไปแตะปีที่ทำโครงงาน เพื่อรองรับนักศึกษาตกค้าง)
    $('#student_id').on('input', function () {
        const val = $(this).val();
        $('#username').val(val);
        $('#password').val(val);
        if (typeof updateCharCounter === 'function') {
            const usernameEl = document.getElementById('username');
            const passwordEl = document.getElementById('password');
            if (usernameEl) updateCharCounter(usernameEl);
            if (passwordEl) updateCharCounter(passwordEl);
        }
    });

    // 2. อัปโหลดรูปภาพโปรไฟล์ (รองรับทั้งคลิกปุ่มและคลิกพื้นที่กล่องรูป)
    $('#btnTriggerUpload, #avatarContainer').on('click', function (e) {
        e.stopPropagation();
        $('#profile_picture').click();
    });

    $('#profile_picture').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result).show();
                $('#avatarPlaceholder').hide();
            };
            reader.readAsDataURL(file);
        }
    });

    // 3. จัดการสลับโหมดกลุ่มโครงงาน (2 โหมด: เพิ่มเข้ากลุ่มเดิม / สร้างกลุ่มใหม่)
    function switchProjectMode(mode) {
        $('.project-mode-card').css({
            'border-color': '#cbd5e1',
            'background-color': '#ffffff'
        }).removeClass('active-mode');

        if (mode === 'create_new') {
            $('#cardModeCreate').css({
                'border-color': '#3b82f6',
                'background-color': '#eff6ff'
            }).addClass('active-mode');
            $('#boxJoinExisting').addClass('d-none');
            $('#boxCreateNew').removeClass('d-none');
        } else {
            // โหมดเริ่มต้น: join_existing
            $('#cardModeJoin').css({
                'border-color': '#3b82f6',
                'background-color': '#eff6ff'
            }).addClass('active-mode');
            $('#boxJoinExisting').removeClass('d-none');
            $('#boxCreateNew').addClass('d-none');
            updateExistingProjectsDropdown();
        }
    }

    // คลิกที่การ์ดเพื่อเลือกโหมด
    $('.project-mode-card').on('click', function (e) {
        if (!$(e.target).is('input[type="radio"]')) {
            const radio = $(this).find('input[name="project_mode"]');
            if (!radio.prop('checked')) {
                radio.prop('checked', true).trigger('change');
            }
        }
    });

    $('input[name="project_mode"]').on('change', function () {
        switchProjectMode($(this).val());
    });

    // ฟังก์ชันเชื่อมโยงสถานะนักศึกษากับการแสดงผลของหมวดที่ 2 (ข้อมูลกลุ่มและโครงงาน)
    function syncProjectSectionWithStudentStatus() {
        const status = $('#status_student').val();
        const $section = $('#sectionProjectGroup');

        if (status === 'no_project') {
            // หากเลือก "ยังไม่มีโครงงาน" ให้ซ่อนหมวดข้อมูลกลุ่มทั้งหมด
            $section.addClass('d-none');
        } else {
            // หากเลือกสถานะอื่น (เช่น กำลังทำโครงงาน, ผ่านโครงงานแล้ว) ให้แสดงหมวดกลุ่ม
            $section.removeClass('d-none');
            let currentMode = $('input[name="project_mode"]:checked').val();
            if (currentMode !== 'create_new' && currentMode !== 'join_existing') {
                $('#modeJoin').prop('checked', true);
                currentMode = 'join_existing';
            }
            switchProjectMode(currentMode);
        }
    }

    // ผูก Event เมื่อเปลี่ยนสถานะนักศึกษา
    $('#status_student').on('change', function () {
        syncProjectSectionWithStudentStatus();
    });

    // ตรวจสอบและซิงค์การแสดงผลทันทีเมื่อโหลดหน้าเว็บ
    syncProjectSectionWithStudentStatus();

    // 4. ตัวกรองและแสดงรายชื่อกลุ่มโครงงานในโหมด "เพิ่มเข้ากลุ่มเดิม"
    function updateExistingProjectsDropdown() {
        const selectedYear = String($('#filter_project_year').val() || '').trim();
        const selectedType = String($('input[name="filter_project_type"]:checked').val() || 'project').trim().toLowerCase();
        const $select = $('#existing_project_id');
        const $infoBox = $('#selectedProjectInfo');

        $select.empty();
        $infoBox.addClass('d-none').html('');

        const allProjects = Array.isArray(window.EXISTING_PROJECTS) ? window.EXISTING_PROJECTS : [];

        // กรองตามปีและประเภทวิชา (โครงงาน 498 / สหกิจ 497)
        const filtered = allProjects.filter(function (p) {
            const pYear = String(p.project_year || '').trim();
            const pType = String(p.type_project || 'project').trim().toLowerCase();
            return (pYear === selectedYear) && (pType === selectedType);
        });

        if (filtered.length === 0) {
            $select.append('<option value="">-- ไม่พบกลุ่มโครงงานในปี พ.ศ. ' + selectedYear + ' สำหรับประเภทนี้ --</option>');
            return;
        }

        $select.append('<option value="">-- กรุณาเลือกกลุ่มโครงงาน (' + filtered.length + ' กลุ่ม) --</option>');

        filtered.forEach(function (p) {
            const titleTh = p.project_name_th || '';
            const titleEn = p.project_name_en || '';
            let displayTitle = titleTh || titleEn || 'กลุ่มโครงงานไม่มีชื่อ';
            if (displayTitle.length > 60) {
                displayTitle = displayTitle.substring(0, 57) + '...';
            }
            const count = p.members_count || 0;
            const optText = displayTitle + ' (สมาชิก ' + count + ' คน)';
            $select.append($('<option>', {
                value: p.id,
                text: optText
            }));
        });
    }

    // เมื่อเปลี่ยนปีหรือเปลี่ยนประเภทวิชา ให้กรองรายชื่อกลุ่มโครงงานใหม่ทันที
    $('#filter_project_year').on('change', function () {
        updateExistingProjectsDropdown();
    });

    $('input[name="filter_project_type"]').on('change', function () {
        updateExistingProjectsDropdown();
    });

    // แสดงพรีวิวรายละเอียดสมาชิกเมื่อเลือกกลุ่มโครงงาน
    $('#existing_project_id').on('change', function () {
        const projectId = $(this).val();
        const $infoBox = $('#selectedProjectInfo');

        if (!projectId) {
            $infoBox.addClass('d-none').html('');
            return;
        }

        const allProjects = Array.isArray(window.EXISTING_PROJECTS) ? window.EXISTING_PROJECTS : [];
        const project = allProjects.find(p => String(p.id) === String(projectId));

        if (project) {
            const titleTh = project.project_name_th ? '<div><strong>ชื่อไทย:</strong> ' + project.project_name_th + '</div>' : '';
            const titleEn = project.project_name_en ? '<div><strong>ชื่ออังกฤษ:</strong> ' + project.project_name_en + '</div>' : '';
            const advisor = project.advisor_name ? '<div><strong>อาจารย์ที่ปรึกษา:</strong> ' + project.advisor_name + '</div>' : '';
            let membersList = 'ยังไม่มีสมาชิกในกลุ่ม';
            if (project.member_names && project.member_names.length > 0) {
                membersList = project.member_names.join(', ');
            }
            const members = '<div><strong>สมาชิกเดิม (' + (project.members_count || 0) + ' คน):</strong> ' + membersList + '</div>';

            $infoBox.html(
                '<div class="text-primary fw-bold mb-1"><i class="fas fa-info-circle me-1"></i> รายละเอียดกลุ่มโครงงานเป้าหมาย</div>' +
                '<div class="text-secondary small ps-3" style="line-height: 1.6;">' +
                    titleTh + titleEn + advisor + members +
                '</div>'
            ).removeClass('d-none');
        } else {
            $infoBox.addClass('d-none').html('');
        }
    });

    // 5. สลับแสดงกล่องสถานประกอบการในโหมด "สร้างกลุ่มใหม่" เมื่อเลือกเป็นสหกิจ
    $('input[name="new_project_type"]').on('change', function () {
        if ($(this).val() === 'coop') {
            $('#boxNewCompany').removeClass('d-none');
        } else {
            $('#boxNewCompany').addClass('d-none');
        }
    });
});

// ==============================================================================
// ก้อนที่ 5: ฟังก์ชันสร้างตารางตรวจสอบและสถิติ (LIVE PREVIEW RENDERER)
// ==============================================================================
/**
 * เรนเดอร์ข้อมูล JSON ที่ได้จาก Controller ออกมาเป็นตาราง HTML
 * @param {Object} data - ก้อนข้อมูลตอบกลับจาก Controller (summary, projects, students, ...)
 */
function renderLivePreview(data) {
    if (data) {
        globalParsedData = data;
        window.globalParsedData = data;
    }
    // 1. สลับกล่องแสดงผล: ซ่อนปุ่มย้อนกลับ, ซ่อนแท็บสลับโหมดหลัก, ซ่อนกล่องอัปโหลด แล้วแสดงกล่องพรีวิว
    $('#btnBackPage').addClass('d-none');
    $('#mainStepTabs').addClass('d-none');
    $('#stepUploadBox').addClass('d-none');
    $('#stepPreviewBox').removeClass('d-none');


    // 2. เติมตัวเลขสถิติลงใน Card ด้านบน
    $('#statTotalStudents').text(data.summary.total_students + ' คน');
    $('#statStudentsDetail').text('มีกลุ่ม ' + data.summary.students_with_project_count + ' / ไม่มีกลุ่ม ' + data.summary.students_without_project_count);

    // 2.1 คำนวณและแสดงสถิตินักศึกษาแยกตาม 2 ตัวหน้าของรหัส (เช่น รหัส 66, 65)
    const studentsList = Array.isArray(data.students)
        ? data.students
        : Object.values(data.students || {});

    const batchCounts = {};
    studentsList.forEach(function (s) {
        if (s.student_id) {
            const sidStr = String(s.student_id).trim();
            const batch = sidStr.length >= 2 ? sidStr.substring(0, 2) : 'อื่นๆ';
            batchCounts[batch] = (batchCounts[batch] || 0) + 1;
        }
    });

    const sortedBatches = Object.keys(batchCounts).sort().reverse();
    let batchBadgesHtml = '';
    sortedBatches.forEach(function (batch, bIdx) {
        const count = batchCounts[batch];
        const isMain = (bIdx === 0);
        const badgeClass = isMain 
            ? 'badge bg-primary-subtle text-primary border border-primary-subtle' 
            : 'badge bg-secondary-subtle text-secondary border border-secondary-subtle';
        batchBadgesHtml += '<span class="' + badgeClass + ' font-monospace px-2 py-1" style="font-size: 0.72rem;">' +
            'รหัส ' + batch + ': ' + count + ' คน' +
            '</span>';
    });
    $('#statStudentBatches').html(batchBadgesHtml || '<span class="text-muted small">-</span>');

    $('#statTotalProjects').text(data.summary.total_projects + ' กลุ่ม');
    $('#statProjectsDetail').text('โครงงาน ' + data.summary.project_498_count + ' / สหกิจ ' + data.summary.coop_497_count);

    // 2.2 คำนวณและแสดงสถิตินักศึกษาแยกรหัส 2 ตัวหน้าสำหรับ โครงงาน vs สหกิจ
    const projectBatchCounts = {};
    const coopBatchCounts = {};

    if (data.projects && data.projects.length > 0) {
        data.projects.forEach(function (p) {
            const isCoop = (p.type === 'coop');
            const targetCounts = isCoop ? coopBatchCounts : projectBatchCounts;
            if (p.members && p.members.length > 0) {
                p.members.forEach(function (m) {
                    if (m.student_id) {
                        const sidStr = String(m.student_id).trim();
                        const batch = sidStr.length >= 2 ? sidStr.substring(0, 2) : 'อื่นๆ';
                        targetCounts[batch] = (targetCounts[batch] || 0) + 1;
                    }
                });
            } else if (p.student_ids && p.student_ids.length > 0) {
                p.student_ids.forEach(function (sid) {
                    const sidStr = String(sid).trim();
                    const batch = sidStr.length >= 2 ? sidStr.substring(0, 2) : 'อื่นๆ';
                    targetCounts[batch] = (targetCounts[batch] || 0) + 1;
                });
            }
        });
    }

    function buildBatchBadges(countsObj, colorClass) {
        const batches = Object.keys(countsObj).sort().reverse();
        if (batches.length === 0) return '<span class="text-muted small">-</span>';
        let html = '';
        batches.forEach(function (b) {
            html += '<span class="badge bg-' + colorClass + '-subtle text-' + colorClass + ' border border-' + colorClass + '-subtle font-monospace px-1.5 py-0.5 me-1" style="font-size: 0.70rem;">' +
                'รหัส ' + b + ': ' + countsObj[b] + ' คน' +
                '</span>';
        });
        return html;
    }

    const projBadgesHtml = buildBatchBadges(projectBatchCounts, 'primary');
    const coopBadgesHtml = buildBatchBadges(coopBatchCounts, 'success');

    const projectBatchesHtml = '<div class="d-flex flex-column gap-1" style="font-size: 0.72rem;">' +
        '<div class="d-flex flex-wrap align-items-center gap-1">' +
            '<span class="fw-semibold text-primary" style="font-size: 0.72rem;"><i class="fas fa-project-diagram me-1"></i>โครงงาน:</span>' +
            projBadgesHtml +
        '</div>' +
        '<div class="d-flex flex-wrap align-items-center gap-1 mt-1">' +
            '<span class="fw-semibold text-success" style="font-size: 0.72rem;"><i class="fas fa-building me-1"></i>สหกิจ:</span>' +
            coopBadgesHtml +
        '</div>' +
    '</div>';

    $('#statProjectBatches').html(projectBatchesHtml);
    $('#statNoProjectStudents').text(data.summary.students_without_project_count + ' คน');

    // 2.3 คำนวณและแสดงสถิตินักศึกษาที่ยังไม่มีโครงงานแยกตามรหัส 2 ตัวหน้า (เช่น 66, 65)
    const noProjBatchCounts = {};
    if (data.students_without_project && data.students_without_project.length > 0) {
        data.students_without_project.forEach(function (s) {
            if (s.student_id) {
                const sidStr = String(s.student_id).trim();
                const batch = sidStr.length >= 2 ? sidStr.substring(0, 2) : 'อื่นๆ';
                noProjBatchCounts[batch] = (noProjBatchCounts[batch] || 0) + 1;
            }
        });
    }

    const noProjBatches = Object.keys(noProjBatchCounts).sort().reverse();
    let noProjBadgesHtml = '';
    if (noProjBatches.length > 0) {
        noProjBatches.forEach(function (b) {
            noProjBadgesHtml += '<span class="badge bg-warning-subtle text-black border border-warning-subtle font-monospace px-1.5 py-0.5 me-1" style="font-size: 0.72rem;">' +
                'รหัส ' + b + ': ' + noProjBatchCounts[b] + ' คน' +
                '</span>';
        });
    } else {
        noProjBadgesHtml = '<span class="text-muted small">ไม่มีตกค้าง</span>';
    }
    $('#statNoProjectBatches').html(noProjBadgesHtml);

    $('#previewProjectYearInput').val(data.project_year);
    $('#previewProjectYearDisplay').text(data.project_year || '-');

    // ปรับตัวเลข Badge บนแท็บ
    $('#tabCountProjects498').text(data.summary.project_498_count || 0);
    $('#tabCountProjectsCoop').text(data.summary.coop_497_count || 0);
    $('#tabCountStudents').text(data.summary.total_students || 0);
    $('#tabCountNoProject').text(data.summary.students_without_project_count || 0);

    // --------------------------------------------------------------------------
    // เรียกใช้โมดูล StudentVerify สแกนหาข้อผิดปกติทั้งหมดจากไฟล์ student_verify_add.js
    // --------------------------------------------------------------------------
    const verificationResult = (window.StudentVerify && typeof window.StudentVerify.scanAll === 'function')
        ? window.StudentVerify.scanAll(data)
        : { anomaliesList: [], totalCount: 0 };
    const anomaliesList = verificationResult.anomaliesList || [];

    // --------------------------------------------------------------------------
    // ตาราง โครงงานกับสหกิจ
    // --------------------------------------------------------------------------
    let htmlProjects498 = '';
    let htmlProjectsCoop = '';
    let count498 = 0;
    let countCoop = 0;

    if (data.projects && data.projects.length > 0) {
        data.projects.forEach(function (p, idx) {
            const isCoop = (p.type === 'coop');
           
            const editedBadge = p.is_edited 
                ? '<div class="mb-1"><span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 small"><i class="fas fa-pen me-1"></i>แก้ไขแล้ว</span></div>' 
                : '';

            // ตรวจสอบความผิดปกติของกลุ่มโครงงานผ่านโมดูล StudentVerify
            const projVerify = (window.StudentVerify && typeof window.StudentVerify.verifyProject === 'function')
                ? window.StudentVerify.verifyProject(p)
                : { hasAnomaly: false, issues: [] };
            const projectHasAnomaly = projVerify.hasAnomaly;

            const rowAnomalyClass = projectHasAnomaly ? ' class="row-anomaly"' : '';
            const anomalyAlertBadge = projectHasAnomaly && projVerify.issues && projVerify.issues.length > 0
                ? '<div class="mt-1 d-flex flex-wrap gap-1">' + projVerify.issues.map(function (iss) { return iss.badge; }).join(' ') + '</div>'
                : '';

            // ประเภท
            const typeBadge = isCoop    
                ? '<span class="badge bg-success-subtle text-success border px-2 py-1"><i class="fas fa-building me-1"></i>สหกิจ (497)</span>'
                : '<span class="badge bg-primary-subtle text-primary border px-2 py-1"><i class="fas fa-project-diagram me-1"></i>โครงงาน (498)</span>';

            // รายชื่อสมาชิกในกลุ่ม (ถ้าคนไหนไม่ใช่ 04101 ให้แสดง Badge สีแดงเตือนชัดเจน)
            let membersList = '<ul class="list-unstyled mb-0">';
            if (p.members && p.members.length > 0) {
                p.members.forEach(function (m) {
                    const memberIsCs = isComputerScienceMajor(m.student_id);
                    if (!memberIsCs) {
                        membersList += '<li class="py-1 border-bottom border-light d-flex justify-content-between align-items-center bg-white px-2 rounded mb-1 border border-danger-subtle">' +
                            '<span><i class="fas fa-user-graduate text-danger me-1"></i><strong class="text-danger">' + m.name + '</strong></span>' +
                            '<span class="badge bg-danger text-white font-monospace" title="รหัสสาขาไม่ใช่ 04101"><i class="fas fa-exclamation-triangle me-1"></i>' + m.student_id + '</span>' +
                            '</li>';
                    } else {
                        membersList += '<li class="py-1 border-bottom border-light d-flex justify-content-between align-items-center">' +
                            '<span><i class="fas fa-user-graduate text-secondary me-1"></i><strong>' + m.name + '</strong></span>' +
                            '<span class="badge bg-light text-dark border font-monospace">' + m.student_id + '</span>' +
                            '</li>';
                    }
                });
            }
            membersList += '</ul>';

            // รายชื่ออาจารย์ที่ปรึกษา/กรรมการ
            let advisorsInfo = '<div><strong class="text-primary"><i class="fas fa-user-tie me-1"></i>ประธาน:</strong> ' + (p.advisor_president || '-') + '</div>';
            if (p.advisor_committee_1) {
                advisorsInfo += '<div><small class="text-muted"><strong>กรรมการ 1:</strong> ' + p.advisor_committee_1 + '</small></div>';
            }
            if (p.advisor_committee_2) {
                advisorsInfo += '<div><small class="text-muted"><strong>กรรมการ 2:</strong> ' + p.advisor_committee_2 + '</small></div>';
            }
            if (p.special_advisors && Array.isArray(p.special_advisors) && p.special_advisors.length > 0) {
                advisorsInfo += '<div class="mt-1 d-flex flex-column gap-1"><strong class="text-info small"><i class="fas fa-user-plus me-1"></i>ที่ปรึกษาพิเศษ:</strong>';
                p.special_advisors.forEach(function (sa) {
                    let badgeClass = 'bg-secondary-subtle text-secondary border';
                    let roleLabel = 'บุคคลภายนอก';
                    if (sa.role === 'teacher') {
                        badgeClass = 'bg-primary-subtle text-primary border';
                        roleLabel = 'อาจารย์';
                    } else if (sa.role === 'officer') {
                        badgeClass = 'bg-success-subtle text-success border';
                        roleLabel = 'เจ้าหน้าที่';
                    }
                    advisorsInfo += '<div class="small d-flex align-items-center gap-1">' +
                        '<span class="badge ' + badgeClass + ' px-1.5 py-0.5" style="font-size: 0.68rem;">' + roleLabel + '</span>' +
                        '<span>' + (sa.name || '-') + '</span>' +
                        '</div>';   
                });
                advisorsInfo += '</div>';
            } else if (p.advisor_special) {
                advisorsInfo += '<div><small class="text-info"><strong>พิเศษ:</strong> ' + p.advisor_special + '</small></div>';
            }
            //หมายเหตุ
            const remarkTd = p.remark 
                ? '<td><span class="small text-muted">' + p.remark + '</span></td>' 
                : '<td class="text-center text-muted small">-</td>';
            // ปุ่มแก้ไข ลบ
            const actionBtns = '<div class="btn-group" role="group">' +
                '<button type="button" class="btn btn-warning btn-sm" onclick="openEditProjectModal(' + idx + ')" title="แก้ไข">' +
                    '<i class="fas fa-edit"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-danger btn-sm" onclick="removeProject(' + idx + ')" title="ลบ">' +
                    '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
                '</div>';
            //สหกิจโปเจค
            if (isCoop) {
                countCoop++;
                htmlProjectsCoop += '<tr' + rowAnomalyClass + '>' +
                    '<td class="text-center">' + countCoop + '</td>' +
                    '<td class="text-center">' + typeBadge + anomalyAlertBadge + '</td>' +
                    '<td><div class="fw-bold text-dark">' + (p.title_th || '-') + '</div>' +
                        (p.title_en ? '<div class="text-muted small"><em>' + p.title_en + '</em></div>' : '') + '</td>' +
                    '<td>' + membersList + '</td>' +
                    '<td>' + advisorsInfo + '</td>' +
                    '<td class="text-center small text-muted">' + (p.company_name || '-') + '</td>' +
                    remarkTd +
                    '<td class="text-center text-nowrap">' + editedBadge + actionBtns + '</td>' +
                    '</tr>';
            } else {
                count498++;
                htmlProjects498 += '<tr' + rowAnomalyClass + '>' +
                    '<td class="text-center">' + count498 + '</td>' +
                    '<td class="text-center">' + typeBadge + anomalyAlertBadge + '</td>' +
                    '<td><div class="fw-bold text-dark">' + (p.title_th || '-') + '</div>' +
                        (p.title_en ? '<div class="text-muted small"><em>' + p.title_en + '</em></div>' : '') + '</td>' +
                    '<td>' + membersList + '</td>' +
                    '<td>' + advisorsInfo + '</td>' +
                    remarkTd +
                    '<td class="text-center text-nowrap">' + editedBadge + actionBtns + '</td>' +
                    '</tr>';
            }
        });
    }

    if (!htmlProjects498) {
        htmlProjects498 = '<tr><td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูลกลุ่มโครงงาน (498) ในไฟล์นี้</td></tr>';
    }
    if (!htmlProjectsCoop) {
        htmlProjectsCoop = '<tr><td colspan="8" class="text-center py-4 text-muted">ไม่พบข้อมูลกลุ่มสหกิจศึกษา (497) ในไฟล์นี้</td></tr>';
    }

    $('#tbodyPreviewProjects498').html(htmlProjects498);
    $('#tbodyPreviewProjectsCoop').html(htmlProjectsCoop);

    // --------------------------------------------------------------------------
    // 5.2 เรนเดอร์ตารางบัญชีรายชื่อนักศึกษาทั้งหมด (Students Table)
    // --------------------------------------------------------------------------
    let htmlStudents = '';

    if (studentsList && studentsList.length > 0) {
        studentsList.forEach(function (s, idx) {
            const isCs = isComputerScienceMajor(s.student_id);
            const studentRowClass = !isCs ? ' class="row-anomaly"' : '';
            const typeText = s.type === 'coop' ? 'สหกิจศึกษา' : 'โครงงาน';
            const statusBadge = s.status_student === 'doing'
                ? '<span class="badge bg-primary-subtle text-primary px-2 py-1">กำลังทำโครงงาน</span>'
                : (s.status_student === 'submitted'
                    ? '<span class="badge bg-info-subtle text-info px-2 py-1">ส่งแล้ว</span>'
                    : (s.status_student === 'passed'
                        ? '<span class="badge bg-success-subtle text-success px-2 py-1">ผ่านแล้ว</span>'
                        : (s.status_student === 'missing'
                            ? '<span class="badge bg-danger-subtle text-danger px-2 py-1">ขาดการติดต่อ</span>'
                            : '<span class="badge bg-warning-subtle text-warning px-2 py-1">ยังไม่มีโครงงาน</span>')));

            const existsBadge = s.exists_in_db
                ? '<span class="badge bg-secondary text-white" title="มีในระบบแล้ว จะข้ามไป">มีอยู่ในระบบ</span>'
                : '<span class="badge bg-success-subtle text-success">ใหม่</span>';

            const editedBadge = s.is_edited 
                ? '<div class="mb-1"><span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 small"><i class="fas fa-pen me-1"></i>แก้ไขแล้ว</span></div>' 
                : '';

            const studentRemarkTd = s.remark 
                ? '<td><span class="small text-muted">' + s.remark + '</span></td>' 
                : '<td class="text-center text-muted small">-</td>';

            const actionBtns = '<div class="btn-group" role="group">' +
                '<button type="button" class="btn btn-warning btn-sm" onclick="openEditStudentModal(\'' + s.student_id + '\')" title="แก้ไข">' +
                    'แก้ไข <i class="fas fa-edit"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-danger btn-sm" onclick="removeStudent(\'' + s.student_id + '\')" title="ลบ">' +
                    '<i class="fas fa-trash-alt"></i> ลบ' +
                '</button>' +
                '</div>';

            const nickTd = s.nickname 
                ? '<td class="text-center fw-semibold text-primary">' + s.nickname + '</td>' 
                : '<td class="text-center text-muted small">-</td>';

            const studentIdTd = !isCs
                ? '<td class="text-center">' +
                    '<span class="badge bg-danger text-white border border-danger font-monospace px-2 py-1 fs-6">' + s.student_id + '</span>' +
                    '<div class="text-danger small mt-1 fw-bold" style="font-size: 0.72rem;"><i class="fas fa-exclamation-circle me-1"></i>ไม่ใช่สาขา 04101</div>' +
                  '</td>'
                : '<td class="text-center"><span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">' + s.student_id + '</span></td>';

            htmlStudents += '<tr' + studentRowClass + '>' +
                '<td class="text-center">' + (idx + 1) + '</td>' +
                studentIdTd +
                nickTd +
                '<td><strong>' + s.name + '</strong></td>' +
                '<td class="text-center">' + typeText + '</td>' +
                '<td class="text-center">' + statusBadge + '</td>' +
                '<td class="text-center">' + (s.project_no ? 'กลุ่มที่ ' + s.project_no : '-') + '</td>' +
                '<td class="text-center">' + existsBadge + '</td>' +
                studentRemarkTd +
                '<td class="text-center text-nowrap">' + editedBadge + actionBtns + '</td>' +
                '</tr>';
        });
    } else {
        htmlStudents = '<tr><td colspan="10" class="text-center py-4 text-muted">ไม่พบข้อมูลนักศึกษาในไฟล์นี้</td></tr>';
    }
    $('#tbodyPreviewStudents').html(htmlStudents);

    // --------------------------------------------------------------------------
    // 5.3 เรนเดอร์ตารางนักศึกษาที่ยังไม่มีโครงงาน (No Project Students Table)
    // --------------------------------------------------------------------------
    let htmlNoProj = '';
    if (data.students_without_project && data.students_without_project.length > 0) {
        data.students_without_project.forEach(function (s, idx) {
            const isCs = isComputerScienceMajor(s.student_id);
            const noProjRowClass = !isCs ? ' class="row-anomaly"' : '';
            const editedBadge = s.is_edited 
                ? '<div class="mb-1"><span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 small"><i class="fas fa-pen me-1"></i>แก้ไขแล้ว</span></div>' 
                : '';
            const typeText = s.type === 'coop' ? 'สหกิจศึกษา' : 'โครงงาน';
            const studentRemarkTd = s.remark 
                ? '<td><span class="small text-muted">' + s.remark + '</span></td>' 
                : '<td class="text-center text-muted small">-</td>';

            const actionBtns = '<div class="btn-group" role="group">' +
                '<button type="button" class="btn btn-warning btn-sm" onclick="openEditStudentModal(\'' + s.student_id + '\')" title="แก้ไข">' +
                    'แก้ไข <i class="fas fa-edit"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-danger btn-sm" onclick="removeStudent(\'' + s.student_id + '\')" title="ลบ">' +
                    '<i class="fas fa-trash-alt"></i> ลบ' +
                '</button>' +
                '</div>';

            const nickTd = s.nickname 
                ? '<td class="text-center fw-semibold text-primary">' + s.nickname + '</td>' 
                : '<td class="text-center text-muted small">-</td>';

            const studentIdTd = !isCs
                ? '<td class="text-center">' +
                    '<span class="badge bg-danger text-white border border-danger font-monospace px-2 py-1 fs-6">' + s.student_id + '</span>' +
                    '<div class="text-danger small mt-1 fw-bold" style="font-size: 0.72rem;"><i class="fas fa-exclamation-circle me-1"></i>ไม่ใช่สาขา 04101</div>' +
                  '</td>'
                : '<td class="text-center"><span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">' + s.student_id + '</span></td>';

            htmlNoProj += '<tr' + noProjRowClass + '>' +
                '<td class="text-center">' + (idx + 1) + '</td>' +
                studentIdTd +
                nickTd +
                '<td><strong>' + s.name + '</strong></td>' +
                '<td class="text-center">' + typeText + '</td>' +
                '<td class="text-center"><span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1">ยังไม่มีโครงงาน (no_project)</span></td>' +
                studentRemarkTd +
                '<td class="text-center text-nowrap">' + editedBadge + actionBtns + '</td>' +
                '</tr>';
        });
    } else {
        htmlNoProj = '<tr><td colspan="8" class="text-center py-4 text-muted">ไม่มีนักศึกษาที่ตกค้าง ทุกคนมีกลุ่มครบถ้วน</td></tr>';
    }
    $('#tbodyPreviewNoProject').html(htmlNoProj);

    // --------------------------------------------------------------------------
    // 5.4 เรนเดอร์ตารางแท็บที่ 5: รายการที่พบข้อมูลผิดปกติ / ต้องตรวจสอบ (Anomalies)
    // --------------------------------------------------------------------------
    let htmlAnomalies = '';
    if (anomaliesList.length > 0) {
        anomaliesList.forEach(function (a, aIdx) {
            const isProject = (a.target_type === 'project');

            // 1. คอลัมน์ประเภท
            const typeBadge = isProject
                ? (a.project_type === 'coop'
                    ? '<span class="badge bg-success-subtle text-success border"><i class="fas fa-building me-1"></i>สหกิจ (497)</span>'
                    : '<span class="badge bg-primary-subtle text-primary border"><i class="fas fa-project-diagram me-1"></i>โครงงาน (498)</span>')
                : '<span class="badge bg-info-subtle text-dark border"><i class="fas fa-user-graduate me-1"></i>นักศึกษา</span>';

            // 2. คอลัมน์รหัส / ลำดับกลุ่ม
            const codeCol = isProject
                ? '<span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">กลุ่มที่ ' + (a.project_no || '-') + '</span>'
                : '<span class="badge bg-danger text-white font-monospace px-2 py-1 fs-6">' + (a.student_id || '-') + '</span>';

            // 3. คอลัมน์ชื่อรายการ
            let titleCol = '';
            if (isProject) {
                titleCol = '<div class="fw-bold text-dark">' + a.title + '</div>';
            } else {
                const nickBadge = a.nickname
                    ? ' <span class="badge bg-primary-subtle text-primary font-monospace ms-1">(' + a.nickname + ')</span>'
                    : '';
                titleCol = '<div><strong class="text-dark">' + a.name + '</strong>' + nickBadge + '</div>' +
                    '<div class="small text-muted mt-0.5"><i class="fas fa-folder me-1 text-secondary"></i>' + a.group + '</div>';
            }

            // 4. คอลัมน์ปัญหา / ข้อผิดปกติที่ตรวจพบ
            let issuesHtml = '';
            if (a.issues && a.issues.length > 0) {
                issuesHtml = '<div class="d-flex flex-wrap gap-1 mb-1">' +
                    a.issues.map(function (iss) { return iss.badge; }).join(' ') +
                    '</div>' +
                    '<div class="d-flex flex-column gap-0.5 text-muted small">' +
                    a.issues.map(function (iss) {
                        return '<div><i class="fas fa-angle-right me-1 text-danger"></i>' + iss.desc + '</div>';
                    }).join('') +
                    '</div>';
            } else {
                issuesHtml = a.issue_badge || '<span class="badge bg-warning text-dark">พบข้อผิดปกติ</span>';
            }

            // 5. คอลัมน์ปุ่มจัดการ
            const actionCol = isProject
                ? '<button type="button" class="btn btn-warning btn-sm shadow-sm" onclick="openEditProjectModal(' + a.project_idx + ')">' +
                    '<i class="fas fa-edit me-1"></i> แก้ไขโครงงาน' +
                  '</button>'
                : '<button type="button" class="btn btn-warning btn-sm shadow-sm" onclick="openEditStudentModal(\'' + a.student_id + '\')">' +
                    '<i class="fas fa-edit me-1"></i> แก้ไขนักศึกษา' +
                  '</button>';

            htmlAnomalies += '<tr class="row-anomaly">' +
                '<td class="text-center fw-bold text-danger">' + (aIdx + 1) + '</td>' +
                '<td class="text-center text-nowrap">' + typeBadge + '</td>' +
                '<td class="text-center text-nowrap">' + codeCol + '</td>' +
                '<td>' + titleCol + '</td>' +
                '<td>' + issuesHtml + '</td>' +
                '<td class="text-center text-nowrap">' + actionCol + '</td>' +
                '</tr>';
        });
    } else {
        htmlAnomalies = '<tr><td colspan="6" class="text-center py-5 text-success">' +
            '<i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>' +
            '<strong class="fs-6">ไม่พบข้อมูลผิดปกติในไฟล์นี้</strong>' +
            '<div class="small text-muted mt-1">โครงงานและนักศึกษาทุกคนมีข้อมูลถูกต้องสมบูรณ์ พร้อมนำเข้าสู่ระบบ</div>' +
            '</td></tr>';
    }
    $('#tbodyPreviewAnomalies').html(htmlAnomalies);

    // ปรับตัวเลข Badge แท็บที่ 5
    $('#tabCountAnomalies').text(anomaliesList.length);
    $('#badgeTotalAnomalies').text(anomaliesList.length + ' รายการ');
    if (anomaliesList.length > 0) {
        $('#tabCountAnomalies').addClass('badge bg-danger text-white');
        $('#btnPreviewTabAnomalies').addClass('text-danger fw-bold');
    } else {
        $('#tabCountAnomalies').removeClass('badge bg-danger text-white');
        $('#btnPreviewTabAnomalies').removeClass('text-danger fw-bold');
    }

    // เปิดแท็บเริ่มต้น (หรือแท็บที่ผู้ใช้เปิดค้างไว้) พร้อมใส่คลาส active
    const activeSubTab = window.currentPreviewTab || 'projects498';
    switchPreviewSubTab(activeSubTab);
    applyPreviewSearch();

    // บันทึกสถานะล่าสุดลง sessionStorage อัตโนมัติทุกครั้งที่มีการเรนเดอร์พรีวิว
    persistPreviewSession();
}
window.renderLivePreview = renderLivePreview;

// ==============================================================================
// ก้อนที่ 6: สลับแท็บย่อยในกล่อง Preview, ระบบค้นหาตาราง, และปุ่มย้อนกลับ
// ==============================================================================
/**
 * ค้นหาและกรองแถวในตารางที่กำลังเปิดอยู่แบบ Real-time พร้อมไฮไลต์คำที่ค้นหาผ่านโมดูล SearchBox
 */
function applyPreviewSearch() {
    const query = $('#inputPreviewSearch').val() || '';
    const $clearBtn = $('#btnClearPreviewSearch');

    if (query.trim().length > 0) {
        $clearBtn.removeClass('d-none');
    } else {
        $clearBtn.addClass('d-none');
    }

    // หาตารางในแท็บย่อยที่กำลังเปิดอยู่
    const activeSubTab = window.currentPreviewTab || 'projects498';
    const capitalized = activeSubTab.charAt(0).toUpperCase() + activeSubTab.slice(1);
    const $activePane = $('#subTabPane' + capitalized);
    const $table = $activePane.find('table');

    if ($table.length === 0) return;

    if (window.SearchBox && typeof window.SearchBox.filterTable === 'function') {
        window.SearchBox.filterTable($table, query, {
            highlightToggle: '#checkHighlightSearch'
        });
    }
}
window.applyPreviewSearch = applyPreviewSearch;

/**
 * สลับแท็บย่อยในหน้าตรวจสอบข้อมูล (projects498 / projectsCoop / students / noProject)
 * @param {string} tab 
 */
function switchPreviewSubTab(tab) {
    window.currentPreviewTab = tab;
    sessionStorage.setItem(PREVIEW_TAB_KEY, tab);

    $('.preview-tab-btn').removeClass('active');
    $('[id^="subTabPane"]').addClass('d-none');

    // แมปชื่อแท็บเข้ากับ ID ปุ่มและ Pane อัตโนมัติ
    const capitalized = tab.charAt(0).toUpperCase() + tab.slice(1);
    $('#btnPreviewTab' + capitalized).addClass('active');
    $('#subTabPane' + capitalized).removeClass('d-none');

    // กรองคำค้นหาต่อในแท็บที่เพิ่งเปิดใหม่ทันที
    applyPreviewSearch();
}
window.switchPreviewSubTab = switchPreviewSubTab;

/**
 * ปุ่มย้อนกลับจากหน้าพรีวิวไปหน้าเลือกไฟล์
 */
function backToUploadStep() {
    // ล้างข้อมูล Session ออกทั้งหมดทันทีที่ผู้ใช้กดเลือกไฟล์ใหม่
    clearPreviewSession();

    $('#stepPreviewBox').addClass('d-none');
    $('#stepUploadBox').removeClass('d-none');
    $('#mainStepTabs').removeClass('d-none');
    $('#btnBackPage').removeClass('d-none');
    
    // เคลียร์ช่องเลือกไฟล์เดิมและสถานะ Dropzone
    const input = document.getElementById('csvFileInput');
    if (input) input.value = '';
    $('#dropzonePrompt').removeClass('d-none');
    $('#fileSelectedBadge').addClass('d-none');
    $('#btnParseCsv').prop('disabled', true);

    // ล้างค่าคำค้นหาและไฮไลต์
    $('#inputPreviewSearch').val('');
    $('#btnClearPreviewSearch').addClass('d-none');
    if (window.SearchBox && typeof window.SearchBox.removeHighlight === 'function') {
        const previewBox = document.getElementById('stepPreviewBox');
        if (previewBox) window.SearchBox.removeHighlight(previewBox);
    }

    window.currentPreviewTab = 'projects498';
    switchPreviewSubTab('projects498');
    
    // เลื่อนกลับขึ้นไปบนสุดของกล่องอัปโหลด
    $('html, body').animate({
        scrollTop: $('#stepUploadBox').offset().top - 80
    }, 300);
}
window.backToUploadStep = backToUploadStep;

// ผูก Event ค้นหาเมื่อพิมพ์ หรือคลิกล้างคำค้นหา หรือติ๊กเปิด/ปิดไฮไลต์
$(document).on('input keyup search', '#inputPreviewSearch', function () {
    applyPreviewSearch();
});

$(document).on('click', '#btnClearPreviewSearch', function () {
    $('#inputPreviewSearch').val('').focus();
    applyPreviewSearch();
});

$(document).on('change', '#checkHighlightSearch', function () {
    applyPreviewSearch();
});

// ==============================================================================
// ก้อนที่ 7: ส่งข้อมูลบันทึกลงฐานข้อมูลจริง (CONFIRM & STORE TO DATABASE)
// ==============================================================================
$(document).on('click', '#btnConfirmSave', function () {
    if (!globalParsedData) {
        alert('ไม่พบข้อมูลที่ต้องบันทึก กรุณาเลือกไฟล์ CSV แล้วตรวจสอบข้อมูลใหม่อีกครั้ง');
        return;
    }

    // ตรวจสอบจำนวนข้อผิดพลาดทั้งหมดจากโมดูล StudentVerify
    const verificationResult = (window.StudentVerify && typeof window.StudentVerify.scanAll === 'function')
        ? window.StudentVerify.scanAll(globalParsedData)
        : { anomaliesList: [], totalCount: 0 };
    const anomalyCount = (verificationResult.anomaliesList || []).length;

    const modalEl = document.getElementById('modalConfirmSaveData');
    if (!modalEl) {
        if (!confirm(anomalyCount > 0 
            ? `พบข้อมูลผิดปกติในไฟล์ ${anomalyCount} รายการ คุณแน่ใจหรือไม่ว่าต้องการบันทึกข้อมูล?` 
            : 'ยืนยันการนำเข้าข้อมูลนักศึกษาและกลุ่มโครงงานทั้งหมดเข้าสู่ฐานข้อมูลจริง?')) {
            return;
        }
        executeSaveData();
        return;
    }

    const $iconBox = $('#confirmSaveDataIconBox');
    const $icon = $('#confirmSaveDataIcon');
    const $label = $('#modalConfirmSaveDataLabel');
    const $msg = $('#confirmSaveDataMessage');
    const $btnExec = $('#btnExecuteSaveData');
    const $btnCancel = $('#btnCancelSaveData');

    if (anomalyCount > 0) {
        // กรณีพบข้อมูลผิดปกติในไฟล์
        $iconBox.removeClass().addClass('d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm bg-warning-subtle text-warning');
        $icon.removeClass().addClass('fas fa-exclamation-triangle');
        $label.text('พบข้อมูลที่ต้องตรวจสอบ!');
        $msg.html(`
            <div class="alert alert-warning border-0 py-2 px-3 rounded-3 text-start small mb-2" style="background-color: #fef3c7; color: #92400e;">
                <i class="fas fa-exclamation-circle me-1 text-warning"></i>
                ตรวจพบข้อมูลผิดปกติหรือยังไม่สมบูรณ์ <strong>${anomalyCount} รายการ</strong>
            </div>
            <div class="text-dark fw-medium">คุณแน่ใจหรือไม่ว่าต้องการยืนยันบันทึกข้อมูลนี้?</div>
            <div class="text-muted mt-1" style="font-size: 0.78rem;">(แนะนำ: สามารถกด "กลับไปแก้ไข" เพื่อปรับปรุงข้อมูลให้เรียบร้อยก่อนได้)</div>
        `);
        $btnExec
            .removeClass('btn-success')
            .addClass('btn-warning text-dark')
            .html('<i class="fas fa-exclamation-triangle me-1"></i> ยืนยันบันทึกข้อมูล');
        $btnCancel.text('กลับไปแก้ไข');
    } else {
        // กรณีไม่มีข้อมูลผิดปกติเลย ข้อมูลสมบูรณ์ 100%
        $iconBox.removeClass().addClass('d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm bg-success-subtle text-success');
        $icon.removeClass().addClass('fas fa-check-circle');
        $label.text('ยืนยันการบันทึกข้อมูล?');
        $msg.html(`
            <div class="text-dark fw-medium">ข้อมูลนักศึกษาและกลุ่มโครงงานถูกต้องครบถ้วน</div>
            <div class="text-muted mt-1">กรุณาตรวจสอบความถูกต้องก่อนยืนยันนำเข้าสู่ระบบจริง</div>
        `);
        $btnExec
            .removeClass('btn-warning text-dark')
            .addClass('btn-success')
            .html('<i class="fas fa-save me-1"></i> ยืนยันการบันทึก');
        $btnCancel.text('ยกเลิก');
    }

    if (window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else {
        $('#modalConfirmSaveData').modal('show');
    }
});

// ดักจับการกดยืนยันใน Modal
$(document).on('click', '#btnExecuteSaveData', function () {
    const modalEl = document.getElementById('modalConfirmSaveData');
    if (modalEl) {
        if (window.bootstrap && bootstrap.Modal) {
            const instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) instance.hide();
        } else {
            $('#modalConfirmSaveData').modal('hide');
        }
    }
    executeSaveData();
});

/**
 * ฟังก์ชันส่ง AJAX บันทึกข้อมูลลงฐานข้อมูลจริง
 */
function executeSaveData() {
    const $btn = $('#btnConfirmSave');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังบันทึกข้อมูลเข้าสู่ฐานข้อมูล...');

    const finalProjectYear = $('#previewProjectYearInput').val() || globalParsedData.project_year;

    $.ajax({
        url: studentPreviewConfig.storeUrl,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            _token: studentPreviewConfig.csrfToken,
            project_year: finalProjectYear,
            students: globalParsedData.students,
            projects: globalParsedData.projects
        }),
        success: function (res) {
            $btn.prop('disabled', false).html(originalHtml);
            if (res.status === 'success') {
                // บันทึกสำเร็จ: ล้าง Session ออก
                clearPreviewSession();
                window.location.href = studentPreviewConfig.redirectUrl;
            } else {
                alert(res.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(originalHtml);
            const msg = xhr.responseJSON && xhr.responseJSON.message 
                ? xhr.responseJSON.message 
                : 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            alert(msg);
        }
    });
}

// หมายเหตุ: Event Listeners สำหรับ Modal แก้ไขโครงงานและนักศึกษา ถูกย้ายไปจัดการใน modal_edit_studentandproject.js เรียบร้อยแล้ว

// เมื่อมีการเปลี่ยนปีโครงงาน ให้บันทึกการเปลี่ยนแปลงลงใน Session ทันที
$(document).on('input change', '#previewProjectYearInput', function () {
    if (globalParsedData) {
        globalParsedData.project_year = $(this).val();
        persistPreviewSession();
    }
});

// เมื่อผู้ใช้คลิกลิงก์เมนูเพื่อออกจากหน้านี้ ให้ล้าง Session ทิ้งเพื่อไม่ให้ค้างเมื่อกลับเข้ามาใหม่ในภายหลัง
$(document).on('click', 'a[href]:not([href^="#"]):not([href^="javascript:"])', function () {
    const href = $(this).attr('href');
    if (href && $(this).attr('target') !== '_blank') {
        clearPreviewSession();
    }
});

// ==============================================================================
// ก้อนที่ 9: ฟังก์ชันจัดการแก้ไขและลบข้อมูลในพรีวิว (LIVE PREVIEW EDIT & REMOVE)
// ==============================================================================

/**
 * คำนวณตัวเลขสถิติภาพรวมใหม่ทุกครั้งที่มีการแก้ไขหรือลบแถว
 */
function recalculateSummary() {
    if (!globalParsedData && window.globalParsedData) {
        globalParsedData = window.globalParsedData;
    }
    if (!globalParsedData) return;

    let p498Count = 0;
    let coop497Count = 0;
    if (globalParsedData.projects) {
        globalParsedData.projects.forEach(function (p) {
            if (p.type === 'coop') coop497Count++;
            else p498Count++;
        });
    }

    const studentsList = Array.isArray(globalParsedData.students)
        ? globalParsedData.students
        : Object.values(globalParsedData.students || {});

    let withProjCount = 0;
    let withoutProjCount = 0;
    studentsList.forEach(function (s) {
        if (s.status_student === 'no_project' || !s.project_no) {
            withoutProjCount++;
        } else {
            withProjCount++;
        }
    });

    globalParsedData.summary = {
        total_students: studentsList.length,
        total_projects: (globalParsedData.projects ? globalParsedData.projects.length : 0),
        project_498_count: p498Count,
        coop_497_count: coop497Count,
        students_with_project_count: withProjCount,
        students_without_project_count: withoutProjCount
    };
    persistPreviewSession();
}
window.recalculateSummary = recalculateSummary;

// หมายเหตุ: ฟังก์ชัน openEditProjectModal, showEmptySpecialAdvisorNotice, addSpecialAdvisorRow, removeSpecialAdvisorRow, onSpecialAdvisorSelectChange, onSpecialAdvisorNameInput, saveEditProject ถูกย้ายไปอยู่ใน modal_edit_studentandproject.js เรียบร้อยแล้ว



/**
 * 9.3 ลบกลุ่มโครงงานออกจากการนำเข้า
 * @param {number} idx - ลำดับ Index ของโครงการ
 */
function removeProject(idx) {
    if (!globalParsedData || !globalParsedData.projects[idx]) return;

    const p = globalParsedData.projects[idx];
    const groupName = p.title_th || ('กลุ่มที่ ' + (p.project_no || (idx + 1)));

    if (!confirm('ยืนยันการนำโครงงาน "' + groupName + '" ออกจากรายการนำเข้าหรือไม่?\n(สมาชิกในกลุ่มนี้จะถูกปรับสถานะเป็น "ยังไม่มีโครงงาน" อัตโนมัติ)')) {
        return;
    }

    // ปรับสถานะสมาชิกในกลุ่มให้กลายเป็น no_project
    if (p.student_ids && p.student_ids.length > 0) {
        p.student_ids.forEach(function (sid) {
            let studentObj = null;
            if (Array.isArray(globalParsedData.students)) {
                studentObj = globalParsedData.students.find(s => s.student_id === sid);
            } else if (globalParsedData.students && globalParsedData.students[sid]) {
                studentObj = globalParsedData.students[sid];
            }

            if (studentObj) {
                studentObj.project_no = null;
                studentObj.status_student = 'no_project';
                studentObj.is_edited = true;

                // ตรวจสอบว่ามีใน students_without_project แล้วหรือยัง
                if (!globalParsedData.students_without_project) {
                    globalParsedData.students_without_project = [];
                }
                const alreadyInNoProj = globalParsedData.students_without_project.some(s => s.student_id === sid);
                if (!alreadyInNoProj) {
                    globalParsedData.students_without_project.push(studentObj);
                }
            }
        });
    }

    // ลบกลุ่มออกจาก Array
    globalParsedData.projects.splice(idx, 1);

    // คำนวณสรุปใหม่และรีเรนเดอร์
    recalculateSummary();
    renderLivePreview(globalParsedData);
}
window.removeProject = removeProject;

// หมายเหตุ: ฟังก์ชัน openEditStudentModal และ saveEditStudent ถูกย้ายไปอยู่ใน modal_edit_studentandproject.js เรียบร้อยแล้ว

/**
 * 9.6 ลบนักศึกษาออกจากการนำเข้า
 * @param {string} studentId - รหัสนักศึกษา
 */
function removeStudent(studentId) {
    if (!confirm('ยืนยันการนำนักศึกษารหัส ' + studentId + ' ออกจากรายการนำเข้าทั้งหมดหรือไม่?')) {
        return;
    }

    // 1. ลบจากก้อน students
    if (Array.isArray(globalParsedData.students)) {
        globalParsedData.students = globalParsedData.students.filter(s => s.student_id !== studentId);
    } else if (globalParsedData.students && globalParsedData.students[studentId]) {
        delete globalParsedData.students[studentId];
    }

    // 2. ลบจาก students_without_project
    if (globalParsedData.students_without_project) {
        globalParsedData.students_without_project = globalParsedData.students_without_project.filter(s => s.student_id !== studentId);
    }

    // 3. ลบออกจากสมาชิกในโครงงาน
    if (globalParsedData.projects && globalParsedData.projects.length > 0) {
        globalParsedData.projects.forEach(function (p) {
            if (p.student_ids) {
                p.student_ids = p.student_ids.filter(id => id !== studentId);
            }
            if (p.members) {
                p.members = p.members.filter(m => m.student_id !== studentId);
            }
        });
    }

    recalculateSummary();
    renderLivePreview(globalParsedData);
}
window.removeStudent = removeStudent;

