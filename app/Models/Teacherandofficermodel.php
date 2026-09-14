<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacherandofficermodel extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ผูก Model นี้เข้ากับตาราง admins ในฐานข้อมูล
     */
    protected $table = 'admins';

    /**
     * กำหนดฟิลด์ที่อนุญาตให้บันทึกหรือแก้ไขข้อมูลได้ (White-list ป้องกัน Mass Assignment)
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
        'remember_token',
        'real_pass',
    ];

    /**
     * Accessor: ตรวจสอบและแปลง path รูปโปรไฟล์ให้ชี้ไปยัง profile_image/teacherandofficer เสมอ
     */
    public function getProfilePictureAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // ถ้ามี subfolder teacherandofficer อยู่แล้ว ให้คืนค่านั้น
        if (str_contains($value, 'teacherandofficer')) {
            return $value;
        }

        // ถ้าเป็น path เก่า profile_image/xxx.jpg ให้แปลงเป็น profile_image/teacherandofficer/xxx.jpg
        return str_replace('profile_image/', 'profile_image/teacherandofficer/', $value);
    }
}

