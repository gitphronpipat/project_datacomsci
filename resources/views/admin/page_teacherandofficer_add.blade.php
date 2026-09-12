<section class="py-3">
    <div class="container-fluid">
        <!-- ส่วนหัว (Page Heading & Breadcrumb) -->
        <div class="page-heading mb-4">
            <div class="row align-items-center gy-2">
                <div class="col-12 col-sm-6">
                    <h3 class="m-0 fw-bold fs-4">
                        <i class="fas fa-user-plus me-2"></i>เพิ่มข้อมูลอาจารย์และผู้ดูแล
                    </h3>
                    <p class="text-muted small m-0 mt-1">กรอกข้อมูลเพื่อสร้างบัญชีผู้ใช้งานใหม่ในระบบ</p>
                </div>
                <div class="col-12 col-sm-6 text-sm-end">
                    <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> กลับหน้ารายการ
                    </a>
                </div>
            </div>
        </div>

        <!-- ฟอร์มกรอกข้อมูล -->
        <div class="page-content">
            <form action="{{ url('admin/create') }}" method="POST" enctype="multipart/form-data" id="adminAddForm">
                @csrf
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4 p-md-5">

                        <div class="row g-4">
                            <!-- คอลัมน์ซ้าย: รูปโปรไฟล์ (Avatar Upload & Preview) -->
                            <div class="col-12 col-lg-3 text-center border-end-lg pe-lg-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold d-block">รูปโปรไฟล์</label>
                                    <div class="position-relative d-inline-block">
                                        <div class="rounded-circle overflow-hidden bg-light border border-2 border-secondary-subtle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                             style="width: 140px; height: 140px;" id="avatarContainer">
                                            <i class="fas fa-user fa-4x text-secondary" id="avatarPlaceholder"></i>
                                            <img id="imagePreview" src="#" alt="Preview"
                                                 class="w-100 h-100 rounded-circle"
                                                 style="object-fit: cover; display: none;">
                                        </div>
                                        <label for="profile_picture"
                                               class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow"
                                               style="width: 38px; height: 38px; cursor: pointer;"
                                               title="เลือกรูปภาพ">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                    </div>
                                </div>
                                <input type="file" id="profile_picture" name="profile_picture"
                                       class="form-control form-control-sm d-none"
                                       accept="image/*" onchange="previewImage(event)">
                                <div class="form-text small text-muted">
                                    รองรับไฟล์ JPG, PNG<br>(ขนาดไม่เกิน 2MB)
                                </div>
                            </div>

                            <!-- คอลัมน์ขวา: รายละเอียดข้อมูลต่างๆ -->
                            <div class="col-12 col-lg-9 ps-lg-4">

                                <!-- หมวดที่ 1: ข้อมูลทั่วไป -->
                                <div class="mb-4">
                                    <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-id-card me-2"></i>ข้อมูลส่วนตัวและการติดต่อ
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="name" class="form-label fw-medium">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                   placeholder="เช่น ดร.สมชาย ใจดี" required>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="email" class="form-label fw-medium">อีเมล</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                   placeholder="เช่น somchai@mju.ac.th">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="phone" class="form-label fw-medium">เบอร์โทรศัพท์</label>
                                            <input type="tel" id="phone" name="phone" class="form-control"
                                                   placeholder="เช่น 081-234-5678" maxlength="20">
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-3">
                                            <label for="role" class="form-label fw-medium">สิทธิ์การใช้งาน <span class="text-danger">*</span></label>
                                            <select id="role" name="role" class="form-select" required>
                                                <option value="officer" selected>เจ้าหน้าที่</option>
                                                <option value="teacher">อาจารย์</option>
                                                <option value="admin">ผู้ดูแลระบบ</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-3">
                                            <label for="status" class="form-label fw-medium">สถานะ <span class="text-danger">*</span></label>
                                            <select id="status" name="status" class="form-select" required>
                                                <option value="0" selected>เปิดใช้งานปกติ</option>
                                                <option value="1">ระงับการใช้งาน</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- หมวดที่ 2: บัญชีผู้ใช้และความปลอดภัย -->
                                <div class="mb-4">
                                    <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-lock me-2"></i>ข้อมูลบัญชีเข้าสู่ระบบ
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <label for="username" class="form-label fw-medium">ชื่อผู้ใช้งาน (Username) <span class="text-danger">*</span></label>
                                            <input type="text" id="username" name="username" class="form-control"
                                                   placeholder="เช่น somchai_mju" required autocomplete="off">
                                            <div class="form-text small">ใช้สำหรับล็อกอินเข้าสู่ระบบ</div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-4">
                                            <label for="password" class="form-label fw-medium">รหัสผ่าน <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password" class="form-control"
                                                       placeholder="อย่างน้อย 6 ตัวอักษร" minlength="6" required autocomplete="new-password">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePasswordVisibility('password', 'eyeIcon1')"
                                                        title="แสดง/ซ่อนรหัสผ่าน">
                                                    <i class="fas fa-eye" id="eyeIcon1"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-md-4">
                                            <label for="passwordCF" class="form-label fw-medium">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" id="passwordCF" name="password_cf" class="form-control"
                                                       placeholder="กรอกรหัสผ่านซ้ำอีกครั้ง" required autocomplete="new-password">
                                                <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePasswordVisibility('passwordCF', 'eyeIcon2')"
                                                        title="แสดง/ซ่อนรหัสผ่าน">
                                                    <i class="fas fa-eye" id="eyeIcon2"></i>
                                                </button>
                                            </div>
                                            <div id="passwordMatchMsg"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- ส่วนปุ่มกดบันทึก (Card Footer) -->
                    <div class="card-footer bg-light px-4 py-3 border-0 d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ url('/') }}" class="btn btn-light border px-4">
                            <i class="fas fa-times me-1"></i> ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-success px-4 shadow-sm" id="btnSubmit">
                            <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- สคริปต์พรีวิวรูปและตรวจสอบรหัสผ่าน (ใช้ฟังก์ชันพื้นฐาน ไม่โหลดไลบรารีซ้ำซ้อน) -->
<script>
    // สลับดูรหัสผ่าน (ดวงตา)
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // พรีวิวรูปโปรไฟล์ทันทีเมื่อเลือกไฟล์
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('avatarPlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    // ตรวจสอบความตรงกันของรหัสผ่าน
    document.addEventListener('DOMContentLoaded', function() {
        const pass = document.getElementById('password');
        const passCF = document.getElementById('passwordCF');
        const msg = document.getElementById('passwordMatchMsg');
        const form = document.getElementById('adminAddForm');

        function validatePasswordMatch() {
            if (!passCF.value) {
                msg.textContent = '';
                passCF.classList.remove('is-valid', 'is-invalid');
                return true;
            }
            if (pass.value === passCF.value) {
                msg.textContent = 'รหัสผ่านตรงกัน';
                msg.className = 'small text-success mt-1 d-block';
                passCF.classList.remove('is-invalid');
                passCF.classList.add('is-valid');
                return true;
            } else {
                msg.textContent = 'รหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง';
                msg.className = 'small text-danger mt-1 d-block';
                passCF.classList.remove('is-valid');
                passCF.classList.add('is-invalid');
                return false;
            }
        }

        if (pass && passCF) {
            pass.addEventListener('input', validatePasswordMatch);
            passCF.addEventListener('input', validatePasswordMatch);
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                if (pass.value !== passCF.value) {
                    e.preventDefault();
                    validatePasswordMatch();
                    passCF.focus();
                }
            });
        }
    });
</script>
