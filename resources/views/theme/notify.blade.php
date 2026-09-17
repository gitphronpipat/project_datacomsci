<?php
// ดึงค่าผลลัพธ์และข้อความจาก Session ของ Laravel
$result  = session('result') ?? (session('success') ? 'true' : (session('error') ? 'false' : null));
$message = session('message') ?? session('success') ?? session('error') ?? '';

// หัวข้อการแจ้งเตือน (สามารถเพิ่ม/เปลี่ยนข้อความหัวข้อตรงนี้ได้ตามใจชอบ)
$titles = [
    'true'       => 'สำเร็จ',
    'false'      => 'ไม่สำเร็จ',
    'editinfo'   => 'แก้ไขข้อมูลสำเร็จ',
    'deleteinfo' => 'ลบข้อมูลสำเร็จ',
    'addinfo'    => 'เพิ่มข้อมูลสำเร็จ',
    'duplicate'  => 'ชื่อผู้ใช้ซ้ำ',
    'userhave'   => 'ชื่อผู้ใช้ซ้ำ',
    'warning'    => 'แจ้งเตือน',
    'info'       => 'ข้อมูล',
    'wellcome'   => 'ยินดีต้อนรับ',
    'welcome'    => 'ยินดีต้อนรับ',
    'logout'     => 'ออกจากระบบ',
    'loginfail'  => 'เข้าสู่ระบบไม่สำเร็จ',
    'loginblock' => 'บัญชีของคุณถูกปิดใช้งาน'
];

// ชนิดของกล่องแจ้งเตือน iziToast (success, error, warning, info)
$types = [
    'true'       => 'success',
    'false'      => 'error',
    'editinfo'   => 'success',
    'deleteinfo' => 'error',      // หรือเปลี่ยนเป็น 'warning' / 'info' ได้
    'addinfo'    => 'success',
    'duplicate'  => 'warning',
    'userhave'   => 'warning',
    'warning'    => 'warning',
    'info'       => 'info',
    'wellcome'   => 'success',
    'welcome'    => 'success',
    'logout'     => 'error',       // สีแดง (error)
    'loginfail'  => 'error',       // สีแดง (error)
    'loginblock' => 'error',       // สีแดง (error)
];

// ไอคอนเสริม FontAwesome สวยๆ สำหรับแต่ละสถานะ
$customIcons = [
    'true'       => 'fas fa-check-circle',
    'editinfo'   => 'fas fa-user-edit',
    'deleteinfo' => 'fas fa-trash-alt',
    'addinfo'    => 'fas fa-user-plus',
    'duplicate'  => 'fas fa-exclamation-triangle',
    'userhave'   => 'fas fa-user-times',
    'warning'    => 'fas fa-exclamation-circle',
    'info'       => 'fas fa-info-circle',
    'false'      => 'fas fa-times-circle',
    'wellcome'   => 'fas fa-user-check',
    'welcome'    => 'fas fa-user-check',
    'logout'     => 'fas fa-sign-out-alt',
    'loginfail'  => 'fas fa-sign-out-alt',
    'loginblock' => 'fas fa-sign-out-alt',
];

$activeType = $result && isset($types[$result]) ? $types[$result] : 'info';
$activeTitle = $result && isset($titles[$result]) ? $titles[$result] : 'แจ้งเตือน';
$activeIcon = $result && isset($customIcons[$result]) ? $customIcons[$result] : '';
?>

