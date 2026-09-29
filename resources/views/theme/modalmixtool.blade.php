<div class="modal fade" id="modalCropImage" tabindex="-1" aria-labelledby="modalCropImageLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg justify-content-center">
        <div class="modal-content" style="width: 80%; margin: 0 auto; border-radius: 10px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title fs-6 text-white" id="modalCropImageLabel">
                    <i class="fas fa-camera me-2"></i>อัปโหลดและปรับขนาดรูปโปรไฟล์ (1:1)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">

                <!-- ส่วนที่ 1: กล่องลากวางรูปภาพ (Dropzone Area) -->
                <div id="dropzoneArea" class="text-center p-4 border border-2 border-dashed rounded-3 bg-light"
                    style="border-color: #08f376 !important; cursor: pointer; transition: all 0.3s ease;">
                    <div class="my-3">
                        <i class="fas fa-cloud-upload-alt fa-4x text-success mb-3"></i>
                        <h5 class="fw-bold text-dark">ลากรูปภาพมาวางที่นี่</h5>
                        <p class="text-muted small mb-3">
                            สามารถลากรูปจาก Google / เว็บไซต์<br>
                            หรือกดปุ่มด้านล่างเพื่อเลือกรูปภาพ
                        </p>
                        <button type="button" class="btn btn-primary px-4 py-2 shadow-sm" id="btnSelectLocalFile">
                            <i class="fas fa-folder-open me-1"></i> เลือกรูปภาพจากเครื่อง
                        </button>
                    </div>
                </div>

                <!-- ส่วนที่ 2: หน้าจอตัดรูป (Cropper Area จะแสดงเมื่อได้รูปแล้ว) -->
                <div id="cropperArea" style="display: none;">
                    <div class="d-flex justify-content-center align-items-center bg-dark rounded overflow-hidden"
                        style="max-height: 420px; min-height: 280px;">
                        <img id="cropImageSource" src="" alt="Source Image"
                            style="max-width: 100%; display: block;">
                    </div>
                    <!-- ปุ่มเครื่องมือเสริม หมุน/ซูม/รีเซ็ต -->
                    <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateLeft"
                            title="หมุนซ้าย">
                            <i class="fas fa-undo"></i> หมุนซ้าย
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateRight"
                            title="หมุนขวา">
                            <i class="fas fa-redo"></i> หมุนขวา
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropZoomIn"
                            title="ซูมเข้า">
                            <i class="fas fa-search-plus"></i> ซูมเข้า
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropZoomOut"
                            title="ซูมออก">
                            <i class="fas fa-search-minus"></i> ซูมออก
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropReset"
                            title="รีเซ็ต">
                            <i class="fas fa-sync-alt"></i> รีเซ็ต
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnCropChangeImage"
                            title="เลือกรูปใหม่">
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
                <button type="button" class="btn-close btn-close-white shadow" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="viewImageTarget" src="" alt="Full Image"
                    class="img-fluid rounded shadow-lg border border-3 border-white"
                    style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>


{{-- =========================================================
     Modal กลางสำหรับแก้ไขข้อมูลกลุ่มโครงงาน และ ข้อมูลนักศึกษา
     ========================================================= --}}

<!-- =========================================================
     1. Modal แก้ไขข้อมูลกลุ่มโครงงาน (Edit Project Modal)
     ========================================================= -->
