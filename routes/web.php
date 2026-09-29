<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Teacherandofficercontroller;
use App\Http\Controllers\admin\Studentcontroller;
use App\Http\Controllers\officer\testcontroller as OfficerTestController;
use App\Http\Controllers\teacher\testcontroller as TeacherTestController;
use App\Http\Controllers\Authcontroller;
use App\Models\Authmodel;
use Illuminate\Http\Request;

/**
 * Helper ตรวจสอบการยืนยันตัวตนฝั่ง Admin โดยเฉพาะ
 * ตรวจสอบผ่าน Session ที่มี role เป็น admin หรือ Auto-Login Cookie (admin_remember)
 */
if (!function_exists('authenticateAdminFromSessionOrCookie')) {
    function authenticateAdminFromSessionOrCookie(Request $request)
    {
        if (session('logged_in') && session('id') && session('role') === 'admin') {
            return true;
        }

        // ตรวจสอบคุกกี้จำการเข้าสู่ระบบฝั่ง Admin เท่านั้น
        if ($request->hasCookie('admin_remember')) {
            $user = Authmodel::find($request->cookie('admin_remember'));
            if ($user && ($user->status === '1' || $user->status === 1) && $user->role === 'admin') {
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
                return true;
            }
        }

        return false;
    }
}

/**
 * Helper ตรวจสอบการยืนยันตัวตนฝั่งอาจารย์และเจ้าหน้าที่ (Teacher & Officer)
 * ตรวจสอบผ่าน Session หรือ Auto-Login Cookie (users_remember) แยกเด็ดขาดจากฝั่ง Admin
 */
if (!function_exists('authenticateUserFromSessionOrCookie')) {
    function authenticateUserFromSessionOrCookie(Request $request)
    {
        if (session('logged_in') && session('id') && in_array(session('role'), ['teacher', 'officer'])) {
            return true;
        }

        // ตรวจสอบคุกกี้จำการเข้าสู่ระบบเฉพาะอาจารย์และเจ้าหน้าที่
        if ($request->hasCookie('users_remember')) {
            $user = Authmodel::find($request->cookie('users_remember'));
            if ($user && ($user->status === '1' || $user->status === 1) && in_array($user->role, ['teacher', 'officer'])) {
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
                return true;
            }
        }

        return false;
    }
}

/**
 * Middleware ตรวจสอบสิทธิ์เฉพาะผู้ดูแลระบบ (Admin Only)
 * ป้องกันไม่ให้ Teacher หรือ Officer ทะลุเข้าสู่ระบบแอดมิน 100%
 */
class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!authenticateAdminFromSessionOrCookie($request)) {
            return redirect('/pc-csmju/admin')
                ->with('result', 'false')
                ->with('message', 'กรุณาเข้าสู่ระบบผู้ดูแลระบบก่อนเข้าใช้งาน');
        }

        // ตรวจสอบสิทธิ์ Role: หากไม่ใช่ Admin ให้ดีดกลับไปยังห้องของตนเอง
        $role = session('role');
        if ($role !== 'admin') {
            if ($role === 'teacher') {
                return redirect('/teacher')
                    ->with('result', 'false')
                    ->with('message', 'คุณไม่มีสิทธิ์เข้าถึงส่วนผู้ดูแลระบบ (เฉพาะ Admin เท่านั้น)');
            } elseif ($role === 'officer') {
                return redirect('/officer')
                    ->with('result', 'false')
                    ->with('message', 'คุณไม่มีสิทธิ์เข้าถึงส่วนผู้ดูแลระบบ (เฉพาะ Admin เท่านั้น)');
            }
            return redirect('/login');
        }

        return $next($request);
    }
}

/**
 * Middleware ตรวจสอบสิทธิ์เฉพาะอาจารย์ (Teacher)
 */
class TeacherAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!authenticateUserFromSessionOrCookie($request)) {
            return redirect('/login')
                ->with('result', 'false')
                ->with('message', 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน');
        }

        $role = session('role');
        if ($role !== 'teacher') {
            if ($role === 'officer') {
                return redirect('/officer')
                    ->with('result', 'false')
                    ->with('message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะอาจารย์เท่านั้น)');
            } elseif ($role === 'admin') {
                return redirect('/pc-csmju');
            }
            return redirect('/login');
        }

        return $next($request);
    }
}

/**
 * Middleware ตรวจสอบสิทธิ์เฉพาะเจ้าหน้าที่ (Officer)
 */
class OfficerAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!authenticateUserFromSessionOrCookie($request)) {
            return redirect('/login')
                ->with('result', 'false')
                ->with('message', 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน');
        }

