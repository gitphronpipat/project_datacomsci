<?php

namespace App\Http\Controllers;

use App\Models\Teacherandofficermodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Authcontroller extends Controller
{
    /**
     * แสดงหน้าฟอร์ม Login สำหรับ Admin
     */
    public function showLoginForm(Request $request)
    {
        // หากเข้าสู่ระบบอยู่แล้ว ให้ส่งต่อไปหน้ารายการอาจารย์และเจ้าหน้าที่
        if (session('logged_in')) {
            return redirect('/pc-csmju');
        }

        // ระบบ Auto-Login: ตรวจสอบ Cookie จำการเข้าสู่ระบบ 30 วัน
        if ($request->hasCookie('admin_remember')) {
            $userId = $request->cookie('admin_remember');
            $user = Teacherandofficermodel::find($userId);

            if ($user && ($user->status === '1' || $user->status === 1)) {
                $this->createAdminSession($user);
                Log::info('Admin auto-logged in via remember cookie: ' . $user->username . ' (ID: ' . $user->id . ')');
                return redirect('/pc-csmju');
            } else {
                // หากบัญชีถูกปิดหรือไม่พบผู้ใช้ ให้ล้าง Cookie ทิ้ง
                Cookie::queue(Cookie::forget('admin_remember'));
            }
        }

        return view('admin.theme.login');
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
                ->with('result', 'loginfail')
                ->with('message', 'ไม่พบบัญชีผู้ใช้งานนี้ในระบบ กรุณาตรวจสอบใหม่อีกครั้ง');
        }

        // ตรวจสอบสถานะการใช้งาน (status 1 = ปกติ, 0 = ปิดใช้งาน)
        if ($user->status !== '1' && $user->status !== 1) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('result', 'loginblock')
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
                ->with('result', 'loginfail')
                ->with('message', 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        // บันทึกข้อมูลลง Session ของ Laravel
        $this->createAdminSession($user);

        // จัดการระบบ Remember Me (ใช้ Cookie เข้ารหัส 30 วัน โดยไม่ต้องยุ่งกับฐานข้อมูล)
        if ($request->has('remember')) {
            // บันทึก Cookie อายุ 30 วัน (เก็บ User ID ที่เข้ารหัสแล้วสำหรับจำการล็อกอิน และเก็บ Username สำหรับช่องกรอก)
            Cookie::queue('admin_remember', $user->id, 60 * 24 * 30);
            Cookie::queue('remember_username', $user->username, 60 * 24 * 30);
        } else {
            // หากไม่ได้ติ๊ก ให้ล้าง Cookie การจำเก่าทิ้ง
            Cookie::queue(Cookie::forget('admin_remember'));
            Cookie::queue(Cookie::forget('remember_username'));
        }

        // อัปเดตเวลาเข้าใช้งานล่าสุด
        try {
            $user->update(['last_login_at' => now()]);
        } catch (\Exception $e) {
            Log::warning('Cannot update last_login_at: ' . $e->getMessage());
        }

        Log::info('Admin login successful: ' . $user->username . ' (ID: ' . $user->id . ')');

        return redirect('/pc-csmju')
            ->with('result', 'wellcome')
            ->with('message', 'ยินดีต้อนรับคุณ ' . ($user->name ?? $user->username) . ' เข้าสู่ระบบสำเร็จ');
    }

    /**
     * ออกจากระบบ (Logout)
     */
    public function logoutAdmin(Request $request)
    {
        $request->session()->flush();

        // ล้าง Cookie Auto-Login ทิ้ง เพื่อให้ต้องกรอกรหัสผ่านใหม่ (แต่ยังคงจำ Username ไว้ในช่องกรอก)
        Cookie::queue(Cookie::forget('admin_remember'));

        return redirect('/pc-csmju/admin')
            ->with('result', 'logout')
            ->with('message', 'ออกจากระบบเรียบร้อยแล้ว');
    }

    /**
     * Helper ฟังก์ชันสำหรับสร้าง Session ข้อมูลผู้ดูแลระบบ (ป้องกันโค้ดซ้ำซ้อนตามกฎข้อ 7)
     */
    private function createAdminSession($user)
    {
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
    }
}
