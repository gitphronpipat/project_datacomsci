/**
 * Custom Smooth Select (เรียกใช้งานผ่าน class="custom-select-smooth")
 * โค้ดสั้น กระชับ อ่านง่าย และใช้ซ้ำได้กับทุก Select ในระบบ
 * สไตล์คลีน โมเดิร์น ไม่มีแถบแยกย่อย พร้อมเช็คถูกฝั่งขวา
 */
(function (window, $) {
    'use strict';

    // 1. ไอคอนกำกับเฉพาะค่าที่เป็น Role
    const icons = {
        officer: '<i class="fas fa-user-tie text-primary me-2"></i>',
        teacher: '<i class="fas fa-chalkboard-teacher text-success me-2"></i>',
        admin:   '<i class="fas fa-user-shield text-danger me-2"></i>'
    };

    function getOptionIcon($opt) {
        if (!$opt || $opt.length === 0) return '';
        const val = $opt.val();
        return icons[val] || '';
    }

    /**
     * ซิงค์การแสดงผลของ UI กับค่าจริงใน <select>
     */
    function syncCustomSelect(selectEl) {
        const $select = $(selectEl);
        const $wrapper = $select.next('.custom-select-wrapper');
        if ($wrapper.length === 0) return;

        const $selected = $select.find('option:selected');
        const text = $selected.length > 0 ? $selected.text() : ($select.find('option').first().text() || '-- เลือก --');
        const icon = getOptionIcon($selected);

        $wrapper.find('.current-text').html(icon + text);
        $wrapper.find('.custom-select-option').removeClass('active');
        $wrapper.find('.custom-select-check').addClass('d-none');

        const currentVal = $select.val();
        const $matched = $wrapper.find(`.custom-select-option[data-val="${currentVal}"]`);
        if ($matched.length > 0) {
            $matched.addClass('active');
            $matched.find('.custom-select-check').removeClass('d-none');
        }
    }

    /**
     * เริ่มต้นแปลง <select class="custom-select-smooth"> ในบริบทที่ระบุ
     */
    function initCustomSelectSmooth(context) {
        const $targets = context 
            ? ($(context).hasClass('custom-select-smooth') ? $(context) : $(context).find('.custom-select-smooth'))
            : $('.custom-select-smooth');

        $targets.each(function () {
            const $select = $(this);
            // หากมี wrapper อยู่แล้วให้ข้าม เพื่อไม่ให้สร้างซ้อน
            if ($select.next('.custom-select-wrapper').length > 0) {
                syncCustomSelect($select);
                return;
            }

            $select.addClass('d-none'); // ซ่อน select ดั้งเดิมไว้ส่งฟอร์ม
            const $selected = $select.find('option:selected');
            const initialText = $selected.length > 0 ? $selected.text() : ($select.find('option').first().text() || '-- เลือก --');
            const initialIcon = getOptionIcon($selected);

            // สร้างกล่องแสดงผลภายนอก (Trigger)
            const $trigger = $(`
                <div class="custom-select-trigger d-flex align-items-center justify-content-between" tabindex="0">
                    <span class="current-text">${initialIcon + initialText}</span>
                    <i class="fas fa-chevron-down custom-select-arrow ms-2"></i>
                </div>
            `);

            // สร้างแผงตัวเลือกที่สไลด์ลงมา (Dropdown Menu)
            const $menu = $('<ul class="custom-select-menu list-unstyled m-0 p-1" style="max-height: 280px; overflow-y: auto;"></ul>');

            // วนลูปสร้างตัวเลือกทุก option แบบเรียบง่าย ไม่มีแถบแยก
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

                    $select.val(val).trigger('change'); // ซิงค์ค่าไปยัง select จริง
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
    }

    // ส่งออกเป็น Global ฟังก์ชัน
    window.initCustomSelectSmooth = initCustomSelectSmooth;
    window.syncCustomSelect = syncCustomSelect;

    $(document).ready(function () {
        initCustomSelectSmooth();

        // ปิดเมนูอัตโนมัติเมื่อคลิกนอกพื้นที่ หรือกดปุ่ม Escape
        $(document).on('click', () => $('.custom-select-wrapper').removeClass('open'));
        $(document).on('keydown', (e) => { if (e.key === 'Escape') $('.custom-select-wrapper').removeClass('open'); });
    });
})(window, jQuery);
