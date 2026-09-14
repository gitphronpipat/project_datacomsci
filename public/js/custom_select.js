/**
 * Custom Smooth Select (เรียกใช้งานผ่าน class="custom-select-smooth")
 * โค้ดสั้น กระชับ อ่านง่าย และใช้ซ้ำได้กับทุก Select ในระบบ
 */
$(document).ready(function () {
    // 1. ไอคอนกำกับแต่ละสิทธิ์ (สามารถเพิ่ม-เปลี่ยนไอคอนได้ตรงนี้)
    const icons = {
        officer: '<i class="fas fa-user-tie text-primary me-2"></i>',
        teacher: '<i class="fas fa-chalkboard-teacher text-success me-2"></i>',
        admin:   '<i class="fas fa-user-shield text-danger me-2"></i>'
    };

    // 2. ค้นหาทุก <select class="custom-select-smooth"> แล้วแปลงเป็น Dropdown โมเดิร์น
    $('.custom-select-smooth').each(function () {
        const $select = $(this).addClass('d-none'); // ซ่อน select ดั้งเดิมไว้ส่งฟอร์ม
        const selected = $select.find(':selected');

        // สร้างกล่องแสดงผลภายนอก (Trigger)
        const $trigger = $(`
            <div class="custom-select-trigger d-flex align-items-center justify-content-between" tabindex="0">
                <span class="current-text">${(icons[selected.val()] || '') + selected.text()}</span>
                <i class="fas fa-chevron-down custom-select-arrow ms-2"></i>
            </div>
        `);

        // สร้างแผงตัวเลือกที่สไลด์ลงมา (Dropdown Menu)
        const $menu = $('<ul class="custom-select-menu list-unstyled m-0 p-1"></ul>');
        
        $select.find('option').each(function () {
            const val = $(this).val();
            const text = $(this).text();
            const icon = icons[val] || '';
            const isActive = $(this).is(':selected') ? 'active' : '';

            const $item = $(`
                <li class="custom-select-option d-flex align-items-center ${isActive}" data-val="${val}">
                    ${icon}<span>${text}</span>
                    <i class="fas fa-check custom-select-check ms-auto ${isActive ? '' : 'd-none'}"></i>
                </li>
            `);

            // เมื่อกดเลือกรายการ
            $item.on('click', function (e) {
                e.stopPropagation();
                $trigger.find('.current-text').html(icon + text);
                $menu.find('.custom-select-option').removeClass('active');
                $menu.find('.custom-select-check').addClass('d-none');
                $item.addClass('active');
                $item.find('.custom-select-check').removeClass('d-none');

                $select.val(val).trigger('change'); // ซิงค์ค่าไปยัง select จริงเพื่อส่งฟอร์ม
                $wrapper.removeClass('open');       // ปิดเมนู
            });

            $menu.append($item);
        });

        // ประกอบร่าง HTML และผูก Event คลิกเปิด/ปิด
        const $wrapper = $('<div class="custom-select-wrapper position-relative"></div>');
        $trigger.on('click', function (e) {
            e.stopPropagation();
            $('.custom-select-wrapper').not($wrapper).removeClass('open');
            $wrapper.toggleClass('open');
        });

        $wrapper.append($trigger, $menu);
        $select.after($wrapper);
    });

    // 3. ปิดเมนูอัตโนมัติเมื่อคลิกนอกพื้นที่ หรือกดปุ่ม Escape
    $(document).on('click', () => $('.custom-select-wrapper').removeClass('open'));
    $(document).on('keydown', (e) => { if (e.key === 'Escape') $('.custom-select-wrapper').removeClass('open'); });
});