        $role = session('role');
        if ($role !== 'officer') {
            if ($role === 'teacher') {
                return redirect('/teacher')
                    ->with('result', 'false')
                    ->with('message', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะเจ้าหน้าที่เท่านั้น)');
            } elseif ($role === 'admin') {
                return redirect('/pc-csmju');
            }
            return redirect('/login');
        }

        return $next($request);
    }
}

// =========================================================
// 1. หน้าแรกสุดของระบบ (Public Homepage หน้าบ้าน ไม่ต้องล็อกอิน ไม่แตะต้อง)
// =========================================================
Route::get('/', function () { return view('index'); });

// =========================================================
// 2. ระบบเข้าสู่ระบบสำหรับอาจารย์และเจ้าหน้าที่ (Portal Login -> resources/views/login.blade.php)
// =========================================================
Route::get('/login', [Authcontroller::class, 'showUserLoginForm'])->name('login');
Route::post('/login', [Authcontroller::class, 'loginUser'])->name('login.post');

// =========================================================
// 3. ระบบเข้าสู่ระบบเฉพาะผู้ดูแลระบบ (Admin Login Only -> admin/theme/login.blade.php)
// =========================================================
Route::get('/pc-csmju/admin', [Authcontroller::class, 'showLoginForm'])->name('admin.login');
Route::post('/pc-csmju/admin', [Authcontroller::class, 'loginAdmin'])->name('admin.login.post');

// ออกจากระบบ
Route::get('/auth/logout', [Authcontroller::class, 'logout'])->name('logout');

// =========================================================
// 4. กลุ่ม Route ผู้ดูแลระบบ (Admin เท่านั้น)
// =========================================================
Route::middleware(AdminAuthMiddleware::class)->group(function () {
    // หน้ารายการอาจารย์และเจ้าหน้าที่
    Route::get('/pc-csmju', [Teacherandofficercontroller::class, 'index']);

    // เพิ่มข้อมูลอาจารย์และเจ้าหน้าที่
    Route::get('/pc-csmju/admin/create', [Teacherandofficercontroller::class, 'create']);
    Route::post('/pc-csmju/admin/create', [Teacherandofficercontroller::class, 'store']);

    // แก้ไขข้อมูลอาจารย์และเจ้าหน้าที่
    Route::get('/pc-csmju/admin/edit/{id}', [Teacherandofficercontroller::class, 'edit']);
    Route::post('/pc-csmju/admin/update/{id}', [Teacherandofficercontroller::class, 'update']);

    // ลบข้อมูลอาจารย์และเจ้าหน้าที่
    Route::get('/pc-csmju/admin/del/{id}', [Teacherandofficercontroller::class, 'destroy']);

    // เปลี่ยนสถานะการใช้งานอาจารย์และเจ้าหน้าที่
    Route::get('/pc-csmju/admin/status/{id}/{status}', [Teacherandofficercontroller::class, 'changeStatus']);

    // =========================================================
    // จัดการข้อมูลนักศึกษา (Student Management)
    // =========================================================
    Route::get('/pc-csmju/admin/student', [Studentcontroller::class, 'index']);
    Route::get('/pc-csmju/admin/student/create', [Studentcontroller::class, 'create']);
    Route::post('/pc-csmju/admin/student/create', [Studentcontroller::class, 'store']);
    Route::get('/pc-csmju/admin/student/status/{id}/{status}', [Studentcontroller::class, 'changeStatus']);
    Route::get('/pc-csmju/admin/student/del/{id}', [Studentcontroller::class, 'destroy']);
    Route::post('/pc-csmju/admin/student/import/preview', [Studentcontroller::class, 'previewCsv']);
    Route::post('/pc-csmju/admin/student/import/store', [Studentcontroller::class, 'storeImportData']);

    // จัดการแก้ไขและลบโครงงาน / นักศึกษา (Standard RESTful Resource Routes)
    Route::post('/pc-csmju/admin/student/update/{id}', [Studentcontroller::class, 'update']);
    Route::post('/pc-csmju/admin/student/project/update/{id}', [Studentcontroller::class, 'updateProject']);
    Route::get('/pc-csmju/admin/student/project/del/{id}', [Studentcontroller::class, 'destroyProject']);
});

// =========================================================
// 5. กลุ่ม Route อาจารย์ (Teacher)
// =========================================================
Route::middleware(TeacherAuthMiddleware::class)->group(function () {
    Route::get('/teacher', [TeacherTestController::class, 'index']);
});

// =========================================================
// 6. กลุ่ม Route เจ้าหน้าที่ (Officer)
// =========================================================
Route::middleware(OfficerAuthMiddleware::class)->group(function () {
    Route::get('/officer', [OfficerTestController::class, 'index']);
});
