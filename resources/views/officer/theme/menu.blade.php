{{-- resources/views/admin/theme/menu.blade.php no.1--}}
    <style>
        /* ===== ตัวแปรความกว้าง ===== */
        :root {
            --sidebar-width: 240px;
            --sidebar-collapsed-width: 65px;
            --sidebar-border-color: rgba(203, 213, 225, 0.7); /* สีเดียวกับ border-bottom border-slate-200/70 ของ Navbar */
        }

        /* ===== ตัว Sidebar หลัก ===== */
        .sidebar {
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 60px;
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - 60px);
            background: #ffffff;
            border-right: 1px solid var(--sidebar-border-color); /* เส้นขวาตรงกับเส้น Navbar ด้านบน */
            overflow: hidden;
            padding: 0;
            font-size: 13.5px;
            color: #475569;
            z-index: 999;
            transition: width 0.25s ease, padding 0.25s ease;
        }

        /* ===== Wrapper สำหรับเมนู (เลื่อนได้เฉพาะส่วนนี้) ===== */
        .sidebar .menu-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 0.7rem 0 0.5rem 0;
            margin-left: 10px;
            margin-right: 10px;
            transition: padding 0.25s ease;
        }

        /* สไตล์ Scrollbar บางเฉียบ */
        .sidebar .menu-wrapper::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar .menu-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar .menu-wrapper::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
        .sidebar .menu-wrapper::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }

        /* ===== Footer ด้านล่าง ===== */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 0.8rem 1rem;
            border-top: 1px solid var(--sidebar-border-color); /* เส้นบน Footer ตรงกับธีม */
            background: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: #475569;
            transition: padding 0.25s ease;
        }

        .sidebar-footer .footer-text {
            display: flex;
            flex-direction: column;
            line-height: 1.4;
        }

        /* ===== ลิงก์เมนูทั่วไป ===== */
        /* .sidebar a {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.25rem;
            
            color: #475569;
            margin-bottom: 0.25rem;
            border-radius: 10px;
            text-decoration: none;
            
            transition: all 0.2s ease;
        } */

        .sidebar a .menu-label {
            margin-left: 14px;
        }
                 /* 1. เมนูหลักทั่วไป (ที่ยังไม่ได้เลือก): เปลี่ยนเป็น #71717a ทั้งหมด */
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 0.65rem 1rem;
            color: #71717a;                             /* 👈 เปลี่ยนจาก #475569 เป็น #71717a */
            font-weight: 500;
            margin-bottom: 0.25rem;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar a .menu-icon {
            width: 20px;                                
            display: inline-flex;
            align-items: center;
            justify-content: center;                 
            flex-shrink: 0;
            color: #71717a;
            transition: color 0.2s ease;
        }


        .sidebar a .arrow {
            color: #71717a;                             /* 👈 เปลี่ยนจาก #64748b เป็น #71717a */
        }


               /* เมื่อเอาเมาส์ชี้เมนูที่ไม่ได้เลือก (Hover): โทน Zinc ดำคมชัด */
        .sidebar a:hover {
            background: #f4f4f5;                        /* 👈 เทานวลตาแบบเดียวกับรูป */
            color: #18181b;                             /* 👈 ตัวหนังสือดำคม */
        }
        .sidebar a:hover .menu-icon,
        .sidebar a:hover .arrow {
            color: #18181b;                             /* 👈 ไอคอนและลูกศรดำคม */
        }


          /* ===== 2. เมนูแม่ที่กลุ่มนี้เปิดอยู่ (เช่น จัดการข้อมูล แบบ Income ในรูป) ===== */
        .sidebar .toggle.active {
            background-color: #f4f4f5 !important;        /* 👈 กล่องเทานวลตามรูป */
            color: #18181b !important;                   /* 👈 ดำคมชัด */
            font-weight: 600 !important;
            border-radius: 12px !important;
            border: none !important;
        }
        .sidebar .toggle.active .menu-icon,
        .sidebar .toggle.active .arrow {
            color: #18181b !important;
        }

        /* .sidebar a:hover {
            background: #f4f4f5;
            color: #18181b;
        }
        .sidebar a:hover .menu-icon,
        .sidebar a:hover .arrow {
            color: #18181b;
        } */

       

                /* ===== 2. เมนูย่อยแบบ Tree View ===== */
        .sidebar .child {
            position: relative;
            margin-left: 1rem;
            padding-left: 0;
            border-left: none !important;
            margin-top: 0.15rem;
            margin-bottom: 0.5rem;
        }

        /* ⭐️ เส้นแกนแนวดิ่งยาวเส้นเดียว (ลากเชื่อมตรงรอยปากกาสีดำที่คุณวาดสนิท 100%) */
        .sidebar .child::before {
            content: '';
            position: absolute;
            left: 8px;                         /* ตรงกับแนวกึ่งกลางไอคอนแม่และเส้นเลี้ยว */
            top: -6px;                         /* เริ่มจากใต้ไอคอนแม่ */
            bottom: 23px;                      /* วิ่งตรงยาวลงมาจบที่จุดกึ่งกลางของเมนูสุดท้ายพอดี */
            width: 0;
            border-left: 1.5px solid #e4e4e7;  /* เส้นแนวดิ่งเส้นเดียวยาวตลอดแนว */
            z-index: 1;
        }

        /* แถบเมนูย่อย */
        .sidebar .child a {
            display: flex;
            align-items: center;
            position: relative;
            margin-left: 24px;
            margin-right: 0;
            padding: 0.5rem 0.85rem;
            margin-bottom: 0.35rem;
            border-radius: 8px !important;
            font-size: 13.5px;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            color: #71717a;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
            z-index: 2;
        }

                .sidebar .child a .menu-icon {
            width: 18px;                 /* 👈 ล็อคความกว้างกล่องไอคอนให้เท่ากันทุกตัว */
            display: inline-flex;        /* 👈 จัดให้อยู่กึ่งกลาง */
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
            font-size: 13px;
            color: #71717a;
            transition: color 0.15s ease;
        }


        .sidebar .child a .menu-label {
            margin-left: 8px;
            white-space: nowrap;
        }

                /* ⭐️ เส้นกิ่งโค้งเลี้ยว: เชื่อมต่อกับแกนตรงเนียนสนิท ไร้รอยซ้อนทับแบบในรูปเป๊ะ */
        .sidebar .child a::before {
            content: '';
            position: absolute;
            left: -16px;
            top: calc(50% - 8px);                       /* 👈 เริ่มต้นโค้งเฉพาะตรงมุมพอดี */
            width: 16px;
            height: 8px;                                /* 👈 สูงแค่ตรงจุดเลี้ยว ไม่ลากยาวขึ้นไปทับแกนหลัก */
            border-left: 1.5px solid #e4e4e7;           /* 👈 สีเทานวลตาเบอร์เดียวกันเป๊ะ */
            border-bottom: 1.5px solid #e4e4e7;
            border-bottom-left-radius: 8px;             /* 👈 โค้งมนนุ่มนวล */
        }


        /* ปิด ::after เพื่อไม่ให้มีเส้นซ้อน */
        .sidebar .child a::after {
            display: none !important;
        }


                 /* ⭐️ 1. เมนูย่อยตอนเอาเมาส์ชี้ (Hover): โทน Zinc เทานวลตัดดำคม */
        .sidebar .child a:hover {
            color: #18181b;                             /* 👈 ดำคมชัด */
            background: #f4f4f5 !important;             /* 👈 เทานวลตาโทนเดียวกับเมนูแม่ */
        }

        .sidebar .child a:hover .menu-icon {
            color: #18181b;                             /* 👈 ไอคอนดำคมชัด */
        }


        /* ===== 3. เมนูย่อยที่ Active (เช่น อาจารย์และเจ้าหน้าที่ แบบ Refunds ในรูป) ===== */
        .sidebar .child a.active {
            background-color: #f4f4f5 !important;        /* 👈 กล่องเทานวลเข้าคู่กับแม่ */
            color: #18181b !important;                   /* 👈 ดำคมชัด */
            font-weight: 600 !important;
            border-radius: 10px !important;
            box-shadow: none !important;
        }
        .sidebar .child a.active .menu-icon,
        .sidebar .child a.active .menu-label {
            color: #18181b !important;
        }

        /* ===== Toggle (ลูกศรขยาย) ===== */
        .sidebar .toggle {
            display: flex;
            justify-content: space-between;
        }

        .sidebar .toggle .arrow {
            font-size: 10px;
            transition: transform 0.2s ease;
        }

        .sidebar .toggle[aria-expanded="true"] .arrow {
            transform: rotate(180deg);
        }

                /* ============================================================ */
        /* ===== เมื่อ Sidebar อยู่ในสถานะ พับ (collapsed) แบบ Flyout ===== */
        /* ============================================================ */
        
        /* ปลดล็อค overflow เพื่อให้กล่องเมนูย่อยลอยทะลุออกมานอก Sidebar ได้ */
        .sidebar.collapsed,
        .sidebar.collapsed .menu-wrapper {
            overflow: visible !important;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* ซ่อนข้อความและลูกศรในแถบ Sidebar หลักเมื่อพับ */
        .sidebar.collapsed > .menu-wrapper .menu-label,
        .sidebar.collapsed .arrow,
        .sidebar.collapsed .footer-text {
            display: none !important;
        }

        /* ตัวครอบเมนูแต่ละกลุ่ม: ตั้งค่าเป็นจุดอ้างอิงตำแหน่งกล่องลอย */
        .menu-item-group {
            position: relative;
        }

        /* ซ่อนหัวข้อ flyout ตอนกางปกติ */
        .flyout-title {
            display: none;
        }

                /* จัดไอคอนเมนูหลักตอนพับให้เป็นวงกลมนุ่มๆ สบายตา */
        .sidebar.collapsed .toggle,
        .sidebar.collapsed .menu-item-group > a {
            width: 40px !important;
            height: 40px !important;
            padding: 0 !important;
            margin: 6px auto !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #71717a;
            font-size: 16px !important;         /* 👈 เพิ่มบรรทัดนี้เพื่อขยายไอคอนให้ใหญ่สมส่วน */
            transition: all 0.2s ease;
        }

        /* ล็อคขนาดให้ไอคอนข้างในขยายตาม */
        .sidebar.collapsed .toggle .menu-icon,
        .sidebar.collapsed .menu-item-group > a .menu-icon {
            font-size: 16px !important;         /* 👈 ถ้าอยากให้ใหญ่ขึ้นอีกนิด ลองปรับเป็น 17px ได้ครับ */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* เปลี่ยนสีพื้นหลังไอคอนเมื่อชี้ที่กลุ่มเมนู หรือเมนูเดี่ยว */
        .sidebar.collapsed .menu-item-group:hover .toggle,
        .sidebar.collapsed .menu-item-group > a:hover {
            background-color: #f4f4f5 !important;
            color: #18181b !important;
        }

        /* ตอนไอคอนถูกเลือกอยู่ (Active): สีเทาอ่อนนุ่มๆ + ตัวหนา คมชัด */
        .sidebar.collapsed .toggle.active,
        .sidebar.collapsed .menu-item-group > a.active {
            background-color: #f4f4f5 !important;
            color: #18181b !important;
        }

        /* ⭐️ โหมดพับ: ปิดการคลิกที่เมนูที่มีเมนูย่อย ทำให้กดแล้วไม่ติด */
        .sidebar.collapsed .toggle {
            pointer-events: none;
        }

        /* ซ่อนเมนูย่อยปกติในโหมดพับ และตัดแอนิเมชันตอนกด */
        .sidebar.collapsed .collapse,
        .sidebar.collapsed .collapsing {
            display: none !important;
        }

        /* ⭐️ เมื่อเอาเมาส์ชี้ (Hover) ที่ไอคอน: กล่องขาวคลีนจะลอยเด้งออกมา และเปิดให้คลิกเมนูย่อยได้ตามปกติ */
        .sidebar.collapsed .menu-item-group:hover .collapse {
            display: block !important;
            position: absolute;
            left: 58px;                                 /* ระยะลอยออกมาทางขวาต่อจากไอคอน */
            top: 0;
            width: 215px;                               /* ความกว้างกล่องลอย */
            z-index: 9999;
            pointer-events: auto;                       /* 👈 คลิกเมนูย่อยข้างในได้ปกติ */
            animation: flyoutFadeIn 0.15s ease-out;
        }


        .sidebar.collapsed .menu-item-group:hover .collapse::before {
            content: '';
            position: absolute;
            left: -15px;
            top: 0;
            bottom: 0;
            width: 15px;
        }

        /* กล่องการ์ดสีขาวคลีน ลอยนุ่มๆ */
        .sidebar.collapsed .child {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.04) !important;
            padding: 0.6rem 0.6rem 0.5rem 0.6rem !important;
            margin: 0 !important;
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
        }
                /* ⭐️ เส้นแกนแนวดิ่งยาวเส้นเดียวในกล่องลอยตอนพับ (จูนค่า Subpixel & ตัดหางล่าง) */
        .sidebar.collapsed .child::before {
            content: '';
            position: absolute;
            left: 17.5px;                                /* 👈 ปรับเป็น 17.5px (ค่ากึ่งกลาง Subpixel พอดีเป๊ะ ไม่เอียงซ้ายไม่เอียงขวา) */
            top: 41px;                                  /* 👈 แตะใต้เส้นคั่นพอดี */
            bottom: 37px;                               /* 👈 ปรับจาก 35px เป็น 37px (ดึงปลายเส้นขึ้น 2px ตัดติ่งหางที่แลบออกสนิท) */
            width: 0;
            border-left: 1.5px solid #e4e4e7;
            display: block !important;
            z-index: 1;
        }




        /* หัวข้อเมนูหลักในกล่องลอย */
                /* หัวข้อเมนูหลักในกล่องลอย (แถว 416-424) */
        .sidebar.collapsed .flyout-title {
            display: block !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #18181b !important;                   /* 👈 ดำคมชัด */
            padding: 0.2rem 0.5rem 0.5rem 0.5rem !important;
            margin-bottom: 0.35rem !important;
            border-bottom: 1px solid #f4f4f5 !important; /* 👈 เส้นคั่นเทานวล */
        }

        /* รายการเมนูย่อยปกติในกล่องลอย (แถว 439) */
        .sidebar.collapsed .child a {
            color: #71717a !important;                   /* 👈 เทานวลตา #71717a */
        }

        /* เมนูย่อยในกล่องลอยตอน Hover (แถว 450-453) */
        .sidebar.collapsed .child a:hover {
            background-color: #f4f4f5 !important;        /* 👈 เทานวลตา */
            color: #18181b !important;                   /* 👈 ดำคมชัด */
        }

        /* เมนูย่อยในกล่องลอยตอน Active (แถว 456-468) */
        .sidebar.collapsed .child a.active {
            background-color: #f4f4f5 !important;        /* 👈 เทานวลตา */
            color: #18181b !important;                   /* 👈 ดำคมชัด */
            font-weight: 600 !important;
        }

        .sidebar.collapsed .child a.active .menu-icon,
        .sidebar.collapsed .child a.active .menu-label {
            color: #18181b !important;
        }


        /* แสดงชื่อเมนูและไอคอนในกล่องลอยให้ครบถ้วน */
        .sidebar.collapsed .child a .menu-label {
            display: inline-block !important;
            margin-left: 8px !important;
            font-size: 13px !important;
        }

        .sidebar.collapsed .child a .menu-icon {
            width: 18px !important;
            display: inline-flex !important;
            justify-content: center !important;
            font-size: 13px !important;
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 0.8rem 0;
            justify-content: center;
        }

        /* เอฟเฟกต์เฟดนุ่มๆ ตอนกล่องลอยโผล่ */
        @keyframes flyoutFadeIn {
            from {
                opacity: 0;
                transform: translateX(-4px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

    </style>


@php
    $active = $active_menu ?? '';

    $menus = [
        [
            'label' => 'จัดการข้อมูล',
            'icon' => '<i class="fas fa-user-plus"></i>',
            'url' => url('/officer'),
            'key' => 'test',
            'children' => [],
        ],
    ];
@endphp


<div class="sidebar" id="sidebar">
        <div class="menu-wrapper" id="sidebarMenuAccordion">
        @foreach ($menus as $menu)
            @php
                $hasChildren = !empty($menu['children']);
                $groupActive = $active === $menu['key'];
                if (!$groupActive && $hasChildren) {
                    foreach ($menu['children'] as $c) {
                        if ($c['key'] === $active) {
                            $groupActive = true;
                            break;
                        }
                    }
                }
            @endphp

            {{-- ครอบด้วย menu-item-group เพื่อให้กล่อง Flyout ลอยตรงตำแหน่งไอคอน --}}
            <div class="menu-item-group">
                @if ($hasChildren)
                    <a class="toggle {{ $groupActive ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#grp-{{ $menu['key'] }}" aria-expanded="{{ $groupActive ? 'true' : 'false' }}"
                        title="{{ $menu['label'] }}">
                        <span class="d-inline-flex align-items-center">
                            <span class="menu-icon">{!! $menu['icon'] !!}</span>
                            <span class="menu-label">{{ $menu['label'] }}</span>
                        </span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>

                    <div class="collapse {{ $groupActive ? 'show' : '' }}" id="grp-{{ $menu['key'] }}" data-bs-parent="#sidebarMenuAccordion">
                        <div class="child">
                            {{-- หัวข้อกล่องลอยตอนพับ --}}
                            <div class="flyout-title">{{ $menu['label'] }}</div>

                            @foreach ($menu['children'] as $child)
                                <a href="{{ $child['url'] }}" class="{{ $active === $child['key'] ? 'active' : '' }}" title="{{ $child['label'] }}">
                                    <span class="menu-icon">{!! $child['icon'] !!}</span>
                                    <span class="menu-label">{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $menu['url'] }}" class="{{ $active === $menu['key'] ? 'active' : '' }}" title="{{ $menu['label'] }}">
                        <span class="menu-icon">{!! $menu['icon'] !!}</span>
                        <span class="menu-label">{{ $menu['label'] }}</span>
                    </a>
                @endif
            </div>
        @endforeach
    </div>


    <div class="sidebar-footer">
        <span class="footer-icon text-secondary">
            <i class="fas fa-graduation-cap fa-lg"></i>
        </span>
        <span class="footer-text">
            <span class="block fs-9 fw-medium tracking-wide ">สาขาวิชาวิทยาการคอมพิวเตอร์</span>
            <span class="block small">คณะวิทยาศาสตร์</span>
        </span>
    </div>
</div>

