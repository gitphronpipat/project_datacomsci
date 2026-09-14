{{-- resources/views/admin/theme/menu.blade.php --}}
<style>
    /* ===== ตัวแปรความกว้าง ===== */
    :root {
        --sidebar-width: 240px;
        --sidebar-collapsed-width: 70px;
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
        border-right: 1px solid #f1f5f9;
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
        /* top = 1.5rem, bottom = 0.5rem */
        margin-left: 10px;
        margin-right: 10px;
        transition: padding 0.25s ease;
    }

    /* สไตล์ Scrollbar บางเฉียบระดับ SaaS สากล */
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

    /* ===== Footer ===== */
    .sidebar-footer {
        flex-shrink: 0;
        padding: 0.8rem 1rem;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        color: #94a3b8;
        transition: padding 0.25s ease;
    }

    .sidebar-footer .footer-text {
        display: flex;
        flex-direction: column;
        line-height: 1.4;
    }

    /* ===== ลิงก์ทั่วไป ===== */
    .sidebar a {
        display: flex;
        align-items: center;
        padding: 0.65rem 1.25rem;
        color: #475569;
        margin-bottom: 0.2rem;
        border-radius: 50px;
        text-decoration: none;
        border: none;
        transition: background 0.15s ease, color 0.15s ease, padding 0.25s ease;
    }

    .sidebar a .menu-label {
        margin-left: 14px;
    }

    .sidebar a:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .sidebar a.active {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 600;
        box-shadow: none;
    }

    /* เมื่อเมนูหลักเป็น Toggle (มีเมนูย่อยที่เปิดอยู่): ไม่ใส่สีพื้นหลัง ให้โปร่งใส เพื่อความโปร่งโล่ง สบายตา */
    .sidebar .toggle.active {
        background: transparent !important;
        color: #0f172a !important;
        font-weight: 600 !important;
    }

    .sidebar .toggle.active:hover {
        background: #f8fafc !important;
    }

    /* ===== เมนูย่อยแบบโปร่งสะอาดตา สไตล์ Modern SaaS (Clean Indented Rail) ===== */
    .sidebar .child {
        position: relative;
        margin-left: 1.6rem;
        padding-left: 0.6rem;
        border-left: 1.5px solid #e2e8f0;
        margin-top: 0.25rem;
        margin-bottom: 0.4rem;
    }

    .sidebar .child a {
        display: flex;
        align-items: center;
        position: relative;
        padding: 0.45rem 1rem;
        margin-bottom: 0.2rem;
        border-radius: 50px !important;
        font-size: 13.5px;
        background: transparent !important;
        border: none !important;
        color: #64748b;
        transition: all 0.15s ease;
    }

    .sidebar .child a .menu-icon {
        font-size: 12px;
        color: #94a3b8;
        transition: color 0.15s ease;
    }

    .sidebar .child a .menu-label {
        margin-left: 10px;
    }

    /* ซ่อนกิ่งแนวนอนเดิมทั้งหมดเพื่อความคลีนตา ไร้ลูกโป่งเสียบก้าน */
    .sidebar .child a::before,
    .sidebar .child a::after {
        display: none !important;
        content: none !important;
    }

    .sidebar .child a:hover {
        background-color: #f8fafc !important;
        color: #0f172a;
    }

    .sidebar .child a:hover .menu-icon {
        color: #0f172a;
    }

    /* เมื่อเมนูย่อย Active: ไฮไลต์ทรง Pill สีเทานวลนุ่มๆ เข้าชุดกับเมนูหลัก */
    .sidebar .child a.active {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: none !important;
    }

    .sidebar .child a.active .menu-icon {
        color: #0f172a !important;
    }

    /* ===== Toggle (หัวข้อที่ขยายได้) ===== */
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

    /* ซ่อนข้อความ, ลูกศร, และข้อความ footer */
    .sidebar.collapsed .menu-label,
    .sidebar.collapsed .arrow,
    .sidebar.collapsed .footer-text {
        display: none !important;
    }

    /* จัดให้ไอคอนของเมนูหลักอยู่กึ่งกลางและเป็นวงกลมเท่ากันทั้งหมด */
    .sidebar.collapsed>.menu-wrapper>a,
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

    .sidebar.collapsed>.menu-wrapper>a:hover,
    .sidebar.collapsed .toggle:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1 !important;
    }

    /* เมนูย่อแบบพับเมื่อ Active หรือเมื่อถูกคลิกเปิดอยู่: ขอบเข้มคมชัด #0f172a */
    .sidebar.collapsed>.menu-wrapper>a.active,
    .sidebar.collapsed .toggle.active,
    .sidebar.collapsed .toggle[aria-expanded="true"] {
        background-color: #f1f5f9 !important;
        border: 1.5px solid #0f172a !important;
        border-radius: 50% !important;
    }


    /* ===== เมื่อ Sidebar พับ (collapsed) แล้วกางเมนูย่อยแบบเพรียวบาง สบายตา ===== */
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

    /* เส้นเชื่อมแนวตั้ง 1px นำสายตาสีเข้มคมชัด (#0f172a) สิ้นสุดที่จุดศูนย์กลางของไอคอนลูกตัวสุดท้าย */
    .sidebar.collapsed .child::before {
        content: '';
        display: block !important;
        position: absolute;
        top: -4px;
        bottom: 28px;
        left: 50%;
        width: 1px;
        background-color: #0f172a;
        transform: translateX(-50%);
        z-index: 1;
    }

    /* สไตล์แบบที่ 1: เส้นคั่นสั้นมินิมอล (Minimal Soft Dash) ปิดท้ายกลุ่มเมนูย่อย คั่นกับเมนูหลักถัดไป (แสดงเฉพาะตอนกดเปิดเมนูย่อย) */
    .sidebar.collapsed .child::after {
        content: '';
        display: block !important;
        width: 26px;
        height: 1px;
        background-color: #e2e8f0;
        margin: 8px auto 4px auto;
        border-radius: 1px;
    }

    /* วงกลมเมนูย่อย: ขนาดเพรียวลง 28px น่ารัก มินิมอล ชัดเจนว่าเป็นเมนูลูก */
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
        box-sizing: border-box !important;
        transition: all 0.15s ease;
    }

    /* ซ่อน pseudo-elements ที่อาจแทรกเข้ามา */
    .sidebar.collapsed .child a::before,
    .sidebar.collapsed .child a::after {
        display: none !important;
        content: none !important;
    }

    .sidebar.collapsed .child a .menu-icon {
        font-size: 11px;
        margin: 0;
        color: #64748b;
        transition: color 0.15s ease;
    }

    /* เมื่อเอาเมาส์ชี้ที่วงกลมเมนูย่อย */
    .sidebar.collapsed .child a:hover {
        background-color: #f8fafc !important;
        border-color: #0f172a !important;
        transform: scale(1.1);
    }

    .sidebar.collapsed .child a:hover .menu-icon {
        color: #0f172a;
    }

    /* เมื่อวงกลมเมนูย่อย Active: ขอบเข้มคมชัด ขนาดกะทัดรัด */
    .sidebar.collapsed .child a.active {
        border: 1.5px solid #0f172a !important;
        background-color: #f1f5f9 !important;
        box-shadow: none !important;
    }

    .sidebar.collapsed .child a.active .menu-icon {
        color: #0f172a !important;
    }

    /* ปรับ padding ของ wrapper และ footer ให้แคบลง */
    .sidebar.collapsed .menu-wrapper {
        padding: 0.5rem 0;
    }

    .sidebar.collapsed .sidebar-footer {
        padding: 0.8rem 0;
        justify-content: center;
    }

    /* ไอคอนเมนูตอนพับ: จัดให้อยู่กึ่งกลางวงกลมคมชัด ขนาดพอดีสายตา */
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
            'icon' => '<i class="fas fa-database"></i>',
            'url' => '',
            'key' => 'data',
            'children' => [
             [
                    'icon' => '<i class="fas fa-user-shield"></i>',
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
