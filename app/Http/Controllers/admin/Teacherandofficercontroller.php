<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Teacherandofficermodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Teacherandofficercontroller extends Controller
{
    public function __construct()
    {
    //   parent::__construct();
    //     if (!$this->session->userdata('logged_in')) {
    //         redirect('auth/login');
    //     }
    //     // ตรวจสอบสิทธิ์ Admin เท่านั้น
    //     if ($this->session->userdata('role') !== 'admin') {
    //         $this->session->set_flashdata('result', 'false');
    //         $this->session->set_flashdata('message', 'เฉพาะแอดมินเท่านั้นที่สามารถเข้าถึงหน้านี้ได้');
    //         redirect('player');
    //     }
    }

    /**
     * หน้ารายการอาจารย์และเจ้าหน้าที่ (ตาราง)
     */
    public function index()
    {
        // ดึงเฉพาะฟิลด์ที่ต้องใช้แสดงในตาราง ไม่ต้องดึง password ออกมา (ประหยัด RAM และปลอดภัย)
        $admins = Teacherandofficermodel::select('id', 'name', 'username', 'email', 'phone', 'role', 'status', 'profile_picture')
            ->orderBy('id', 'desc')
            ->get();    

        $data = [
            'content'     => 'admin/page_teacherandofficer',
            'active_menu' => 'page_teacherandofficer',
            'admins'      => $admins,
        ];
        Log::info('Teacherandofficercontroller index method called');

        return view('admin/index', $data);
        // echo '<pre>';
		// print_r($this->_data);
		// echo '</pre>';
		// exit;
    }

    /**
     * หน้าฟอร์มเพิ่มข้อมูลอาจารย์และเจ้าหน้าที่
     */
    public function create()
    {
        $data = [
            'content'     => 'admin/page_teacherandofficer_add',
            'active_menu' => 'page_teacherandofficer'
        ];
        Log::info('Teacherandofficercontroller create method called');

        return view('admin/index', $data);
    }

    /**
     * ฟังก์ชันบันทึกข้อมูล (POST)
     */
    public function store(Request $request)
    {
        // กำหนดข้อความแจ้งเตือนภาษาไทยสำหรับแต่ละเงื่อนไข
        $messages = [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน (Username)',
            'username.max'      => 'ชื่อผู้ใช้งาน (Username) ต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min'      => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'name.required'     => 'กรุณากรอกชื่อ-นามสกุล',
            'name.max'          => 'ชื่อ-นามสกุล ต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'name.unique'       => 'ชื่อ-นามสกุล "' . $request->name . '" มีอยู่ในระบบแล้ว กรุณาตรวจสอบอีกครั้ง',
            'role.required'     => 'กรุณาเลือกสิทธิ์การใช้งาน',
            'role.in'           => 'สิทธิ์การใช้งานไม่ถูกต้อง',
            'email.email'       => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.max'         => 'อีเมลต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'email.unique'      => 'อีเมล "' . $request->email . '" มีอยู่ในระบบแล้ว กรุณาใช้อีเมลอื่น',
            'phone.max'         => 'เบอร์โทรศัพท์ต้องมีความยาวไม่เกิน 20 ตัวอักษร',
            'phone.unique'      => 'เบอร์โทรศัพท์ "' . $request->phone . '" มีอยู่ในระบบแล้ว กรุณาใช้เบอร์อื่น',
            'profile_picture.image' => 'ไฟล์รูปโปรไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'profile_picture.max'   => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
        ];

        // ตรวจสอบความถูกต้องของข้อมูล (Username ซ้ำได้ แต่ ชื่อ-นามสกุล, อีเมล, เบอร์โทร ห้ามซ้ำ)
        $validator = Validator::make($request->all(), [
            'username'        => 'required|string|max:50', // สามารถซ้ำได้
            'password'        => 'required|string|min:6',
            'name'            => 'required|string|max:50|unique:admins,name', // ห้ามซ้ำ
            'role'            => 'required|in:admin,teacher,officer',
            'email'           => 'nullable|email|max:50|unique:admins,email', // ห้ามซ้ำ
            'phone'           => 'nullable|string|max:20|unique:admins,phone', // ห้ามซ้ำ
            'profile_picture' => 'nullable|image|max:10240',
        ], $messages);

        // หากข้อมูลไม่ผ่านเงื่อนไข ให้ตีกลับพร้อมแจ้งเตือนผ่าน notify.blade.php
        if ($validator->fails()) {
            $errors = $validator->errors();
            $resultKey = 'warning';

            // ถ้ามีข้อมูลซ้ำ (ชื่อ-นามสกุล, อีเมล, เบอร์โทรศัพท์)
            if (($errors->has('name') && str_contains($errors->first('name'), 'มีอยู่ในระบบแล้ว')) ||
                ($errors->has('email') && str_contains($errors->first('email'), 'มีอยู่ในระบบแล้ว')) ||
                ($errors->has('phone') && str_contains($errors->first('phone'), 'มีอยู่ในระบบแล้ว'))) {
                $resultKey = 'duplicate';
            }

            return redirect()->back()
                ->withInput()
                ->with('result', $resultKey)
                ->with('message', $errors->first());
        }


        // 1. บันทึกข้อมูลเบื้องต้นลงฐานข้อมูลก่อนเพื่อเอา ID มาตั้งชื่อไฟล์รูปภาพ
        $admin = Teacherandofficermodel::create([
            'username'        => $request->username,
            'password'        => Hash::make($request->password),
            'real_pass'       => $request->password,
            'role'            => $request->role,
            'status'          => '1', // 1 = เปิดใช้งานปกติ (ค่าเริ่มต้น)
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'profile_picture' => null,
        ]);

        // 2. จัดการรูปภาพ (บันทึกลง public/profile_image/teacherandofficer และตั้งชื่อตาม id_role_วันเวลา)
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $uploadDir = public_path('profile_image/teacherandofficer');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // แปลง Role เป็นตัวเลข: อาจารย์ (teacher) = 1, เจ้าหน้าที่ (officer) = 2, ผู้ดูแลระบบ (admin) = 3
            $roleMap = [
                'teacher' => '1',
                'officer' => '2',
                'admin'   => '3',
            ];
            $roleCode = $roleMap[$request->role] ?? '0';

            // ตั้งชื่อรูปตาม: id_role_วันเวลา (เช่น 5_1_20260913_212030.jpg)
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = $admin->id . '_' . $roleCode . '_' . date('Ymd_His') . '.' . $ext;
            $file->move($uploadDir, $fileName);

            // อัปเดต Path ของรูปลงใน record
            $admin->update([
                'profile_picture' => 'profile_image/teacherandofficer/' . $fileName,
            ]);
        }

        Log::info('Teacher/Officer created successfully: ' . $request->username);

        // บันทึกเสร็จแล้ว redirect กลับไปยังหน้ารายการ (แจ้งเตือนสถานะเพิ่มข้อมูลสำเร็จ: addinfo พร้อมใส่ชื่อ)
        return redirect('/')->with('result', 'addinfo')->with('message', 'เพิ่มข้อมูล "' . $admin->name . '" เรียบร้อยแล้ว');
    }

    /** 
     * หน้าฟอร์มแก้ไขข้อมูลอาจารย์และเจ้าหน้าที่
     */
    public function edit($id)
    {
        // ค้นหาข้อมูลตาม ID ผ่าน Model
        $admin = Teacherandofficermodel::find($id);
        if (!$admin) {
            return redirect('/')->with('result', 'false')->with('message', 'ไม่พบข้อมูลที่ต้องการแก้ไข');
        }

        $data = [
            'content'     => 'admin/page_teacherandofficer_edit',
            'active_menu' => 'page_teacherandofficer',
            'admin'       => $admin,
        ];
        Log::info('Teacherandofficercontroller edit method called for ID: ' . $id);

        return view('admin/index', $data);
    }

    /**
     * ฟังก์ชันบันทึกการแก้ไขข้อมูล (POST)
     */
    public function update(Request $request, $id)
    {
        $admin = Teacherandofficermodel::find($id);
        if (!$admin) {
            return redirect('/')->with('result', 'false')->with('message', 'ไม่พบข้อมูลที่ต้องการแก้ไข');
        }

        // กำหนดข้อความแจ้งเตือนภาษาไทยสำหรับแต่ละเงื่อนไข
        $messages = [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน (Username)',
            'username.max'      => 'ชื่อผู้ใช้งาน (Username) ต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'password.min'      => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร',
            'name.required'     => 'กรุณากรอกชื่อ-นามสกุล',
            'name.max'          => 'ชื่อ-นามสกุล ต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'name.unique'       => 'ชื่อ-นามสกุล "' . $request->name . '" มีอยู่ในระบบแล้ว กรุณาตรวจสอบอีกครั้ง',
            'role.required'     => 'กรุณาเลือกสิทธิ์การใช้งาน',
            'role.in'           => 'สิทธิ์การใช้งานไม่ถูกต้อง',
            'email.email'       => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.max'         => 'อีเมลต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'email.unique'      => 'อีเมล "' . $request->email . '" มีอยู่ในระบบแล้ว กรุณาใช้อีเมลอื่น',
            'phone.max'         => 'เบอร์โทรศัพท์ต้องมีความยาวไม่เกิน 20 ตัวอักษร',
            'phone.unique'      => 'เบอร์โทรศัพท์ "' . $request->phone . '" มีอยู่ในระบบแล้ว กรุณาใช้เบอร์อื่น',
            'profile_picture.image' => 'ไฟล์รูปโปรไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'profile_picture.max'   => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
        ];

        // ตรวจสอบความถูกต้องของข้อมูล (ชื่อ-นามสกุล, Email, และ เบอร์โทรศัพท์ ไม่ซ้ำกับคนอื่น ยกเว้นตัวเอง ส่วน username ซ้ำได้)
        $validator = Validator::make($request->all(), [
            'username'        => 'required|string|max:50', // สามารถซ้ำได้
            'password'        => 'nullable|string|min:6',
            'name'            => 'required|string|max:50|unique:admins,name,' . $id, // ห้ามซ้ำ ยกเว้นของตนเอง
            'role'            => 'required|in:admin,teacher,officer',
            'email'           => 'nullable|email|max:50|unique:admins,email,' . $id, // ห้ามซ้ำ ยกเว้นของตนเอง
            'phone'           => 'nullable|string|max:20|unique:admins,phone,' . $id, // ห้ามซ้ำ ยกเว้นของตนเอง
            'profile_picture' => 'nullable|image|max:10240',
        ], $messages);

        // หากข้อมูลไม่ผ่านเงื่อนไข ให้ตีกลับพร้อมแจ้งเตือนผ่าน notify.blade.php
        if ($validator->fails()) {
            $errors = $validator->errors();
            $resultKey = 'warning';

            // ถ้ามีข้อมูลซ้ำ (ชื่อ-นามสกุล, อีเมล, เบอร์โทรศัพท์)
            if (($errors->has('name') && str_contains($errors->first('name'), 'มีอยู่ในระบบแล้ว')) ||
                ($errors->has('email') && str_contains($errors->first('email'), 'มีอยู่ในระบบแล้ว')) ||
                ($errors->has('phone') && str_contains($errors->first('phone'), 'มีอยู่ในระบบแล้ว'))) {
                $resultKey = 'duplicate';
            }

            return redirect()->back()
                ->withInput()
                ->with('result', $resultKey)
                ->with('message', $errors->first());
        }


        $updateData = [
            'username' => $request->username,
            'name'     => $request->name,
            'role'     => $request->role,
            'email'    => $request->email,
            'phone'    => $request->phone,
        ];

        // ถ้าระบุรหัสผ่านใหม่ ให้เข้ารหัสและอัปเดต ถ้าไม่ระบุให้ใช้รหัสผ่านเดิม
        if (!empty($request->password)) {
            $updateData['password']  = Hash::make($request->password);
            $updateData['real_pass'] = $request->password;
        }

        // ถ้ามีการอัปโหลดรูปภาพใหม่ (ลบรูปเก่าทิ้ง และตั้งชื่อตาม id_role_วันเวลา)
        if ($request->hasFile('profile_picture')) {
            // ลบรูปเก่าทิ้งก่อน ถ้ามี
            if ($admin->profile_picture && file_exists(public_path($admin->profile_picture))) {
                @unlink(public_path($admin->profile_picture));
            }

            $file = $request->file('profile_picture');
            $uploadDir = public_path('profile_image/teacherandofficer');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // แปลง Role เป็นตัวเลข: อาจารย์ (teacher) = 1, เจ้าหน้าที่ (officer) = 2, ผู้ดูแลระบบ (admin) = 3
            $roleMap = [
                'teacher' => '1',
                'officer' => '2',
                'admin'   => '3',
            ];
            $roleCode = $roleMap[$request->role] ?? '0';

            // ตั้งชื่อรูปตาม: id_role_วันเวลา (เช่น 5_1_20260913_212030.jpg)
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = $admin->id . '_' . $roleCode . '_' . date('Ymd_His') . '.' . $ext;
            $file->move($uploadDir, $fileName);
            $updateData['profile_picture'] = 'profile_image/teacherandofficer/' . $fileName;
        }


        // อัปเดตข้อมูลผ่าน Model
        $admin->update($updateData);

        // ส่ง Session ไปแจ้งเตือน editinfo ผ่าน notify.blade.php พร้อมใส่ชื่อที่แก้ไข
        return redirect('/')->with('result', 'editinfo')->with('message', 'แก้ไขข้อมูล "' . $admin->name . '" เรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันลบข้อมูลอาจารย์และเจ้าหน้าที่
     */
    public function destroy($id)
    {
        $admin = Teacherandofficermodel::find($id);
        if (!$admin) {
            return redirect('/')->with('result', 'false')->with('message', 'ไม่พบข้อมูลที่ต้องการลบ');
        }

        // เก็บชื่อไว้ก่อน เพื่อนำไปแสดงในแจ้งเตือนหลังลบ
        $deletedName = $admin->name;

        // ตรวจสอบและลบไฟล์รูปภาพออกจากเซิร์ฟเวอร์ ถ้ามี
        if ($admin->profile_picture && file_exists(public_path($admin->profile_picture))) {
            @unlink(public_path($admin->profile_picture));
        }

        // ลบข้อมูลออกจากฐานข้อมูล
        $admin->delete();

        Log::info('Teacher/Officer deleted successfully for ID: ' . $id);

        // ส่ง Session ไปแจ้งเตือน deleteinfo ผ่าน notify.blade.php พร้อมใส่ชื่อที่ลบ
        return redirect('/')->with('result', 'deleteinfo')->with('message', 'ลบข้อมูล "' . $deletedName . '" เรียบร้อยแล้ว');
    }

    /**
     * เปลี่ยนสถานะการใช้งาน (1 = ใช้งานปกติ, 0 = ปิดใช้งาน)
     */
    public function changeStatus($id, $status)
    {
        $admin = Teacherandofficermodel::find($id);
        if (!$admin) {
            return redirect('/')->with('result', 'false')->with('message', 'ไม่พบข้อมูลที่ต้องการเปลี่ยนสถานะ');
        }

        // ตรวจสอบค่าสถานะที่ส่งมา: ถ้าเป็น '1' ให้เป็น 1 (เปิดใช้งานปกติ), ถ้าไม่ใช่ให้เป็น '0' (ปิดใช้งาน)
        $newStatus = ($status == '1') ? '1' : '0';
        $admin->update(['status' => $newStatus]);

        Log::info('Status changed for ID ' . $id . ' to ' . $newStatus);

        $statusText = ($newStatus == '1') ? 'เปิดใช้งานปกติ' : 'ปิดใช้งาน';
        return redirect('/')->with('result', 'true')->with('message', 'เปลี่ยนสถานะของ "' . $admin->name . '" เป็น "' . $statusText . '" เรียบร้อยแล้ว');
    }

}


