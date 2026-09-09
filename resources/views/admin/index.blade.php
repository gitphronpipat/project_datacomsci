<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ถ้า component.blade.php มีเฉพาะ CSS/JS (ไม่ใช่ HTML เต็ม) --}}
    @include('component')

    {{-- หรือถ้าเป็น Laravel Component --}}
    {{-- <x-component /> --}}

</head>
<body>

    {{-- ส่วนประกอบร่วม --}}
    @include('admin.theme.navbar')
    @include('admin.theme.menu')

    {{-- เนื้อหาหลัก --}}
    <div id="main">
        @include($content)
    </div>

</body>
</html>
