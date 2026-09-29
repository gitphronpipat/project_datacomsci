    {{-- ฟอนต์ Sarabun --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    {{-- DataTables + Bootstrap5 CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    {{-- jQuery (ต้องมาก่อน Bootstrap JS และ DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    {{-- DataTables + Bootstrap5 JS --}}
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    {{-- Cropper.js สำหรับตัดรูปโปรไฟล์ (Image Crop) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    {{-- Script ของระบบ (แยกตามโมดูลอย่างเป็นระเบียบ) --}}
    <script src="{{ asset('js/table.js') }}"></script>
    <script src="{{ asset('js/deleteinfotable.js') }}"></script>
    <script src="{{ asset('js/cropper.js') }}"></script>
    <script src="{{ asset('js/custom_select.js') }}"></script>
    <script src="{{ asset('js/eyepass.js') }}"></script>
    <script src="{{ asset('js/steppage.js') }}"></script>


    <style>
        /* =========================================================
           ระบบสลับ Step / Tabs (steppage.js) - สไตล์ Underline ตามภาพตัวอย่าง
           ========================================================= */
        .step-tabs {
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #e2e8f0;
            gap: 0.25rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0;
            overflow-x: auto;
            overflow-y: hidden !important;
            white-space: nowrap;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .step-tabs::-webkit-scrollbar {
            display: none;
        }

        .step-tabs .step-btn {
            position: relative;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 15px;
            font-weight: 500;
            padding: 0.5rem 0.85rem 0.75rem 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 3px solid transparent;
            margin-bottom: -1.5px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            text-decoration: none;
        }

        .step-tabs .step-btn:hover {
            color: #1e293b;
        }

        .step-tabs .step-btn.active {
            color: #1a56db;
            font-weight: 600;
            border-bottom: 3px solid #1a56db;
            background: transparent;
            box-shadow: none;
        }

        .step-tabs .tab-divider {
            color: #cbd5e1;
            font-size: 16px;
            user-select: none;
            margin: 0 0.5rem 0.4rem 0.5rem;
        }

        .step-pane {
            display: none;
        }

        .step-pane.active {
            display: block;
        }

        /* =========================================================
           1. โครงสร้างพื้นฐานของหน้าเว็บ (Base Layout & Typography)
           ========================================================= */
        /* ตัวครอบหลักของทั้งเว็บ: ฟอนต์ Sarabun, สีตัวหนังสือหลัก, สีพื้นหลังเว็บ และระยะห่างบรรทัด */
        body {
            font-family: 'Sarabun', sans-serif;
            color: #05173f;
            background-color: #f7fafc;
            font-size: 16px;
            line-height: 1.5;
            letter-spacing: 0.3px;
        }

        /* ขนาดและความหนาของหัวข้อระดับต่างๆ (H1 - H6) */
        h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        h2 {
            font-size: 1.75rem;
            font-weight: 600;
        }

        h3 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        h4 {
            font-size: 1.25rem;
            font-weight: 500;
        }

        h5 {
            font-size: 1rem;
            font-weight: 500;
        }

        h6 {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* =========================================================
           2. พื้นที่เนื้อหาหลัก (Main Content Area) & Responsive
           ========================================================= */
        /* ขยับพื้นที่เนื้อหาหลักไปทางขวา 240px เพื่อหลบแถบเมนูด้านซ้าย (Sidebar) */
        #main {
            margin-left: 240px;
            padding: 1.5rem;
        }

        /* ปรับแต่งสำหรับหน้าจอมือถือและแท็บเล็ต (กว้างไม่เกิน 768px) */
        @media (max-width: 768px) {
            #main {
                margin-left: 0;
                /* ไม่ต้องเว้นระยะซ้าย เพราะเมนูจะซ่อนหรือพับ */
                padding: 1rem;
                /* ลดช่องว่างขอบจอให้พอดีกับหน้าจอมือถือ */
            }

            body {
                font-size: 15px;
                /* ลดขนาดตัวอักษรลงเล็กน้อยเพื่อให้อ่านง่ายบนมือถือ */
            }
        }

        /* =========================================================
           3. กล่องการ์ดเนื้อหา (Card Container)
           ========================================================= */
        /* กล่องการ์ดสีขาวสำหรับครอบส่วนเนื้อหา: มุมโค้งมน 12px, มีเงาตกกระทบจางๆ */
        .page-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 1.5rem 2rem;
        }

        /* =========================================================
           4. ส่วนประกอบของฟอร์มกรอกข้อมูล (Form Controls)
           ========================================================= */
        /* ป้ายชื่อหัวข้อช่องกรอก (Label เช่น ชื่อ-นามสกุล, อีเมล, รหัสผ่าน) */
        .form-label {
            color: #000000 !important;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 0.4rem;
        }

        /* =========================================================
           5. คลาสสีข้อความพิเศษ (Color Utilities)
           ========================================================= */
        /* สีข้อความเน้นสำคัญหลัก (เช่น หัวข้อย่อยข้อมูลส่วนตัว, บัญชีผู้ใช้) */
        .text-primary {
            color: #000000 !important;
        }

        /* สีข้อความรอง / ข้อความจาง (เช่น คำอธิบายย่อยใต้หัวข้อหน้า) */
        .text-muted {
            color: #16248c !important;
        }

        /* ข้อความตัวอย่างในช่องพิมพ์ (Placeholder) ทุกช่อง */
        .form-control::placeholder {
            color: #b6ccd0 !important;
            /* 👈 ใส่โค้ดสีที่ต้องการ เช่น เทานวลสบายตา */
            opacity: 1 !important;
            /* 👈 สำคัญมาก: ต้องใส่ 1 เพื่อไม่ให้เบราว์เซอร์ปรับสีจางลงเอง */
        }


        .btn-link {
            color: #f8d6e4;
        }

        .navbar-brand {
            color: #b8b6a3;
            /* เปลี่ยนสีชื่อแบรนด์ */
            font-weight: 700;
            /* ปรับให้หนาขึ้น */
        }

        .navbar-brand:hover {
            color: #4a5d23;
            /* สีตอนเอาเมาส์ชี้ */
        }

        /* =========================================================
           6. ปุ่มกดรูปแบบต่างๆ (Buttons)
           ========================================================= */
        /* ปุ่มหลัก (Primary Button เช่น ปุ่ม 'เพิ่มข้อมูล' สีเขียวเข้ม) */
        .btn-primary {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff;
            border-radius: 8px;
        }

        /* สีปุ่มหลักเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-primary:hover {
            background-color: #1f55c9 !important;
            border-color: #1f55c9 !important;
            color: #fff;
        }

        /* ปุ่มบันทึกข้อมูลสำเร็จ (Success Button เช่น ปุ่ม 'บันทึกข้อมูล') */
        .btn-success {
            background-color: #198754;
            border-color: #198754;
            border-radius: 8px;
        }

        /* สีปุ่มสำเร็จเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-success:hover {
            background-color: #136840;
            border-color: #136840;

        }

        /* ปุ่มสีสว่าง (Light Button เช่น ปุ่ม 'ยกเลิก') */
        /* .btn-light {
            border-color: #f30505;
            border-radius: 8px;
        } */

        /* ปุ่มขอบบางสีเทา (Outline Button เช่น ปุ่ม 'กลับหน้ารายการ', ปุ่มรูปลูกตาดูรหัสผ่าน) */
        .btn-outline-secondary {
            color: #c0bcf1;
        }

        /* สีปุ่มขอบบางเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-outline-secondary:hover {
            color: #fff;
        }

        /* ปุ่มแจ้งเตือน/ปุ่มแก้ไขสีส้ม (Warning Button เช่น ปุ่มแก้ไขในตาราง) */
         {
            background-color: #f6d59b;
            border-color: #f59e0b;
            color: #fff;
            border-radius: 8px;
        }

        /* สีปุ่มเตือนเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-warning:hover {
            background-color: #f59e0b !important;
            border-color: #f59e0b !important;
        }

        /* ปุ่มลบ/ปุ่มอันตราย (Danger Button เช่น ปุ่มลบในตาราง ขาวขอบแดง) */
        .btn-warning { 
            border: 1.5px solid #f59e0b !important; 
        }

        .btn-warning:hover i {
            color: #ffffff !important;
        }

        .btn-warning i {
            color: #f59e0b !important;
            font-size: 13.5px !important;
            margin: 0 !important;
            transition: color 0.2s ease !important;
        }

        .btn-danger {
            background-color: #ffffff !important;
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            border-radius: 6px !important;
            border: 1.5px solid #ef4444 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            transition: all 0.2s ease !important;
        }

        /* สีปุ่มลบเมื่อเอาเมาส์ไปชี้ (Hover เปลี่ยนเป็นพื้นแดงตัวหนังสือขาว) */

        .btn-danger i {
            color: #ef4444 !important;
            font-size: 13.5px !important;
            margin: 0 !important;
            transition: color 0.2s ease !important;
        }

        .btn-danger:hover {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
        }

        .btn-danger:hover i {
            color: #ffffff !important;
        }

        .bg-primary {
            color: black !important;
            background-color: #08f376 !important;
            min-height: 25px !important;
            font-size: 12px;
        }

        .btn-group {
            display: inline-flex !important;
            gap: 6px !important;
            box-shadow: none !important;
        }

        .btn-outline-danger {
            background-color: #ffffff !important;
            border: 1.5px solid #ef4444 !important;
            color: #dc2626 !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            border-radius: 6px !important;
            padding: 0.28rem 0.75rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            transform: none !important;
            transition: background-color 0.15s ease, color 0.15s ease !important;
        }

        .btn-outline-danger i {
            color: #ef4444 !important;
            transition: color 0.15s ease !important;
        }

        .btn-outline-danger:hover {
            background-color: #ef4444 !important;
            border: 1.5px solid #ef4444 !important;
            color: #ffffff !important;
            transform: none !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }

        .btn-outline-danger:hover i {
            color: #ffffff !important;
        }

        .btn-outline-success {
            background-color: #ffffff !important;
            border: 1.5px solid #10b981 !important;
            color: #059669 !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            border-radius: 6px !important;
            padding: 0.28rem 0.75rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            transform: none !important;
            transition: background-color 0.15s ease, color 0.15s ease !important;
        }

        .btn-outline-success i {
            color: #10b981 !important;
            transition: color 0.15s ease !important;
        }


        .btn-outline-success:hover {
            background-color: #10b981 !important;
            border: 1.5px solid #10b981 !important;
            color: #ffffff !important;
            transform: none !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }

        .btn-outline-success:hover i {
            color: #ffffff !important;
        }


        .btn-back-page {
            color: #ffffff !important;
            background: #5b6e75 !important;
            border-color: #5b6e75 !important;
            text-align: center !important;
            width: 110px !important;
            padding: 8px 0px !important;
            height: 40px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }

        .btn-back-page:hover {
            color: #ffffff !important;
            background: #425156 !important;
            border-color: #425156 !important;

        }

        /* คลาสสำหรับป้ายแสดงสิทธิ์อาจารย์ */
        .roleteacher {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.25rem 0.65rem !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            line-height: 1.35 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            white-space: nowrap !important;
            background-color: #f0f9ff !important;
            border: 1px solid #bae6fd !important;
            color: #0369a1 !important;
        }

        /* คลาสสำหรับเจ้าหน้าที่ */
        .roleofficer {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.25rem 0.65rem !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            line-height: 1.35 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            white-space: nowrap !important;
            background-color: #f5f3ff !important;
            border: 1px solid #ddd6fe !important;
            color: #6d28d9 !important;
        }

        /* แถวตารางที่ปิดใช้งาน (Inactive Row) ปรับสีจางลง พร้อมเอฟเฟกต์ชี้เมาส์ */
        .table-row-inactive {
            opacity: 0.55;
            background-color: #f8fafc !important;
            transition: all 0.2s ease;
        }

        .table-row-inactive:hover {
            opacity: 0.9;
            background-color: #f1f5f9 !important;
        }

        .table-row-inactive td {
            color: #64748b !important;
        }
    </style>

    <style>
        tbody td strong {
            color: #09090b !important;
            font-weight: 600 !important;
            font-size: 14px !important;
        }

        table code {
            background-color: transparent !important;
            color: #18181b !important;
            font-family: inherit !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            padding: 0 !important;
        }

        table .text-muted {
            color: #18181b !important;
        }
        table td .fa-envelope,
        table td .fa-phone {
            color: #71717a !important;
            width: 14px !important;
        }

        /* 5. โครงสร้างและสีตาราง: Pure White Minimalist */
        table.table-bordered th,
        table.table-bordered td,
        table.table-bordered {
            border-color: #e4e4e7 !important;
        }
        table thead.table-light,
        table thead.table-light tr,
        table thead.table-light th {
            background-color: #f9fafb !important;
            box-shadow: inset 0 0 0 9999px #f9fafb !important;
            border-color: #e4e4e7 !important;
            color: #374151 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
        }
        table.table-striped > tbody > tr > *,
        table tbody tr > * {
            background-color: #ffffff !important;
            box-shadow: inset 0 0 0 9999px #ffffff !important;
        }
        table.table-hover > tbody > tr:hover > * {
            background-color: #f4f4f5 !important;
            box-shadow: inset 0 0 0 9999px #f4f4f5 !important;
            transition: background-color 0.15s ease !important;
        }
    </style>

    <style>
        /* =========================================================
           แนวทางที่ 2: Modern Smooth Custom Select Dropdown
           ========================================================= */
        .custom-select-wrapper {
            position: relative;
            user-select: none;
            width: 100%;
        }

        /* กล่องปุ่มกดเลือกภายนอก (Trigger) */
        .custom-select-trigger {
            background-color: #ffffff;
            border: 1.5px solid #dee2e6;
            border-radius: 8px;
            padding: 0.55rem 0.95rem;
            cursor: pointer;
            font-size: 15px;
            color: #212529;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* เอฟเฟกต์ตอนนำเมาส์ไปชี้กล่อง (Hover) */
        .custom-select-trigger:hover {
            border-color: #86b7fe;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        /* เมื่อเปิดเมนูกางออกมา หรือตอนคลิกเลือก: ขอบเรืองแสงสีฟ้าแบบเดียวกับกล่อง Input */
        .custom-select-wrapper.open .custom-select-trigger,
        .custom-select-trigger:focus {
            border-color: #91bdff;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            outline: 0;
        }

        /* ลูกศรชี้ลง หมุนกลับด้าน 180 องศาแบบสมูท */
        .custom-select-arrow {
            font-size: 12px;
            color: #6c757d;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-select-wrapper.open .custom-select-arrow {
            transform: rotate(180deg);
            color: #0d6efd;
        }

        /* แผงตัวเลือกที่เลื่อนสไลด์ลงมา (Slide Down + Fade-in) */
        .custom-select-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            z-index: 1050;
            overflow: hidden;

            /* สถานะซ่อน: เลื่อนขึ้นเล็กน้อยและจางหาย */
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.98);
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        /* สถานะเปิด: สไลด์ลงมาพร้อมปรากฏอย่างนุ่มนวล สมูท 100% */
        .custom-select-wrapper.open .custom-select-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        /* แถบตัวเลือกแต่ละรายการ */
        .custom-select-option {
            padding: 9px 14px;
            font-size: 14.5px;
            color: #333333;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .custom-select-option:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            padding-left: 18px;
            /* ขยับตัวหนังสือนิดๆ เพิ่มมิติ */
        }

        /* ตัวเลือกที่กำลังถูกเลือกอยู่ในปัจจุบัน (ไฮไลต์สีฟ้าเข้าชุด) */
        .custom-select-option.active {
            background-color: rgba(13, 110, 253, 0.08);
            color: #0d6efd;
            font-weight: 600;
        }

        .custom-select-check {
            font-size: 12px;
            color: #0d6efd;
        }
    </style>


    @stack('styles')
