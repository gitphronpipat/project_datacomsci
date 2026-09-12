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
    {{-- Script ตารางของเรา --}}
    <script src="{{ asset('js/table.js') }}"></script>

    <style>
        /* =========================================================
           1. โครงสร้างพื้นฐานของหน้าเว็บ (Base Layout & Typography)
           ========================================================= */
        /* ตัวครอบหลักของทั้งเว็บ: ฟอนต์ Sarabun, สีตัวหนังสือหลัก, สีพื้นหลังเว็บ และระยะห่างบรรทัด */
        body {
            font-family: 'Sarabun', sans-serif;
            color: #cc2727;
            background-color: #f4f4f4;
            font-size: 16px;
            line-height: 1.7;
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
            color: #e20d0d !important;
        }

        /* ข้อความคำแนะนำย่อยใต้ช่องกรอก (เช่น รองรับไฟล์ JPG..., ใช้สำหรับล็อกอิน...) */
        .form-text {
            color: #e20d0d !important;
        }

        /* ตัวหนังสือขณะผู้ใช้กำลังพิมพ์ข้อความลงในช่องกรอก (Input Box) */
        .form-control {
            color: #ff05c5 !important;
        }

        /* ตัวหนังสือในกล่องเลือกแบบ Dropdown (Select Box เช่น เลือกสิทธิ์, สถานะ) */
        .form-select {
            color: #fff709 !important;
        }

        /* ข้อความตัวอย่างจางๆ ในช่องพิมพ์ (Placeholder เช่น ดร.สมชาย ใจดี, somchai@mju.ac.th) */
        .form-control::placeholder {
            color: #a0a5b1 !important;    /* สีข้อความตัวอย่าง */
            opacity: 1 !important;        /* บังคับความคมชัด */
            font-size: 14px;              /* ขนาดตัวอักษรตัวอย่าง */
            font-style: italic;           /* ทำตัวเอียง */
            font-weight: 300;             /* ความหนา-บางของตัวหนังสือ */
        }

        /* =========================================================
           5. คลาสสีข้อความพิเศษ (Color Utilities)
           ========================================================= */
        /* สีข้อความเน้นสำคัญหลัก (เช่น หัวข้อย่อยข้อมูลส่วนตัว, บัญชีผู้ใช้) */
        .text-primary {
            color: #08f376 !important;
        }

        /* สีข้อความรอง / ข้อความจาง (เช่น คำอธิบายย่อยใต้หัวข้อหน้า) */
        .text-muted {
            color: brown !important;
        }

        /* =========================================================
           6. ปุ่มกดรูปแบบต่างๆ (Buttons)
           ========================================================= */
        /* ปุ่มหลัก (Primary Button เช่น ปุ่ม 'เพิ่มข้อมูล' สีเขียวเข้ม) */
        .btn-primary {
            background-color: #4a5d23;
            border-color: #4a5d23;
            color: #fff;
            border-radius: 8px;
        }
        /* สีปุ่มหลักเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-primary:hover {
            background-color: #3a4a1c;
            border-color: #3a4a1c;
            color: #fff;
        }

        /* ปุ่มบันทึกข้อมูลสำเร็จ (Success Button เช่น ปุ่ม 'บันทึกข้อมูล') */
        .btn-success {
            background-color: #08f376;
            border-color: #08f376;
            color: #d81919;
            border-radius: 8px;
        }
        /* สีปุ่มสำเร็จเมื่อเอาเมาส์ไปชี้ (Hover) */
        .btn-success:hover {
            background-color: #f8f8f8;
            border-color: #08f376;
            color: #f3e4e4;
        }

        /* ปุ่มสีสว่าง (Light Button เช่น ปุ่ม 'ยกเลิก') */
        .btn-light {
            color: #1804f0;
        }

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
            background-color: #f59e0b;
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
    </style>

    @stack('styles')
