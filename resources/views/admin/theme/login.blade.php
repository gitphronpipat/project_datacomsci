<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบผู้ดูแลระบบ | Admin Login</title>

    <!-- เรียกใช้ Asset และไลบรารีกลางของระบบ (Font Awesome, Bootstrap 5, ฟอนต์ Sarabun, eyepass.js) -->
    @include('theme.component')

    <!-- ฟอนต์เสริม Prompt สำหรับหัวข้อ LOGIN สไตล์โมเดิร์น -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           1. โครงสร้างพื้นฐานของหน้า Login (Override สีและ Layout จาก component)
           ========================================================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Prompt', 'Sarabun', sans-serif !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: linear-gradient(135deg, #e4ebf3 0%, #cbd8e6 50%, #bac9da 100%) !important;
            padding: 24px 16px !important;
            position: relative !important;
            overflow-x: hidden !important;
            color: #334155 !important;
            line-height: 1.5 !important;
        }

        /* Ambient Lighting: รัศมีแสงสีฟ้านุ่มนวลตกแต่งพื้นหลัง */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(255, 255, 255, 0) 70%);
            top: -120px;
            left: -120px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.18) 0%, rgba(255, 255, 255, 0) 70%);
            bottom: -100px;
            right: -100px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        /* =========================================================
           2. ตัวการ์ดครอบ Login Card (ตรงตามรูปเรฟเฟอเรนซ์)
           ========================================================= */
        .login-card-wrapper {
            position: relative;
            width: 100%;
            max-width: 410px;
            z-index: 1;
            margin-top: 45px; /* เว้นที่ด้านบนเพื่อให้วงกลมโปรไฟล์ลอยพ้นขอบการ์ด */
        }

        .login-card {
            background: linear-gradient(180deg, #9bb0c3 0%, #879bb0 100%);
            border-radius: 28px;
            padding: 72px 34px 34px 34px;
            box-shadow: 0 24px 50px rgba(35, 55, 75, 0.22), 0 4px 16px rgba(0, 0, 0, 0.06);
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            text-align: center;
            position: relative;
            backdrop-filter: blur(8px);
        }

        /* วงกลมรูปโปรไฟล์ด้านบน กึ่งกลางการ์ด */
        .avatar-circle {
            width: 110px;
            height: 110px;
            background: linear-gradient(180deg, #8ba1b6 0%, #758c9f 100%);
            border-radius: 50%;
            position: absolute;
            top: -55px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(25, 45, 65, 0.2);
            border: 5px solid #e4ebf3;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .avatar-circle:hover {
            transform: translateX(-50%) scale(1.03);
            box-shadow: 0 12px 28px rgba(25, 45, 65, 0.26);
        }

        .avatar-circle i {
            font-size: 64px;
            color: #ffffff;
            margin-top: 14px;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.18));
        }

        /* หัวข้อ LOGIN */
        .login-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 2.5px;
            margin-bottom: 26px;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        /* =========================================================
           3. กล่อง Input และช่องกรอกข้อมูล
           ========================================================= */
        .input-group-custom {
            position: relative;
            margin-bottom: 16px;
            text-align: left;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 16px;
            z-index: 2;
            transition: color 0.25s ease;
        }

        .input-group-custom input {
            width: 100%;
            height: 48px;
            background: #ffffff;
            border: 1.5px solid transparent;
            border-radius: 8px;
            padding: 10px 42px 10px 44px;
            font-size: 14.5px;
            color: #1e293b;
            transition: all 0.25s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .input-group-custom input::placeholder {
            color: #94a3b8;
            font-size: 14px;
            font-weight: 400;
        }

        .input-group-custom input:focus {
            outline: none;
            border-color: #38bdf8;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.26);
        }

        .input-group-custom input:focus ~ .input-icon {
            color: #0284c7;
        }

        /* ไอคอนเปิด-ปิดตาดูรหัสผ่าน */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            cursor: pointer;
            z-index: 2;
            padding: 6px;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .toggle-password:hover {
            color: #334155;
            transform: translateY(-50%) scale(1.1);
        }

        /* ตัวเลือก Remember me */
        .form-check-custom {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            margin-top: 4px;
            margin-bottom: 22px;
            font-size: 13.5px;
            color: #f1f5f9;
            user-select: none;
        }

        .form-check-custom input[type="checkbox"] {
            width: 17px;
            height: 17px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background-color: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            accent-color: #38bdf8;
        }

        .form-check-custom label {
            cursor: pointer;
            color: #f8fafc;
            font-weight: 400;
        }

        /* =========================================================
           4. ปุ่มกด LOGIN สีฟ้าโมเดิร์น
           ========================================================= */
        .btn-login {
            width: 100%;
            max-width: 210px;
            height: 44px;
            margin: 0 auto 18px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);
            border: none;
            border-radius: 24px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.38);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.5);
            color: #ffffff;
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(14, 165, 233, 0.3);
        }

        /* ลิงก์ Forgot Username / Password */
        .forgot-link {
            display: inline-block;
            font-size: 12.5px;
            color: #f1f5f9;
            text-decoration: none;
            opacity: 0.88;
            transition: all 0.2s ease;
        }

        .forgot-link:hover {
            color: #ffffff;
            opacity: 1;
            text-decoration: underline;
        }

        /* =========================================================
           5. รองรับหน้าจอมือถือและ iPad ทุกขนาด (ตามกฎข้อ 6)
           ========================================================= */
        @media (max-width: 576px) {
            .login-card-wrapper {
                margin-top: 35px;
            }
            .login-card {
                padding: 64px 22px 28px 22px;
                border-radius: 24px;
            }
            .avatar-circle {
                width: 96px;
                height: 96px;
                top: -48px;
            }
            .avatar-circle i {
                font-size: 56px;
            }
            .login-title {
                font-size: 22px;
                margin-bottom: 20px;
            }
            .btn-login {
                max-width: 100%;
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- กล่องครอบการ์ดเข้าสู่ระบบ -->
    <div class="login-card-wrapper">
        <div class="login-card">
            
            <!-- วงกลมไอคอนโปรไฟล์ด้านบนกึ่งกลาง -->
            <div class="avatar-circle">
                <i class="fa-solid fa-user"></i>
            </div>

            <!-- หัวข้อ LOGIN -->
            <h1 class="login-title">ADMIN</h1>

            <!-- ฟอร์มเข้าสู่ระบบส่งไปยัง route login.post (ฟังก์ชัน loginAdmin ใน Controller) -->
            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <!-- ช่องกรอกชื่อผู้ใช้งาน (Username) -->
                <div class="input-group-custom">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" 
                           name="username" 
                           id="username"
                           autocomplete="username"
                           placeholder="Username" 
                           value="{{ old('username', Cookie::get('remember_username')) }}" 
                           required 
                           autofocus>
                </div>

                <!-- ช่องกรอกรหัสผ่าน (Password) -->
                <div class="input-group-custom">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           id="password"
                           autocomplete="current-password"
                           placeholder="Password" 
                           required>
                    <i class="fa-solid fa-eye-slash toggle-password" id="togglePasswordBtn" onclick="togglePasswordVisibility('password', 'togglePasswordBtn')" title="แสดง/ซ่อนรหัสผ่าน"></i>
                </div>

                <!-- ตัวเลือก Remember me -->
                <div class="form-check-custom">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') || Cookie::has('remember_username') ? 'checked' : '' }}>
                    <label for="remember">Remember me</label>
                </div>

                <!-- ปุ่มส่งข้อมูลเข้าสู่ระบบ LOGIN -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <span>LOGIN</span>
                </button>  

                <!-- ลิงก์ Forgot Username / Password -->
                {{-- <div>
                    <a href="javascript:void(0);" 
                       onclick="alert('หากลืมชื่อผู้ใช้หรือรหัสผ่าน กรุณาติดต่อผู้ดูแลระบบเพื่อรีเซ็ตข้อมูล');" 
                       class="forgot-link">
                        Forgot Username / Password?
                    </a>
                </div> --}}
            </form>
        </div>
    </div>

    <!-- ใช้งานระบบแจ้งเตือนกลาง iziToast (ที่มีการปรับแต่งไว้แล้ว) -->
    @include('theme.notify')
</body>
</html>
