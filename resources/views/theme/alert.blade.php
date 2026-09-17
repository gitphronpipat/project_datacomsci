<!-- =========================================================
     ระบบ Modal กล่องแจ้งเตือนยืนยันการตัดสินใจ (theme/alert.blade.php)
     ครอบคลุม: ยืนยันออกจากระบบ, ยืนยันลบข้อมูล, และยืนยันเปลี่ยนสถานะ/การกระทำสำคัญ
     ดีไซน์: สบายตา โปร่งโล่ง ข้อความกระชับ อยู่ตรงกลางจอเด่นชัด
     ========================================================= -->
<style>

    .alert-i-con{
        margin-bottom: 2rem !important;
    }
    .alert-btn{
        gap: 1rem !important;

    }
</style>
<!-- 1. Modal ยืนยันออกจากระบบ -->
<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body text-center p-4 pt-4 pb-5">
                <div class="alert-i-con">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle shadow-sm"
                         style="width: 76px; height: 76px; font-size: 30px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-3 fs-5" id="modalLogoutLabel">
                    ยืนยันการออกจากระบบ?
                </h4>
                <p class="text-muted small" style="margin-bottom: 2rem !important">
                    คุณต้องการออกจากระบบใช่หรือไม่
                </p>
                <div class="d-flex justify-content-center alert-btn">
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <a href="{{ url('auth/logout') }}" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> ออก
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Modal ยืนยันการลบข้อมูล (เชื่อมกับ deleteinfotable.js อัตโนมัติ) -->
<div class="modal fade" id="modalDel" tabindex="-1" aria-labelledby="modalDelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body text-center p-4 pt-4 pb-5">
                <div class="alert-i-con">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle shadow-sm"
                         style="width: 76px; height: 76px; font-size: 30px;">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-3 fs-5" id="modalDelLabel">
                    ยืนยันการลบข้อมูล?
                </h4>
                <p class="text-muted small" style="margin-bottom: 2rem !important">
                    ต้องการลบข้อมูล <strong class="text-danger" id="del_name"></strong> ใช่หรือไม่
                </p>
                <div class="d-flex justify-content-center alert-btn">
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <a id="btnConfirmDelete" href="#" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm">
                        <i class="fas fa-trash-alt me-1"></i> ลบ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Modal กลางยืนยันการกระทำสำคัญ / เปิด-ปิดสถานะ (Action Confirmation Modal) -->
<div class="modal fade" id="modalConfirmAction" tabindex="-1" aria-labelledby="modalConfirmActionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body text-center p-4 pt-4 pb-5">
                <div class="alert-i-con">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle shadow-sm"
                         id="confirmActionIconBox"
                         style="width: 76px; height: 76px; font-size: 30px;">
                        <i class="fas fa-question-circle" id="confirmActionIcon"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-3 fs-5" id="modalConfirmActionLabel">
                    ยืนยันการทำรายการ?
                </h4>
                <p class="text-muted small" style="margin-bottom: 2rem !important" id="confirmActionMessage">
                    คุณต้องการดำเนินการใช่หรือไม่
                </p>
                <div class="d-flex justify-content-center alert-btn">
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <a id="btnConfirmActionSubmit" href="#" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                        ยืนยัน
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ดักฟังการเรียกใช้ Modal ยืนยันการกระทำทั่วไป (modalConfirmAction)
    $(document).ready(function() {
        $('#modalConfirmAction').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            if (!button.length) return;

            const title = button.data('title') || 'ยืนยันการทำรายการ?';
            const message = button.data('message') || 'คุณต้องการดำเนินการใช่หรือไม่';
            const url = button.data('url') || '#';
            const btnText = button.data('btn-text') || 'ยืนยัน';
            const btnClass = button.data('btn-class') || 'btn-primary';
            const iconClass = button.data('icon') || 'fas fa-question-circle';
            const iconBoxClass = button.data('icon-box') || 'bg-primary-subtle text-primary';

            $('#modalConfirmActionLabel').text(title);
            $('#confirmActionMessage').html(message);
            $('#btnConfirmActionSubmit')
                .attr('href', url)
                .text(btnText)
                .removeClass()
                .addClass('btn rounded-pill px-4 py-2 shadow-sm ' + btnClass);

            $('#confirmActionIcon').removeClass().addClass(iconClass);
            $('#confirmActionIconBox').removeClass().addClass('d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm ' + iconBoxClass);
        });
    });
</script>
