<section>
    <div class="container-fluid">
        <div class="row">
            <div class="page-heading mb-3 py-2">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h3 class="m-0"><i class="fas fa-user-shield me-2"></i>จัดการข้อมูลอาจารย์และผู้ดูแล</h3>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ url('admin/create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มข้อมูล
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-content">

            <form action="{{ url('admin') }}" method="post">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle dataTable">
                                <thead class="table-light">
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

                                                if ($profile_picture && !str_contains($profile_picture, 'teacherandofficer')) { //ถ้ารูปมันมีคำว่า teacherandofficer ให้ยัด 'profile_image/teacherandofficer/ เข้าไป
                                                    $profile_picture = str_replace('profile_image/', 'profile_image/teacherandofficer/', $profile_picture);
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>
                                                <td class="text-center">
                                                    @if (!empty($profile_picture))
                                                        <img src="{{ asset($profile_picture) }}" alt="Profile"
                                                            class="rounded-circle view-image-trigger shadow-sm" width="40" height="40"
                                                            style="object-fit: cover; cursor: pointer;" title="คลิกเพื่อดูรูปขนาดใหญ่">
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
                                                        <span class="roleadmin">ผู้ดูแลระบบ (Admin)</span>
                                                    @elseif ($role === 'teacher')
                                                        <span class="roleteacher">อาจารย์ (Teacher)</span>
                                                    @elseif ($role === 'officer')
                                                        <span class="roleofficer">เจ้าหน้าที่ (Officer)</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{-- อิงตามตาราง admins: 1 = ใช้งานปกติ, 0 = ปิดใช้งาน --}}
                                                    @if ($status == '1')
                                                        <a href="{{ url('admin/status/' . $id . '/0') }}"
                                                            class="btn btn-sm btn-outline-success"
                                                            title="คลิกเพื่อปิดใช้งาน">
                                                            <i class="fas fa-check-circle me-1"></i> ใช้งานปกติ
                                                        </a>
                                                    @else
                                                        <a href="{{ url('admin/status/' . $id . '/1') }}"
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
                                                            แก้ไข <i class="fas fa-edit"></i> 
                                                        </a>
                                                        <a href="#" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalDel" 
                                                            data-url="{{ url('admin/del/' . $id) }}" 
                                                            data-name="{{ $name }}" 
                                                            class="btn btn-danger btn-sm " title="ลบ">
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
            </form>
        </div>
    </div>
</section>



