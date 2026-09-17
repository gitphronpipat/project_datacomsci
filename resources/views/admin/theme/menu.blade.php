{{-- resources/views/admin/theme/menu.blade.php no.1--}}
    <style>
        /* ===== ตัวแปรความกว้าง ===== */
        :root {
            --sidebar-width: 240px;
            --sidebar-collapsed-width: 70px;
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
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.25rem;
            
            color: #475569;
            margin-bottom: 0.25rem;
            border-radius: 10px;
            text-decoration: none;
            
            transition: all 0.2s ease;
        }

        .sidebar a .menu-label {
            margin-left: 14px;
        }

        .sidebar a:hover {
            background: #e9e9e9;
            color: #0f172a;
        }

        /* เมนูเดี่ยวทั่วไปเมื่อ Active */
        .sidebar a.active:not(.toggle) {
            background: #e9e9e9;
            color: #0f172a;
            font-weight: 600;
        }

        /* เมนูใหญ่ (Toggle) จะเป็นสีเทาเฉพาะตอนที่เมนูนั้น Active (มีหน้าย่อยเปิดใช้งานอยู่) เท่านั้น
           การกดเปิดแท็บดูเมนูย่อยเฉยๆ จะไม่เป็นสีเทาค้าง */
        .sidebar .toggle.active {
            background-color: #eeeeee !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            border: 0.1px solid #abb1c0 !important;
        }

        .sidebar .toggle.active:hover {
            background-color: #dedede !important;
            color: #0f172a !important;
        }

        /* ===== เมนูย่อยแบบมีเส้นเชื่อมโยง (Tree Connecting Lines ตามรูปที่ 2) ===== */
        .sidebar .child {
            position: relative;
            margin-left: 1.8rem;
            padding-left: 1.2rem;
            padding-top:0.5rem;
            border-left: 2px solid #cbd5e1; /* เส้นแนวตั้งเชื่อมจากเมนูแม่ลงมา */
            margin-top: -0.1rem;
            margin-bottom: 0.5rem;
        }

        /* กล่องเมนูย่อย: การ์ดสีขาวมีกรอบโค้งมนตามรูปที่ 2 */
        .sidebar .child a {
            display: flex;
            align-items: center;
            position: relative;
            padding: 0.6rem 0.8rem;
            margin-bottom: 0.35rem;
            border-radius: 16px !important;
            font-size: 13px;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569;
            transition: all 0.15s ease;
        }

        .sidebar .child a .menu-icon {
            font-size: 12px;
            color: #64748b;
            transition: color 0.15s ease;
        }

        .sidebar .child a .menu-label {
            margin-left: 10px;
        }

        /* เส้นกิ่งแนวนอนเชื่อมจากเส้นตั้งเข้าหากรอบเมนูย่อย (ตามรูปที่ 2) */
        .sidebar .child a::before {
            content: '';
            position: absolute;
            left: -1.2rem;
            top: 50%;
            width: 1.2rem;
            height: 1px;
            background-color: #cbd5e1;
            transform: translateY(-50%);
        }

        .sidebar .child a:hover {
            background-color: #f9f9f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a;
        }

        .sidebar .child a:hover::before {
            background-color: #94a3b8;
        }

        .sidebar .child a:hover .menu-icon {
            color: #0f172a;
        }

        /* เมื่อเมนูย่อย Active: กรอบเข้มขึ้นและเส้นเชื่อมเข้มตาม */
        .sidebar .child a.active {
            background-color: #ffffff !important;
            border: 1px solid #0f172a !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
        }

        .sidebar .child a.active:hover {
            background-color: #f9f9f9 !important;
            border: 1.5px solid #0f172a !important; /* กรอบเข้มเด่นชัด */
            color: #0f172a !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
        }

        .sidebar .child a.active::before {
            background-color: #0f172a !important; /* เส้นเชื่อมกิ่งเข้มตาม */
        }

        .sidebar .child a.active .menu-icon {
            color: #0f172a !important;
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
        /* ===== เมื่อ Sidebar อยู่ในสถานะ พับ (collapsed) ===== */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* ซ่อนข้อความและลูกศรเมื่อพับ */
        .sidebar.collapsed .menu-label,
        .sidebar.collapsed .arrow,
        .sidebar.collapsed .footer-text {
            display: none !important;
        }

        /* จัดไอคอนเมนูหลักตอนพับให้เป็นวงกลมตรงกลาง */
        .sidebar.collapsed > .menu-wrapper > a,
        .sidebar.collapsed .toggle {
            width: 44px !important;
            height: 44px !important;
            padding: 0 !important;
            margin: 6px auto !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 2px solid transparent !important;
            position: relative !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease;
        }

        .sidebar.collapsed > .menu-wrapper > a:hover,
        .sidebar.collapsed .toggle:hover {
            background-color: #e9e9e9 !important;
            border-color: transparent !important;
        }

        /* เมนูย่อตอน Active: ปรับสีพื้นหลังให้เป็น #e9e9e9 ตรงกับตอนเปิดเมนู */
        .sidebar.collapsed > .menu-wrapper > a.active,
        .sidebar.collapsed .toggle.active {
            background-color: #eeeeee !important;
            color: #0f172a !important;
            border: 0.1px solid #abb1c0 !important;
            border-radius: 50% !important;
        }

        /* เมนูย่อยในโหมดพับ: เรียงแนวดิ่งพร้อมเส้นเชื่อมตรงกลาง */
        .sidebar.collapsed .child {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 auto;
            padding: 2px 0 6px 0;
            border-left: none !important;
            position: relative;
            width: 100%;
        }

        .sidebar.collapsed .child::before {
            content: '';
            display: block !important;
            position: absolute;
            top: -4px;
            bottom: 28px;
            left: 50%;
            width: 1px;
            background-color: #727b86 !important;
            transform: translateX(-50%);
            z-index: 1;
        }

        .sidebar.collapsed .child a {
            width: 28px !important;
            height: 28px !important;
            padding: 0 !important;
            margin: 5px auto !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            position: relative !important;
            z-index: 2 !important;
            box-shadow: none !important;
            transition: all 0.15s ease;
        }

        .sidebar.collapsed .child a::before {
            display: none !important;
        }

        .sidebar.collapsed .child a .menu-icon {
            font-size: 11px;
            margin: 0;
            color: #64748b;
        }

        .sidebar.collapsed .child a:hover {
            background-color: #f9f9f9 !important;
            border-color: #94a3b8 !important;
            transform: scale(1.1);
        }

        .sidebar.collapsed .child a:hover .menu-icon {
            color: #0f172a;
        }

        .sidebar.collapsed .child a.active {
            border: 2px solid #1e2d50 !important;
            background-color: #ffffff !important; /* การ์ดสีขาวกรอบเข้มเหมือนตอนเปิดเมนู */
        }

        .sidebar.collapsed .child a.active .menu-icon {
            color: #0f172a !important;
        }

        .sidebar.collapsed .menu-wrapper {
            padding: 0.5rem 0;
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 0.8rem 0;
            justify-content: center;
        }

        .sidebar.collapsed .menu-icon {
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>


@php
    $active = $active_menu ?? '';

    $menus = [
        [
            'label' => 'จัดการข้อมูล',
            'icon' => '<i class="fas fa-user-plus"></i>',
            'url' => '',
            'key' => 'manage_data',
            'children' => [
             [
                    'icon' => '<i class="fas fa-leaf"></i>',
                    'label' => 'อาจารย์และเจ้าหน้าที่',
                    'url' => url('/'),

                    'key' => 'page_teacherandofficer',
                ],
                [
                    'icon' => '<i class="fas fa-leaf"></i>',
                    'label' => 'ข้าวโพด',
                    'url' => url('home/corn'),
                    'key' => 'das',
                ],
            ],
        ],
        [
            'label' => 'ผลผลิตจากฟาร์ม',
            'icon' => '<i class="fas fa-seedling"></i>',
            'url' => '',
            'key' => 'farm',
            'children' => [
                [
                    'icon' => '<i class="fas fa-leaf"></i>',
                    'label' => 'ข้าวโพด',
                    'url' => url('home/corn'),
                    'key' => 'dashboard',
                ],
                [
                    'icon' => '<i class="fas fa-leaf"></i>',
                    'label' => 'ข้าวโพด',
                    'url' => url('home/corn'),
                    'key' => 'das',
                ],
            ],
        ],
        [
            'label' => 'เครื่องดื่ม',
            'icon' => '<i class="fas fa-wine-glass"></i>',
            'url' => '',
            'key' => 'drinks',
            'children' => [
                [
                    'icon' => '<i class="fas fa-martini-glass"></i>',
                    'label' => 'Beverages',
                    'url' => url('home/drinks'),
                    'key' => 'drinks',
                ],
            ],
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