<!-- iziToast CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<style>
    /* ปรับแต่งขนาดกล่องแจ้งเตือน iziToast ให้กะทัดรัด พอดีตา สไตล์ Modern Compact */
    .iziToast { 
        min-width: 260px !important;  /* ขนาดความกว้างเริ่มต้น */
        max-width: 360px !important;  /* ความกว้างสูงสุดพอดีคำ ไม่ยาวเทอะทะ */
        min-height: unset !important; /* ยกเลิกความสูงขั้นต่ำเดิมเพื่อให้กล่องกระชับ */
        padding: 10px 14px !important; /* ลด padding ให้กล่องเตี้ยลง ไม่อ้วนหนา */
        border-radius: 8px !important; /* ขอบโค้งมนสวยงาม */
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important; /* เงาบางเบาสบายตา */
    }

    /* เว้นระยะกันชนฝั่งขวาพอดีๆ ไม่ให้ตัวหนังสือทับปุ่มกากบาท (×) */
    .iziToast > .iziToast-body {
        padding-right: 28px !important;
    }

    /* ปุ่มกากบาท (×) ขนาดพอดีมือ มุมขวาบน */
    .iziToast > .iziToast-close {
        right: 10px !important;
        top: 10px !important;
        width: 20px !important;
        height: 20px !important;
        opacity: 0.55 !important;
        transition: all 0.2s ease !important;
        border-radius: 4px !important;
    }
    .iziToast > .iziToast-close:hover {
        opacity: 1 !important;
        background-color: rgba(0, 0, 0, 0.08) !important;
    }

    .iziToast > .iziToast-body .iziToast-title {
        margin-left: 8px !important;
        font-size: 14px !important;   /* ขนาดตัวหนังสือหัวข้อ กระชับพอดี */
        font-weight: 700 !important;
        margin-bottom: 2px !important;
    }
    .iziToast > .iziToast-body .iziToast-message {
        margin-left: 8px !important;
        font-size: 12.5px !important; /* ขนาดข้อความอ่านง่าย ไม่ใหญ่เกิน */
        line-height: 1.4 !important;  /* ระยะบรรทัดชิดขึ้นอย่างสวยงาม */
        word-break: break-word !important;
    }
    .iziToast > .iziToast-body .iziToast-icon {
        font-size: 20px !important;   /* ขนาดไอคอนสมส่วนกับตัวกล่อง */
    }

    /* รองรับโทรศัพท์มือถือและ iPad ทุกขนาดหน้าจอ (Rule 6) */
    @media (max-width: 768px) {
        .iziToast {
            min-width: 240px !important;
            max-width: 88vw !important;  /* ย่อขยายตามหน้าจออัตโนมัติ */
            margin: 8px auto !important;
        }
    }
</style>

<script>
    // ฟังก์ชันส่วนกลาง สามารถพิมพ์เรียกเล่นได้จาก Console (F12) เช่น:
    // showNotify('editinfo', 'ข้อความทดสอบ');
    // showNotify('deleteinfo', 'ลบข้อมูลสำเร็จ');
    window.showNotify = function(resultKey, customMsg, customTitle) {
        const titles = <?= json_encode($titles) ?>;
        const types  = <?= json_encode($types) ?>;
        const icons  = <?= json_encode($customIcons) ?>;

        const type  = types[resultKey] || 'info';
        const title = customTitle || titles[resultKey] || 'แจ้งเตือน';
        const msg   = customMsg || '';
        const icon  = icons[resultKey] || '';

        if (typeof iziToast !== 'undefined' && iziToast[type]) {
            iziToast[type]({
                title: title,
                message: msg,
                position: 'topRight',          // ตำแหน่ง: topRight, topLeft, topCenter, bottomRight, bottomLeft, bottomCenter
                timeout: 4000,                 // เวลาแสดงผล (มิลลิวินาที) 4 วินาที
                transitionIn: 'fadeInDown',    // แอนิเมชันตอนโผล่: bounceInLeft, bounceInRight, fadeInDown, fadeInUp
                transitionOut: 'fadeOutUp',    // แอนิเมชันตอนหาย: fadeOut, fadeOutUp, fadeOutDown
                progressBar: true,             // แสดงแถบเวลานับถอยหลัง
                closeOnEscape: true,           // กดปุ่ม Esc เพื่อปิดได้
                pauseOnHover: true,            // เอาเมาส์ชี้แล้วหยุดเวลา
                layout: 2,                     // รูปแบบการจัดวางกล่อง
                icon: icon,                    // ไอคอนเสริม FontAwesome
            });
        }
    };

    <?php if ($result): ?>
    document.addEventListener("DOMContentLoaded", function () {
        window.showNotify(
            <?= json_encode($result) ?>,
            <?= json_encode($message) ?>,
            <?= json_encode($activeTitle) ?>
        );
    });
    <?php endif; ?>
</script>
