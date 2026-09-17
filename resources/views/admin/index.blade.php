<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('theme.component')
</head>
<body>

    @include('admin.theme.navbar')
    @include('admin.theme.menu')

    <div id="main">
        @include($content)
    </div>


    @include('theme.modalmix')

    <!-- ระบบ Modal ยืนยันการตัดสินใจ (ออกจากระบบ, ลบข้อมูล, ยืนยันเปลี่ยนสถานะ) -->
    @include('theme.alert')
    
    <!-- ระบบแจ้งเตือนกลาง (iziToast Notifications) -->
    @include('theme.notify')

</body>
</html>
