    {{-- ฟอนต์ Inter (ภาษาอังกฤษ) + Sarabun (ภาษาไทย) เพื่อความเป็นสากล --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
           Design System — Ultra-Clean White & Modern Luxury
           สไตล์พรีเมียม สบายตา มาตรฐาน SaaS สากล (Stripe, Apple, Notion)
           ========================================================= */

        /* =========================================================
           1. โครงสร้างพื้นฐานของหน้าเว็บ (Base Layout & Typography)
           ========================================================= */
        body {
            font-family: 'Inter', 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #0f172a;               /* Charcoal Slate คมชัด สบายตา */
            background-color: #f8fafc;    /* พื้นหลังนวลไอหมอกจางๆ เพื่อขับ Card ขาวให้ลอยเด่น */
            font-size: 14.5px;
            line-height: 1.6;
            letter-spacing: 0.01em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #0f172a;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        h1 { font-size: 1.85rem; }
        h2 { font-size: 1.55rem; }
        h3 { font-size: 1.35rem; }
        h4 { font-size: 1.15rem; }
        h5 { font-size: 1rem; }
        h6 { font-size: 0.875rem; }

        /* =========================================================
           2. พื้นที่เนื้อหาหลัก (Main Content Area) & Responsive
           ========================================================= */
        #main {
            margin-left: 240px;
            padding: 1.75rem 2rem;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.25s ease;
        }

        @media (max-width: 768px) {
            #main {
                margin-left: 0 !important;
                padding: 1rem;
            }
            body {
                font-size: 14px;
            }
        }

        /* =========================================================
           3. กล่องการ์ดเนื้อหา (Card Container)
           ========================================================= */
        .card, .page-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        /* =========================================================
           4. ส่วนประกอบของฟอร์มกรอกข้อมูล (Form Controls)
           ========================================================= */
        .form-label {
            color: #334155 !important;
            font-weight: 500;
            font-size: 13.5px;
            margin-bottom: 0.4rem;
        }

        .form-text {
            color: #64748b !important;
            font-size: 12.5px;
            margin-top: 0.35rem;
        }

        .form-control, .form-select {
            color: #0f172a !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.55rem 0.85rem;
            font-size: 14px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
            outline: none;
        }

        .form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
            font-size: 13.5px;
            font-style: normal;
            font-weight: 400;
        }

        /* =========================================================
           5. คลาสสีข้อความพิเศษ (Color Utilities)
           ========================================================= */
        .text-primary {
            color: #0f172a !important;
        }

        .text-muted {
            color: #64748b !important;
        }

        /* =========================================================
           6. ปุ่มกดรูปแบบต่างๆ (Buttons)
           ========================================================= */
        .btn {
            font-weight: 500;
            border-radius: 8px;
            font-size: 13.5px;
            padding: 0.48rem 1rem;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* ปุ่มหลัก: Dark Slate สุขุม เรียบหรู */
        .btn-primary {
            background-color: #0f172a;
            border: 1px solid #0f172a;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #1e293b;
            border-color: #1e293b;
            color: #ffffff;
        }

        /* ปุ่มบันทึกข้อมูล (Save Button) */
        .btn-success {
            background-color: #0f172a;
            border: 1px solid #0f172a;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .btn-success:hover, .btn-success:focus {
            background-color: #1e293b;
            border-color: #1e293b;
            color: #ffffff;
        }

        /* ปุ่มสีสว่าง (Cancel / Back Button) */
        .btn-light {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475569;
        }
        .btn-light:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* ปุ่ม Outline เทา */
        .btn-outline-secondary {
            color: #475569;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
        }
        .btn-outline-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* ปุ่มแก้ไข (Warning) แบบ Soft Pill */
        .btn-warning {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        .btn-warning:hover {
            background-color: #fde68a;
            border-color: #f59e0b;
            color: #78350f;
        }

        /* ปุ่มลบ (Danger) แบบ Soft Minimal */
        .btn-danger {
            background-color: #ffffff;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .btn-danger:hover {
            background-color: #fef2f2;
            border-color: #dc2626;
            color: #b91c1c;
        }

        /* Outline Status buttons in table */
        .btn-outline-success {
            color: #059669;
            border-color: #a7f3d0;
            background-color: #ecfdf5;
        }
        .btn-outline-success:hover {
            background-color: #d1fae5;
            border-color: #059669;
            color: #047857;
        }

        .btn-outline-danger {
            color: #dc2626;
            border-color: #fecaca;
            background-color: #fef2f2;
        }
        .btn-outline-danger:hover {
            background-color: #fee2e2;
            border-color: #dc2626;
            color: #991b1b;
        }

        /* =========================================================
           7. สไตล์ตาราง (Table Aesthetics)
           ========================================================= */
        .table {
            color: #1e293b;
            margin-bottom: 0;
            border-color: #f1f5f9;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #64748b;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 0.85rem 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* =========================================================
           8. ป้ายสถานะ (Badges & Pills)
           ========================================================= */
        .badge {
            font-weight: 500;
            font-size: 12px;
            padding: 0.35rem 0.65rem;
            border-radius: 9999px;
            letter-spacing: 0.01em;
        }

        /* =========================================================
           9. หน้าต่างป๊อปอัป (Modal)
           ========================================================= */
        .modal-content {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