<div class="modal fade" id="modalEditProject" tabindex="-1"
    aria-labelledby="modalEditProjectLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white border-0 px-4 py-3">
                <h5 class="modal-title fw-bold" id="modalEditProjectLabel">
                    <i class="fas fa-edit me-2"></i>แก้ไขข้อมูลกลุ่มโครงงานทั้งหมด
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editProjectIndex">

                <div class="row g-3">
                    <!-- ประเภทวิชา & หมายเลขกลุ่ม -->
                    <div class="col-12 col-md-6">
                        <label for="editProjectType" class="form-label fw-medium">ประเภทวิชา <span
                                class="text-danger">*</span></label>
                        <select class="form-select custom-select-smooth" id="editProjectType"
                            required>
                            <option value="project">โครงงาน (498)</option>
                            <option value="coop">สหกิจศึกษา (497)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="editProjectNo"
                            class="form-label fw-medium">ลำดับกลุ่มโครงงาน</label>
                        <input type="text" class="form-control" id="editProjectNo"
                            placeholder="เช่น 1, 2, 3">
                    </div>

                    <!-- ชื่อโครงงานภาษาไทย -->
                    <div class="col-12">
                        <label for="editProjectTitleTh" class="form-label fw-medium">ชื่อโครงงาน
                            (ภาษาไทย)</label>
                        <textarea class="form-control" id="editProjectTitleTh" rows="2" placeholder="ชื่อโครงงานภาษาไทย"></textarea>
                    </div>

                    <!-- ชื่อโครงงานภาษาอังกฤษ -->
                    <div class="col-12">
                        <label for="editProjectTitleEn" class="form-label fw-medium">ชื่อโครงงาน
                            (ภาษาอังกฤษ)</label>
                        <textarea class="form-control" id="editProjectTitleEn" rows="2" placeholder="Project Title in English"></textarea>
                    </div>

                    <!-- สถานที่ฝึกงาน / บริษัท (สำหรับสหกิจ) -->
                    <div class="col-12" id="editProjectCompanyContainer">
                        <label for="editProjectCompany" class="form-label fw-medium">สถานที่ฝึกงาน
                            / สหกิจ (ถ้ามี)</label>
                        <input type="text" class="form-control" id="editProjectCompany"
                            placeholder="เช่น บริษัท เอบีซี จำกัด">
                    </div>

                    <!-- อาจารย์ที่ปรึกษาและกรรมการ -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-1 mt-2">
                            <i class="fas fa-user-tie me-1"></i>อาจารย์ที่ปรึกษาและกรรมการ
                        </h6>
                        <div class="small text-muted mb-2">
                            <i class="fas fa-info-circle me-1"></i>สามารถเลือกจากรายชื่ออาจารย์ในระบบเพื่อความถูกต้อง หรือพิมพ์ระบุเองกรณีชื่อไม่อยู่ในระบบ
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="editProjectAdvisorPresident"
                            class="form-label fw-medium">ประธานอาจารย์ที่ปรึกษา <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-1">
                            <select class="custom-select-smooth edit-advisor-select" id="editProjectAdvisorPresidentSelect">
                                <option value="">-- เลือกอาจารย์ในระบบ --</option>
                                @if(!empty($teachers))
                                    @foreach($teachers->where('role', 'teacher') as $t)
                                        <option value="{{ $t->name }}" data-id="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="text" class="form-control"
                                id="editProjectAdvisorPresident" placeholder="หรือพิมพ์ชื่อระบุเอง เช่น ผศ.ดร. ...">
                            <input type="hidden" id="editProjectAdvisorPresidentId">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="editProjectAdvisorCommittee1"
                            class="form-label fw-medium">กรรมการ 1</label>
                        <div class="d-flex flex-column gap-1">
                            <select class="custom-select-smooth edit-advisor-select" id="editProjectAdvisorCommittee1Select">
                                <option value="">-- เลือกอาจารย์ในระบบ --</option>
                                @if(!empty($teachers))
                                    @foreach($teachers->where('role', 'teacher') as $t)
                                        <option value="{{ $t->name }}" data-id="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="text" class="form-control"
                                id="editProjectAdvisorCommittee1" placeholder="หรือพิมพ์ชื่อระบุเอง เช่น อ. ...">
                            <input type="hidden" id="editProjectAdvisorCommittee1Id">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="editProjectAdvisorCommittee2"
                            class="form-label fw-medium">กรรมการ 2</label>
                        <div class="d-flex flex-column gap-1">
                            <select class="custom-select-smooth edit-advisor-select" id="editProjectAdvisorCommittee2Select">
                                <option value="">-- เลือกอาจารย์ในระบบ --</option>
                                @if(!empty($teachers))
                                    @foreach($teachers->where('role', 'teacher') as $t)
                                        <option value="{{ $t->name }}" data-id="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="text" class="form-control"
                                id="editProjectAdvisorCommittee2" placeholder="หรือพิมพ์ชื่อระบุเอง เช่น ผศ. ...">
                            <input type="hidden" id="editProjectAdvisorCommittee2Id">
                        </div>
                    </div>
                    <!-- ที่ปรึกษาพิเศษ (รองรับหลายคน: อาจารย์, เจ้าหน้าที่, บุคคลภายนอก) -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-medium mb-0">
                                <i class="fas fa-user-plus text-info me-1"></i>อาจารย์ที่ปรึกษาพิเศษ / ผู้ทรงคุณวุฒิ
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm text-nowrap" onclick="addSpecialAdvisorRow()">
                                <i class="fas fa-plus me-1"></i>เพิ่มที่ปรึกษาพิเศษ
                            </button>
                        </div>
                        <div class="row g-3" id="specialAdvisorsContainer">
                            <!-- แถวที่ปรึกษาพิเศษจะถูกสร้างแบบไดนามิกที่นี่ -->
                        </div>
                        <div class="small text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>เลือกอาจารย์หรือเจ้าหน้าที่ในระบบ หรือเลือก "บุคคลภายนอก" เพื่อพิมพ์ระบุชื่อผู้ทรงคุณวุฒิภายนอก
                        </div>
                        <!-- Fallback inputs สำหรับความเข้ากันได้ -->
                        <input type="hidden" id="editProjectAdvisorSpecial">
                        <input type="hidden" id="editProjectAdvisorSpecialId">
                    </div>

                    <!-- หมายเหตุ -->
                    <div class="col-12">
                        <label for="editProjectRemark"
                            class="form-label fw-medium">หมายเหตุ</label>
                        <input type="text" class="form-control" id="editProjectRemark"
                            placeholder="หมายเหตุเพิ่มเติม">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3">
                <button type="button" class="btn btn-secondary px-3"
                    data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary px-4 shadow-sm"
                    onclick="saveEditProject()">
                    <i class="fas fa-check me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     2. Modal แก้ไขข้อมูลนักศึกษา (Edit Student Modal)
     ========================================================= -->
