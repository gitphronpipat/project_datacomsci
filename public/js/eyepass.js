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

/**
 * ตัวนับจำนวนตัวอักษรแบบ Real-time นับลดลงตามที่ผู้ใช้พิมพ์ (เช่น 50/50 -> 49/50)
 * แสดงสีเตือนเมื่อใกล้เต็ม: สีแดง (0 ตัว), สีเหลือง (<= 5 ตัว), สีเทา (ปกติ)
 */
function updateCharCounter(input) {
    if (!input) return;
    const max = parseInt(input.getAttribute('maxlength'), 10);
    if (!max) return;
    const currentLength = input.value ? input.value.length : 0;
    const remaining = Math.max(0, max - currentLength);
    const counterEl = document.getElementById('counter_' + input.id);
    if (counterEl) {
        counterEl.textContent = `${remaining}/${max}`;
        if (remaining === 0) {
            counterEl.classList.remove('text-muted', 'text-warning');
            counterEl.classList.add('text-danger', 'fw-bold');
        } else if (remaining <= 5) {
            counterEl.classList.remove('text-muted', 'text-danger', 'fw-bold');
            counterEl.classList.add('text-warning', 'fw-medium');
        } else {
            counterEl.classList.remove('text-danger', 'text-warning', 'fw-bold', 'fw-medium');
            counterEl.classList.add('text-muted');
        }
    }
}

// ติดตั้ง Event Listener ให้กับทุกช่องที่มี maxlength ในระบบโดยอัตโนมัติ
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[maxlength]').forEach(function(input) {
        input.addEventListener('input', function() {
            updateCharCounter(this);
        });
        // คำนวณทันทีเมื่อโหลดหน้า (รองรับข้อมูลเดิมในหน้าแก้ไข และ old input)
        updateCharCounter(input);
    });
});
