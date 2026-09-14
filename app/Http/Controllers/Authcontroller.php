<?php

namespace App\Http\Controllers;

use App\Models\Teacherandofficermodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Authcontroller extends Controller
{
    /**
     * แสดงหน้าฟอร์ม Login สำหรับ Admin
     */
    public function showLoginForm()
    {
        // หากเข้าสู่ระบบอยู่แล้ว ให้ส่งต่อไปหน้าแรก
        if (session('logged_in')) {
            return redirect('/');
        }

        return view('admin.theme.login');
    }

    /**
     * Method สำรอง alias เพื่อรองรับ Route เก่า /pc-csmju/login -> auth
     */
    public function auth()
    {
        return $this->showLoginForm();
    }

        /**
     * Alias method รองรับการเรียก login()
     */
    public function login(Request $request)
    {
        return $this->loginAdmin($request);
    }

    /**
     * Alias method รองรับการเรียก logout()
     */
    public function logout(Request $request)
    {
        return $this->logoutAdmin($request);
    }

    /**
     * ดำเนินการตรวจสอบการเข้าสู่ระบบ (Authentication)
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน (Username)',
            'password.required' => 'กรุณากรอกรหัสผ่าน (Password)',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        // ค้นหาผู้ใช้ในตาราง admins โดยใช้ username
        $user = Teacherandofficermodel::where('username', $username)->first();

        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->with('result', 'false')
                ->with('message', 'ไม่พบบัญชีผู้ใช้งานนี้ในระบบ กรุณาตรวจสอบใหม่อีกครั้ง');
        }

        // ตรวจสอบสถานะการใช้งาน (status 1 = ปกติ, 0 = ปิดใช้งาน)
        if ($user->status !== '1' && $user->status !== 1) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('result', 'false')
                ->with('message', 'บัญชีนี้ถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบหลัก');
        }

        // ตรวจสอบรหัสผ่าน: รองรับทั้ง Bcrypt Hash, ข้อความธรรมดา (Plain text), หรือตรงกับ real_pass
        $passwordMatch = false;

        if (!empty($user->password) && Hash::check($password, $user->password)) {
            $passwordMatch = true;
        } elseif (!empty($user->password) && $password === (string)$user->password) {
            $passwordMatch = true;
        } elseif (!empty($user->real_pass) && $password === (string)$user->real_pass) {
            $passwordMatch = true;
        }

        if (!$passwordMatch) {
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->with('result', 'false')
                ->with('message', 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        // บันทึกข้อมูลลง Session ของ Laravel
        session([
            'logged_in'       => true,
            'id'              => $user->id,
            'user_id'         => $user->id,
            'username'        => $user->username,
            'name'            => $user->name,
            'role'            => $user->role,
            'email'           => $user->email,
            'profile_picture' => $user->profile_picture,
        ]);

        // อัปเดตเวลาเข้าใช้งานล่าสุด
        try {
            $user->update(['last_login_at' => now()]);
        } catch (\Exception $e) {
            Log::warning('Cannot update last_login_at: ' . $e->getMessage());
        }

        Log::info('Admin login successful: ' . $user->username . ' (ID: ' . $user->id . ')');

        return redirect('/')
            ->with('result', 'wellcome')
            ->with('message', 'ยินดีต้อนรับคุณ ' . ($user->name ?? $user->username) . ' เข้าสู่ระบบสำเร็จ');
    }

    /**
     * ออกจากระบบ (Logout)
     */
    public function logoutAdmin(Request $request)
    {
        $request->session()->flush();

        return redirect('/pc-csmju/login')
            ->with('result', 'logout')
            ->with('message', 'ออกจากระบบเรียบร้อยแล้ว');
    }


}
