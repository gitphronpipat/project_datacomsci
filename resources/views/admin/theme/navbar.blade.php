<style>
    #sidebarToggleBtn:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }

    /* =========================================================
       Smooth User Profile Dropdown (สไตล์และแอนิเมชันแบบเดียวกับ custom_select)
       ========================================================= */
    .user-profile-wrapper {
        position: relative;
        user-select: none;
    }

    /* กล่องปุ่มกดโปรไฟล์ (Trigger) */
    .user-profile-trigger {
        background-color: #ffffff;
        border: 1.5px solid #dee2e6;
        border-radius: 50px;
        padding: 0.35rem 0.9rem 0.35rem 0.45rem;
        cursor: pointer;
        font-size: 14px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        outline: none;
    }

    /* เอฟเฟกต์ตอนนำเมาส์ไปชี้กล่อง (Hover) */
    .user-profile-trigger:hover {
        border-color: #86b7fe;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    }

    /* เมื่อเปิดเมนูออก: ขอบเรืองแสงสีฟ้าแบบเดียวกับ custom_select */
    .user-profile-wrapper.open .user-profile-trigger,
    .user-profile-trigger:focus {
        border-color: #91bdff;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.22);
    }

    /* วงกลมไอคอนผู้ใช้ */
    .user-avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 1px solid #bfdbfe;
        flex-shrink: 0;
        overflow: hidden;
    }

    /* ลูกศรชี้ลง หมุนกลับด้าน 180 องศาแบบสมูท */
    .user-profile-arrow {
        font-size: 11px;
        color: #6c757d;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s ease;
    }

    .user-profile-wrapper.open .user-profile-arrow {
        transform: rotate(180deg);
        color: #0d6efd;
    }

    /* แผงตัวเลือกที่เลื่อนสไลด์ลงมา (Slide Down + Fade-in + Scale) */
    .user-profile-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 235px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        z-index: 1050;
        padding: 6px;
        list-style: none;
        margin: 0;

        /* สถานะซ่อน: เลื่อนขึ้นเล็กน้อยและจางหาย */
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.97);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    /* สถานะเปิด: สไลด์ลงมาพร้อมปรากฏอย่างนุ่มนวล สมูท 100% */
    .user-profile-wrapper.open .user-profile-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* กล่องข้อมูลด้านบนของเมนู */
    .user-profile-header {
        padding: 7px 12px;
        background-color: #f8fafc;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
        margin-bottom: 4px;
    }

    /* แถบตัวเลือกแต่ละรายการ */
    .user-profile-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        font-size: 14px;
        font-weight: 500;
        color: #334155;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .user-profile-item:hover {
        background-color: #f1f5f9;
        color: #0f172a;
        padding-left: 18px; /* ขยับตัวหนังสือนิดๆ เพิ่มมิติเหมือน custom_select */
    }

    /* เมนูออกจากระบบ (สีแดง) */
    .user-profile-item.logout-item {
        color: #dc2626;
    }

    .user-profile-item.logout-item:hover {
        background-color: #fef2f2;
        color: #b91c1c;
        padding-left: 18px;
    }

    .user-menu-divider {
        height: 1px;
        background-color: #f1f5f9;
        margin: 5px 0;
    }
</style>

