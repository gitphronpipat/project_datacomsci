<section>
    <div class="container-fluid">
        <div class="row">
        <div class="page-heading mb-4">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="m-0 fw-bold" style="color: #0f172a; font-size: 1.35rem; letter-spacing: -0.01em;">
                        <i class="fas fa-user-shield me-2 text-secondary"></i>จัดการข้อมูลอาจารย์และผู้ดูแล
                    </h3>
                    
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <a href="{{ url('admin/create') }}" class="btn btn-primary px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> เพิ่มข้อมูล
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <form action="{{ url('admin') }}" method="post">
            @csrf
            <div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dataTable w-100 mb-0">
                            <thead>
                                    <tr>
                                        <th scope="col" class="text-center" width="5%">ลำดับ</th>
                                        <th scope="col" class="text-center" width="8%">รูปภาพ</th>
                                        <th scope="col" class="text-center">ชื่อ-นามสกุล</th>
                                        <th scope="col" class="text-center">ชื่อผู้ใช้</th>
                                        <th scope="col" class="text-center">อีเมล / เบอร์โทร</th>
                                        <th scope="col" class="text-center" width="12%">สิทธิ์</th>
                                        <th scope="col" class="text-center" width="12%">สถานะ</th>
                                        <th scope="col" class="text-center" width="12%">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($admins) && count($admins) > 0)
                                        @foreach ($admins as $i => $a)
                                            @php
                                                // รองรับทั้งแบบ Eloquent Object และ Array ป้องกันข้อผิดพลาด
                                                $id = is_object($a) ? $a->id : $a['id'];
                                                $name = is_object($a) ? $a->name ?? '-' : $a['name'] ?? '-';
                                                $username = is_object($a) ? $a->username : $a['username'];
                                                $email = is_object($a) ? $a->email ?? '-' : $a['email'] ?? '-';
                                                $phone = is_object($a) ? $a->phone ?? '-' : $a['phone'] ?? '-';
                                                $role = is_object($a) ? $a->role : $a['role'];
                                                $status = is_object($a) ? $a->status : $a['status'];
                                                $profile_picture = is_object($a)
                                                    ? $a->profile_picture ?? null
                                                    : $a['profile_picture'] ?? null;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>
                                                <td class="text-center">
                                                    @if (!empty($profile_picture))
                                                        <img src="{{ asset($profile_picture) }}" alt="Profile"
                                                            class="rounded-circle" width="40" height="40"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center text-muted"
                                                            style="width: 40px; height: 40px; border: 1px solid #dee2e6;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $name }}</strong>
                                                </td>
                                                <td>
                                                    <code>{{ $username }}</code>
                                                </td>
                                                <td>
                                                    <div><i class="far fa-envelope text-muted me-1"></i>
                                                        {{ $email }}</div>
                                                    <div><small class="text-muted"><i
                                                                class="fas fa-phone text-muted me-1"></i>
                                                            {{ $phone }}</small></div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($role === 'admin')
                                        <span class="badge" style="background-color: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0;">ผู้ดูแลระบบ (Admin)</span>
                                    @elseif ($role === 'teacher')
                                        <span class="badge" style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">อาจารย์ (Teacher)</span>
                                    @elseif ($role === 'officer')
                                        <span class="badge" style="background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">เจ้าหน้าที่ (Officer)</span>
                                    @else
                                        <span class="badge" style="background-color: #f8fafc; color: #94a3b8; border: 1px solid #f1f5f9;">-</span>
                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{-- อิงตามตาราง create_admins_table.sql: 0 = ใช้งานปกติ, 1 = ปิดใช้งาน --}}
                                                    @if ($status == '0')
                                                        <a href="{{ url('admin/status/' . $id . '/1') }}"
                                                            class="btn btn-sm btn-outline-success"
                                                            title="คลิกเพื่อปิดใช้งาน">
                                                            <i class="fas fa-check-circle me-1"></i> ใช้งานปกติ
                                                        </a>
                                                    @else
                                                        <a href="{{ url('admin/status/' . $id . '/0') }}"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="คลิกเพื่อเปิดใช้งาน">
                                                            <i class="fas fa-ban me-1"></i> ปิดใช้งาน
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('admin/edit/' . $id) }}"
                                                            class="btn btn-warning btn-sm" title="แก้ไข">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal" data-bs-target="#modalDel"
                                                            data-id="{{ $id }}"
                                                            data-url="{{ url('admin/del/' . $id) }}" title="ลบ">
                                                            <i class="far fa-trash-alt"></i>
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
            </form>
        </div>
    </div>
</section>

<!-- Modal สำหรับยืนยันการลบข้อมูล -->
<div class="modal fade" id="modalDel" tabindex="-1" aria-labelledby="modalDelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.08);">
            <div class="modal-header" style="background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;">
                <h5 class="modal-title fw-semibold" id="modalDelLabel" style="color: #0f172a; font-size: 1.1rem;">
                    <i class="fas fa-exclamation-circle me-2 text-danger"></i>ยืนยันการลบข้อมูล
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="color: #475569; font-size: 14px; line-height: 1.6;">
                คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้? เมื่อลบแล้วจะไม่สามารถกู้คืนได้
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem; background-color: #fcfcfc;">
                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <a id="btnConfirmDelete" href="#" class="btn btn-danger px-3" style="background-color: #dc2626; border-color: #dc2626; color: #fff;">ยืนยันการลบ</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalDel = document.getElementById('modalDel');
        if (modalDel) {
            modalDel.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var url = button.getAttribute('data-url');
                var confirmBtn = document.getElementById('btnConfirmDelete');
                if (confirmBtn) {
                    confirmBtn.setAttribute('href', url);
                }
            });
        }
    });
</script>
