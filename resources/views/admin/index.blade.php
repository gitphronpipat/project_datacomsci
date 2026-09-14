<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('component')


</head>
<body>

    @include('admin.theme.navbar')
    @include('admin.theme.menu')

    <div id="main">
        @include($content)
    </div>










    

<div class="modal fade" id="modalDel" tabindex="-1" aria-labelledby="modalDelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDelLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบข้อมูล
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูล <strong class="text-danger" id="del_name"></strong> <br>
                เมื่อลบแล้วจะไม่สามารถกู้คืนได้
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <a id="btnConfirmDelete" href="#" class="btn btn-danger">ยืนยันการลบ</a>
            </div>
        </div>
    </div>
</div>

    <!-- Modal สำหรับอัปโหลดและตัดรูปโปรไฟล์ (Dropzone + Cropper 2-in-1 กลาง) -->
    <div class="modal fade" id="modalCropImage" tabindex="-1" aria-labelledby="modalCropImageLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h5 class="modal-title fs-6 text-white" id="modalCropImageLabel">
                        <i class="fas fa-camera me-2"></i>อัปโหลดและปรับขนาดรูปโปรไฟล์ (1:1)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    
                    <!-- ส่วนที่ 1: กล่องลากวางรูปภาพ (Dropzone Area) -->
                    <div id="dropzoneArea" class="text-center p-4 border border-2 border-dashed rounded-3 bg-light" 
                         style="border-color: #08f376 !important; cursor: pointer; transition: all 0.3s ease;">
                        <div class="my-3">
                            <i class="fas fa-cloud-upload-alt fa-4x text-success mb-3"></i>
                            <h5 class="fw-bold text-dark">ลากรูปภาพมาวางที่นี่</h5>
                            <p class="text-muted small mb-3">
                                สามารถลากรูปจาก Google / เว็บไซต์อื่น หรือจากในเครื่องมาวางได้โดยตรง<br>
                                หรือกดปุ่มด้านล่างเพื่อเลือกรูปภาพ
                            </p>
                            <button type="button" class="btn btn-primary px-4 py-2 shadow-sm" id="btnSelectLocalFile">
                                <i class="fas fa-folder-open me-1"></i> เลือกรูปภาพจากเครื่อง
                            </button>
                        </div>
                    </div>

                    <!-- ส่วนที่ 2: หน้าจอตัดรูป (Cropper Area จะแสดงเมื่อได้รูปแล้ว) -->
                    <div id="cropperArea" style="display: none;">
                        <div class="d-flex justify-content-center align-items-center bg-dark rounded overflow-hidden" style="max-height: 420px; min-height: 280px;">
                            <img id="cropImageSource" src="" alt="Source Image" style="max-width: 100%; display: block;">
                        </div>
                        <!-- ปุ่มเครื่องมือเสริม หมุน/ซูม/รีเซ็ต -->
                        <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateLeft" title="หมุนซ้าย">
                                <i class="fas fa-undo"></i> หมุนซ้าย
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateRight" title="หมุนขวา">
                                <i class="fas fa-redo"></i> หมุนขวา
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropZoomIn" title="ซูมเข้า">
                                <i class="fas fa-search-plus"></i> ซูมเข้า
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropZoomOut" title="ซูมออก">
                                <i class="fas fa-search-minus"></i> ซูมออก
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropReset" title="รีเซ็ต">
                                <i class="fas fa-sync-alt"></i> รีเซ็ต
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnCropChangeImage" title="เลือกรูปใหม่">
                                <i class="fas fa-exchange-alt"></i> เปลี่ยนรูปใหม่
                            </button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-success" id="btnCropApply" style="display: none;">
                        <i class="fas fa-check me-1"></i> ใช้รูปนี้
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal สำหรับดูรูปภาพขนาดใหญ่ (View Image Modal กลาง) -->

    <div class="modal fade" id="modalViewImage" tabindex="-1" aria-labelledby="modalViewImageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white shadow" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="viewImageTarget" src="" alt="Full Image" 
                         class="img-fluid rounded shadow-lg border border-3 border-white" 
                         style="max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- ระบบแจ้งเตือนกลาง (iziToast Notifications) -->
    @include('notify')

</body>
</html>