<nav class="navbar navbar-expand bg-white p-0 pe-4 border-bottom border-slate-200/70 sticky-top" style="height: 60px; border-bottom: 1px solid #f1f5f9;">
    <div class="container-fluid p-0 d-flex align-items-center h-100">
        <!-- กล่องสำหรับปุ่ม toggle sidebar: กว้าง 70px เท่ากับความกว้าง Sidebar ตอนพับ จัดกึ่งกลางตรงเป๊ะระดับพิกเซล -->
        <div class="d-flex align-items-center justify-content-center" style="width: 70px; height: 60px; flex-shrink: 0;">
            <button class="btn btn-link border-0 p-0 d-flex align-items-center justify-content-center"
                    type="button"
                    id="sidebarToggleBtn"
                    title="พับ/ขยายเมนู"
                    style="width: 38px; height: 38px; border-radius: 8px; color: #475569; text-decoration: none; transition: background 0.15s ease;">
                <i class="fas fa-bars" id="toggleIcon"></i>
            </button>
        </div>

        <!-- โลโก้ LUMIÈRE -->
        <a class="navbar-brand me-auto ps-2"
           href="{{ url('/') }}"
           style=" margin-left: 20px; letter-spacing: 0.16em; font-weight: 600; font-size: 0.95rem; color: #0f172a;">
            LUMIÈRE
        </a>

        @php
            $navUserPic = session('profile_picture');
            if ($navUserPic && !str_contains($navUserPic, 'teacherandofficer')) {
                $navUserPic = str_replace('profile_image/', 'profile_image/teacherandofficer/', $navUserPic);
            }
        @endphp

        <!-- ส่วนผู้ใช้งาน (Smooth Custom Dropdown สวยและสมูตแบบเดียวกับ custom_select) -->
        <div class="ms-auto d-flex align-items-center pe-2">
            <div class="user-profile-wrapper" id="userProfileWrapper">
                <div class="user-profile-trigger" id="userProfileTrigger" tabindex="0" role="button" title="เมนูผู้ใช้งาน">
                    <div class="user-avatar-circle">
                        @if(!empty($navUserPic) && file_exists(public_path($navUserPic)))
                            <img src="{{ asset($navUserPic) }}" alt="Avatar" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </div>
                    <span class="fw-semibold text-slate-800 text-truncate" style="max-width: 140px;">
                        {{ session('username') ?? 'Admin' }}
                    </span>
                    <i class="fas fa-chevron-down user-profile-arrow ms-1"></i>
                </div>

                <ul class="user-profile-menu">
                    <li class="user-profile-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-circle">
                                @if(!empty($navUserPic) && file_exists(public_path($navUserPic)))
                                    <img src="{{ asset($navUserPic) }}" alt="Avatar" class="w-100 h-100 view-image-trigger" style="object-fit: cover; cursor: pointer;" title="คลิกเพื่อดูรูปขนาดใหญ่">
                                @else
                                    <i class="fa-solid fa-user"></i>
                                @endif
                            </div>
                            
                            <div class="text-truncate">
                                <div class="fw-bold text-dark text-truncate" style="font-size: 14px;">
                                    {{ session('username') ?? 'Admin' }}
                                </div>
                            </div>
                            <span class="text-muted text-nowrap flex-shrink-0 ms-auto" style="font-size: 10px; opacity: 0.85;">
                                @if(session('role'))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10.5px; font-weight: 500;">
                                        <i class="fas fa-shield-halved"></i>   
                                    </span>
                                @endif
                            </span>
                        </div>
                    </li>

                    <li>
                        @if(session('role') === 'admin')
                            <a class="user-profile-item" href="{{ url('#') }}">
                                <i class="fas fa-database text-primary" style="width: 18px;"></i>
                                <span>ฐานข้อมูล</span>
                            </a>
                        @else
                            <a class="user-profile-item" href="{{ url('#') }}">
                                <i class="fas fa-sliders-h text-primary" style="width: 18px;"></i>
                                <span>ตั้งค่าระบบ</span>
                            </a>
                        @endif
                    </li>
                    <li class="user-menu-divider"></li>
                    <li>
                        <a class="user-profile-item logout-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalLogout">
                            <i class="fas fa-arrow-right-from-bracket" style="width: 18px;"></i>
                            <span>ออกจากระบบ</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. ระบบพับ/ขยาย Sidebar
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const mainContent = document.getElementById('main');

    if (sidebar && toggleBtn) {
        function updateIcon(isCollapsed) {
            toggleIcon.className = isCollapsed ? 'fas fa-bars' : 'fas fa-bars';
        }

        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            if (mainContent) mainContent.style.marginLeft = '70px';
        }
        updateIcon(isCollapsed);

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            const collapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', collapsed);
            if (mainContent) {
                mainContent.style.marginLeft = collapsed ? '70px' : '240px';
            }
            updateIcon(collapsed);
        });
    }

    // 2. ระบบ Smooth User Profile Dropdown (แบบเดียวกับ custom_select)
    const userWrapper = document.getElementById('userProfileWrapper');
    const userTrigger = document.getElementById('userProfileTrigger');

    if (userWrapper && userTrigger) {
        // คลิกเพื่อเปิด/ปิด พร้อม toggle ลูกศรและเมนูสไลด์
        userTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            userWrapper.classList.toggle('open');
        });

        // คลิกพื้นที่อื่นภายนอกเพื่อปิด
        document.addEventListener('click', function (e) {
            if (!userWrapper.contains(e.target)) {
                userWrapper.classList.remove('open');
            }
        });

        // กดปุ่ม Escape เพื่อปิด
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                userWrapper.classList.remove('open');
            }
        });

        // เมื่อคลิกดูรูปใหญ่ ให้พับ Dropdown เก็บอัตโนมัติ
        document.querySelectorAll('.view-image-trigger').forEach(function(img) {
            img.addEventListener('click', function() {
                userWrapper.classList.remove('open');
            });
        });
    }
});
</script>
