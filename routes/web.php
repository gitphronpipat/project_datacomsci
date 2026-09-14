<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Teacherandofficercontroller;
use App\Http\Controllers\Authcontroller;

// หน้ารายการอาจารย์และเจ้าหน้าที่
Route::get('/', [Teacherandofficercontroller::class, 'index']);

// ระบบยืนยันตัวตน Admin (Login / Logout)
Route::get('/pc-csmju/login', [Authcontroller::class, 'showLoginForm'])->name('login');
Route::post('/pc-csmju/login', [Authcontroller::class, 'loginAdmin'])->name('login.post');
Route::get('/pc-csmju/logout', [Authcontroller::class, 'logoutAdmin'])->name('logout');
Route::get('/auth/logout', [Authcontroller::class, 'logoutAdmin']); // รองรับลิงก์เดิมใน navbar

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
