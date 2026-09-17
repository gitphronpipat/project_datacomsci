<section>
    <div class="container-fluid" >
        <!-- ส่วนหัว (Page Heading & Breadcrumb) -->
        <div class="page-heading mb-3 py-2">
            <div class="row align-items-center gy-2">
                <div class="col-12 col-sm-6">
                    <h3 class="m-0 fs-4">
                        <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลอาจารย์และผู้ดูแล
                    </h3>
                    <p class="text-muted small m-0 mt-1">แก้ไขข้อมูลบัญชีผู้ใช้งานในระบบ</p>
                </div>
                <div class="col-12 col-sm-6 text-sm-end">
                    <a href="{{ url('/') }}" class="btn btn-back-page btn-sm px-3 shadow-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                    </a>
                </div>
            </div>
        </div>

        <!-- ฟอร์มแก้ไขข้อมูล -->
        <div class="page-content">
            <form action="{{ url('admin/update/' . $admin->id) }}" method="POST" enctype="multipart/form-data" id="adminEditForm">
                @csrf
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"style="border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-4 p-md-5">

                        <div class="row g-4">
                            <!-- คอลัมน์ซ้าย: รูปโปรไฟล์ (Avatar Upload & Preview) -->
                            <div class="col-12 col-lg-3 text-center border-end-lg pe-lg-4" >
                                <div class="mb-3">
                                    <label class="form-label fw-semibold d-block">รูปโปรไฟล์ <br><sup> (ไม่บังคับ) </sup></label >
                                    <div class="position-relative d-inline-block">
                                        <div class="rounded-circle overflow-hidden bg-light border border-2 border-secondary-subtle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                             style="width: 140px; height: 140px; cursor: pointer;" id="avatarContainer" title="คลิกเพื่อดูรูปขนาดใหญ่">

                                            @if(!empty($admin->profile_picture))
                                                <img id="imagePreview" src="{{ asset($admin->profile_picture) }}" alt="Preview"
                                                     class="w-100 h-100 rounded-circle"
                                                     style="object-fit: cover; display: block;">
                                                <i class="fas fa-user fa-4x text-secondary" id="avatarPlaceholder" style="display: none;"></i>
                                            @else
                                                <i class="fas fa-user fa-4x text-secondary" id="avatarPlaceholder"></i>
                                                <img id="imagePreview" src="#" alt="Preview"
                                                     class="w-100 h-100 rounded-circle"
                                                     style="object-fit: cover; display: none;">
                                            @endif
                                        </div>
                                        <button type="button" id="btnTriggerUpload"
                                               class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow"
                                               style="width: 38px; height: 38px; cursor: pointer;"
                                               title="เปลี่ยนรูปภาพ">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="file" id="profile_picture" name="profile_picture"
                                       class="form-control form-control-sm d-none image-crop"
                                       accept="image/*">
                                <input type="hidden" id="remove_profile_picture" name="remove_profile_picture" value="0">

                                <!-- ปุ่มล้างรูปภาพ (จะแสดงเมื่อมีรูปภาพอยู่เท่านั้น) -->
                                <div class="mt-2 mb-2" id="clearPictureWrapper" style="{{ empty($admin->profile_picture) ? 'display: none;' : '' }}">
                                    <button type="button" id="btnClearPicture"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 shadow-2xs"
                                            style="font-size: 12px; font-weight: 500;">
                                        <i class="fas fa-trash-alt me-1"></i> ล้างรูปภาพ
                                    </button>
                                </div>

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
                                                   value="{{ old('name', $admin->name) }}" maxlength="50" required>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="form-text small text-muted"></div>
                                                <div class="form-text small text-muted ms-auto text-end" id="counter_name">50/50</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="email" class="form-label fw-medium">อีเมล</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                   value="{{ old('email', $admin->email) }}" maxlength="50">
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="form-text small text-muted"></div>
                                                <div class="form-text small text-muted ms-auto text-end" id="counter_email">50/50</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="phone" class="form-label fw-medium">เบอร์โทรศัพท์</label>
                                            <input type="tel" id="phone" name="phone" class="form-control"
                                                   value="{{ old('phone', $admin->phone) }}" maxlength="20">
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="form-text small text-muted"></div>
                                                <div class="form-text small text-muted ms-auto text-end" id="counter_phone">20/20</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="role" class="form-label fw-medium">สิทธิ์การใช้งาน <span class="text-danger">*</span></label>
                                            <select name="role" class="custom-select-smooth" required>
                                                <option value="officer" {{ old('role', $admin->role) === 'officer' ? 'selected' : '' }}>เจ้าหน้าที่</option>
                                                <option value="teacher" {{ old('role', $admin->role) === 'teacher' ? 'selected' : '' }}>อาจารย์</option>
                                                <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ</option>
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
                                        <div class="col-12 col-md-6">
                                            <label for="username" class="form-label fw-medium">ชื่อผู้ใช้งาน (Username) <span class="text-danger">*</span></label>
                                            <input type="text" id="username" name="username" class="form-control"
                                                   value="{{ old('username', $admin->username) }}" maxlength="30" required autocomplete="off">
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="form-text small text-muted">แก้ไขผู้ใช้งานสำหรับล็อกอิน</div>
                                                <div class="form-text small text-muted ms-auto text-end" id="counter_username">30/30</div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label for="password" class="form-label fw-medium">รหัสผ่าน <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password" class="form-control" value="{{ old('real_pass', $admin->real_pass) }}"
                                                       placeholder="เว้นว่างไว้หากใช้รหัสผ่านเดิม" minlength="6" maxlength="50" autocomplete="new-password">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="togglePasswordVisibility('password', 'eyeIcon1')"
                                                        title="แสดง/ซ่อนรหัสผ่าน">
                                                    <i class="fas fa-eye" id="eyeIcon1"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="form-text small text-muted">เว้นว่างไว้หากใช้รหัสผ่านเดิม (อย่างน้อย 6 ตัวอักษร)</div>
                                                <div class="form-text small text-muted ms-auto text-end" id="counter_password">50/50</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- ส่วนปุ่มกดบันทึก (Card Footer) -->
                    <div class="card-footer bg-light px-4 py-3 border-0 d-flex flex-wrap justify-content-end gap-2">
                        {{-- <a href="{{ url('/') }}" class="btn btn-light border px-4">
                            <i class="fas fa-times me-1"></i> ยกเลิก
                        </a> --}}
                        <button type="submit" class="btn btn-success px-4 shadow-sm" id="btnSubmit">
                            <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
