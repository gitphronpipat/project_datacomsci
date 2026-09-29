<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model กลางสำหรับการยืนยันตัวตน (Authentication) ของทุก Role ในระบบ
 * ผูกกับตาราง admins เพื่อไม่ให้สับสนกับการจัดการข้อมูลอาจารย์/เจ้าหน้าที่
 */
class Authmodel extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ผูก Model นี้เข้ากับตาราง admins ในฐานข้อมูล
     */
    protected $table = 'admins';

    /**
     * ฟิลด์ที่อนุญาตให้บันทึกหรือแก้ไขข้อมูลได้
     */
    protected $fillable = [
        'username',
        'password',
        'real_pass',
        'role',
        'status',
        'name',
        'email',
        'phone',
        'profile_picture',
        'last_login_at',
    ];

    /**
     * ซ่อนฟิลด์เหล่านี้เวลาถูกส่งออกไปเป็น JSON หรือ Array เพื่อความปลอดภัย
     */
    protected $hidden = [
        'password',
        'real_pass',
    ];

    /**
     * Accessor: แปลง path รูปโปรไฟล์ให้ถูกต้องเสมอ
     */
    public function getProfilePictureAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (str_contains($value, 'teacherandofficer')) {
            return $value;
        }

        return str_replace('profile_image/', 'profile_image/teacherandofficer/', $value);
    }
}
