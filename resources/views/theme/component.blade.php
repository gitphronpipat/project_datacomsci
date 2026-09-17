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


    <style>
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
                margin-left: 0;       /* ไม่ต้องเว้นระยะซ้าย เพราะเมนูจะซ่อนหรือพับ */
                padding: 1rem;        /* ลดช่องว่างขอบจอให้พอดีกับหน้าจอมือถือ */
            }

            body {
                font-size: 15px;      /* ลดขนาดตัวอักษรลงเล็กน้อยเพื่อให้อ่านง่ายบนมือถือ */
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
            color: #b6ccd0 !important; /* 👈 ใส่โค้ดสีที่ต้องการ เช่น เทานวลสบายตา */
            opacity: 1 !important;     /* 👈 สำคัญมาก: ต้องใส่ 1 เพื่อไม่ให้เบราว์เซอร์ปรับสีจางลงเอง */
        }


        .btn-link {
            color: #f8d6e4;
        }

        .navbar-brand {
            color: #b8b6a3;          /* เปลี่ยนสีชื่อแบรนด์ */
            font-weight: 700;        /* ปรับให้หนาขึ้น */
        }
        .navbar-brand:hover {
            color: #4a5d23;          /* สีตอนเอาเมาส์ชี้ */
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
        .btn-warning {
            background-color: #f6d59b;
            border-color: #f59e0b;
            color: #fff;
            border-radius: 8px;
        }
        /* สีปุ่มเตือนเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: #fff;
        }

        /* ปุ่มลบ/ปุ่มอันตราย (Danger Button เช่น ปุ่มลบในตาราง ขาวขอบแดง) */
        .btn-danger {
            background-color: #fff;
            border: 1px solid #dc3545;
            color: #dc3545;
            border-radius: 8px;
        }
        /* สีปุ่มลบเมื่อเอาเมาส์ไปชี้ (Hover เปลี่ยนเป็นพื้นแดงตัวหนังสือขาว) */
        .btn-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .bg-primary {
            color: black !important;
            background-color : #08f376 !important;
            min-height: 25px !important;
            font-size: 12px;
        }
        .btn-outline-success {
            color: #04f4ec !important;
            border-color: #04f4ec !important;
            border-width: 2px !important;
        }
        .btn-outline-success:hover {
            color: #000 !important;
            border-color: #04f4ec !important;
            border-width: 2px !important;
        }

        .btn-back-page {
            color: #ffffff !important;
            background: #5b6e75  !important;
            border-color: #5b6e75  !important;
            text-align: center !important;
            width: 110px !important;
            padding: 8px 0px !important;
            height: 40px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
        }

        .btn-back-page:hover {
            color: #ffffff !important;
            background: #425156  !important;
            border-color: #425156  !important;

        }

        /* คลาสสำหรับป้ายแสดงสิทธิ์อาจารย์ */
        .roleteacher {
            color: #4a5d23;              /* สีตัวหนังสือ */
            background-color: #eaf3db;   /* สีพื้นหลังกล่องจางๆ ให้ดูสวย */
            padding: 4px 10px;           /* ช่องไฟขอบใน */
            border-radius: 6px;          /* มุมโค้งมน */
            font-weight: 600;            /* ตัวหนังสือหนา */
            font-size: 12px;
            display: inline-block;  
        }

        /* คลาสสำหรับเจ้าหน้าที่ */
        .roleofficer {
            color: #0d6efd;
            background-color: #e7f1ff;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
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
            padding-left: 18px; /* ขยับตัวหนังสือนิดๆ เพิ่มมิติ */
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
