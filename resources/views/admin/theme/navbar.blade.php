<nav class="navbar navbar-expand bg-white border-bottom border-slate-200/70 px-4" style="height: 60px;">
    <div class="container-fluid">
        <!-- ปุ่ม toggle sidebar -->
        <button class="btn btn-link text-black border-0 p-2 me-2"
                type="button"
                id="sidebarToggleBtn"
                title="พับ/ขยายเมนู">
            <i class="fas fa-bars" id="toggleIcon"></i>
        </button>

        <!-- โลโก้ LUMIÈRE -->
        <a class="navbar-brand text-black me-auto "
           href="{{ url('/') }}"
           style="letter-spacing: 0.18em; ">
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
