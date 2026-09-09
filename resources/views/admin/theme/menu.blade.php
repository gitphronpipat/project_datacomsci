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
    padding: 0.7rem 0 0.5rem 0;   /* top = 1.5rem, bottom = 0.5rem */
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
    transition: background 0.15s, padding 0.25s ease, gap 0.25s ease;
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
    box-shadow: inset 0 0 5px rgba(0,0,0,0.05);

  }

  /* ===== เมนูย่อย ===== */
  .sidebar .child a {
    padding-left: 2.2rem;
    font-size: 13px;
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
    display: none;
  }

  /* จัดให้ไอคอนอยู่กึ่งกลาง */
  .sidebar.collapsed a {
    justify-content: center;

  }

  /* ซ่อนเมนูย่อยทั้งหมด (ไม่ให้แสดงตอนพับ) */
  .sidebar.collapsed .child {
    display: none;
  }

  /* ปรับ padding ของ wrapper และ footer ให้แคบลง */
  .sidebar.collapsed .menu-wrapper {
    padding: 0.5rem 0;
  }
  .sidebar.collapsed .sidebar-footer {
    padding: 0.8rem 0;
    justify-content: center;
  }

  /* ไอคอนใน footer ให้อยู่กลาง */
  .sidebar.collapsed .sidebar-footer .footer-icon {
    margin: 0;
  }
</style>

@php
    $active = $active_menu ?? '';

    $menus = [
        [
            'label'    => 'เพิ่มแอดมิน',
            'icon'     => '<i class="fas fa-user-plus"></i>',
            'url'      => url('/'),
            'key'      => 'dashboard',
            'children' => []
        ],
        [
            'label'    => 'ผลผลิตจากฟาร์ม',
            'icon'     => '<i class="fas fa-seedling"></i>',
            'url'      => '',
            'key'      => 'farm',
            'children' => [
                [
                    'icon'  => '<i class="fas fa-leaf"></i>',
                    'label' => 'ข้าวโพด',
                    'url'   => url('home/corn'),
                    'key'   => 'corn'
                ],
            ]
        ],
        [
            'label'    => 'เครื่องดื่ม',
            'icon'     => '<i class="fas fa-wine-glass"></i>',
            'url'      => '',
            'key'      => 'drinks',
            'children' => [
                [
                    'icon'  => '<i class="fas fa-martini-glass"></i>',
                    'label' => 'Beverages',
                    'url'   => url('home/drinks'),
                    'key'   => 'drinks'
                ],
            ]
        ],
    ];
@endphp

<div class="sidebar" id="sidebar">
    <div class="menu-wrapper">
        @foreach ($menus as $menu)
            @php
                $hasChildren = !empty($menu['children']);
                $groupActive = ($active === $menu['key']);
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
                <a class="toggle {{ $groupActive ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   href="#grp-{{ $menu['key'] }}"
                   aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                    <span>
                        <span class="menu-icon">{!! $menu['icon'] !!}</span>
                        <span class="menu-label">{{ $menu['label'] }}</span>
                    </span>
                    <i class="fas fa-chevron-down arrow"></i>
                </a>

                <div class="collapse {{ $groupActive ? 'show' : '' }}" id="grp-{{ $menu['key'] }}">
                    <div class="child">
                        @foreach ($menu['children'] as $child)
                            <a href="{{ $child['url'] }}"
                               class="{{ $active === $child['key'] ? 'active' : '' }}">
                                <span class="menu-icon">{!! $child['icon'] !!}</span>
                                <span class="menu-label">{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $menu['url'] }}"
                   class="{{ $active === $menu['key'] ? 'active' : '' }}">
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

