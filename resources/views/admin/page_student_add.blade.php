<section>
    <div class="container-fluid">
        <!-- ส่วนหัว (Page Heading & Breadcrumb) เหมือนหน้าอื่นในระบบเป๊ะ -->
        <div class="page-heading mb-3 py-2">
            <div class="row align-items-center gy-2">
                <div class="col-12 col-sm-6">
                    <h3 class="m-0 fs-4">
                        <i class="fas fa-user-plus me-2"></i>เพิ่มข้อมูลนักศึกษา
                    </h3>
                    <p class="text-muted small m-0 mt-1">นำเข้าข้อมูลนักศึกษาและกลุ่มโครงงานจากไฟล์ CSV
                        หรือกรอกเพิ่มรายคน</p>
                </div>
                <div class="col-12 col-sm-6 text-sm-end">
                    <a href="{{ url('/pc-csmju/admin/student') }}"
                        class="btn btn-back-page btn-sm px-3 shadow-sm rounded-pill" id="btnBackPage">
                        <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                    </a>
                </div>
            </div>
        </div>

        <div class="page-content">
            <!-- แท็บสลับโหมดการเพิ่มข้อมูล (Step Tabs) -->
            <div class="step-tabs mb-4" id="mainStepTabs">
                <button type="button" class="step-btn active" id="tabModeCsv" onclick="switchAddMode('csv')">
                    <i class="fas fa-file-csv me-2 text-success"></i>1. นำเข้าจากไฟล์ CSV (ทั้งกลุ่มโครงงาน)
                </button>
                <span class="tab-divider">|</span>
                <button type="button" class="step-btn" id="tabModeManual" onclick="switchAddMode('manual')">
                    <i class="fas fa-user-edit me-2 text-primary"></i>2. กรอกเพิ่มรายคนด้วยตนเอง (Manual Form)
                </button>
            </div>

            <!-- =========================================================
                 โหมดที่ 1: นำเข้าจากไฟล์ CSV (พร้อมระบบ Step และ Live Preview ทันที)
                 ========================================================= -->
            <div id="modeCsvContainer">

                <!-- -----------------------------------------------------
                     Step 1: กล่องอัปโหลด / ลากไฟล์ CSV มาวาง (Upload Area)
                     ----------------------------------------------------- -->
                <div id="stepUploadBox" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
                    style="border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-4 p-md-5">
                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-10">

                                <!-- คำแนะนำโครงสร้างไฟล์ -->
                                <div class="card bg-light border-0 rounded-3">
                                    <div class="card-body p-3 p-md-4 small text-muted">
                                        <div class="fw-bold text-dark mb-2"><i
                                                class="fas fa-info-circle text-info me-1"></i>รูปแบบและข้อกำหนดของไฟล์
                                            CSV:</div>
                                        <ul class="mb-0 ps-3">
                                            <li>รองรับทั้ง <strong>วิชา 10300498 (โครงงาน)</strong> และ <strong>วิชา วท
                                                    497 (สหกิจศึกษา)</strong> รวมกันได้ในไฟล์เดียว</li>
                                            <li>หัวตารางจะถูกตรวจจับอัตโนมัติ: ลำดับ, รหัสนักศึกษา, ชื่อ-สกุล,
                                                ชื่อโครงงาน, ประธาน, กรรมการ</li>
                                            <li><strong>นักศึกษาที่ยังไม่มีกลุ่มโครงงาน</strong>
                                                ระบบจะแยกออกมาให้อัตโนมัติ เพื่อไม่ให้ไปปนกับกลุ่มอื่น</li>
                                            <li><strong>อาจารย์ที่ปรึกษา</strong> ระบบจะค้นหาและจับคู่ ID
                                                อาจารย์ในระบบให้โดยอัตโนมัติ</li>
                                        </ul>
                                    </div>
                                </div>

                                <h5 class="fw-semibold text-primary border-bottom pb-2 mb-4 mt-4">
                                    <i class="fas fa-cloud-arrow-up me-2"></i>เลือกหรือลากไฟล์ CSV
                                    เพื่อตรวจสอบข้อมูลก่อนบันทึก
                                </h5>

                                <!-- กล่องลากวางและเลือกไฟล์ CSV (ปรับขนาดให้กะทัดรัด สบายตา สมส่วนกับทุกอุปกรณ์) -->
                                <div id="csvDropzoneBox"
                                    class="text-center py-3 px-3 py-md-4 px-md-4 border border-2 border-dashed rounded-4 bg-light mb-4"
                                    style="border-color: #3b82f6 !important; cursor: pointer; transition: all 0.3s ease; max-width: 620px; margin-left: auto; margin-right: auto;">
                                    <input type="file" id="csvFileInput" class="d-none"
                                        accept=".csv,text/csv,text/plain">

                                    <div class="my-2" id="dropzonePrompt">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                        <div class="fw-bold text-dark fs-6 mb-1">ลากไฟล์ CSV มาวางที่นี่</div>
                                        <p class="text-muted small mb-2" style="font-size: 0.85rem;">
                                            รองรับไฟล์ <code>.csv</code> จาก Excel (ตรวจจับภาษาไทยและปีการศึกษาให้อัตโนมัติ)<br>
                                            หรือกดปุ่มด้านล่างเพื่อเลือกไฟล์จากคอมพิวเตอร์
                                        </p>
                                        <label for="csvFileInput" class="btn btn-primary btn-sm px-3 py-1.5 shadow-sm mb-0 rounded-pill"
                                            id="btnSelectCsvFile" style="cursor: pointer;">
                                            <i class="fas fa-folder-open me-1"></i> เลือกไฟล์ CSV จากเครื่อง
                                        </label>
                                    </div>

                                    <!-- ส่วนแสดงข้อมูลเมื่อเลือกไฟล์แล้ว -->
                                    <div id="fileSelectedBadge" class="my-2 d-none">
                                        <div
                                            class="alert alert-success d-inline-flex align-items-center gap-3 py-2 px-3 shadow-sm mb-2 rounded-3 text-start">
                                            <i class="fas fa-file-csv fa-2x text-success"></i>
                                            <div>
                                                <div class="fw-bold fs-6 text-dark" id="fileNameText">ชื่อไฟล์.csv</div>
                                                <small class="text-muted" id="fileSizeText">0 KB</small>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="csvFileInput"
                                                class="btn btn-outline-secondary btn-sm px-3 py-1 shadow-sm rounded-pill mb-0"
                                                id="btnChangeCsvFile" style="cursor: pointer;">
                                                <i class="fas fa-sync-alt me-1"></i> เปลี่ยนไฟล์อื่น
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนปุ่มกด (Card Footer) เหมือนหน้าอื่น -->
                    <div class="card-footer bg-light px-4 py-3 border-0 d-flex flex-wrap justify-content-end gap-2">
                        <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnParseCsv" disabled>
                            <i class="fas fa-search me-1"></i> ตรวจสอบและดูตัวอย่างข้อมูล
                        </button>
                    </div>
                </div>

                <!-- -----------------------------------------------------
                     Step 2: กล่อง Live Preview ข้อมูลทั้งหมด (ซ่อนไว้ก่อน)
                     ----------------------------------------------------- -->
                <div id="stepPreviewBox" class="d-none">

                    <!-- สรุปตัวเลขสถิติภาพรวม (Summary Stats Cards) -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 border-start border-primary border-4 h-100"
                                style="border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">จำนวนนักศึกษาทั้งหมด</div>
                                    <div class="fs-4 fw-bold text-primary" id="statTotalStudents">0 คน</div>
                                    <div class="small text-muted mb-2" id="statStudentsDetail">มีกลุ่ม 0 / ไม่มีกลุ่ม 0
                                    </div>
                                    <div class="pt-2 border-top border-light d-flex flex-wrap align-items-center gap-1"
                                        id="statStudentBatches">
                                        <!-- สถิตินับแยกตามรหัส 2 ตัวหน้า (เช่น รหัส 66, 65) จะแสดงที่นี่ -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 border-start border-success border-4 h-100"
                                style="border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">กลุ่มโครงงานทั้งหมด</div>
                                    <div class="fs-4 fw-bold text-success" id="statTotalProjects">0 กลุ่ม</div>
                                    <div class="small text-muted mb-2" id="statProjectsDetail">โครงงาน 0 / สหกิจ 0</div>
                                    <div class="pt-2 border-top border-light" id="statProjectBatches">
                                        <!-- สถิตินับแยกตามรหัส 2 ตัวหน้าของโครงงานและสหกิจจะแสดงที่นี่ -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 border-start border-warning border-4 h-100"
                                style="border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-3">
                                    <div class="text-muted small fw-semibold">ยังไม่มีกลุ่มโครงงาน</div>
                                    <div class="fs-4 fw-bold text-warning" id="statNoProjectStudents">0 คน</div>
                                    <div class="small text-muted mb-2">แยกไว้สำหรับจัดกลุ่มภายหลัง</div>
                                    <div class="pt-2 border-top border-light d-flex flex-wrap align-items-center gap-1"
                                        id="statNoProjectBatches">
                                        <!-- สถิตินับแยกตามรหัส 2 ตัวหน้าของคนที่ยังไม่มีโครงงานจะแสดงที่นี่ -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 border-start border-info border-4 h-100"
                                style="border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small fw-semibold">ปีที่ทำโครงงาน</span>

                                    </div>
                                    <div class="fs-4 fw-bold text-info" id="previewProjectYearDisplay">-</div>
                                    <small class="text-muted">ปีการศึกษาที่ตรวจพบจากไฟล์</small>
                                    <input type="hidden" id="previewProjectYearInput">
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <style>
                        .preview-tab-btn {
                            position: relative;
                            border: none;
                            background: transparent;
                            color: #64748b;
                            font-size: 15px;
                            font-weight: 500;
                            padding: 0.5rem 0.85rem 0.75rem 0.85rem;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            border-bottom: 3px solid transparent;
                            margin-bottom: -1.5px;
                            display: inline-flex;
                            align-items: center;
                            white-space: nowrap;
                            text-decoration: none;
                        }

                        .preview-tab-btn:hover {
                            color: #1e293b;
                        }

                        .preview-tab-btn.active {
                            color: #1a56db !important;
                            font-weight: 600 !important;
                            border-bottom: 3px solid #1a56db !important;
                            background: transparent !important;
                            box-shadow: none !important;
                        }

                        /* ไฮไลต์แถวที่พบข้อมูลผิดปกติ (สีแดงพาสเทลอ่อนๆ สบายตา) */
                        tr.row-anomaly > td {
                            background-color: #fff1f2 !important;
                        }
                        tr.row-anomaly:hover > td {
                            background-color: #ffe4e6 !important;
                        }
                    </style>

                    <!-- แถบสลับดูข้อมูล Live Preview แต่ละส่วน (Step Tabs) พร้อมช่องค้นหาทางขวา -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 border-bottom pb-2">
                        <div class="step-tabs border-0 mb-0 p-0" style="margin-bottom: -10px !important;">
                            <button type="button" class="preview-tab-btn active" id="btnPreviewTabProjects498"
                                onclick="switchPreviewSubTab('projects498')">
                                <i class="fas fa-project-diagram me-2 text-primary"></i>โครงงาน (<span
                                    id="tabCountProjects498">0</span>)
                            </button>
                            <span class="tab-divider">|</span>
                            <button type="button" class="preview-tab-btn" id="btnPreviewTabProjectsCoop"
                                onclick="switchPreviewSubTab('projectsCoop')">
                                <i class="fas fa-building me-2 text-success"></i>สหกิจศึกษา (<span
                                    id="tabCountProjectsCoop">0</span>)
                            </button>
                            <span class="tab-divider">|</span>
                            <button type="button" class="preview-tab-btn" id="btnPreviewTabStudents"
                                onclick="switchPreviewSubTab('students')">
                                <i class="fas fa-user-graduate me-2 text-info"></i>บัญชีนักศึกษา (<span
                                    id="tabCountStudents">0</span>)
                            </button>
                            <span class="tab-divider">|</span>
                            <button type="button" class="preview-tab-btn" id="btnPreviewTabNoProject"
                                onclick="switchPreviewSubTab('noProject')">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>ยังไม่มีกลุ่มโครงงาน (<span
                                    id="tabCountNoProject">0</span>)
                            </button>
                            <span class="tab-divider">|</span>
                            <button type="button" class="preview-tab-btn" id="btnPreviewTabAnomalies"
                                onclick="switchPreviewSubTab('anomalies')">
                                <i class="fas fa-exclamation-circle me-2 text-danger"></i>ข้อมูลพบข้อผิดปกติ (<span
                                    id="tabCountAnomalies" class="fw-bold">0</span>)
                            </button>
                        </div>

                        <!-- ช่องค้นหาข้อมูลแบบ Real-time พร้อม Checkbox ไฮไลต์คำค้นหา -->
                        <div class="d-flex align-items-center gap-2 ms-auto flex-nowrap justify-content-end">
                            <div class="preview-search-wrapper position-relative" style="width: 250px;">
                                <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px; font-size: 13px;"></i>
                                <input type="text" id="inputPreviewSearch" class="form-control form-control-sm ps-5 pe-4 rounded-3 shadow-none"
                                       placeholder="ค้นหาในตารางนี้..." style="height: 38px; font-size: 13.5px; border-color: #cbd5e1; background-color: #ffffff;">
                                <button type="button" id="btnClearPreviewSearch" class="btn btn-link position-absolute top-50 translate-middle-y end-0 text-muted p-0 pe-3 d-none"
                                        style="border: none; text-decoration: none;" title="ล้างคำค้นหา">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>

                            <!-- Checkbox เปิด/ปิดไฮไลต์: ข้อความ "ไฮไลต์" อยู่หน้า กล่องเช็คอยู่หลัง (ค่าเริ่มต้นไม่ติ๊ก) -->
                            <label for="checkHighlightSearch" class="d-flex align-items-center gap-2 user-select-none bg-white border px-2 rounded-3 shadow-sm mb-0 text-nowrap"
                                 style="border-color: #cbd5e1 !important; height: 38px; cursor: pointer;" title="เปิด/ปิดการไฮไลต์คำที่ค้นหาในตาราง">
                                <span class="small text-secondary fw-medium" style="font-size: 12px;">ไฮไลต์คำค้นหา</span>
                                <input class="form-check-input mt-0" type="checkbox" id="checkHighlightSearch" style="cursor: pointer; width: 1.15em; height: 1.15em;">
                            </label>
                        </div>
                    </div>

                    <!-- กล่อง Card แสดงตารางข้อมูลพรีวิว สไตล์เดียวกับหน้าอื่น -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
                        style="border: 1px solid #e2e8f0 !important;">
                        <div class="card-body px-3 px-md-4 py-4">

                            <!-- แท็บย่อย 1: ตารางกลุ่มโครงงาน (498) -->
                            <div id="subTabPaneProjects498">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle w-100"
                                        id="tablePreviewProjects498">
                                        <thead class="table-light text-nowrap">
                                            <tr>
                                                <th scope="col" class="text-center" width="4%">ลำดับ</th>
                                                <th scope="col" class="text-center" width="9%">ประเภท</th>
                                                <th scope="col" width="28%">ชื่อโครงงาน</th>
                                                <th scope="col" width="22%">สมาชิกในกลุ่ม (นักศึกษา)</th>
                                                <th scope="col" width="20%">อาจารย์ที่ปรึกษา</th>
                                                <th scope="col" class="text-center" width="8%">หมายเหตุ</th>
                                                <th scope="col" class="text-center" width="9%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPreviewProjects498">
                                            <!-- เติมข้อมูลด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- แท็บย่อย 2: ตารางกลุ่มสหกิจศึกษา (497) -->
                            <div id="subTabPaneProjectsCoop" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle w-100"
                                        id="tablePreviewProjectsCoop">
                                        <thead class="table-light text-nowrap">
                                            <tr>
                                                <th scope="col" class="text-center" width="4%">ลำดับ</th>
                                                <th scope="col" class="text-center" width="9%">ประเภท</th>
                                                <th scope="col" width="22%">ชื่อโครงงาน / หัวข้องานสหกิจ</th>
                                                <th scope="col" width="19%">สมาชิกในกลุ่ม (นักศึกษา)</th>
                                                <th scope="col" width="16%">อาจารย์ที่ปรึกษา</th>
                                                <th scope="col" class="text-center" width="10%">สถานที่ฝึก /
                                                    บริษัท</th>
                                                <th scope="col" class="text-center" width="8%">หมายเหตุ</th>
                                                <th scope="col" class="text-center" width="12%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPreviewProjectsCoop">
                                            <!-- เติมข้อมูลด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- แท็บย่อย 2: ตารางบัญชีนักศึกษาทั้งหมด -->
                            <div id="subTabPaneStudents" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle w-100"
                                        id="tablePreviewStudents">
                                        <thead class="table-light text-nowrap">
                                            <tr>
                                                <th scope="col" class="text-center" width="4%">ลำดับ</th>
                                                <th scope="col" class="text-center" width="12%">รหัสนักศึกษา
                                                </th>
                                                <th scope="col" class="text-center" width="7%">ชื่อเล่น</th>
                                                <th scope="col" width="18%">ชื่อ-นามสกุล</th>
                                                <th scope="col" class="text-center" width="8%">วิชา</th>
                                                <th scope="col" class="text-center" width="12%">สถานะนักศึกษา
                                                </th>
                                                <th scope="col" class="text-center" width="9%">กลุ่มโครงงาน
                                                </th>
                                                <th scope="col" class="text-center" width="5%">สถานะในระบบ
                                                </th>
                                                <th scope="col" class="text-center" width="9%">หมายเหตุ</th>
                                                <th scope="col" class="text-center" width="10%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPreviewStudents">
                                            <!-- เติมข้อมูลด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- แท็บย่อย 3: ตารางนักศึกษาที่ยังไม่มีโครงงาน -->
                            <div id="subTabPaneNoProject" class="d-none">
                                <div class="alert alert-warning border-0 d-flex align-items-center gap-3 mb-3">
                                    <i class="fas fa-info-circle fa-2x text-warning"></i>
                                    <div>
                                        <div class="fw-bold">นักศึกษากลุ่มนี้จะถูกสร้างบัญชีเข้าสู่ระบบตามปกติ
                                            แต่จะยังไม่มีโครงงาน</div>
                                        <div class="small">ระบบจะตั้งค่า (ยังไม่มีโครงงาน) ให้อัตโนมัติ
                                            เพื่อรอให้นักศึกษาหรืออาจารย์ไปจัดกลุ่มในภายหลัง</div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle w-100"
                                        id="tablePreviewNoProject">
                                        <thead class="table-light text-nowrap">
                                            <tr>
                                                <th scope="col" class="text-center" width="5%">ลำดับ</th>
                                                <th scope="col" class="text-center" width="14%">รหัสนักศึกษา
                                                </th>
                                                <th scope="col" class="text-center" width="7%">ชื่อเล่น</th>
                                                <th scope="col">ชื่อ-นามสกุล</th>
                                                <th scope="col" class="text-center" width="10%">วิชา</th>
                                                <th scope="col" class="text-center" width="14%">สถานะที่กำหนด
                                                </th>
                                                <th scope="col" class="text-center" width="12%">หมายเหตุ</th>
                                                <th scope="col" class="text-center" width="12%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPreviewNoProject">
                                            <!-- เติมข้อมูลด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- แท็บย่อย 5: ตารางรายการที่พบข้อมูลผิดปกติ / ต้องตรวจสอบ -->
                            <div id="subTabPaneAnomalies" class="d-none">
                                <div class="alert alert-danger border-0 d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 rounded-3 py-2.5 px-3"
                                    style="background-color: #fee2e2; color: #991b1b;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                                        <div>
                                            <div class="fw-bold fs-6">พบรายการข้อมูลผิดปกติหรือต้องตรวจสอบก่อนบันทึก</div>
                                            <div class="small" style="color: #7f1d1d;">รวบรวมทั้งกลุ่มโครงงานและนักศึกษาที่พบปัญหา เช่น ไม่มีชื่อโครงงาน, สหกิจไม่ระบุสถานที่ฝึกงาน, รหัสซ้ำ, รหัสไม่ครบ 10 หลัก, รหัสไม่ใช่สาขา 04101 หรือไม่พบอาจารย์ในระบบ สามารถกดปุ่มแก้ไขเพื่อปรับปรุงข้อมูลได้ทันที</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-1.5 fs-6" id="badgeTotalAnomalies">0 รายการ</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle w-100"
                                        id="tablePreviewAnomalies">
                                        <thead class="table-light text-nowrap">
                                            <tr>
                                                <th scope="col" class="text-center" width="4%">ลำดับ</th>
                                                <th scope="col" class="text-center" width="9%">ประเภท</th>
                                                <th scope="col" class="text-center" width="13%">รหัส / ลำดับกลุ่ม</th>
                                                <th scope="col" width="22%">ชื่อรายการ (นักศึกษา / โครงงาน)</th>
                                                <th scope="col" width="38%">ปัญหา / ข้อผิดปกติที่ตรวจพบ</th>
                                                <th scope="col" class="text-center" width="14%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPreviewAnomalies">
                                            <!-- เติมข้อมูลด้วย JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- แถบปุ่มกดยืนยันบันทึกข้อมูล (Card Footer) สไตล์มาตรฐานหน้าอื่น -->
                        <div
                            class="card-footer bg-light px-4 py-3 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <button type="button" class="btn btn-secondary px-3 shadow-sm"
                                onclick="backToUploadStep()">
                                <i class="fas fa-arrow-left me-1"></i> เลือกไฟล์ใหม่
                            </button>
                            <button type="button" class="btn btn-success px-4 shadow-sm" id="btnConfirmSave">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูลลงระบบ
                            </button>
                        </div>
                    </div>


                </div>

            </div>

            <!-- =========================================================
                 โหมดที่ 2: กรอกเพิ่มรายคนด้วยตนเอง (Manual Single Add Form)
                 เหมือนหน้า page_teacherandofficer_add.blade.php ทุกประการ
                 ========================================================= -->
            <div id="modeManualContainer" class="d-none">
                <form action="{{ url('pc-csmju/admin/student/create') }}" method="POST"
                    enctype="multipart/form-data" id="studentManualForm">
                    @csrf
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
                        style="border: 1px solid #e2e8f0 !important;">
                        <div class="card-body p-4 p-md-5">

                            <div class="row g-4">
                                <!-- คอลัมน์ซ้าย: รูปโปรไฟล์ (Avatar Upload & Preview) เหมือนหน้าเพิ่มอาจารย์ -->
                                <div class="col-12 col-lg-3 text-center border-end-lg pe-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold d-block">รูปโปรไฟล์ <br><sup> (ไม่บังคับ)
                                            </sup></label>

                                        <div class="position-relative d-inline-block">
                                            <div class="rounded-circle overflow-hidden bg-light border border-2 border-secondary-subtle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                style="width: 140px; height: 140px; cursor: pointer;"
                                                id="avatarContainer" title="คลิกเพื่อเลือกรูปภาพ">
                                                <i class="fas fa-user-graduate fa-4x text-secondary"
                                                    id="avatarPlaceholder"></i>
                                                <img id="imagePreview" src="#" alt="Preview"
                                                    class="w-100 h-100 rounded-circle"
                                                    style="object-fit: cover; display: none;">
                                            </div>
                                            <button type="button" id="btnTriggerUpload"
                                                class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow"
                                                style="width: 38px; height: 38px; cursor: pointer;"
                                                title="เลือกรูปภาพ">
                                                <i class="fas fa-camera"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" id="profile_picture" name="profile_picture"
                                        class="form-control form-control-sm d-none image-crop" accept="image/*">
                                    <div class="form-text small text-muted">
                                        เลือกรูปภาพ<br>ระบบจะตัดรูปเป็น 1:1
                                    </div>
                                </div>

                                <!-- คอลัมน์ขวา: รายละเอียดข้อมูลต่างๆ -->
                                <div class="col-12 col-lg-9 ps-lg-4">

                                    <!-- หมวดที่ 1: ข้อมูลทั่วไป -->
                                    <div class="mb-4">
                                        <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-id-card me-2"></i>ข้อมูลส่วนตัวและการศึกษา
                                        </h5>
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label for="student_id" class="form-label fw-medium">รหัสนักศึกษา
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" id="student_id" name="student_id"
                                                    class="form-control" placeholder="เช่น 6604101306" maxlength="15"
                                                    value="{{ old('student_id') }}" required>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_student_id">15/15</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="name" class="form-label fw-medium">ชื่อ-นามสกุล <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="name" name="name"
                                                    class="form-control" placeholder="เช่น นายสมชาย ใจดี"
                                                    maxlength="50" value="{{ old('name') }}" required>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted"></div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_name">50/50</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="nickname" class="form-label fw-medium">ชื่อเล่น</label>
                                                <input type="text" id="nickname" name="nickname"
                                                    class="form-control" placeholder="เช่น กาย, บอส, แนน"
                                                    maxlength="30" value="{{ old('nickname') }}">
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted"></div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_nickname">30/30</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="email" class="form-label fw-medium">อีเมล</label>
                                                <input type="email" id="email" name="email"
                                                    class="form-control" placeholder="เช่น somchai@mju.ac.th"
                                                    maxlength="50" value="{{ old('email') }}">
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted"></div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_email">50/50</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="phone"
                                                    class="form-label fw-medium">เบอร์โทรศัพท์</label>
                                                <input type="tel" id="phone" name="phone"
                                                    class="form-control" placeholder="เช่น 081-234-5678"
                                                    maxlength="20" value="{{ old('phone') }}">
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted"></div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_phone">20/20</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="status_student" class="form-label fw-medium">สถานะนักศึกษา
                                                    <span class="text-danger">*</span></label>
                                                <select name="status_student" id="status_student"
                                                    class="custom-select-smooth" required>
                                                    <option value="no_project"
                                                        {{ old('status_student', 'no_project') == 'no_project' ? 'selected' : '' }}>
                                                        ยังไม่มีโครงงาน (no_project)</option>
                                                    <option value="doing"
                                                        {{ old('status_student') == 'doing' ? 'selected' : '' }}>
                                                        กำลังทำโครงงาน (doing)</option>
                                                    <option value="passed"
                                                        {{ old('status_student') == 'passed' ? 'selected' : '' }}>
                                                        ผ่านโครงงานแล้ว (passed)</option>
                                                    <option value="missing"
                                                        {{ old('status_student') == 'missing' ? 'selected' : '' }}>
                                                        ขาดการติดต่อ/หายไปนาน (missing)</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label for="remark" class="form-label fw-medium">หมายเหตุ</label>
                                                <textarea id="remark" name="remark" class="form-control" rows="2"
                                                    placeholder="ระบุหมายเหตุเพิ่มเติมของนักศึกษา (ถ้ามี)" maxlength="255">{{ old('remark') }}</textarea>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted"></div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_remark">255/255</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- หมวดที่ 2: ข้อมูลกลุ่มและโครงงาน (Project & Group Association) -->
                                    <div class="mb-4 {{ old('status_student', 'no_project') == 'no_project' ? 'd-none' : '' }}" id="sectionProjectGroup">
                                        <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-project-diagram me-2"></i>ข้อมูลกลุ่มและโครงงาน
                                        </h5>

                                        <!-- ตัวเลือกรูปแบบการจัดกลุ่ม (2 Interactive Mode Cards) -->
                                        <div class="row g-2 mb-3">
                                            <div class="col-12 col-md-6">
                                                <div class="card h-100 border p-3 rounded-3 project-mode-card shadow-sm {{ old('project_mode', 'join_existing') == 'join_existing' ? 'active-mode' : '' }}" id="cardModeJoin" style="cursor: pointer; border-color: {{ old('project_mode', 'join_existing') == 'join_existing' ? '#3b82f6 !important; background-color: #eff6ff;' : '#cbd5e1;' }} transition: all 0.2s ease;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="project_mode" id="modeJoin" value="join_existing" {{ old('project_mode', 'join_existing') == 'join_existing' ? 'checked' : '' }} style="cursor: pointer;">
                                                        <label class="form-check-label fw-bold text-dark d-block" for="modeJoin" style="cursor: pointer;">
                                                            <i class="fas fa-users text-primary me-1"></i> เพิ่มเข้ากลุ่มเดิมในระบบ
                                                        </label>
                                                        <small class="text-muted d-block mt-1">เลือกปีและดึงเข้ากลุ่มโครงงานที่มีอยู่แล้ว</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <div class="card h-100 border p-3 rounded-3 project-mode-card shadow-sm {{ old('project_mode') == 'create_new' ? 'active-mode' : '' }}" id="cardModeCreate" style="cursor: pointer; border-color: {{ old('project_mode') == 'create_new' ? '#3b82f6 !important; background-color: #eff6ff;' : '#cbd5e1;' }} transition: all 0.2s ease;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="project_mode" id="modeCreate" value="create_new" {{ old('project_mode') == 'create_new' ? 'checked' : '' }} style="cursor: pointer;">
                                                        <label class="form-check-label fw-bold text-dark d-block" for="modeCreate" style="cursor: pointer;">
                                                            <i class="fas fa-plus-circle text-success me-1"></i> สร้างกลุ่มโครงงานใหม่
                                                        </label>
                                                        <small class="text-muted d-block mt-1">เปิดกลุ่มใหม่และเป็นสมาชิกคนแรกของกลุ่ม</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- กล่องฟอร์มย่อยเมื่อเลือก "เพิ่มเข้ากลุ่มที่มีอยู่แล้ว" -->
                                        <div id="boxJoinExisting" class="p-3 p-md-4 rounded-3 border bg-light mb-3 d-none">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-search me-1"></i> ค้นหาและเลือกกลุ่มโครงงานในระบบ
                                            </h6>
                                            <div class="row g-3">
                                                <!-- ปีที่ทำโครงงาน (เลือกได้อิสระ ไม่ผูกกับรหัสนักศึกษา รองรับนักศึกษาตกค้าง) -->
                                                <div class="col-12 col-md-4">
                                                    <label for="filter_project_year" class="form-label fw-medium">
                                                        ปีที่ทำโครงงาน <span class="text-danger">*</span>
                                                    </label>
                                                    <select id="filter_project_year" name="filter_project_year" class="custom-select-smooth">
                                                        @if(isset($existingYears) && count($existingYears) > 0)
                                                            @foreach($existingYears as $yr)
                                                                <option value="{{ $yr }}" {{ $yr == $currentYear ? 'selected' : '' }}>ปี พ.ศ. {{ $yr }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="{{ $currentYear }}">ปี พ.ศ. {{ $currentYear }}</option>
                                                        @endif
                                                    </select>
                                                    <small class="text-muted">เลือกปีการศึกษาที่ลงทำโครงงาน (รองรับตกค้าง)</small>
                                                </div>

                                                <!-- ประเภทวิชา -->
                                                <div class="col-12 col-md-5">
                                                    <label class="form-label fw-medium d-block">ประเภทรายวิชา <span class="text-danger">*</span></label>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" class="btn-check" name="filter_project_type" id="filterTypeProject" value="project" checked autocomplete="off">
                                                        <label class="btn btn-outline-primary py-2" for="filterTypeProject">
                                                            <i class="fas fa-project-diagram me-1"></i> โครงงาน (498)
                                                        </label>

                                                        <input type="radio" class="btn-check" name="filter_project_type" id="filterTypeCoop" value="coop" autocomplete="off">
                                                        <label class="btn btn-outline-success py-2" for="filterTypeCoop">
                                                            <i class="fas fa-building me-1"></i> สหกิจ (497)
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Dropdown รายชื่อกลุ่มโครงงานเป้าหมาย -->
                                                <div class="col-12">
                                                    <label for="existing_project_id" class="form-label fw-medium">
                                                        เลือกกลุ่มโครงงานที่ต้องการเพิ่มนักศึกษาเข้าไป <span class="text-danger">*</span>
                                                    </label>
                                                    <select id="existing_project_id" name="existing_project_id" class="form-select">
                                                        <option value="">-- กรุณาเลือกกลุ่มโครงงาน --</option>
                                                    </select>
                                                    <div id="selectedProjectInfo" class="mt-2 small text-muted d-none p-2 rounded bg-white border">
                                                        <!-- แสดงพรีวิวสมาชิกเดิม -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- กล่องฟอร์มย่อยเมื่อเลือก "สร้างกลุ่มใหม่" -->
                                        <div id="boxCreateNew" class="p-3 p-md-4 rounded-3 border bg-light mb-3 d-none">
                                            <h6 class="fw-bold text-success mb-3">
                                                <i class="fas fa-folder-plus me-1"></i> กำหนดข้อมูลกลุ่มโครงงานใหม่
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="new_project_year" class="form-label fw-medium">
                                                        ปีที่ทำโครงงาน <span class="text-danger">*</span>
                                                    </label>
                                                    <select id="new_project_year" name="new_project_year" class="custom-select-smooth">
                                                        @if(isset($existingYears) && count($existingYears) > 0)
                                                            @foreach($existingYears as $yr)
                                                                 <option value="{{ $yr }}" {{ $yr == $currentYear ? 'selected' : '' }}>ปี พ.ศ. {{ $yr }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="{{ $currentYear }}">ปี พ.ศ. {{ $currentYear }}</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label class="form-label fw-medium d-block">ประเภทรายวิชา <span class="text-danger">*</span></label>
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" class="btn-check" name="new_project_type" id="newTypeProject" value="project" checked autocomplete="off">
                                                        <label class="btn btn-outline-primary py-2" for="newTypeProject">
                                                            <i class="fas fa-project-diagram me-1"></i> โครงงาน (498)
                                                        </label>

                                                        <input type="radio" class="btn-check" name="new_project_type" id="newTypeCoop" value="coop" autocomplete="off">
                                                        <label class="btn btn-outline-success py-2" for="newTypeCoop">
                                                            <i class="fas fa-building me-1"></i> สหกิจ (497)
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- คณะกรรมการและอาจารย์ที่ปรึกษา (4 ท่าน) -->
                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <label for="new_advisor_president_id" class="form-label fw-medium">อาจารย์ที่ปรึกษาประธาน</label>
                                                    <select id="new_advisor_president_id" name="new_advisor_president_id" class="custom-select-smooth">
                                                        <option value="">-- ไม่ระบุ / ระบุภายหลัง --</option>
                                                        @foreach($teachers->where('role', 'teacher') as $t)
                                                            <option value="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <label for="new_advisor_committee_1_id" class="form-label fw-medium">กรรมการ 1</label>
                                                    <select id="new_advisor_committee_1_id" name="new_advisor_committee_1_id" class="custom-select-smooth">
                                                        <option value="">-- ไม่ระบุ / ระบุภายหลัง --</option>
                                                        @foreach($teachers->where('role', 'teacher') as $t)
                                                            <option value="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <label for="new_advisor_committee_2_id" class="form-label fw-medium">กรรมการ 2</label>
                                                    <select id="new_advisor_committee_2_id" name="new_advisor_committee_2_id" class="custom-select-smooth">
                                                        <option value="">-- ไม่ระบุ / ระบุภายหลัง --</option>
                                                        @foreach($teachers->where('role', 'teacher') as $t)
                                                            <option value="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-6 col-lg-3">
                                                    <label for="new_advisor_special" class="form-label fw-medium">อาจารย์ที่ปรึกษาพิเศษ</label>
                                                    <select id="new_advisor_special" name="new_advisor_special" class="custom-select-smooth">
                                                        <option value="">-- ไม่ระบุ / ระบุภายหลัง --</option>
                                                        <optgroup label="อาจารย์ในระบบ">
                                                            @foreach($teachers->where('role', 'teacher') as $t)
                                                                <option value="{{ $t->id }}">{{ $t->name }} (อาจารย์)</option>
                                                            @endforeach
                                                        </optgroup>
                                                        <optgroup label="เจ้าหน้าที่ในระบบ">
                                                            @foreach($teachers->where('role', 'officer') as $t)
                                                                <option value="{{ $t->id }}">{{ $t->name }} (เจ้าหน้าที่)</option>
                                                            @endforeach
                                                        </optgroup>
                                                    </select>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label for="new_project_title_th" class="form-label fw-medium">ชื่อโครงงาน (ภาษาไทย) <span class="text-danger">*</span></label>
                                                    <input type="text" id="new_project_title_th" name="new_project_title_th" class="form-control" placeholder="เช่น ระบบจัดการคลังสินค้าอัจฉริยะ" maxlength="255">
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <label for="new_project_title_en" class="form-label fw-medium">ชื่อโครงงาน (ภาษาอังกฤษ)</label>
                                                    <input type="text" id="new_project_title_en" name="new_project_title_en" class="form-control" placeholder="เช่น Smart Inventory Management System" maxlength="255">
                                                </div>

                                                <div class="col-12 col-md-6 d-none" id="boxNewCompany">
                                                    <label for="new_company_name" class="form-label fw-medium">ชื่อสถานประกอบการ / บริษัท (สหกิจศึกษา)</label>
                                                    <input type="text" id="new_company_name" name="new_company_name" class="form-control" placeholder="เช่น บริษัท เอไอ โซลูชั่น จำกัด" maxlength="255">
                                                </div>

                                                <div class="col-12">
                                                    <label for="new_project_remark" class="form-label fw-medium">หมายเหตุโครงงาน</label>
                                                    <textarea id="new_project_remark" name="new_project_remark" class="form-control" rows="2" placeholder="ระบุหมายเหตุเพิ่มเติมเกี่ยวกับกลุ่มโครงงานนี้ (ถ้ามี)"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- หมวดที่ 3: บัญชีผู้ใช้และความปลอดภัย -->
                                    <div class="mb-4">
                                        <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-lock me-2"></i>ข้อมูลบัญชีเข้าสู่ระบบ
                                        </h5>
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6">
                                                <label for="username" class="form-label fw-medium">ชื่อผู้ใช้งาน
                                                    (Username) <span class="text-danger">*</span></label>
                                                <input type="text" id="username" name="username"
                                                    class="form-control"
                                                    placeholder="เช่น 6604101306 (สูงสุด 50 ตัวอักษร)" maxlength="50"
                                                    value="{{ old('username') }}" required autocomplete="off">
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted">ใช้สำหรับล็อกอินเข้าสู่ระบบ
                                                        (หากไม่ใส่ระบบจะดึงจากรหัสนักศึกษาให้อัตโนมัติ)</div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_username">50/50</div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="password" class="form-label fw-medium">รหัสผ่าน</label>
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password"
                                                        class="form-control" placeholder="เริ่มต้นใช้รหัสนักศึกษา (หรือระบุใหม่)"
                                                        minlength="6" maxlength="50"
                                                        autocomplete="new-password">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePasswordVisibility('password', 'eyeIcon1')"
                                                        title="แสดง/ซ่อนรหัสผ่าน">
                                                        <i class="fas fa-eye" id="eyeIcon1"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <div class="form-text small text-muted">เริ่มต้นจะใช้รหัสนักศึกษาเป็นรหัสผ่านให้อัตโนมัติ (นักศึกษาสามารถเปลี่ยนเองได้ในภายหลัง)</div>
                                                    <div class="form-text small text-muted ms-auto text-end"
                                                        id="counter_password">50/50</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- ส่วนปุ่มกดบันทึก (Card Footer) เหมือนหน้าอื่น -->
                        <div
                            class="card-footer bg-light px-4 py-3 border-0 d-flex flex-wrap justify-content-end gap-2">
                            <button type="submit" class="btn btn-success px-4 shadow-sm" id="btnSubmit">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- ==============================================================================
     คำแนะนำสำหรับนักพัฒนา (DEVELOPER DOCUMENTATION & SCRIPT ARCHITECTURE)
     ==============================================================================
     * สคริปต์ควบคุมการทำงานทั้งหมดของหน้านี้ (Drag & Drop, CSV Live Preview,
       การสลับโหมด, และการบันทึกข้อมูลเข้าฐานข้อมูล) ถูกแยกเป็นโมดูลไฟล์ภายนอกไว้ที่:
       👉 public/js/student/page_preview_info_add_studentandproject.js

     * เหตุผลที่แยกไฟล์:
       1. Browser Caching: เบราว์เซอร์จะจำไฟล์ไว้ ทำให้เปิดหน้าเว็บครั้งต่อไปเร็วขึ้นทันที
       2. Clean View: หน้า Blade นี้จะกระชับ ดูแลส่วน UI ได้ง่าย ไม่ปะปนกับโค้ด JavaScript

     * โครงสร้างของสคริปต์แบ่งออกเป็น 8 ก้อน (BLOCKS) อย่างชัดเจน:
       - ก้อนที่ 1: CONFIG & STATE MANAGEMENT (ตั้งค่า URL และตัวแปรจำสถานะไฟล์)
       - ก้อนที่ 2: MODE SWITCHER (ฟังก์ชัน switchAddMode สลับระหว่างโหมด CSV กับ Manual)
       - ก้อนที่ 3: DRAG & DROP / FILE SELECTION (จัดการกล่องลากไฟล์และปุ่มเลือกไฟล์)
       - ก้อนที่ 4: AJAX PREVIEW (ส่งไฟล์ CSV ไปแกะโครงสร้างที่ Controller)
       - ก้อนที่ 5: LIVE PREVIEW RENDERER (ฟังก์ชัน renderLivePreview สร้าง HTML ตารางโครงการ/นักศึกษา)
       - ก้อนที่ 6: SUB-TAB SWITCHER & NAVIGATION (สลับแท็บย่อยและปุ่มย้อนกลับไปหน้าเลือกไฟล์)
       - ก้อนที่ 7: CONFIRM & STORE (ส่งข้อมูล JSON ไปบันทึกลงฐานข้อมูลจริง)
       - ก้อนที่ 8: MANUAL FORM HELPERS (ซิงค์ Username จากรหัสนักศึกษา และพรีวิวรูปภาพโปรไฟล์)

     * วิธีการส่งข้อมูล (Configuration Bridge) จาก Blade ไปยัง JavaScript:
       เราส่ง Endpoint Route และ CSRF Token ผ่านตัวแปร Global Object 'window.STUDENT_PREVIEW_CONFIG'
       เพื่อให้ไฟล์ JavaScript ภายนอกสามารถเรียกใช้ได้อย่างปลอดภัยและตรงกับ Route ของ Laravel 100%
     ============================================================================== -->

<!-- ส่งค่า Route URLs, CSRF Token และรายชื่อกลุ่มโครงงานเดิม จาก Laravel ไปให้ไฟล์ JS ภายนอก -->
<script>
    window.STUDENT_PREVIEW_CONFIG = {
        previewUrl: '{{ url('/pc-csmju/admin/student/import/preview') }}',
        storeUrl: '{{ url('/pc-csmju/admin/student/import/store') }}',
        redirectUrl: '{{ url('/pc-csmju/admin/student') }}',
        csrfToken: '{{ csrf_token() }}'
    };
    window.EXISTING_PROJECTS = @json($existingProjects ?? []);
    window.TEACHERS_LIST = @json($teachers ?? []);
</script>

<!-- เรียกใช้โมดูลกล่องค้นหาส่วนกลาง (SearchBox), โมดูลตรวจจับข้อผิดพลาด (StudentVerify), โมดูล Modal แก้ไขข้อมูล และสคริปต์ควบคุมหน้าจอ -->
<script src="{{ asset('js/searchbox.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/student/student_verify_add.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/student/modal_edit_studentandproject.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/student/page_preview_info_add_studentandproject.js') }}?v={{ time() }}"></script>
