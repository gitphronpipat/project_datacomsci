<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Studentmodel extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * ผูก Model นี้เข้ากับตาราง student ในฐานข้อมูล
     */
    protected $table = 'student';

    /**
     * กำหนดฟิลด์ที่อนุญาตให้บันทึกหรือแก้ไขข้อมูลได้ (ป้องกัน Mass Assignment)
     */
    protected $fillable = [
        'student_id',
        'username',
        'password',
        'real_pass',
        'status',
        'status_student',
        'name',
        'nickname',
        'email',
        'phone',
        'remark',
        'profile_picture',
        'last_login_at',
    ];

    /**
     * ซ่อนฟิลด์เหล่านี้เวลาถูกแปลงเป็น JSON หรือ Array เพื่อความปลอดภัย
     */
    protected $hidden = [
        'password',
        'real_pass',
    ];

    /**
     * Accessor: ตรวจสอบและแปลง path รูปโปรไฟล์ให้ชี้ไปยัง profile_image/student เสมอ
     */
    public function getProfilePictureAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // ถ้ามี subfolder student อยู่แล้ว ให้คืนค่านั้น
        if (str_contains($value, 'student')) {
            return $value;
        }

        // ถ้าเป็น path เก่า profile_image/xxx.jpg ให้แปลงเป็น profile_image/student/xxx.jpg
        return str_replace('profile_image/', 'profile_image/student/', $value);
    }
}
