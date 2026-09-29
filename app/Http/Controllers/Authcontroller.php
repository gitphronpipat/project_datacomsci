<?php

namespace App\Http\Controllers;

use App\Models\Authmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Authcontroller extends Controller
{
    /**
     * แสดงหน้าฟอร์ม Login สำหรับ Admin โดยเฉพาะ (/pc-csmju/admin)
     */
    public function showLoginForm(Request $request)
    {
        // หากเข้าสู่ระบบอยู่แล้ว
        if (session('logged_in')) {
            if (session('role') === 'admin') {
                return redirect('/pc-csmju');
            }
            return $this->redirectByRole(session('role'));
        }

        // ระบบ Auto-Login: ตรวจสอบ Cookie จำการเข้าสู่ระบบของ Admin โดยเฉพาะ
        if ($request->hasCookie('admin_remember')) {
            $userId = $request->cookie('admin_remember');
            $user = Authmodel::find($userId);

            if ($user && ($user->status === '1' || $user->status === 1) && $user->role === 'admin') {
                $this->createAdminSession($user);
                Log::info('Admin auto-logged in via admin_remember cookie: ' . $user->username);
                return redirect('/pc-csmju');
            } else {
                Cookie::queue(Cookie::forget('admin_remember'));
                Cookie::queue(Cookie::forget('admin_remember_username'));
                Cookie::queue(Cookie::forget('admin_remember_password'));
            }
        }

        return view('admin.theme.login');
    }

    /**
     * แสดงหน้าฟอร์ม Login สำหรับอาจารย์และเจ้าหน้าที่ (/login)
     */
    public function showUserLoginForm(Request $request)
    {
        // หากเข้าสู่ระบบอยู่แล้ว และเป็นอาจารย์หรือเจ้าหน้าที่ ให้ส่งต่อไปหน้าประจำ Role ของตนเอง
        if (session('logged_in') && in_array(session('role'), ['teacher', 'officer'])) {
            return $this->redirectByRole(session('role'));
        }

        // ระบบ Auto-Login จาก Cookie จำการเข้าสู่ระบบ 30 วัน เฉพาะฝั่งอาจารย์และเจ้าหน้าที่ (users_remember)
        // แยกเด็ดขาดจาก admin_remember เพื่อไม่ให้ Cookie ของแอดมินตามมา
        if ($request->hasCookie('users_remember')) {
            $userId = $request->cookie('users_remember');
            $user = Authmodel::find($userId);

            if ($user && ($user->status === '1' || $user->status === 1) && in_array($user->role, ['teacher', 'officer'])) {
                $this->createAdminSession($user);
                Log::info('User auto-logged in via users_remember cookie: ' . $user->username . ' (Role: ' . $user->role . ')');
                return $this->redirectByRole($user->role);
            } else {
                Cookie::queue(Cookie::forget('users_remember'));
                Cookie::queue(Cookie::forget('user_remember_username'));
                Cookie::queue(Cookie::forget('user_remember_password'));
            }
        }

        return view('login');
    }

    /**
     * ดำเนินการตรวจสอบการเข้าสู่ระบบเฉพาะผู้ดูแลระบบ (Admin Only)
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

        $user = Authmodel::where('username', $username)->first();

        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->with('result', 'loginfail')
                ->with('message', 'ไม่พบบัญชีผู้ใช้งานนี้ในระบบ กรุณาตรวจสอบใหม่อีกครั้ง');
        }

        if ($user->status !== '1' && $user->status !== 1) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('result', 'loginblock')
                ->with('message', 'บัญชีนี้ถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบหลัก');
        }

        if (!$this->verifyPassword($user, $password)) {
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->with('result', 'loginfail')
                ->with('message', 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        // ข้อกำหนด: หน้า Admin อนุญาตเฉพาะผู้ที่มี role เป็น admin เท่านั้น
        if ($user->role !== 'admin') {
            $roleName = ($user->role === 'teacher') ? 'อาจารย์' : 'เจ้าหน้าที่';
            return redirect('/login')
                ->withInput($request->only('username'))
                ->with('result', 'false')
                ->with('message', "หน้านี้สำหรับผู้ดูแลระบบ (Admin) เท่านั้น บัญชีของคุณคือ {$roleName} กรุณาเข้าสู่ระบบที่หน้านี้ครับ");
        }

        // บันทึก Cookie และ Session ฝั่ง Admin แยกขาดจากฝั่ง User ทั่วไป (จำทั้ง username และ password)
        $this->handleLoginSuccess($user, $request, 'admin_remember', 'admin_remember_username', 'admin_remember_password');

        return redirect('/pc-csmju')
            ->with('result', 'wellcome')
            ->with('message', 'ยินดีต้อนรับคุณ ' . ($user->name ?? $user->username) . ' เข้าสู่ระบบ Admin สำเร็จ');
    }

    /**
     * ดำเนินการตรวจสอบการเข้าสู่ระบบสำหรับอาจารย์และเจ้าหน้าที่ (/login)
     */
    public function loginUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้งาน (Username) หรืออีเมล',
            'password.required' => 'กรุณากรอกรหัสผ่าน (Password)',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        // ค้นหาได้ทั้ง Username และ Email
        $user = Authmodel::where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->with('result', 'loginfail')
                ->with('message', 'ไม่พบบัญชีผู้ใช้งานนี้ในระบบ กรุณาตรวจสอบใหม่อีกครั้ง');
        }

        if ($user->status !== '1' && $user->status !== 1) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('result', 'loginblock')
                ->with('message', 'บัญชีนี้ถูกปิดการใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
        }

        if (!$this->verifyPassword($user, $password)) {
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->with('result', 'loginfail')
                ->with('message', 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }


        // จัดการล็อกอินและจำการเข้าสู่ระบบผ่าน users_remember, user_remember_username และ user_remember_password
        $this->handleLoginSuccess($user, $request, 'users_remember', 'user_remember_username', 'user_remember_password');

        return $this->redirectByRole($user->role)
            ->with('result', 'wellcome')
            ->with('message', 'ยินดีต้อนรับคุณ ' . ($user->name ?? $user->username) . ' เข้าสู่ระบบสำเร็จ');
    }

    /**
     * ออกจากระบบ (Logout)
     */
    public function logout(Request $request)
    {
        $wasAdmin = (session('role') === 'admin');

        $request->session()->flush();

        // ล้าง Cookie Auto-Login ทั้งสองชุด
        Cookie::queue(Cookie::forget('admin_remember'));
        Cookie::queue(Cookie::forget('users_remember'));

        // ส่งกลับไปยังหน้า Login ตามสิทธิ์เดิม
        $targetUrl = $wasAdmin ? '/pc-csmju/admin' : '/login';

        return redirect($targetUrl)
            ->with('result', 'logout')
            ->with('message', 'ออกจากระบบเรียบร้อยแล้ว');
    }

    /**
     * Helper ตรวจสอบความถูกต้องของรหัสผ่าน
     */
    private function verifyPassword($user, $password)
    {
        if (!empty($user->password) && Hash::check($password, $user->password)) {
            return true;
        } elseif (!empty($user->password) && $password === (string)$user->password) {
            return true;
        } elseif (!empty($user->real_pass) && $password === (string)$user->real_pass) {
            return true;
        }
        return false;
    }

    /**
     * Helper จัดการกระบวนการเมื่อล็อกอินสำเร็จ
     * แยกคุกกี้ token, คุกกี้จำชื่อผู้ใช้ และคุกกี้จำรหัสผ่านตามชุดล็อกอิน
     */
    private function handleLoginSuccess($user, Request $request, $cookieName, $usernameCookieName, $passwordCookieName)
    {
        $this->createAdminSession($user);

        if ($request->has('remember')) {
            Cookie::queue($cookieName, $user->id, 60 * 24 * 30);
            Cookie::queue($usernameCookieName, $user->username, 60 * 24 * 30);
            Cookie::queue($passwordCookieName, $request->input('password'), 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget($cookieName));
            Cookie::queue(Cookie::forget($usernameCookieName));
            Cookie::queue(Cookie::forget($passwordCookieName));
        }

        try {
            $user->update(['last_login_at' => now()]);
        } catch (\Exception $e) {
            Log::warning('Cannot update last_login_at: ' . $e->getMessage());
        }

        Log::info('Login successful: ' . $user->username . ' (Role: ' . $user->role . ')');
    }

    /**
     * Helper สร้าง Session
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

    /**
     * คืนค่า Redirect ตามบทบาท (Role) ของผู้ใช้งาน
     */
    private function redirectByRole($role)
    {
        if ($role === 'teacher') {
            return redirect('/teacher');
        } elseif ($role === 'officer') {
            return redirect('/officer');
        }
        return redirect('/pc-csmju');
    }
}
