<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Teacherandofficercontroller;
use App\Http\Controllers\Authcontroller;
use App\Models\Teacherandofficermodel;
use Illuminate\Http\Request;

/**
 * Middleware ตรวจสอบสิทธิ์การเข้าสู่ระบบ (เขียนไว้ใน routes/web.php ไฟล์เดียวจบ ไม่ต้องสร้างไฟล์ใหม่)
 */
class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. ตรวจสอบว่ามี Session การเข้าสู่ระบบหรือไม่
        if (!session('logged_in') || !session('id')) {
            
            // 2. หากไม่มี Session ให้ตรวจสอบระบบ Auto-Login จาก Cookie 30 วัน
            if ($request->hasCookie('admin_remember')) {
                $user = Teacherandofficermodel::find($request->cookie('admin_remember'));
                if ($user && ($user->status === '1' || $user->status === 1)) {
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
                    return $next($request);
                }
            }

            // 3. หากไม่มีทั้ง Session และ Cookie จำการล็อกอิน ให้ดีดกลับไปหน้าล็อกอินทันที
            return redirect('/pc-csmju/admin')
                ->with('result', 'false')
                ->with('message', 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน');


        }

        return $next($request);
    }
}

// หน้าแรกสุดของระบบ: หากล็อกอินแล้ว (หรือมีคุกกี้จำการเข้าสู่ระบบ) ให้ไป /pc-csmju หากไม่มีให้ไปหน้าล็อกอิน /pc-csmju/admin ทันที

Route::get('/', function () { return view('index'); }); // route ไปหน้า index

// ระบบยืนยันตัวตน Admin (Login / Logout)
Route::get('/pc-csmju/admin', [Authcontroller::class, 'showLoginForm'])->name('login');
Route::post('/pc-csmju/admin', [Authcontroller::class, 'loginAdmin'])->name('login.post');
Route::get('/auth/logout', [Authcontroller::class, 'logoutAdmin'])->name('logout'); // รองรับลิงก์เดิมใน navbar

// Route::get('/pc-csmju/logout', [Authcontroller::class, 'logoutAdmin'])
// Route::get('/pc-csmju/login', function () { return redirect('/pc-csmju/admin'); }); // เผื่อพิมพ์ URL เก่า

// กลุ่ม Route ผู้ดูแลระบบ (ติด Middleware ตรวจสอบสิทธิ์ใน Route ชัดเจน ห้ามเข้าหากไม่ได้ล็อกอิน)
Route::middleware(AdminAuthMiddleware::class)->group(function () {
    // if (session('logged_in') || request()->hasCookie('admin_remember')) {
    //     return redirect('/pc-csmju');
    // }
    // return redirect('/pc-csmju/admin');

    // หน้ารายการอาจารย์และเจ้าหน้าที่
    Route::get('/pc-csmju', [Teacherandofficercontroller::class, 'index']);

    // หน้าเปิดฟอร์มเพิ่มข้อมูล (GET) และบันทึกข้อมูล (POST)
    Route::get('/admin/create', [Teacherandofficercontroller::class, 'create']);
    Route::post('/admin/create', [Teacherandofficercontroller::class, 'store']);

    // หน้าเปิดฟอร์มแก้ไขข้อมูล (GET) และบันทึกการแก้ไข (POST)
    Route::get('/admin/edit/{id}', [Teacherandofficercontroller::class, 'edit']);
    Route::post('/admin/update/{id}', [Teacherandofficercontroller::class, 'update']);

    // ลบข้อมูลอาจารย์และเจ้าหน้าที่
    Route::get('/admin/del/{id}', [Teacherandofficercontroller::class, 'destroy']);

    // เปลี่ยนสถานะการใช้งาน (1 = ใช้งานปกติ, 0 = ปิดใช้งาน)
    Route::get('/admin/status/{id}/{status}', [Teacherandofficercontroller::class, 'changeStatus']);

});
