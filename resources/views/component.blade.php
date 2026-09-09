    {{-- ฟอนต์ Sarabun --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            color: #333333;
            background-color: #f4f4f4;
            font-size: 16px;
            line-height: 1.7;
            letter-spacing: 0.3px;
        }
        h1 { font-size: 2rem;    font-weight: 700; }
        h2 { font-size: 1.75rem; font-weight: 600; }
        h3 { font-size: 1.5rem;  font-weight: 600; }
        h4 { font-size: 1.25rem; font-weight: 500; }
        h5 { font-size: 1rem;    font-weight: 500; }
        h6 { font-size: 0.875rem;font-weight: 500; }

        #main {
            margin-left: 240px;
            padding: 1.5rem;
        }

        @media (max-width: 768px) {
            #main {
                margin-left: 0;
                padding: 1rem;
            }
            body {
                font-size: 15px;
            }
        }

        /* การ์ดหลัก */
        .page-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 1.5rem 2rem;
        }

        /* ปุ่ม */
        .btn-primary {
            background-color: #4a5d23;
            border-color: #4a5d23;
            color: #fff;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background-color: #3a4a1c;
            border-color: #3a4a1c;
            color: #fff;
        }
        .btn-warning {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #fff;
            border-radius: 8px;
        }
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: #fff;
        }
        .btn-danger {
            background-color: #fff;
            border: 1px solid #dc3545;
            color: #dc3545;
            border-radius: 8px;
        }
        .btn-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }
    </style>

    @stack('styles')
