<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Studentmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class Studentcontroller extends Controller
{
    /**
     * หน้ารายการนักศึกษา (ตาราง)
     */
    public function index()
    {
        // 1. ดึงข้อมูลบัญชีนักศึกษา (สำหรับ Step 2: บัญชีผู้ใช้นักศึกษา)
        if (Schema::hasTable('student')) {
            // ดึงเฉพาะฟิลด์ที่ต้องใช้แสดงในตาราง ไม่ต้องดึง password ออกมา (ประหยัด RAM และปลอดภัย)
            $students = Studentmodel::select('id', 'student_id', 'name', 'nickname', 'username', 'email', 'phone', 'status', 'status_student', 'remark', 'profile_picture', 'last_login_at')
                ->orderBy('id', 'desc')
                ->get();

            if (Schema::hasTable('project_members') && Schema::hasTable('projects')) {
                $studentProjectsMap = DB::table('project_members')
                    ->join('projects', 'project_members.project_id', '=', 'projects.id')
                    ->select('project_members.student_id', 'projects.id as project_id', 'projects.type')
                    ->get()
                    ->keyBy('student_id');

                foreach ($students as $s) {
                    $sid = $s->student_id;
                    if (isset($studentProjectsMap[$sid])) {
                        $s->project_type = $studentProjectsMap[$sid]->type;
                        $s->project_id = $studentProjectsMap[$sid]->project_id;
                    } else {
                        $s->project_type = 'project';
                        $s->project_id = null;
                    }
                }
            }
        } else {
            $students = collect([]);
        }

        // 2. ดึงข้อมูลโครงงานและสมาชิกกลุ่ม (สำหรับ Step 1: ข้อมูลกลุ่มโครงงาน project_members)
        if (Schema::hasTable('projects') && Schema::hasTable('project_members')) {
            $projectGroups = \Illuminate\Support\Facades\DB::table('projects')
                ->leftJoin('admins as adv_pres', 'projects.advisor_president_id', '=', 'adv_pres.id')
                ->leftJoin('admins as adv_com1', 'projects.advisor_committee_1_id', '=', 'adv_com1.id')
                ->leftJoin('admins as adv_com2', 'projects.advisor_committee_2_id', '=', 'adv_com2.id')
                ->select(
                    'projects.*',
                    'adv_pres.name as advisor_president_name',
                    'adv_com1.name as advisor_committee_1_name',
                    'adv_com2.name as advisor_committee_2_name'
                )
                ->orderBy('projects.id', 'desc')
                ->get();

            // ดึงรายชื่อนักศึกษาที่เป็นสมาชิกของแต่ละโครงงาน
            foreach ($projectGroups as $p) {
                if (Schema::hasTable('student')) {
                    $p->members = \Illuminate\Support\Facades\DB::table('project_members')
                        ->join('student', 'project_members.student_id', '=', 'student.student_id')
                        ->where('project_members.project_id', $p->id)
                        ->select('student.student_id', 'student.name', 'student.profile_picture', 'project_members.joined_at')
                        ->get();
                } else {
                    $p->members = \Illuminate\Support\Facades\DB::table('project_members')
                        ->where('project_id', $p->id)
                        ->select('student_id', 'joined_at')
                        ->get();
                }

                // ดึงรายชื่อที่ปรึกษาพิเศษจากตาราง project_special_advisors
                if (Schema::hasTable('project_special_advisors')) {
                    $p->special_advisors = \Illuminate\Support\Facades\DB::table('project_special_advisors')
                        ->where('project_id', $p->id)
                        ->pluck('name')
                        ->toArray();
                } else {
                    $p->special_advisors = [];
                }
            }
        } else {
            $projectGroups = collect([]);
        }

        $teachers = Schema::hasTable('admins')
            ? DB::table('admins')->where('status', '1')->orderBy('name', 'asc')->get()
            : collect([]);

        $data = [
            'content'       => 'admin/page_student',
            'active_menu'   => 'page_student',
            'students'      => $students,
            'projectGroups' => $projectGroups,
            'teachers'      => $teachers,
        ];
        Log::info('Studentcontroller index method called');

        return view('admin/index', $data);
    }

    /**
     * หน้าฟอร์มเพิ่มข้อมูลนักศึกษา (เตรียมพร้อมสำหรับการสร้างฟอร์มเพิ่มข้อมูล)
     */
    /**
     * หน้าฟอร์มเพิ่มข้อมูลนักศึกษา (เตรียมพร้อมสำหรับการสร้างฟอร์มเพิ่มข้อมูล)
     */
    public function create()
    {
        $teachers = Schema::hasTable('admins')
            ? DB::table('admins')->where('status', '1')->orderBy('name', 'asc')->get()
            : collect([]);

        $currentYear = date('Y') + 543;

        // ดึงรายการปีที่ทำโครงงานทั้งหมดในระบบ
        $existingYears = [];
        $existingProjects = collect([]);

        if (Schema::hasTable('projects')) {
            $yearsFromDb = DB::table('projects')
                ->whereNotNull('project_year')
                ->distinct()
                ->pluck('project_year')
                ->toArray();

            $existingYears = array_unique(array_merge([$currentYear, $currentYear - 1, $currentYear - 2, $currentYear - 3], $yearsFromDb));
            rsort($existingYears);

            // ดึงโครงการเดิมในระบบ พร้อมนับจำนวนสมาชิกและชื่อสมาชิกสำหรับพรีวิว
            $existingProjects = DB::table('projects')
                ->select('id', 'title_th', 'title_en', 'type', 'project_year', 'company_name')
                ->orderBy('id', 'desc')
                ->get();

            if (Schema::hasTable('project_members')) {
                $memberCounts = DB::table('project_members')
                    ->select('project_id', DB::raw('count(*) as total'))
                    ->groupBy('project_id')
                    ->pluck('total', 'project_id');

                $membersByProject = DB::table('project_members')
                    ->leftJoin('student', 'project_members.student_id', '=', 'student.student_id')
                    ->select('project_members.project_id', 'project_members.student_id', 'student.name')
                    ->get()
                    ->groupBy('project_id');

                foreach ($existingProjects as $p) {
                    $p->member_count = $memberCounts[$p->id] ?? 0;
                    $p->member_names = isset($membersByProject[$p->id]) 
                        ? $membersByProject[$p->id]->pluck('name')->filter()->toArray() 
                        : [];
                }
            }
        } else {
            $existingYears = [$currentYear, $currentYear - 1, $currentYear - 2, $currentYear - 3];
        }

        $data = [
            'content'          => 'admin/page_student_add',
            'active_menu'      => 'page_student',
            'teachers'         => $teachers,
            'currentYear'      => $currentYear,
            'existingYears'    => array_values($existingYears),
            'existingProjects' => $existingProjects,
        ];
        Log::info('Studentcontroller create method called');

        return view('admin/index', $data);
    }

    /**
     * ฟังก์ชันบันทึกข้อมูลนักศึกษาใหม่ (POST)
     */
    public function store(Request $request)
    {
        // หากผู้ใช้ไม่ได้กรอก Username หรือ Password ให้ดึงจากรหัสนักศึกษาให้อัตโนมัติ
        if (empty($request->username) && !empty($request->student_id)) {
            $request->merge(['username' => $request->student_id]);
        }
        if (empty($request->password) && !empty($request->student_id)) {
            $request->merge(['password' => $request->student_id]);
        }

        $messages = [
            'student_id.required'           => 'กรุณากรอกรหัสนักศึกษา',
            'student_id.max'                => 'รหัสนักศึกษาต้องมีความยาวไม่เกิน 15 ตัวอักษร',
            'student_id.unique'             => 'รหัสนักศึกษา "' . $request->student_id . '" มีอยู่ในระบบแล้ว',
            'username.required'             => 'กรุณากรอกชื่อผู้ใช้งาน (Username)',
            'username.max'                  => 'ชื่อผู้ใช้งาน (Username) ต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'username.unique'               => 'ชื่อผู้ใช้งาน (Username) "' . $request->username . '" มีอยู่ในระบบแล้ว',
            'password.required'             => 'กรุณากรอกรหัสผ่าน',
            'password.min'                  => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'name.required'                 => 'กรุณากรอกชื่อ-นามสกุล',
            'name.max'                      => 'ชื่อ-นามสกุล ต้องมีความยาวไม่เกิน 255 ตัวอักษร',
            'nickname.max'                  => 'ชื่อเล่น ต้องมีความยาวไม่เกิน 30 ตัวอักษร',
            'email.email'                   => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.max'                     => 'อีเมลต้องมีความยาวไม่เกิน 100 ตัวอักษร',
            'email.unique'                  => 'อีเมล "' . $request->email . '" มีอยู่ในระบบแล้ว',
            'phone.max'                     => 'เบอร์โทรศัพท์ต้องมีความยาวไม่เกิน 20 ตัวอักษร',
            'profile_picture.image'         => 'ไฟล์รูปโปรไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'profile_picture.max'           => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
            'existing_project_id.required'  => 'กรุณาเลือกกลุ่มโครงงานที่ต้องการเพิ่มนักศึกษาเข้าไป',
            'new_project_title_th.required' => 'กรุณากรอกชื่อโครงงานภาษาไทย',
            'new_project_year.required'     => 'กรุณาเลือกปีที่ทำโครงงาน',
        ];

        $rules = [
            'student_id'      => 'required|string|max:15|unique:student,student_id',
            'username'        => 'required|string|max:50|unique:student,username',
            'password'        => 'nullable|string|min:6',
            'name'            => 'required|string|max:255',
            'nickname'        => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:100|unique:student,email',
            'phone'           => 'nullable|string|max:20',
            'remark'          => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:10240',
        ];

        // ตรวจสอบความถูกต้องของข้อมูลตามสถานะนักศึกษา
        $statusStudent = $request->status_student ?: 'no_project';
        $projectMode   = ($statusStudent !== 'no_project') ? ($request->project_mode ?: 'join_existing') : 'no_project';

        if ($statusStudent !== 'no_project') {
            if ($projectMode === 'join_existing') {
                $rules['existing_project_id'] = 'required';
            } elseif ($projectMode === 'create_new') {
                $rules['new_project_title_th'] = 'required|string|max:255';
                $rules['new_project_year']     = 'required|numeric';
                $rules['new_project_type']     = 'required|in:project,coop';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $resultKey = 'warning';

            if (($errors->has('student_id') && str_contains($errors->first('student_id'), 'มีอยู่ในระบบแล้ว')) ||
                ($errors->has('username') && str_contains($errors->first('username'), 'มีอยู่ในระบบแล้ว')) ||
                ($errors->has('email') && str_contains($errors->first('email'), 'มีอยู่ในระบบแล้ว'))) {
                $resultKey = 'duplicate';
            }

            return redirect()->back()
                ->withInput()
                ->with('result', $resultKey)
                ->with('message', $errors->first());
        }

        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $uploadDir = public_path('profile_image/student');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = $request->student_id . '_' . date('Ymd_His') . '.' . $ext;
            $file->move($uploadDir, $fileName);
            $profilePath = 'profile_image/student/' . $fileName;
        }

        $finalPassword = !empty($request->password) ? $request->password : $request->student_id;
        $finalUsername = !empty($request->username) ? $request->username : $request->student_id;

        $studentData = [
            'student_id'      => $request->student_id,
            'username'        => $finalUsername,
            'password'        => Hash::make($finalPassword),
            'real_pass'       => $finalPassword,
            'status'          => 1,
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'profile_picture' => $profilePath,
        ];

        if (Schema::hasColumn('student', 'nickname')) {
            $studentData['nickname'] = $request->nickname;
        }
        if (Schema::hasColumn('student', 'remark')) {
            $studentData['remark'] = $request->remark;
        }
        if (Schema::hasColumn('student', 'status_student')) {
            $studentData['status_student'] = $statusStudent;
        }

        $student = DB::transaction(function () use ($request, $studentData) {
            $newStudent = Studentmodel::create($studentData);

            // จัดการความสัมพันธ์กลุ่มโครงงาน (เฉพาะกรณีที่สถานะนักศึกษาไม่ใช่ no_project)
            $statusStudent = $request->status_student ?: 'no_project';
            $projectMode   = ($statusStudent !== 'no_project') ? ($request->project_mode ?: 'join_existing') : 'no_project';

            if ($statusStudent !== 'no_project' && $projectMode === 'join_existing' && !empty($request->existing_project_id)) {
                if (Schema::hasTable('project_members')) {
                    DB::table('project_members')->updateOrInsert(
                        ['project_id' => $request->existing_project_id, 'student_id' => $request->student_id],
                        ['joined_at' => now()]
                    );
                }
            } elseif ($statusStudent !== 'no_project' && $projectMode === 'create_new') {
                if (Schema::hasTable('projects')) {
                    $projectInsertData = [
                        'type'                 => $request->new_project_type ?: 'project',
                        'title_th'             => $request->new_project_title_th,
                        'title_en'             => $request->new_project_title_en ?: null,
                        'project_year'         => $request->new_project_year,
                        'advisor_president_id' => $request->new_advisor_president_id ?: null,
                        'company_name'         => ($request->new_project_type === 'coop') ? $request->new_company_name : null,
                        'status_project'       => 'wait',
                        'created_at'           => now(),
                        'updated_at'           => now(),
                    ];

                    if (Schema::hasColumn('projects', 'advisor_committee_1_id')) {
                        $projectInsertData['advisor_committee_1_id'] = $request->new_advisor_committee_1_id ?: null;
                    }
                    if (Schema::hasColumn('projects', 'advisor_committee_2_id')) {
                        $projectInsertData['advisor_committee_2_id'] = $request->new_advisor_committee_2_id ?: null;
                    }
                    $specAdvisorVal = $request->new_advisor_special ?: null;
                    $specAdvisorAdminId = is_numeric($specAdvisorVal) ? intval($specAdvisorVal) : null;

                    if (Schema::hasColumn('projects', 'advisor_special')) {
                        $projectInsertData['advisor_special'] = $specAdvisorAdminId;
                    }
                    if (Schema::hasColumn('projects', 'remark')) {
                        $projectInsertData['remark'] = $request->new_project_remark ?: null;
                    }

                    $newProjectId = DB::table('projects')->insertGetId($projectInsertData);

                    // บันทึกลงตาราง project_special_advisors
                    if (Schema::hasTable('project_special_advisors') && !empty($specAdvisorVal)) {
                        $saName = $specAdvisorVal;
                        if ($specAdvisorAdminId) {
                            $adminObj = DB::table('admins')->where('id', $specAdvisorAdminId)->first();
                            if ($adminObj) {
                                $saName = $adminObj->name;
                            }
                        }
                        DB::table('project_special_advisors')->insert([
                            'project_id' => $newProjectId,
                            'admin_id'   => $specAdvisorAdminId,
                            'name'       => $saName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    if (Schema::hasTable('project_members')) {
                        DB::table('project_members')->insert([
                            'project_id' => $newProjectId,
                            'student_id' => $request->student_id,
                            'joined_at'  => now(),
                        ]);
                    }
                }
            }

            return $newStudent;
        });

        return redirect('/pc-csmju/admin/student')->with('result', 'true')->with('message', 'เพิ่มข้อมูลนักศึกษา "' . $student->name . '" (' . $student->student_id . ') เรียบร้อยแล้ว');
    }

    /**
     * เปลี่ยนสถานะการใช้งานนักศึกษา (1 = ใช้งานปกติ, 0 = ปิดใช้งาน)
     */
    public function changeStatus($id, $status)
    {
        $student = Studentmodel::find($id);
        if (!$student) {
            return redirect('/pc-csmju/admin/student')->with('result', 'false')->with('message', 'ไม่พบข้อมูลนักศึกษาที่ต้องการเปลี่ยนสถานะ');
        }

        $newStatus = ($status == '1' || $status === 1) ? 1 : 0;
        $student->update(['status' => $newStatus]);

        Log::info('Student status changed for ID ' . $id . ' to ' . $newStatus);

        $statusText = ($newStatus === 1) ? 'เปิดใช้งานปกติ' : 'ปิดใช้งาน';
        return redirect('/pc-csmju/admin/student')->with('result', 'true')->with('message', 'เปลี่ยนสถานะของ "' . $student->name . '" (' . $student->student_id . ') เป็น "' . $statusText . '" เรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันลบข้อมูลนักศึกษา
     */
    public function destroy($id)
    {
        $student = Studentmodel::find($id);
        if (!$student) {
            return redirect('/pc-csmju/admin/student')->with('result', 'false')->with('message', 'ไม่พบข้อมูลนักศึกษาที่ต้องการลบ');
        }

        $deletedName = $student->name;
        $deletedStudentId = $student->student_id;

        // ลบไฟล์รูปภาพถ้ามี
        if ($student->profile_picture && file_exists(public_path($student->profile_picture))) {
            @unlink(public_path($student->profile_picture));
        }

        // ลบสมาชิกโครงงานที่ผูกกับนักศึกษาคนนี้ออกด้วย
        if (Schema::hasTable('project_members')) {
            DB::table('project_members')->where('student_id', $deletedStudentId)->delete();
        }

        $student->delete();

        Log::info('Student deleted successfully for ID: ' . $id);

        return redirect('/pc-csmju/admin/student')->with('result', 'deleteinfo')->with('message', 'ลบข้อมูลนักศึกษา "' . $deletedName . '" (' . $deletedStudentId . ') เรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันลบข้อมูลโครงงาน
     */
    public function destroyProject($id)
    {
        $project = DB::table('projects')->where('id', $id)->first();
        if (!$project) {
            return redirect('/pc-csmju/admin/student')->with('result', 'false')->with('message', 'ไม่พบข้อมูลโครงงานที่ต้องการลบ');
        }

        $title = $project->title_th ?: ('โครงงาน ID: ' . $id);

        DB::beginTransaction();
        try {
            // ลบที่ปรึกษาพิเศษ
            if (Schema::hasTable('project_special_advisors')) {
                DB::table('project_special_advisors')->where('project_id', $id)->delete();
            }

            // ลบสมาชิกโครงงาน
            if (Schema::hasTable('project_members')) {
                DB::table('project_members')->where('project_id', $id)->delete();
            }

            // ลบโครงงาน
            DB::table('projects')->where('id', $id)->delete();

            DB::commit();
            Log::info('Project deleted successfully for ID: ' . $id);

            return redirect('/pc-csmju/admin/student')->with('result', 'deleteinfo')->with('message', 'ลบข้อมูลโครงงาน "' . $title . '" เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting project: ' . $e->getMessage());
            return redirect('/pc-csmju/admin/student')->with('result', 'false')->with('message', 'เกิดข้อผิดพลาดในการลบโครงงาน: ' . $e->getMessage());
        }
    }

    /**
     * บันทึกการแก้ไขข้อมูลโครงงาน (Update Project)
     */
    public function updateProject(Request $request, $id = null)
    {
        $id = $id ?: $request->id;
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'ไม่พบรหัสโครงงานที่ต้องการแก้ไข'], 400);
        }

        $project = DB::table('projects')->where('id', $id)->first();
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลโครงงานในระบบ'], 404);
        }

        $type = $request->type ?: 'project';
        $titleTh = $request->title_th;
        $titleEn = $request->title_en ?: null;
        $companyName = ($type === 'coop') ? ($request->company_name ?: null) : null;
        $remark = $request->remark ?: null;

        $presId = $request->advisor_president_id ?: $this->findAdminIdByName($request->advisor_president, ['teacher']);
        $com1Id = $request->advisor_committee_1_id ?: $this->findAdminIdByName($request->advisor_committee_1, ['teacher']);
        $com2Id = $request->advisor_committee_2_id ?: $this->findAdminIdByName($request->advisor_committee_2, ['teacher']);

        $updateData = [
            'type'         => $type,
            'title_th'     => $titleTh,
            'title_en'     => $titleEn,
            'company_name' => $companyName,
            'remark'       => $remark,
            'updated_at'   => now(),
        ];

        if (Schema::hasColumn('projects', 'advisor_president_id')) {
            $updateData['advisor_president_id'] = $presId;
        }
        if (Schema::hasColumn('projects', 'advisor_committee_1_id')) {
            $updateData['advisor_committee_1_id'] = $com1Id;
        }
        if (Schema::hasColumn('projects', 'advisor_committee_2_id')) {
            $updateData['advisor_committee_2_id'] = $com2Id;
        }

        DB::beginTransaction();
        try {
            DB::table('projects')->where('id', $id)->update($updateData);

            // ซิงค์ที่ปรึกษาพิเศษ
            if (Schema::hasTable('project_special_advisors')) {
                DB::table('project_special_advisors')->where('project_id', $id)->delete();

                $specialAdvisors = $request->special_advisors;
                if (!empty($specialAdvisors) && is_array($specialAdvisors)) {
                    foreach ($specialAdvisors as $sa) {
                        $saName = trim($sa['name'] ?? '');
                        if (empty($saName) || $saName === '-' || $saName === 'ไม่มี') continue;

                        $saAdminId = !empty($sa['admin_id']) ? $sa['admin_id'] : $this->findAdminIdByName($saName, ['teacher', 'officer']);

                        DB::table('project_special_advisors')->insert([
                            'project_id' => $id,
                            'admin_id'   => $saAdminId ?: null,
                            'name'       => $saName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            Log::info('Project updated successfully for ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกการแก้ไขข้อมูลโครงงานเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating project: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * บันทึกการแก้ไขข้อมูลนักศึกษา (Update Student)
     */
    public function update(Request $request, $id = null)
    {
        $origId = $id ?: $request->original_student_id ?: $request->student_id;
        $newId  = trim($request->student_id ?? '');

        $student = Studentmodel::where('student_id', $origId)->first();
        if (!$student && is_numeric($origId)) {
            $student = Studentmodel::find($origId);
        }

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลนักศึกษาที่ต้องการแก้ไข'], 404);
        }

        // ตรวจสอบกรณีเปลี่ยน student_id ว่าซ้ำกับคนอื่นหรือไม่
        if ($newId && $newId !== $student->student_id) {
            $dup = Studentmodel::where('student_id', $newId)->where('id', '!=', $student->id)->exists();
            if ($dup) {
                return response()->json(['success' => false, 'message' => 'รหัสนักศึกษา ' . $newId . ' มีอยู่ในระบบแล้ว'], 422);
            }
        }

        DB::beginTransaction();
        try {
            $studentData = [
                'name'           => $request->name,
                'nickname'       => $request->nickname ?: null,
                'email'          => $request->email ?: null,
                'phone'          => $request->phone ?: null,
                'status_student' => $request->status_student ?: 'doing',
                'remark'         => $request->remark ?: null,
            ];

            if ($newId && $newId !== $student->student_id) {
                $studentData['student_id'] = $newId;
                if ($student->username === $student->student_id) {
                    $studentData['username'] = $newId;
                }
                // อัปเดตรหัสในตาราง project_members ด้วย
                if (Schema::hasTable('project_members')) {
                    DB::table('project_members')->where('student_id', $student->student_id)->update(['student_id' => $newId]);
                }
            }

            $student->update($studentData);

            DB::commit();
            Log::info('Student updated successfully for ID: ' . $student->id);

            return response()->json([
                'success' => true,
                'message' => 'บันทึกการแก้ไขข้อมูลนักศึกษา "' . $student->name . '" เรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // ระบบนำเข้าข้อมูลนักศึกษาและกลุ่มโครงงานจากไฟล์ CSV (CSV Import System)
    // =========================================================================

    /**
     * 1. พรีวิวและแกะข้อมูลจากไฟล์ CSV (Preview & Parse CSV)
     */
    public function previewCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file'     => 'required|file|max:20480',
            'project_year' => 'nullable|integer',
        ], [
            'csv_file.required' => 'กรุณาเลือกไฟล์ CSV ที่ต้องการนำเข้า',
            'csv_file.max'      => 'ขนาดไฟล์ต้องไม่เกิน 20MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $file = $request->file('csv_file');
        $inputYear = $request->input('project_year');
        $path = $file->getRealPath();

        try {
            $parsedData = $this->extractDataFromCsv($path, $inputYear);

            return response()->json([
                'status'                   => 'success',
                'project_year'             => $parsedData['detected_year'],
                'summary'                  => $parsedData['summary'],
                'students'                 => $parsedData['students'],
                'projects'                 => $parsedData['projects'],
                'students_without_project' => $parsedData['students_without_project'],
            ]);
        } catch (\Exception $e) {
            Log::error('CSV Preview Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'ไม่สามารถอ่านไฟล์ได้: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 2. บันทึกข้อมูลนักศึกษาและโครงงานลงฐานข้อมูลจริง (Store Import Data)
     */
    public function storeImportData(Request $request)
    {
        $students    = $request->input('students', []);
        $projects    = $request->input('projects', []);
        $projectYear = $request->input('project_year', date('Y') + 543);

        if (empty($students) && empty($projects)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'ไม่มีข้อมูลที่ต้องนำเข้า',
            ], 400);
        }

        $result = [
            'students_created' => 0,
            'students_updated' => 0,
            'projects_created' => 0,
            'members_linked'   => 0,
            'errors'           => [],
        ];

        DB::beginTransaction();
        try {
            // ก) สร้างหรืออัปเดตบัญชีนักศึกษาลงตาราง student
            if (Schema::hasTable('student')) {
                foreach ($students as $s) {
                    $studentId = trim($s['student_id'] ?? '');
                    $name      = trim($s['name'] ?? '');
                    $statusStudent = $s['status_student'] ?? 'no_project';

                    if (empty($studentId)) continue;

                    $exists = Studentmodel::where('student_id', $studentId)
                        ->orWhere('username', $studentId)
                        ->first();

                    if ($exists) {
                        // มีบัญชีอยู่แล้ว: อัปเดตข้อมูลส่วนตัวล่าสุด และปรับสถานะนักศึกษาตามโครงงานปีใหม่
                        $updateData = [];
                        if (!empty($name)) $updateData['name'] = $name;
                        if (!empty($s['email'])) $updateData['email'] = $s['email'];
                        if (!empty($s['phone'])) $updateData['phone'] = $s['phone'];
                        if (!empty($s['nickname']) && Schema::hasColumn('student', 'nickname')) {
                            $updateData['nickname'] = $s['nickname'];
                        }
                        if (!empty($s['remark']) && Schema::hasColumn('student', 'remark')) {
                            $updateData['remark'] = $s['remark'];
                        }
                        if (Schema::hasColumn('student', 'status_student')) {
                            $updateData['status_student'] = $statusStudent;
                        }
                        // รหัสผ่านอิงตามรหัสนักศึกษา
                        $updateData['password']  = Hash::make($studentId);
                        $updateData['real_pass'] = $studentId;

                        if (!empty($updateData)) {
                            $exists->update($updateData);
                        }
                        $result['students_updated']++;
                        continue;
                    }

                    $insertStudent = [
                        'student_id' => $studentId,
                        'username'   => $studentId,
                        'password'   => Hash::make($studentId),
                        'real_pass'  => $studentId,
                        'status'     => 1,
                        'name'       => $name,
                        'email'      => $s['email'] ?? null,
                        'phone'      => $s['phone'] ?? null,
                    ];

                    if (Schema::hasColumn('student', 'status_student')) {
                        $insertStudent['status_student'] = $statusStudent;
                    }

                    if (Schema::hasColumn('student', 'remark')) {
                        $insertStudent['remark'] = $s['remark'] ?? null;
                    }

                    if (Schema::hasColumn('student', 'nickname')) {
                        $insertStudent['nickname'] = $s['nickname'] ?? null;
                    }

                    Studentmodel::create($insertStudent);
                    $result['students_created']++;
                }
            }

            // ข) สร้างข้อมูลกลุ่มโครงงานลงตาราง projects และผูก project_members
            if (Schema::hasTable('projects')) {
                foreach ($projects as $p) {
                    $studentIds = $p['student_ids'] ?? [];

                    $projectInsertData = [
                        'type'         => $p['type'] ?? 'project',
                        'title_th'     => $p['title_th'] ?? null,
                        'title_en'     => $p['title_en'] ?? null,
                        'company_name' => $p['company_name'] ?? null,
                        'remark'       => $p['remark'] ?? null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    // รองรับ status_project หรือ status
                    if (Schema::hasColumn('projects', 'status_project')) {
                        $projectInsertData['status_project'] = 'wait';
                    } elseif (Schema::hasColumn('projects', 'status')) {
                        $projectInsertData['status'] = 'wait';
                    }

                    // รองรับ project_year หรือ academic_year
                    if (Schema::hasColumn('projects', 'project_year')) {
                        $projectInsertData['project_year'] = $projectYear;
                    } elseif (Schema::hasColumn('projects', 'academic_year')) {
                        $projectInsertData['academic_year'] = $projectYear;
                    }

                    // อาจารย์ที่ปรึกษา (อิงตาม admins.id เฉพาะ role teacher)
                    if (Schema::hasColumn('projects', 'advisor_president_id')) {
                        $projectInsertData['advisor_president_id'] = $p['advisor_president_id'] ?? $this->findAdminIdByName($p['advisor_president'] ?? null, ['teacher']);
                    }
                    if (Schema::hasColumn('projects', 'advisor_committee_1_id')) {
                        $projectInsertData['advisor_committee_1_id'] = $p['advisor_committee_1_id'] ?? $this->findAdminIdByName($p['advisor_committee_1'] ?? null, ['teacher']);
                    }
                    if (Schema::hasColumn('projects', 'advisor_committee_2_id')) {
                        $projectInsertData['advisor_committee_2_id'] = $p['advisor_committee_2_id'] ?? $this->findAdminIdByName($p['advisor_committee_2'] ?? null, ['teacher']);
                    }
                    if (Schema::hasColumn('projects', 'advisor_special')) {
                        $projectInsertData['advisor_special'] = $p['advisor_special_id'] ?? $this->findAdminIdByName($p['advisor_special'] ?? null, ['teacher', 'officer']);
                    }

                    $projectId = DB::table('projects')->insertGetId($projectInsertData);
                    $result['projects_created']++;

                    // บันทึกรายชื่อที่ปรึกษาพิเศษลงตาราง project_special_advisors (รองรับทั้งอาจารย์, เจ้าหน้าที่, และบุคคลภายนอก)
                    if (Schema::hasTable('project_special_advisors')) {
                        $specialAdvisorsList = $p['special_advisors'] ?? [];
                        if (empty($specialAdvisorsList) && !empty($p['advisor_special'])) {
                            $specialAdvisorsList = $this->parseSpecialAdvisors($p['advisor_special']);
                        }

                        foreach ($specialAdvisorsList as $sa) {
                            $saName = trim($sa['name'] ?? '');
                            if (empty($saName) || $saName === '-' || $saName === 'ไม่มี') continue;

                            DB::table('project_special_advisors')->insert([
                                'project_id' => $projectId,
                                'admin_id'   => !empty($sa['admin_id']) ? $sa['admin_id'] : null,
                                'name'       => $saName,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    // ผูกสมาชิกกลุ่มลง project_members
                    if (Schema::hasTable('project_members')) {
                        foreach ($studentIds as $sid) {
                            $sid = trim($sid);
                            if (empty($sid)) continue;

                            DB::table('project_members')->updateOrInsert(
                                ['project_id' => $projectId, 'student_id' => $sid],
                                ['joined_at'  => now()]
                            );
                            $result['members_linked']++;
                        }
                    }
                }
            }

            $msgParts = [];
            if ($result['students_created'] > 0) {
                $msgParts[] = "สร้างบัญชีนักศึกษาใหม่ {$result['students_created']} คน";
            }
            if ($result['students_updated'] > 0) {
                $msgParts[] = "อัปเดตข้อมูลนักศึกษาเดิม {$result['students_updated']} คน";
            }
            $msgParts[] = "สร้างกลุ่มโครงงาน {$result['projects_created']} กลุ่ม";
            $finalMessage = "นำเข้าข้อมูลสำเร็จ! " . implode(', ', $msgParts);

            DB::commit();
            Log::info("CSV Import committed: {$finalMessage}");

            session()->flash('result', 'addinfo');
            session()->flash('message', $finalMessage);

            return response()->json([
                'status'  => 'success',
                'message' => $finalMessage,
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV Store Import Error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * คอร์ฟังก์ชันอ่านและแกะข้อมูลจากไฟล์ CSV (Private Helper)
     */
    private function extractDataFromCsv($filePath, $projectYear)
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \Exception('ไม่สามารถเปิดไฟล์ CSV ได้');
        }

        // 1. ตรวจสอบและแปลงรหัสภาษาไทย (TIS-620 / Windows-874 -> UTF-8)
        if (!mb_check_encoding($content, 'UTF-8')) {
            $converted = @iconv('TIS-620', 'UTF-8//IGNORE', $content);
            if (!$converted || strlen($converted) < 10) {
                $converted = @iconv('WINDOWS-874', 'UTF-8//IGNORE', $content);
            }
            if ($converted && strlen($converted) >= 10) {
                $content = $converted;
            }
        }

        // ตัด BOM ถ้ามี
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // ตรวจจับปีการศึกษาอัตโนมัติจากเนื้อหาไฟล์ CSV
        $detectedYear = $this->detectAcademicYearFromContent($content, $projectYear);

        // 2. แยกแถวโดยคำนึงถึง multi-line ภายในเครื่องหมายคำพูด (RFC 4180)
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $allRows = [];
        while (($row = fgetcsv($stream, 0, ',')) !== false) {
            // ทำความสะอาดช่องว่างรอบด้าน
            $cleanRow = array_map(function ($val) {
                return trim(preg_replace('/\s+/u', ' ', $val ?? ''));
            }, $row);
            $allRows[] = $cleanRow;
        }
        fclose($stream);

        $columnMap = null;
        $currentType = 'project';
        $projects = [];
        $currentProject = null;
        $students = [];
        $studentsWithoutProject = [];

        foreach ($allRows as $row) {
            // หยุดการอ่านถ้าเจอตารางสรุปภาระงานอาจารย์ท้ายไฟล์
            $firstFewCells = implode(' ', array_slice($row, 0, 5));
            if (mb_strpos($firstFewCells, 'ลำดับอาจารย์') !== false || 
                mb_strpos($firstFewCells, 'ภาระงาน') !== false) {
                break;
            }

            // ตรวจจับประเภทวิชา (498 โครงงาน หรือ 497 สหกิจ)
            $sectionType = $this->detectSectionType($row);
            if ($sectionType !== false) {
                if ($currentProject !== null) {
                    $projects[] = $currentProject;
                    $currentProject = null;
                }
                $currentType = $sectionType;
                continue;
            }

            // ตรวจจับหัวตารางอัตโนมัติ
            if ($this->isHeaderRow($row)) {
                $columnMap = $this->autoDetectColumns($row);
                if ($currentProject !== null) {
                    $projects[] = $currentProject;
                    $currentProject = null;
                }
                continue;
            }

            if ($columnMap === null) {
                continue;
            }

            $projectNo      = $this->getCellValue($row, $columnMap, 'project_no');
            $studentId      = $this->getCellValue($row, $columnMap, 'student_id');
            $name           = $this->getCellValue($row, $columnMap, 'name');
            $nickname       = $this->getCellValue($row, $columnMap, 'nickname');
            $titleTh        = $this->getCellValue($row, $columnMap, 'title_th');
            $titleEn        = $this->getCellValue($row, $columnMap, 'title_en');
            $advisor1       = $this->getCellValue($row, $columnMap, 'advisor1');
            $advisor2       = $this->getCellValue($row, $columnMap, 'advisor2');
            $advisor3       = $this->getCellValue($row, $columnMap, 'advisor3');
            $advisorSpecial = $this->getCellValue($row, $columnMap, 'advisor_special');
            $company        = $this->getCellValue($row, $columnMap, 'company_name');
            $remark         = $this->getCellValue($row, $columnMap, 'remark');

            // ตรวจสอบว่าเป็นรหัสนักศึกษา 10 หลัก
            if (!$this->isStudentId($studentId)) {
                continue;
            }

            // ตรวจสอบว่ามีข้อมูลโครงงานระบุไว้หรือไม่
            $hasProjectInfo = (!empty($titleTh) || !empty($titleEn) || !empty($advisor1) || !empty($advisor2) || !empty($company));

            // ตรวจสอบสถานะการมีอยู่ในฐานข้อมูล
            $existsInDb = Schema::hasTable('student') 
                ? DB::table('student')->where('student_id', $studentId)->exists() 
                : false;

            if ($projectNo !== '' && is_numeric($projectNo)) {
                // กลุ่มโครงงานใหม่
                if ($currentProject !== null) {
                    $projects[] = $currentProject;
                }

                $presId          = $this->findAdminIdByName($advisor1, ['teacher']);
                $com1Id          = $this->findAdminIdByName($advisor2, ['teacher']);
                $com2Id          = $this->findAdminIdByName($advisor3, ['teacher']);
                $specialAdvisors = $this->parseSpecialAdvisors($advisorSpecial);
                $specId          = !empty($specialAdvisors[0]['admin_id']) ? $specialAdvisors[0]['admin_id'] : null;

                $currentProject = [
                    'project_no'             => $projectNo,
                    'type'                   => $currentType,
                    'project_year'           => $detectedYear,
                    'title_th'               => $titleTh,
                    'title_en'               => $titleEn,
                    'advisor_president'      => $advisor1,
                    'advisor_president_id'   => $presId,
                    'advisor_committee_1'    => $advisor2,
                    'advisor_committee_1_id' => $com1Id,
                    'advisor_committee_2'    => $advisor3,
                    'advisor_committee_2_id' => $com2Id,
                    'advisor_special'        => $advisorSpecial,
                    'advisor_special_id'     => $specId,
                    'special_advisors'       => $specialAdvisors,
                    'company_name'           => $company,
                    'remark'                 => $remark,
                    'student_ids'            => [$studentId],
                    'members'                => [[
                        'student_id' => $studentId,
                        'name'       => $name,
                        'nickname'   => $nickname,
                    ]],
                ];

                $students[$studentId] = [
                    'student_id'     => $studentId,
                    'name'           => $name,
                    'nickname'       => $nickname,
                    'type'           => $currentType,
                    'project_no'     => $projectNo,
                    'status_student' => 'doing',
                    'remark'         => $remark,
                    'exists_in_db'   => $existsInDb,
                ];
            } elseif ($currentProject !== null && $hasProjectInfo) {
                // สมาชิกคนที่ 2, 3 ของกลุ่มเดิม
                $currentProject['student_ids'][] = $studentId;
                $currentProject['members'][] = [
                    'student_id' => $studentId,
                    'name'       => $name,
                    'nickname'   => $nickname,
                ];

                if (empty($currentProject['title_th']) && !empty($titleTh)) {
                    $currentProject['title_th'] = $titleTh;
                }
                if (empty($currentProject['title_en']) && !empty($titleEn)) {
                    $currentProject['title_en'] = $titleEn;
                }

                $students[$studentId] = [
                    'student_id'     => $studentId,
                    'name'           => $name,
                    'nickname'       => $nickname,
                    'type'           => $currentType,
                    'project_no'     => $currentProject['project_no'],
                    'status_student' => 'doing',
                    'remark'         => $remark,
                    'exists_in_db'   => $existsInDb,
                ];
            } else {
                // นักศึกษาที่ยังไม่มีกลุ่มโครงงาน (เช่น 4 คนท้ายวิชา 498)
                if ($currentProject !== null && !$hasProjectInfo && $projectNo === '') {
                    $projects[] = $currentProject;
                    $currentProject = null;
                }

                $noProjItem = [
                    'student_id'     => $studentId,
                    'name'           => $name,
                    'nickname'       => $nickname,
                    'type'           => $currentType,
                    'status_student' => 'no_project',
                    'remark'         => $remark,
                    'exists_in_db'   => $existsInDb,
                ];
                $studentsWithoutProject[] = $noProjItem;
                $students[$studentId] = $noProjItem;
            }
        }

        if ($currentProject !== null) {
            $projects[] = $currentProject;
        }

        $project498Count = 0;
        $coop497Count = 0;
        foreach ($projects as $p) {
            if (($p['type'] ?? '') === 'coop') {
                $coop497Count++;
            } else {
                $project498Count++;
            }
        }

        return [
            'summary' => [
                'total_students'                 => count($students),
                'total_projects'                 => count($projects),
                'students_with_project_count'    => count($students) - count($studentsWithoutProject),
                'students_without_project_count' => count($studentsWithoutProject),
                'project_498_count'              => $project498Count,
                'coop_497_count'                 => $coop497Count,
            ],
            'detected_year'            => $detectedYear,
            'projects'                 => $projects,
            'students'                 => array_values($students),
            'students_without_project' => $studentsWithoutProject,
        ];
    }

    /**
     * ตรวจจับปีการศึกษาอัตโนมัติจากเนื้อหาไฟล์ CSV อย่างยืดหยุ่นและครอบคลุม
     * รองรับทั้ง พ.ศ. (เช่น 2567, 2600, 3000+), ค.ศ. (เช่น 2024, 2026 -> แปลงเป็น พ.ศ.)
     */
    private function detectAcademicYearFromContent($content, $fallbackYear = null)
    {
        // 1. ถ้ามีปี fallback ส่งเข้ามา (เช่น เลือกจากหน้าฟอร์ม)
        if (!empty($fallbackYear) && is_numeric($fallbackYear)) {
            $year = intval($fallbackYear);
            // ถ้าเป็น ค.ศ. (2000-2400) ให้แปลงเป็น พ.ศ.
            return ($year >= 2000 && $year < 2400) ? $year + 543 : $year;
        }

        // 2. ค้นหาคำระบุปี: "ปีการศึกษา", "พ.ศ.", "ปี", "Academic Year", "Year" ตามด้วยตัวเลข 4 หลัก (2000 - 3999)
        if (preg_match('/(?:ปีการศึกษา|พ\.?ศ\.?|ปี|academic\s*year|year)\s*[:=\-]?\s*([2-3]\d{3})/iu', $content, $matches)) {
            $foundYear = intval($matches[1]);
            // หากเป็น ค.ศ. สากล (เช่น 2024, 2026) แปลงเป็น พ.ศ. (+543)
            return ($foundYear >= 2000 && $foundYear < 2400) ? $foundYear + 543 : $foundYear;
        }

        // 3. ค้นหา ค.ศ. เจาะจง: เช่น "ค.ศ. 2026" หรือ "A.D. 2026"
        if (preg_match('/(?:ค\.?ศ\.?|ad|ce)\s*[:=\-]?\s*(20\d{2}|2[1-9]\d{2})/iu', $content, $matches)) {
            return intval($matches[1]) + 543;
        }

        // 4. สแกนหาตัวเลข พ.ศ. 4 หลัก (2500 - 3999) ลอยๆ ในช่วง 1,500 ตัวอักษรแรกของไฟล์
        if (preg_match('/\b(2[5-9]\d{2}|[3-9]\d{3})\b/', substr($content, 0, 1500), $matches)) {
            return intval($matches[1]);
        }

        // 5. หากตรวจไม่พบอะไรเลย ให้สำรองใช้ปี พ.ศ. ปัจจุบันของระบบ
        return intval(date('Y') + 543);
    }

    /**
     * ดึงค่าจาก Cell ตามคอลัมน์ใน Map
     */
    private function getCellValue($row, $map, $key)
    {
        return isset($map[$key]) && $map[$key] !== null ? trim($row[$map[$key]] ?? '') : '';
    }

    /**
     * ค้นหา ID อาจารย์/เจ้าหน้าที่จากตาราง admins โดยเทียบชื่อ (รองรับการกรอง role)
     */
    private function findAdminIdByName($name, $allowedRoles = null)
    {
        if (empty($name) || !Schema::hasTable('admins')) {
            return null;
        }

        $cleanName = trim(preg_replace('/\s+/u', ' ', $name));
        if (empty($cleanName)) return null;

        $query = DB::table('admins')->where('status', '1');
        if (!empty($allowedRoles)) {
            $query->whereIn('role', (array)$allowedRoles);
        }

        // 1. ค้นหาแบบชื่อตรงกันเป๊ะ 100%
        $admin = (clone $query)->where('name', $cleanName)->first();
        if ($admin) return $admin->id;

        // 2. ค้นหาแบบ LIKE หากมีช่องว่างเล็กน้อย
        $admin = (clone $query)->where('name', 'like', '%' . $cleanName . '%')->first();
        if ($admin) return $admin->id;

        // 3. ค้นหาแบบตัดคำนำหน้าออก เทียบสองทิศทาง (ป้องกันกรณีใน DB มีแค่ชื่อหรือยศต่างกัน)
        $stripPrefix = function ($str) {
            return trim(preg_replace('/^(ผศ\.ดร\.|รศ\.ดร\.|ศ\.ดร\.|ผศ\.|รศ\.|ศ\.|ดร\.|อ\.|อาจารย์|นาย|นาง|นางสาว|น\.ส\.)\s*/u', '', $str));
        };
        $cleanNoPrefix = $stripPrefix($cleanName);
        if (mb_strlen($cleanNoPrefix) >= 3) {
            $admins = (clone $query)->get();
            foreach ($admins as $adm) {
                $admNoPrefix = $stripPrefix($adm->name);
                if ($admNoPrefix === $cleanNoPrefix 
                    || mb_strpos($adm->name, $cleanNoPrefix) !== false 
                    || mb_strpos($cleanNoPrefix, $admNoPrefix) !== false) {
                    return $adm->id;
                }
            }
        }

        return null;
    }

    /**
     * แยกและจัดกลุ่มรายชื่อที่ปรึกษาพิเศษ (รองรับหลายคน, ตัดคำ 2 ชั้น, และจัดกลุ่ม อาจารย์/เจ้าหน้าที่/บุคคลภายนอก)
     */
    private function parseSpecialAdvisors($rawText)
    {
        if (empty($rawText)) {
            return [];
        }

        // ชั้นที่ 1: แยกด้วยเครื่องหมายขึ้นบรรทัดใหม่, comma, semicolon, slash
        $firstPass = preg_split('/[\r\n,;\/]+/u', $rawText, -1, PREG_SPLIT_NO_EMPTY);
        $names = [];

        // ชั้นที่ 2: แยกด้วยคำนำหน้าชื่อไทย กรณีมีหลายคนติดกันในบรรทัดเดียว
        $prefixPattern = '/(?<=\S)\s+(?=(นาย|นาง|นางสาว|น\.ส\.|อ\.|อาจารย์|ดร\.|ผศ\.|รศ\.|ศ\.))/u';
        foreach ($firstPass as $chunk) {
            $chunk = trim($chunk);
            if (empty($chunk) || $chunk === '-' || $chunk === 'ไม่มี') continue;

            $subChunks = preg_split($prefixPattern, $chunk, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($subChunks as $sub) {
                $sub = trim(preg_replace('/\s+/u', ' ', $sub));
                if (!empty($sub) && $sub !== '-' && $sub !== 'ไม่มี') {
                    $names[] = $sub;
                }
            }
        }

        $names = array_values(array_unique($names));
        $result = [];

        // ดึงรายชื่ออาจารย์และเจ้าหน้าที่มาเทียบ (เฉพาะ role teacher และ officer)
        $allowedAdmins = Schema::hasTable('admins')
            ? DB::table('admins')
                ->where('status', '1')
                ->whereIn('role', ['teacher', 'officer'])
                ->get()
            : collect([]);

        foreach ($names as $nameItem) {
            $matchedAdmin = null;

            // เทียบชื่อ 100%
            $matchedAdmin = $allowedAdmins->first(function ($adm) use ($nameItem) {
                return trim($adm->name) === $nameItem;
            });

            // เทียบแบบตัดคำนำหน้าออก
            if (!$matchedAdmin) {
                $stripPrefix = function ($str) {
                    return trim(preg_replace('/^(ผศ\.ดร\.|รศ\.ดร\.|ศ\.ดร\.|ผศ\.|รศ\.|ศ\.|ดร\.|อ\.|อาจารย์|นาย|นาง|นางสาว|น\.ส\.)\s*/u', '', $str));
                };
                $cleanNoPrefix = $stripPrefix($nameItem);

                if (mb_strlen($cleanNoPrefix) >= 3) {
                    $matchedAdmin = $allowedAdmins->first(function ($adm) use ($cleanNoPrefix, $stripPrefix) {
                        $adminNoPrefix = $stripPrefix($adm->name);
                        return $adminNoPrefix === $cleanNoPrefix 
                            || mb_strpos($adm->name, $cleanNoPrefix) !== false 
                            || mb_strpos($cleanNoPrefix, $adminNoPrefix) !== false;
                    });
                }
            }

            if ($matchedAdmin) {
                $result[] = [
                    'admin_id'    => $matchedAdmin->id,
                    'name'        => $nameItem, // คงชื่อเดิมจากไฟล์ CSV ไว้เหมือน advisor_president, advisor_committee_1, advisor_committee_2
                    'role'        => $matchedAdmin->role, // 'teacher' หรือ 'officer'
                    'is_external' => false,
                ];
            } else {
                $result[] = [
                    'admin_id'    => null,
                    'name'        => $nameItem,
                    'role'        => 'external', // บุคคลภายนอก
                    'is_external' => true,
                ];
            }
        }

        return $result;
    }

    /**
     * ตรวจจับตำแหน่งคอลัมน์อัตโนมัติจากคำสำคัญในหัวตาราง
     */
    private function autoDetectColumns($headerRow)
    {
        $map = [
            'project_no'      => null,
            'student_id'      => null,
            'name'            => null,
            'nickname'        => null,
            'title_th'        => null,
            'title_en'        => null,
            'advisor1'        => null,
            'advisor2'        => null,
            'advisor3'        => null,
            'advisor_special' => null,
            'company_name'    => null,
            'remark'          => null,
        ];

        $keywords = [
            'project_no'      => ['ลำดับโครงงาน', 'ลำดับโครง', 'ลำดับ'],
            'student_id'      => ['รหัสนักศึกษา', 'รหัส'],
            'name'            => ['ชื่อ-สกุล', 'ชื่อ-นามสกุล', 'ชื่อสกุล'],
            'nickname'        => ['ชื่อเล่น', 'ชื่อเล่น(ถ้ามี)', 'nickname'],
            'title_th'        => ['ภาษาไทย', '(ไทย)', 'โครงงาน(ไทย)'],
            'title_en'        => ['ภาษาอังกฤษ', '(อังกฤษ)', 'english'],
            'advisor_special' => ['ที่ปรึกษาพิเศษ', 'พิเศษ'],
            'advisor1'        => ['ประธาน'],
            'advisor2'        => ['กรรมการ'],
            'company_name'    => ['สถานที่ฝึก', 'สหกิจศึกษา', 'บริษัท'],
            'remark'          => ['หมายเหตุ', 'remark'],
        ];

        foreach ($headerRow as $index => $cellValue) {
            $cellClean = mb_strtolower(str_replace(["\r", "\n", " "], '', trim($cellValue)));
            if ($cellClean === '') continue;

            foreach ($keywords as $field => $kwList) {
                foreach ($kwList as $kw) {
                    $kwClean = mb_strtolower(str_replace(["\r", "\n", " "], '', $kw));
                    if (mb_strpos($cellClean, $kwClean) !== false) {
                        if ($field === 'advisor2' && $map['advisor2'] !== null && $map['advisor2'] !== $index) {
                            $map['advisor3'] = $index;
                        } else {
                            if ($map[$field] === null) {
                                $map[$field] = $index;
                            }
                        }
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * ตรวจสอบว่าเป็นรหัสนักศึกษาจริงหรือไม่ (ตัวเลข 10 หลัก)
     */
    private function isStudentId($value)
    {
        $value = trim($value);
        return is_numeric($value) && strlen($value) === 10;
    }

    /**
     * ตรวจว่าเป็นแถวหัวตาราง (Header Row) หรือไม่
     */
    private function isHeaderRow($row)
    {
        $line = implode(' ', $row);
        return (mb_strpos($line, 'รหัสนักศึกษา') !== false || mb_strpos($line, 'รหัส') !== false);
    }

    /**
     * ตรวจประเภทส่วน (วิชา 497 สหกิจ หรือ วิชา 498 โครงงาน)
     */
    private function detectSectionType($row)
    {
        $line = mb_strtolower(implode(' ', $row));
        if (mb_strpos($line, 'วิชา') === false) return false;

        if (mb_strpos($line, 'สหกิจ') !== false || mb_strpos($line, '497') !== false) {
            return 'coop';
        }
        if (mb_strpos($line, '498') !== false || mb_strpos($line, 'เตรียมการสัมมนา') !== false || mb_strpos($line, 'การเรียนรู้อิสระ') !== false) {
            return 'project';
        }
        return false;
    }
}
