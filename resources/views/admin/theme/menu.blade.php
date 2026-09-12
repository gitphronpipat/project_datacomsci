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
        background: #fff;
        border-right: 1px solid rgba(203, 213, 225, 0.7);
        overflow: hidden;
        padding: 0;
        font-size: 14px;
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

    /* ===== Footer ===== */
    .sidebar-footer {
        flex-shrink: 0;
        padding: 0.8rem 1rem;
        border-top: 1px solid rgba(203, 213, 225, 0.7);
        background: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
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
        padding: 0.7rem 1.25rem;
        color: inherit;
        margin-bottom: 0.2rem;
        border-radius: 50px;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: background 0.15s, padding 0.25s ease;
    }

    .sidebar a .menu-label {
        margin-left: 20px;

    }

    .sidebar a:hover {
        background: #f0f0f0;
        color: #222;
    }

    .sidebar a.active {
        border-left-color: #4a5d23;
        background: #eef2e6;
        color: inherit;
        font-weight: 600;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05);

    }

    /* ===== เมนูย่อยแบบมีเส้นเชื่อมโยง (Tree connecting lines) ===== */
    .sidebar .child {
        position: relative;
        margin-left: 1.5rem;
        padding-left: 1rem;
        border-left: 2px solid #cbd5e1;
        /* เส้นแนวตั้งเชื่อมจากเมนูแม่ลงมา */
        margin-top: 0.35rem;
        margin-bottom: 0.5rem;
    }

    .sidebar .child a {
        position: relative;
        padding: 0.5rem 0.9rem;
        margin-bottom: 0.4rem;
        border-radius: 20px !important;
        /* กรอบสี่เหลี่ยมมุมมน */
        font-size: 13px;
        background: #fff;
        border: 1px solid #e2e8f0 !important;
        /* กรอบของเมนูย่อย */
        border-left-width: 1px !important;
        transition: all 0.2s ease;
    }

    .sidebar .child a .menu-label {
        margin-left: 10px;
    }

    /* เส้นแนวนอนยื่นออกมาเชื่อมจากเส้นตั้งเข้าหากรอบเมนูย่อย */
    .sidebar .child a::before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 50%;
        width: 1rem;
        height: 2px;
        background-color: #cbd5e1;
        transform: translateY(-50%);
    }

    .sidebar .child a:hover {
        background: #f8fafc;
        border-color: #94a3b8 !important;
        color: #1e293b;
    }

    .sidebar .child a:hover::before {
        background-color: #94a3b8;
    }

    /* เมื่อเมนูย่อย Active: ไม่ถมสีทึบ มีแค่กรอบและเส้นเชื่อมที่เข้มขึ้นตามรูปที่ 2 */
    .sidebar .child a.active {
        background: #fff !important;
        border: 2px solid #25396f !important;
        /* กรอบเข้มขึ้น */
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .sidebar .child a.active::before {
        background-color: #25396f !important;
        /* เส้นเชื่อมเข้มตาม */
    }


    /* ===== Toggle (หัวข้อที่ขยายได้) ===== */
    .sidebar .toggle {
        display: flex;
        justify-content: space-between;

    }

    .sidebar .toggle .arrow {
        font-size: 10px;
        transition: transform 0.2s;
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

    /* แบบใหม่: แถบสีเขียวเฉพาะฝั่งซ้ายครึ่งเดียวเหมือนตอนเปิด */
    .sidebar.collapsed>.menu-wrapper>a.active,
    .sidebar.collapsed .toggle.active {
        background-color: #eef2e6 !important;
        border: none !important;
        border-left: 3px solid #4a5d23 !important;
        /* แถบสีเขียวเฉพาะด้านซ้าย */
        border-radius: 50% !important;
        /* ด้านซ้ายตรง ด้านขวาโค้งมน หรือใช้ 50px รอบวง */

    }


    /* ===== เมื่อ Sidebar พับ (collapsed) แล้วกางเมนูย่อย ===== */
    .sidebar.collapsed .child {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 auto;
        padding: 0;
        border-left: none !important;
        position: relative;
        width: 100%;
    }

    /* เส้นเชื่อมแนวตั้งเส้นเดียวตรงกลาง: เชื่อมจากใต้ปุ่มแม่ลงมา และหยุดที่กึ่งกลางของวงกลมสุดท้ายพอดี */
    .sidebar.collapsed .child::before {
        content: '';
        display: block !important;
        position: absolute;
        top: -4px;
        bottom: 24px;
        /* หยุดตรงที่จุดกึ่งกลางของวงกลมสุดท้าย (ถูกวงกลมทับ ไม่โผล่เลยลงไปข้างล่าง) */
        left: 50%;
        width: 2px;
        background-color: #cbd5e1;
        transform: translateX(-50%);
        z-index: 1;
    }

    /* ไอคอนเมนูย่อยในโหมดพับ: เป็นวงกลมสีขาวทับเส้นเชื่อมไว้ */
    .sidebar.collapsed .child a {
        width: 36px !important;
        height: 36px !important;
        padding: 0 !important;
        margin: 6px auto !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        position: relative !important;
        z-index: 2 !important;
        /* อยู่เหนือเส้นเชื่อมเสมอ */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        box-sizing: border-box !important;
        transition: all 0.2s ease;
    }

    /* ซ่อนเส้นแนวนอนเดิมและ pseudo-element ทั้งหมดบนลิงก์เมนูย่อย ไม่ให้มีอะไรแทงทะลุวงกลม */
    .sidebar.collapsed .child a::before,
    .sidebar.collapsed .child a::after {
        display: none !important;
        content: none !important;
    }

    .sidebar.collapsed .child a .menu-icon {
        font-size: 13px;
        margin: 0;
        color: #475569;
    }

    /* เมื่อเอาเมาส์ชี้ที่วงกลมเมนูย่อย */
    .sidebar.collapsed .child a:hover {
        background-color: #ffffff !important;
        border-color: #25396f !important;
        transform: scale(1.08);
    }

    /* เมื่อวงกลมเมนูย่อย Active: ขอบวงกลมหนาและเข้มขึ้น พื้นหลังสีขาวทึบ */
    .sidebar.collapsed .child a.active {
        border: 2px solid #25396f !important;
        background-color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(37, 57, 111, 0.25);
    }

    .sidebar.collapsed .child a.active .menu-icon {
        color: #25396f;
    }

    /* ปรับ padding ของ wrapper และ footer ให้แคบลง */
    .sidebar.collapsed .menu-wrapper {
        padding: 0.5rem 0;
    }

    .sidebar.collapsed .sidebar-footer {
        padding: 0.8rem 0;
        justify-content: center;
    }

    /* ===== ตัวบอกจำนวนเมนูย่อยและลูกศรจิ๋วตอนพับ Sidebar ===== */
    .menu-icon-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* ตอนเปิดเมนูปกติ: ซ่อนตัวบอกจำนวนไว้ */
    .sub-indicator {
        display: none;
    }

    /* ตอนพับเมนู: แสดง Badge ตัวเลขเมนูย่อยและลูกศรจิ๋วหลอมรวมแนบกับไอคอนตรงกลางล่าง */
    .sidebar.collapsed .sub-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        bottom: -7px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #25396f;
        color: #ffffff;
        font-size: 9px;
        font-weight: 700;
        line-height: 1;
        padding: 2px 5px;
        border-radius: 10px;
        border: 1.5px solid #ffffff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
        z-index: 5;
        pointer-events: none;
        transition: all 0.2s ease;
    }

    .sidebar.collapsed .sub-indicator .sub-arrow {
        font-size: 7px;
        margin-left: 2px;
        transition: transform 0.2s ease;
    }

    /* เมื่อเปิดขยายรายการเมนูย่อย (เปิดลูกศรอยู่): ตัวเลขหายไป เหลือเฉพาะลูกศรชี้ขึ้น */
    .sidebar.collapsed .toggle[aria-expanded="true"] .sub-indicator {
        padding: 3px 5px;
    }

    .sidebar.collapsed .toggle[aria-expanded="true"] .sub-indicator .sub-count {
        display: none;
    }

    .sidebar.collapsed .toggle[aria-expanded="true"] .sub-indicator .sub-arrow {
        margin-left: 0;
        transform: rotate(180deg);
    }
</style>

@php
    $active = $active_menu ?? '';

    $menus = [
        [
            'label' => 'จัดการข้อมูล',
            'icon' => '<i class="fas fa-user-plus"></i>',
            'url' => '',
            'key' => '',
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
    <div class="menu-wrapper">
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
                @php $childCount = count($menu['children']); @endphp
                <a class="toggle {{ $groupActive ? 'active' : '' }}" data-bs-toggle="collapse"
                    href="#grp-{{ $menu['key'] }}" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                    <span class="d-inline-flex align-items-center">
                        <span class="menu-icon-wrapper">
                            <span class="menu-icon">{!! $menu['icon'] !!}</span>
                            <span class="sub-indicator">
                                <span class="sub-count">{{ $childCount }}</span>
                                <i class="fas fa-chevron-down sub-arrow"></i>
                            </span>
                        </span>
                        <span class="menu-label">{{ $menu['label'] }}</span>
                    </span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>

                <div class="collapse {{ $groupActive ? 'show' : '' }}" id="grp-{{ $menu['key'] }}">
                    <div class="child">
                        @foreach ($menu['children'] as $child)
                            <a href="{{ $child['url'] }}" class="{{ $active === $child['key'] ? 'active' : '' }}">
                                <span class="menu-icon">{!! $child['icon'] !!}</span>
                                <span class="menu-label">{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $menu['url'] }}" class="{{ $active === $menu['key'] ? 'active' : '' }}">
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
