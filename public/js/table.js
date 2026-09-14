// ==========================================================
// สคริปต์จัดการและตกแต่ง DataTables
// ที่ตั้งไฟล์: public/js/table.js
// ==========================================================

$(document).ready(function () {

    // 1. ฝัง CSS ตกแต่งตารางลงใน <head> ผ่าน JavaScript โดยตรง (ไม่ต้องไปแก้ใน component)
    const datatableCustomStyles = `
        /* ปิดเส้นขอบแนวตั้งแบบ Excel ออกทั้งหมด 100% ให้เหลือเฉพาะเส้นนอนบางเฉียบ สไตล์ Modern SaaS */
        table.dataTable {
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            width: 100% !important;
            border: none !important;
            margin-top: 14px !important;
            margin-bottom: 16px !important;
        }

        table.dataTable thead th,
        table.dataTable tbody td {
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 13px 16px !important;
            vertical-align: middle !important;
            font-family: inherit !important;
            font-size: 13.5px !important;
        }

        /* ตกแต่งหัวตาราง (Table Header) โทนเทาไอหมอก สะอาดตา */
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 12.5px !important;
            letter-spacing: 0.01em !important;
            border-bottom: 1px solid #e2e8f0 !important;
            white-space: nowrap;
        }

        /* ตกแต่งแถวและ Hover Effect */
        table.dataTable tbody tr {
            background-color: #ffffff !important;
            transition: background-color 0.15s ease;
        }

        table.dataTable tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* สไตล์ลูกศรจัดเรียงคอลัมน์ (Chevron Sorting Arrows) */
        table.dataTable thead .sorting::before,
        table.dataTable thead .sorting_asc::before,
        table.dataTable thead .sorting_desc::before,
        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            font-size: 9.5px !important;
            right: 8px !important;
            color: #cbd5e1 !important;
            opacity: 0.6 !important;
        }

        table.dataTable thead .sorting::before,
        table.dataTable thead .sorting_asc::before,
        table.dataTable thead .sorting_desc::before {
            content: "\\f077" !important;
            top: 28% !important;
        }

        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after {
            content: "\\f078" !important;
            bottom: 28% !important;
        }

        table.dataTable thead .sorting_asc::before {
            color: #0f172a !important;
            opacity: 1 !important;
        }
        table.dataTable thead .sorting_desc::after {
            color: #0f172a !important;
            opacity: 1 !important;
        }

        table.dataTable thead th.sorting {
            padding-right: 24px !important;
        }

        /* กล่องค้นหา (Modern Floating Search Box) */
        .dataTables_filter {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
        }

        .dataTables_filter label {
            position: relative !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            font-size: 0 !important; /* ซ่อนข้อความ 'ค้นหา:' */
        }

        .dataTables_filter label::before {
            content: "\\f002";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 13px;
            font-size: 13px;
            color: #94a3b8;
            pointer-events: none;
            z-index: 5;
        }

        .dataTables_filter input {
            width: 240px !important;
            height: 38px !important;
            padding: 6px 14px 6px 36px !important;
            font-size: 13.5px !important;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            outline: none !important;
            transition: all 0.15s ease;
        }

        .dataTables_filter input:focus {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06) !important;
        }

        /* กล่องเลือกจำนวนแถว (Length Dropdown) */
        .dataTables_length label {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 13.5px !important;
            color: #64748b !important;
            font-weight: 400 !important;
            margin: 0 !important;
        }

        .dataTables_length select {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 6px 30px 6px 12px !important;
            font-size: 13.5px !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            margin: 0 8px !important;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .dataTables_length select:focus {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06) !important;
            outline: none !important;
        }

        /* ปุ่ม 'คืนค่ารายการ' */
        .btn-reset-filter {
            height: 38px !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            margin: 0 0 0 8px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
            transition: all 0.15s ease;
            display: inline-flex !important;
            align-items: center !important;
            cursor: pointer;
        }

        .btn-reset-filter:hover {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        /* ข้อความ Info ด้านล่าง */
        .dataTables_info {
            color: #64748b !important;
            font-size: 13px !important;
            padding-top: 8px !important;
        }

        /* ปุ่มแบ่งหน้า (Pagination) สไตล์โมเดิร์น สบายตา */
        .dataTables_wrapper .pagination .page-item .page-link {
            font-size: 12.5px !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            margin: 0 2px !important;
            padding: 6px 12px !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
            transition: all 0.15s ease;
        }

        .dataTables_wrapper .pagination .page-item:not(.disabled) .page-link:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }

        .dataTables_wrapper .pagination .page-item.disabled .page-link {
            background-color: #ffffff !important;
            color: #cbd5e1 !important;
            border-color: #f1f5f9 !important;
            cursor: not-allowed !important;
        }

        /* Empty State สวยงามตอนไม่มีข้อมูล */
        .empty-state-container {
             
            text-align: center;
        }
        .empty-state-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 12px auto;
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
            emptyTable: `
                <div class="empty-state-container">
                    <div class="empty-state-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="fw-semibold" style="color: #334155; font-size: 14.5px;">ยังไม่มีข้อมูลในตาราง</div>
                    
                </div>
            `,
            zeroRecords: `
                <div class="empty-state-container">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="fw-semibold" style="color: #334155; font-size: 14.5px;">ไม่พบข้อมูลที่ตรงกับการค้นหา</div>
                    <div class="text-muted small mt-1">ลองตรวจสอบคำสะกด หรือกดปุ่ม "คืนค่ารายการ" เพื่อล้างการค้นหา</div>
                </div>
            `,
            lengthMenu: "แสดง _MENU_ แถว",
            search: "",
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