// ==========================================================
// สคริปต์จัดการและตกแต่ง DataTables
// ที่ตั้งไฟล์: public/js/table.js
// ==========================================================

$(document).ready(function () {

// 1. ฝัง CSS ตกแต่งตารางลงใน <head> ผ่าน JavaScript โดยตรง (ไม่ต้องไปแก้ใน component)
    const datatableCustomStyles = `
        /* ปรับระยะห่างความสูงของแต่ละแถว (Padding) และจัดกึ่งกลาง */
        table.dataTable tbody td, 
        table.dataTable thead th {
            padding: 10px 12px !important;
            vertical-align: middle !important;
            font-family: inherit !important;
            color: inherit !important;
            font-size: 13px !important;
        }

        /* เปลี่ยนลูกศรจัดเรียงเป็น Font Awesome เส้นโค้งมน (Chevron) */
        table.dataTable thead .sorting::before,
        table.dataTable thead .sorting_asc::before,
        table.dataTable thead .sorting_desc::before,
        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            font-size: 10px !important;
            right: 8px !important;
            color: #94a3b8 !important;
            opacity: 0.4 !important;
        }

        /* ลูกศรชี้ขึ้น */
        table.dataTable thead .sorting::before,
        table.dataTable thead .sorting_asc::before,
        table.dataTable thead .sorting_desc::before {
            content: "\\f30c" !important;
            top: 20% !important;
        }

        /* ลูกศรชี้ลง */
        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after {
            content: "\\f309" !important;
            bottom: 20% !important;
        }

        /* เมื่อคลิกเรียงคอลัมน์: ไฮไลต์ลูกศรที่กำลังทำงานเป็นสีน้ำเงินเข้มเด่นชัด */
        table.dataTable thead .sorting_asc::before {
            color: #2563eb !important;
            opacity: 1 !important;
        }
        table.dataTable thead .sorting_desc::after {
            color: #2563eb !important;
            opacity: 1 !important;
        }

        /* เผื่อระยะขอบขวาของหัวตาราง ไม่ให้ตัวหนังสือทับลูกศร */
        table.dataTable thead th.sorting {
            padding-right: 24px !important;
        }



        /* ตกแต่งหัวตารางให้ดูโมเดิร์น */
        table.dataTable thead th {
            font-weight: 600 !important;
            white-space: nowrap;
        }

        /* สลับสีแถวให้อ่านง่าย สบายตา (Zebra stripes) */
        table.dataTable tbody tr:nth-of-type(odd) {
            background-color: #fcfdfe;
        }

        /* เอฟเฟกต์สีเวลาชี้เมาส์ที่แถว (Hover) */
        table.dataTable tbody tr:hover {
            background-color: #f1f5f9 !important;
            transition: background-color 0.15s ease-in-out;
        }

        /* แต่งข้อความกำกับของทั้ง 'ค้นหา' และ 'แสดงแถว' ให้ตรงกันตาม body */
        .dataTables_filter label,
        .dataTables_length label {
            font-family: inherit !important;   /* ใช้ฟอนต์ Sarabun ตาม body */
            color: inherit !important;         /* ใช้สี #25396f ตาม body */
            font-size: inherit !important;      /* ใช้ขนาดเท่ากับ body */
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        /* แต่งทั้งกล่องค้นหา (input) และกล่องเลือกจำนวนแถว (select) ให้มีสไตล์คู่กัน */
        .dataTables_filter input {
            width: 250px !important;         /* ปรับความกว้าง (เช่น 250px หรือ 300px) */
            padding: 8px 8px !important;     /* เพิ่มความโปร่งสบาย */
            margin: 4px 0 4px 8px !important;
        }
        .dataTables_length select {
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            outline: none !important;
            font-family: inherit !important;
            color: inherit !important;
            font-size: inherit !important;
            background-color: #fff !important;
            transition: all 0.2s ease;
        }

        /* เฉพาะกล่องเลือกจำนวนแถว (Length Select) */
        .dataTables_length select {
            cursor: pointer;
            padding: 6px 32px 6px 12px !important;   /* เผื่อที่ให้ลูกศร */
            margin: 4px 6px !important;              /* เว้นที่บนล่างไม่ให้แสงแหว่ง */
            background-position: right 10px center !important;
            background-size: 14px 14px !important;
        }

        /* แสงเรืองสีฟ้าตอนคลิกเลือก (:focus) ของทั้งสองกล่องให้เหมือนกันเป๊ะ */
        .dataTables_filter input:focus,
        .dataTables_length select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
            outline: none !important;
        }


        /* ปรับแต่งข้อความแสดงจำนวนแถว (Info) */
        .dataTables_info {
            font-family: inherit !important;
            color: #475569 !important;
            font-size: 14px !important;
            padding-top: 8px !important;
        }

        /* ปรับแต่งปุ่มแบ่งหน้า (Pagination) ให้โค้งมน เป็นกล่องสวยงาม */
        .dataTables_wrapper .pagination .page-item .page-link {
            font-family: inherit !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            margin: 0 3px !important;
            padding: 6px 14px !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            background-color: #fff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .pagination .page-item:not(.disabled) .page-link:hover {
            background-color: #eff6ff !important;
            color: #2563eb !important;
            border-color: #bfdbfe !important;
        }
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }
        .dataTables_wrapper .pagination .page-item.disabled .page-link {
            background-color: #f8fafc !important;
            color: #cbd5e1 !important;
            border-color: #f1f5f9 !important;
            cursor: not-allowed !important;
            box-shadow: none !important;
        }

        /* ปุ่มคืนค่ารายการ (ล้างการค้นหา) */
        .btn-reset-filter {
            border-radius: 8px !important;
            padding: 6px 14px !important;
            margin: 4px 0 4px 8px !important;
            font-family: inherit !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            background-color: #fff !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            display: inline-flex !important;
            align-items: center !important;
            cursor: pointer;
        }
        .btn-reset-filter:hover {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            border-color: #94a3b8 !important;
        }
        .btn-reset-filter:active {
            transform: scale(0.98);
        }
    `;

    // ทำการแทรก Style เข้าไปในหน้าเว็บอัตโนมัติ
    $('<style>').text(datatableCustomStyles).appendTo('head');


    // 2. เรียกใช้งานและตั้งค่า DataTable
    $('#Table, .dataTable').DataTable({

        // ภาษาไทย (กำหนดเอง ไม่ดึง CDN เพื่อแก้ปัญหาคำว่า to/of ปนมา)
        language: {
            info: "แสดงทั้งหมด _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            infoEmpty: "แสดงทั้งหมด 0 ถึง 0 จาก 0 รายการ",
            infoFiltered: "(ค้นหาจากทั้งหมด _MAX_ รายการ)",
            emptyTable: "ไม่พบข้อมูลในตาราง",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
            lengthMenu: "แสดง _MENU_ แถว",
            search: "ค้นหา:",
            searchPlaceholder: "พิมพ์เพื่อค้นหา...",
            paginate: {
                previous: '<i class="fas fa-chevron-left me-1"></i> ก่อนหน้า',
                next: 'ถัดไป <i class="fas fa-chevron-right ms-1"></i>',
                first: "หน้าแรก",
                last: "หน้าสุดท้าย"
            }
        },

        // จัดแจงตำแหน่งแต่ละส่วนด้วยระบบ Grid ของ Bootstrap
        dom:
            "<'row mb-3 align-items-center'" +
            "<'col-sm-12 col-md-6'l>" +        // บนซ้าย: ตัวเลือกจำนวนแถว (Length)
            "<'col-sm-12 col-md-6 text-end'f>" + // บนขวา: ช่องค้นหา (Filter/Search)
            ">" +
            "<'row'" +
            "<'col-sm-12'tr>" +                 // กลาง: ตัวตาราง (Table)
            ">" +
            "<'row mt-3 align-items-center'" +
            "<'col-sm-12 col-md-5'i>" +        // ล่างซ้าย: ข้อความข้อมูลจำนวนแถว (Info)
            "<'col-sm-12 col-md-7 d-flex justify-content-end'p>" + // ล่างขวา: ปุ่มเปลี่ยนหน้า (Pagination)
            ">",

        pageLength: 10,                 // จำนวนแถวเริ่มต้นต่อหน้า
        lengthMenu: [10, 15, 20, 25, 30, 50, 75, 100],   // ตัวเลือก dropdown จำนวนแถว
        searching: true,                // ✅ เปิดระบบค้นหา
        ordering: true,                 // ✅ เปิดระบบเรียงลำดับ
        order: [[0, 'asc']],            // เรียงคอลัมน์แรก (ลำดับ) จากน้อยไปมาก
        paging: true,                   // แบ่งหน้า (Pagination)
        info: true,                     // แสดงข้อความ "แสดง 1 ถึง 10 จากทั้งหมด 50 รายการ"
        responsive: true,               // รองรับหน้าจอมือถือ
        autoWidth: false,               // ปิดคำนวณความกว้างอัตโนมัติ

        columnDefs: [
            // ปิดการเรียงลำดับที่คอลัมน์สุดท้าย (ปุ่มแก้ไข/ลบ)
            { targets: -1, orderable: false },
        ],

        // สร้างปุ่ม 'คืนค่ารายการ' และระบบล้างค่าการค้นหา
        initComplete: function () {
            const api = this.api();

            // สร้างปุ่ม 'คืนค่ารายการ' พร้อมไอคอนลูกศรหมุนวน
            const $resetBtn = $(
                '<button type="button" class="btn btn-outline-secondary btn-sm btn-reset-filter" title="ล้างการค้นหาและคืนค่าตารางเดิม">' +
                    '<i class="fas fa-rotate-left me-1"></i> คืนค่ารายการ' +
                '</button>'
            );

            // นำปุ่มไปวางต่อท้ายช่องค้นหา
            $('.dataTables_filter').append($resetBtn);

            // เมื่อคลิกปุ่ม ให้ล้างคำค้นหาและคืนค่าตารางกลับสู่สภาพเดิม
            $resetBtn.on('click', function () {
                $('.dataTables_filter input').val(''); // ล้างข้อความในช่องพิมพ์
                api.search('').order([[0, 'asc']]).draw(); // ล้างตัวกรองและคืนค่าเรียงลำดับ
            });
        }

    });
});