<div class="modal fade" id="modalEditStudent" tabindex="-1"
    aria-labelledby="modalEditStudentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success text-white border-0 px-4 py-3">
                <h5 class="modal-title fw-bold" id="modalEditStudentLabel">
                    <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลนักศึกษาทั้งหมด
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editStudentOriginalId">

                <div class="row g-3">
                    <!-- รหัสนักศึกษา -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentId" class="form-label fw-medium">รหัสนักศึกษา <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace"
                            id="editStudentId" maxlength="15" placeholder="เช่น 6604101306"
                            required>
                    </div>

                    <!-- ชื่อเล่น -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentNickname" class="form-label fw-medium">ชื่อเล่น
                            (ถ้ามี)</label>
                        <input type="text" class="form-control" id="editStudentNickname"
                            placeholder="เช่น วาก, บอล, นนท์">
                    </div>

                    <!-- ชื่อ-นามสกุล -->
                    <div class="col-12">
                        <label for="editStudentName" class="form-label fw-medium">ชื่อ-นามสกุล
                            <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editStudentName"
                            placeholder="เช่น นายสมชาย ใจดี" required>
                    </div>

                    <!-- ประเภทวิชา & กลุ่มโครงงาน -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentType" class="form-label fw-medium">ประเภทวิชา <span
                                class="text-danger">*</span></label>
                        <select class="form-select custom-select-smooth" id="editStudentType"
                            required>
                            <option value="project">โครงงาน (498)</option>
                            <option value="coop">สหกิจศึกษา (497)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="editStudentProjectNo"
                            class="form-label fw-medium">กลุ่มโครงงานที่</label>
                        <input type="text" class="form-control" id="editStudentProjectNo"
                            placeholder="เช่น 1 หรือ เว้นว่างถ้าไม่มีกลุ่ม">
                    </div>

                    <!-- สถานะนักศึกษา -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentStatus" class="form-label fw-medium">สถานะนักศึกษา
                            <span class="text-danger">*</span></label>
                        <select class="form-select custom-select-smooth" id="editStudentStatus"
                            required>
                            <option value="doing">กำลังทำโครงงาน (doing)</option>
                            <option value="no_project">ยังไม่มีโครงงาน (no_project)</option>
                            <option value="submitted">ส่งเล่ม/โครงงานแล้ว (submitted)</option>
                            <option value="passed">ผ่านโครงงานแล้ว (passed)</option>
                            <option value="missing">ขาดการติดต่อ/หายไปนาน (missing)</option>
                        </select>
                    </div>

                    <!-- อีเมล -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentEmail" class="form-label fw-medium">อีเมล</label>
                        <input type="email" class="form-control" id="editStudentEmail"
                            placeholder="เช่น student@mju.ac.th">
                    </div>

                    <!-- เบอร์โทรศัพท์ -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentPhone"
                            class="form-label fw-medium">เบอร์โทรศัพท์</label>
                        <input type="tel" class="form-control" id="editStudentPhone"
                            placeholder="เช่น 081-234-5678">
                    </div>

                    <!-- หมายเหตุ -->
                    <div class="col-12 col-md-6">
                        <label for="editStudentRemark"
                            class="form-label fw-medium">หมายเหตุ</label>
                        <input type="text" class="form-control" id="editStudentRemark"
                            placeholder="หมายเหตุเพิ่มเติม">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3">
                <button type="button" class="btn btn-secondary px-3"
                    data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success px-4 shadow-sm"
                    onclick="saveEditStudent()">
                    <i class="fas fa-check me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>
