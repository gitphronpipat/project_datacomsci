<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading mb-3 py-2">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <h3 class="m-0"><i class="fas fa-user-graduate me-2"></i>จัดการข้อมูลนักศึกษา</h3>
                    </div>
                    <div class="col-12 col-md-6 text-md-end text-start">
                        <a href="{{ url('pc-csmju/admin/student/create') }}" class="btn btn-primary shadow-sm">
                            <i class="fas fa-plus"></i> เพิ่มข้อมูลนักศึกษา
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-content">
            <!-- แท็บสลับข้อมูล (Step Tabs) จัดวางไว้ด้านบนใน page-content เหนือ Card ตาราง -->
            <div class="step-tabs">
                <button type="button" class="step-btn active" data-step="1">
                    <i class="fas fa-layer-group me-2"></i>ข้อมูลนักศึกษาเกี่ยวกับโครงงาน (Student Project Detail)
                </button>
                <span class="tab-divider">|</span>
                <button type="button" class="step-btn" data-step="2">
                    <i class="fas fa-id-card me-2"></i>ข้อมูลบัญชีนักศึกษา (Student Accounts)
                </button>
            </div>

            <!-- กล่อง Card หน้าตาเดิมเป๊ะ มีเส้นขอบเหมือนหน้าแรก (ตัด border-0 ออก) -->
            <div class="card shadow-sm">
                <div class="card-body px-3 px-md-4">
                    <div class="step-content">
                        <!-- Step 1: ตารางข้อมูลกลุ่มโครงงาน (Project Members) -->
                        <div class="step-pane active" data-step="1">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle dataTable w-100">
                                    <thead class="table-light text-nowrap">
                                        <tr>
                                            <th scope="col" class="text-center" width="4%">ลำดับ</th>
                                            <th scope="col" class="text-center" width="6%">ปีที่ทำ</th>
                                            <th scope="col" class="text-center" width="8%">ประเภท</th>
                                            <th scope="col" class="text-center" width="11%">นักศึกษา</th>
                                            <th scope="col" class="text-center" style="min-width: 220px; max-width: 320px;">ชื่อโครงงาน</th>
                                            <th scope="col" class="text-center" width="12%">ที่ปรึกษาโครงงาน</th>
                                            <th scope="col" class="text-center" width="12%">กรรมการโครงงาน</th>
                                            <th scope="col" class="text-center">ที่ปรึกษาพิเศษ</th>
                                            <th scope="col" class="text-center text-nowrap" width="10%">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $studentProjects = [];
                                            $pList = $projectGroups ?? [];
                                            if (!empty($pList) && count($pList) > 0) {
                                                foreach ($pList as $p) {
                                                    $pId = is_object($p) ? $p->id : $p['id'];
                                                    $pType = is_object($p) ? ($p->type ?? 'project') : ($p['type'] ?? 'project');
                                                    $pTitleTh = is_object($p) ? ($p->title_th ?? '-') : ($p['title_th'] ?? '-');
                                                    $pTitleEn = is_object($p) ? ($p->title_en ?? '-') : ($p['title_en'] ?? '-');
                                                    $pYear = is_object($p) ? ($p->project_year ?? $p->academic_year ?? '-') : ($p['project_year'] ?? $p['academic_year'] ?? '-');
                                                    $pCompany = is_object($p) ? ($p->company_name ?? '') : ($p['company_name'] ?? '');
                                                    $pRemark = is_object($p) ? ($p->remark ?? '') : ($p['remark'] ?? '');
                                                    $pAdvisor = is_object($p) ? ($p->advisor_president_name ?? '-') : ($p['advisor_president_name'] ?? '-');
                                                    $pAdvisorId = is_object($p) ? ($p->advisor_president_id ?? null) : ($p['advisor_president_id'] ?? null);
                                                    $pCom1 = is_object($p) ? ($p->advisor_committee_1_name ?? null) : ($p['advisor_committee_1_name'] ?? null);
                                                    $pCom1Id = is_object($p) ? ($p->advisor_committee_1_id ?? null) : ($p['advisor_committee_1_id'] ?? null);
                                                    $pCom2 = is_object($p) ? ($p->advisor_committee_2_name ?? null) : ($p['advisor_committee_2_name'] ?? null);
                                                    $pCom2Id = is_object($p) ? ($p->advisor_committee_2_id ?? null) : ($p['advisor_committee_2_id'] ?? null);
                                                    $pMembers = is_object($p) ? ($p->members ?? []) : ($p['members'] ?? []);
                                                    $pSpecialAdvisors = is_object($p) ? ($p->special_advisors ?? []) : ($p['special_advisors'] ?? []);

                                                    if (!empty($pMembers) && count($pMembers) > 0) {
                                                        foreach ($pMembers as $m) {
                                                            $studentProjects[] = [
                                                                'project_id'              => $pId,
                                                                'type'                    => $pType,
                                                                'student_id'              => is_object($m) ? $m->student_id : $m['student_id'],
                                                                'student_name'            => is_object($m) ? ($m->name ?? '-') : ($m['name'] ?? '-'),
                                                                'title_th'                => $pTitleTh,
                                                                'title_en'                => $pTitleEn,
                                                                'company_name'            => $pCompany,
                                                                'remark'                  => $pRemark,
                                                                'project_year'            => $pYear,
                                                                'advisor_president_name'  => $pAdvisor,
                                                                'advisor_president_id'    => $pAdvisorId,
                                                                'advisor_committee_1_name'=> $pCom1,
                                                                'advisor_committee_1_id'  => $pCom1Id,
                                                                'advisor_committee_2_name'=> $pCom2,
                                                                'advisor_committee_2_id'  => $pCom2Id,
                                                                'special_advisors'        => $pSpecialAdvisors,
                                                            ];
                                                        }
                                                    } else {
                                                        $studentProjects[] = [
                                                            'project_id'              => $pId,
                                                            'type'                    => $pType,
                                                            'student_id'              => '-',
                                                            'student_name'            => '-',
                                                            'title_th'                => $pTitleTh,
                                                            'title_en'                => $pTitleEn,
                                                            'company_name'            => $pCompany,
                                                            'remark'                  => $pRemark,
                                                            'project_year'            => $pYear,
                                                            'advisor_president_name'  => $pAdvisor,
                                                            'advisor_president_id'    => $pAdvisorId,
                                                            'advisor_committee_1_name'=> $pCom1,
                                                            'advisor_committee_1_id'  => $pCom1Id,
                                                            'advisor_committee_2_name'=> $pCom2,
                                                            'advisor_committee_2_id'  => $pCom2Id,
                                                            'special_advisors'        => $pSpecialAdvisors,
                                                        ];
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if (!empty($studentProjects) && count($studentProjects) > 0)
                                            @foreach ($studentProjects as $idx => $sp)
                                                <tr>
                                                    {{-- 1. ลำดับ --}}
                                                    <td class="text-center fw-semibold text-secondary">{{ $idx + 1 }}</td>

                                                    {{-- 8. ปีที่ทำโครงงาน --}}
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 font-monospace">
                                                            {{ $sp['project_year'] }}
                                                        </span>
                                                    </td>
                                                    
                                                    {{-- 2. ประเภท --}}
                                                    <td class="text-center">
                                                        @if ($sp['type'] == 'coop')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                                <i class="fas fa-building me-1"></i>สหกิจ (497)
                                                            </span>
                                                        @else
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                                <i class="fas fa-project-diagram me-1"></i>โครงงาน (498)
                                                            </span>
                                                        @endif
                                                    </td>

                                                    {{-- 3. นักศึกษา (รหัสอยู่บน ชื่อ-สกุลอยู่ล่าง) --}}
                                                    <td>
                                                        @if ($sp['student_id'] !== '-')
                                                            <div class="mb-1">
                                                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                                                    {{ $sp['student_id'] }}
                                                                </span>
                                                            </div>
                                                            <div class="fw-semibold text-dark small">
                                                                <i class="fas fa-user-graduate text-secondary me-1"></i>{{ $sp['student_name'] }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted small"><i class="fas fa-exclamation-circle me-1"></i>ยังไม่มีสมาชิก</span>
                                                        @endif
                                                    </td>

                                                    {{-- 4. ชื่อโครงงาน (ไทยอยู่บน อังกฤษตัวเอียงอยู่ล่าง + ตัดขึ้นบรรทัดใหม่อัตโนมัติเมื่อยาวเกินไป) --}}
                                                    <td class="text-wrap" style="white-space: normal !important; min-width: 220px; max-width: 320px;">
                                                        <div class="fw-bold text-dark" style="line-height: 1.5; word-break: break-word; overflow-wrap: break-word;">
                                                            {{ $sp['title_th'] }}
                                                        </div>
                                                        @if (!empty($sp['title_en']) && $sp['title_en'] !== '-')
                                                            <div class="text-muted small mt-1" style="line-height: 1.4; word-break: break-word; overflow-wrap: break-word;">
                                                                <em>{{ $sp['title_en'] }}</em>
                                                            </div>
                                                        @endif
                                                    </td>

                                                    {{-- 5. ที่ปรึกษาโครงงาน (ประธานอาจารย์ที่ปรึกษา) --}}
                                                    <td>
                                                        @if (!empty($sp['advisor_president_name']) && $sp['advisor_president_name'] !== '-')
                                                            <div class="fw-medium text-dark small">
                                                                <i class="fas fa-chalkboard-teacher text-primary me-1"></i>{{ $sp['advisor_president_name'] }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted text-center d-block small">-</span>
                                                        @endif
                                                    </td>

                                                     {{-- 7. กรรมการโครงงาน --}}
                                                    <td>
                                                        @php
                                                            $com1 = $sp['advisor_committee_1_name'];
                                                            $com2 = $sp['advisor_committee_2_name'];
                                                            $hasCom = (!empty($com1) && $com1 !== '-') || (!empty($com2) && $com2 !== '-');
                                                        @endphp
                                                        @if ($hasCom)
                                                            <div class="d-flex flex-column gap-1 small">
                                                                @if (!empty($com1) && $com1 !== '-')
                                                                    <div class="text-truncate"><i class="fas fa-user-check text-secondary me-1"></i>{{ $com1 }}</div>
                                                                @endif
                                                                @if (!empty($com2) && $com2 !== '-')
                                                                    <div class="text-truncate"><i class="fas fa-user-check text-secondary me-1"></i>{{ $com2 }}</div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted text-center d-block small">-</span>
                                                        @endif
                                                    </td>

                                                    {{-- 6. ที่ปรึกษาพิเศษ (คอลัมน์เฉพาะ) --}}
                                                    <td>
                                                        @if (!empty($sp['special_advisors']) && count($sp['special_advisors']) > 0)
                                                            <div class="d-flex flex-column gap-1 small">
                                                                @foreach ($sp['special_advisors'] as $sa)
                                                                    <div class="text-truncate" title="ที่ปรึกษาพิเศษ">
                                                                        <i class="fas fa-user-tag text-info me-1"></i>{{ $sa }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted text-center d-block small">-</span>
                                                        @endif
                                                    </td>

                                                    {{-- 9. การจัดการโครงงาน (แก้ไข / ลบ) --}}
                                                    <td class="text-center text-nowrap">
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-warning btn-sm btn-edit-project"
                                                                title="แก้ไขข้อมูลโครงงาน"
                                                                data-id="{{ $sp['project_id'] }}"
                                                                data-type="{{ $sp['type'] }}"
                                                                data-title-th="{{ $sp['title_th'] }}"
                                                                data-title-en="{{ $sp['title_en'] !== '-' ? $sp['title_en'] : '' }}"
                                                                data-company="{{ $sp['company_name'] ?? '' }}"
                                                                data-remark="{{ $sp['remark'] ?? '' }}"
                                                                data-advisor-president="{{ $sp['advisor_president_name'] !== '-' ? $sp['advisor_president_name'] : '' }}"
                                                                data-advisor-president-id="{{ $sp['advisor_president_id'] ?? '' }}"
                                                                data-advisor-committee1="{{ $sp['advisor_committee_1_name'] !== '-' ? $sp['advisor_committee_1_name'] : '' }}"
                                                                data-advisor-committee1-id="{{ $sp['advisor_committee_1_id'] ?? '' }}"
                                                                data-advisor-committee2="{{ $sp['advisor_committee_2_name'] !== '-' ? $sp['advisor_committee_2_name'] : '' }}"
                                                                data-advisor-committee2-id="{{ $sp['advisor_committee_2_id'] ?? '' }}"
                                                                data-special-advisors="{{ implode(',', $sp['special_advisors'] ?? []) }}">
                                                                แก้ไข <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="#" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalDel" 
                                                                data-url="{{ url('pc-csmju/admin/student/project/del/' . $sp['project_id']) }}" 
                                                                data-name="โครงงาน: {{ $sp['title_th'] }}" 
                                                                class="btn btn-danger btn-sm" title="ลบโครงงาน">
                                                                <i class="fas fa-trash-alt"></i> ลบ
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Step 2: ตารางข้อมูลบัญชีนักศึกษา (Student Accounts - โครงสร้างตารางเดิมเป๊ะ 100%) -->
                        <div class="step-pane" data-step="2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle dataTable w-100">
                                    <thead class="table-light text-nowrap">
                                        <tr>
                                            <th scope="col" class="text-center" width="5%">ลำดับ</th>
                                            <th scope="col" class="text-center" width="8%">รูปภาพ</th>
                                            <th scope="col" class="text-center" width="14%">รหัสนักศึกษา</th>
                                            <th scope="col" class="text-center">ชื่อ-นามสกุล</th>
                                            <th scope="col" class="text-center">ชื่อผู้ใช้</th>
                                            <th scope="col" class="text-center">อีเมล / เบอร์โทร</th>
                                            <th scope="col" class="text-center" width="12%">สถานะ</th>
                                            <th scope="col" class="text-center text-nowrap" width="12%">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // รองรับตัวแปร $students หรือ $admins เพื่อความยืดหยุ่นในการส่งข้อมูลจาก Controller
                                            $studentList = $students ?? $admins ?? [];
                                        @endphp

                                        @if (!empty($studentList) && count($studentList) > 0)
                                            @foreach ($studentList as $i => $s)
                                                @php
                                                    // รองรับทั้งแบบ Eloquent Model และ Array ป้องกัน Error
                                                    $id = is_object($s) ? $s->id : $s['id'];
                                                    $student_id = is_object($s) ? ($s->student_id ?? '-') : ($s['student_id'] ?? '-');
                                                    $name = is_object($s) ? ($s->name ?? '-') : ($s['name'] ?? '-');
                                                    $username = is_object($s) ? $s->username : $s['username'];
                                                    $email = is_object($s) ? ($s->email ?? '-') : ($s['email'] ?? '-');
                                                    $phone = is_object($s) ? ($s->phone ?? '-') : ($s['phone'] ?? '-');
                                                    $status = is_object($s) ? $s->status : $s['status'];
                                                    $last_login_at = is_object($s) ? ($s->last_login_at ?? null) : ($s['last_login_at'] ?? null);
                                                    $profile_picture = is_object($s)
                                                        ? ($s->profile_picture ?? null)
                                                        : ($s['profile_picture'] ?? null);

                                                    // ตรวจสอบ path รูปภาพนักศึกษา (ถ้าเก็บแยกโฟลเดอร์ profile_image/student/)
                                                    if ($profile_picture && !str_contains($profile_picture, 'student') && !str_contains($profile_picture, 'teacherandofficer')) {
                                                        $profile_picture = str_replace('profile_image/', 'profile_image/student/', $profile_picture);
                                                    }
                                                @endphp
                                                <tr class="{{ $status == '0' ? 'table-row-inactive' : '' }}">
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td class="text-center">
                                                        @if (!empty($profile_picture))
                                                            <img src="{{ asset($profile_picture) }}" alt="Profile"
                                                                class="rounded-circle view-image-trigger shadow-sm" width="40" height="40"
                                                                style="object-fit: cover; cursor: pointer;" title="คลิกเพื่อดูรูปขนาดใหญ่">
                                                        @else
                                                            <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center text-muted"
                                                                style="width: 40px; height: 40px; border: 1px solid #dee2e6;">
                                                                <i class="fas fa-user-graduate"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">
                                                            {{ $student_id }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $name }}</strong>
                                                    </td>
                                                    <td>
                                                        <code>{{ $username }}</code>
                                                        @if (!empty($last_login_at))
                                                            <div>
                                                                <small class="text-muted" style="font-size: 11px;" title="เข้าสู่ระบบล่าสุด">
                                                                    <i class="far fa-clock me-1"></i>{{ date('d/m/Y H:i', strtotime($last_login_at)) }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div><i class="far fa-envelope text-muted me-1"></i>
                                                            {{ $email }}</div>
                                                        <div><small class="text-muted"><i
                                                                    class="fas fa-phone text-muted me-1"></i>
                                                                {{ $phone }}</small></div>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        {{-- อิงตามตาราง student: 1 = ใช้งานปกติ, 0 = ปิดใช้งาน พร้อมแจ้งเตือนยืนยันก่อนเปลี่ยน --}}
                                                        @if ($status == '1' || $status === 1)
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-outline-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalConfirmAction"
                                                                data-title="ยืนยันการปิดใช้งาน?"
                                                                data-message="คุณต้องการปิดการใช้งานบัญชีนักศึกษา <b style='font-size: 18px;' >'{{ $name }}'</b> ({{ $student_id }})<br>นักศึกษาจะไม่สามารถเข้าสู่ระบบได้ชั่วคราว"
                                                                data-url="{{ url('pc-csmju/admin/student/status/' . $id . '/0') }}"
                                                                data-btn-text="ปิดใช้งาน"
                                                                data-btn-class="btn-danger"
                                                                data-icon="fas fa-ban"
                                                                data-icon-box="bg-danger-subtle text-danger"
                                                                title="คลิกเพื่อปิดใช้งาน">
                                                                <i class="fas fa-check-circle me-1"></i> ใช้งานปกติ
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalConfirmAction"
                                                                data-title="ยืนยันการเปิดใช้งาน?"
                                                                data-message="คุณต้องการเปิดใช้งานบัญชีนักศึกษา <b style='font-size: 18px;' >'{{ $name }}'</b> ({{ $student_id }})"
                                                                data-url="{{ url('pc-csmju/admin/student/status/' . $id . '/1') }}"
                                                                data-btn-text="เปิดใช้งาน"
                                                                data-btn-class="btn-success"
                                                                data-icon="fas fa-check-circle"
                                                                data-icon-box="bg-success-subtle text-success"
                                                                title="คลิกเพื่อเปิดใช้งาน">
                                                                <i class="fas fa-ban me-1"></i> ปิดใช้งาน
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-warning btn-sm btn-edit-student"
                                                                title="แก้ไขข้อมูลนักศึกษา"
                                                                data-id="{{ $id }}"
                                                                data-student-id="{{ $student_id }}"
                                                                data-name="{{ $name }}"
                                                                data-nickname="{{ is_object($s) ? ($s->nickname ?? '') : ($s['nickname'] ?? '') }}"
                                                                data-type="{{ is_object($s) ? ($s->project_type ?? 'project') : 'project' }}"
                                                                data-status="{{ is_object($s) ? ($s->status_student ?? 'doing') : ($s['status_student'] ?? 'doing') }}"
                                                                data-email="{{ $email !== '-' ? $email : '' }}"
                                                                data-phone="{{ $phone !== '-' ? $phone : '' }}"
                                                                data-remark="{{ is_object($s) ? ($s->remark ?? '') : ($s['remark'] ?? '') }}">
                                                                แก้ไข <i class="fas fa-edit"></i> 
                                                            </button>
                                                            <a href="#" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalDel" 
                                                                data-url="{{ url('pc-csmju/admin/student/del/' . $id) }}" 
                                                                data-name="{{ $name }} ({{ $student_id }})" 
                                                                class="btn btn-danger btn-sm" title="ลบ">
                                                                <i class="fas fa-trash-alt"></i> ลบ
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    window.TEACHERS_LIST = {!! json_encode($teachers ?? []) !!};
    window.CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/student/modal_edit_studentandproject.js') }}?v={{ time() }}"></script>
