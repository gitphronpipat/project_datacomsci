/**
 * =========================================================
 * ระบบสลับแท็บ / สเต็ป (public/js/steppage.js)
 * ใช้งานง่าย โค้ดกระชับ:
 * - ปุ่มแท็บ: ใส่ class="step-btn" data-step="1" (หรือ 2, 3)
 * - กล่องเนื้อหา: ใส่ class="step-pane" data-step="1" (หรือ 2, 3)
 * - แก้ปัญหา DataTables คอลัมน์เพี้ยนอัตโนมัติ
 * - จำค่าแท็บล่าสุดผ่าน sessionStorage รีเฟรชแล้วไม่หลุดหน้าเดิม
 * =========================================================
 */
$(document).ready(function () {
    /**
     * ฟังก์ชันเปิด Step ที่ต้องการ
     * @param {string|number} step เลข step เช่น 1, 2
     */
    window.switchStep = function (step) {
        step = String(step);

        // 1. ปรับสถานะปุ่มแท็บ
        $('.step-btn').removeClass('active');
        $('.step-btn[data-step="' + step + '"]').addClass('active');

        // 2. สลับแสดงกล่องเนื้อหา
        $('.step-pane').removeClass('active').hide();
        const targetPane = $('.step-pane[data-step="' + step + '"]');
        targetPane.addClass('active').fadeIn(150);

        // 3. ปรับขนาดคอลัมน์ของ DataTables ใหม่อัตโนมัติ (ป้องกันตารางเบียดกันเมื่อเปิดจากแท็บที่เคยซ่อน)
        if ($.fn.dataTable) {
            setTimeout(function () {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            }, 100);
        }

        // 4. บันทึก Step ล่าสุดไว้ใน sessionStorage (รีเฟรชหน้าแล้วไม่หลุด)
        try {
            sessionStorage.setItem('active_student_step', step);
        } catch (e) {}
    };

    // ดักฟังการคลิกปุ่มแท็บ .step-btn (เฉพาะหน้าที่มีระบบ .step-pane)
    $(document).on('click', '.step-btn', function (e) {
        const step = $(this).data('step');
        if (step && $('.step-pane').length > 0) {
            e.preventDefault();
            window.switchStep(step);
        }
    });

    // ถ้าหน้านี้ไม่มีกล่องเนื้อหา .step-pane เลย (เช่น หน้าที่มีแท็บแยกเฉพาะ หรือไม่มีสเต็ป) ไม่ต้องทำงาน
    if ($('.step-pane').length === 0) {
        return;
    }

    // เมื่อเปิดหน้าเว็บ: คืนค่าแท็บเดิมที่ผู้ใช้เคยเปิดไว้ หรือเริ่มที่ Step 1
    let initialStep = 1;
    try {
        initialStep = sessionStorage.getItem('active_student_step') || 1;
    } catch (e) {}

    // ถ้าไม่มีกล่องเนื้อหาของ step นั้น ให้เริ่มที่ step 1
    if (!$('.step-pane[data-step="' + initialStep + '"]').length) {
        initialStep = 1;
    }
    window.switchStep(initialStep);
});
