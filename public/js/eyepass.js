/**
 * eyepass.js - สคริปต์ส่วนกลางสำหรับสลับเปิด/ปิดดูรหัสผ่าน (Show/Hide Password)
 * ใช้ร่วมกันได้ทุกหน้าในระบบ (หน้า Login, หน้าเพิ่มข้อมูล, หน้าแก้ไขข้อมูล)
 */

function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (!input) return;

    // ตรวจสอบชนิดของช่องกรอก ถ้าเป็น password ให้เปลี่ยนเป็น text และในทางกลับกัน
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    // สลับไอคอนดวงตา Font Awesome (fa-eye และ fa-eye-slash)
    if (icon) {
        if (isPassword) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
}
