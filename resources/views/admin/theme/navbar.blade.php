<style>
    #sidebarToggleBtn:hover {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
    }
</style>

<nav class="navbar navbar-expand bg-white p-0 pe-4" style="height: 60px; border-bottom: 1px solid #f1f5f9;">
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
           style="letter-spacing: 0.16em; font-weight: 600; font-size: 0.95rem; color: #0f172a;">
            LUMIÈRE
        </a>

        <!-- ส่วนขวา (ยังคงคอมเมนต์ไว้) -->
        {{-- <div class="d-flex align-items-center gap-3">
            <span class="bg-white/12 text-white text-sm tracking-[0.08em] px-[14px] py-1.5 rounded-full select-none">
                <i class="fa-solid fa-circle-user"></i>
                {{ session('username') }}
            </span>
            <a href="{{ url('auth/logout') }}"
               class="text-white/80 hover:text-white hover:bg-white/15 text-sm tracking-[0.08em] px-[14px] py-1.5 rounded-full border border-white/30 no-underline transition-all duration-200">
                Logout
            </a>
        </div> --}}
    </div>
</nav>

<script>
// ใน navbar.blade.php
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');   // ← ตรงกับ id ที่ตั้ง
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const mainContent = document.getElementById('main');

    if (!sidebar || !toggleBtn) return;

    function updateIcon(isCollapsed) {
        toggleIcon.className = isCollapsed ? 'fas fa-bars' : 'fas fa-bars';
    }

    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.style.marginLeft = '70px';  // 60px = collapsed
    }
    updateIcon(isCollapsed);

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        const collapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', collapsed);
        if (mainContent) {
            mainContent.style.marginLeft = collapsed ? '70px' : '240px';   // ← เปลี่ยน 220 → 240
        }
        updateIcon(collapsed);
    });
});
</script>
