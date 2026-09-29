<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบอาจารย์และเจ้าหน้าที่ — CS-DocProject</title>

    {{-- นำเข้าไลบรารีกลาง (Bootstrap 5, Font Awesome, Bootstrap Icons, ฟอนต์ Sarabun, eyepass.js) --}}
    @include('theme.component')

    <!-- ฟอนต์เสริม Prompt สำหรับหัวข้อโมเดิร์น -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy-dark:  #07192C;
            --navy-main:  #0A2540;
            --navy-light: #163E66;
            --accent:     #2563EB;
            --paper-bg:   #F8FAFC;
            --border-clr: #E2E8F0;
            --text-main:  #1E293B;
        }

        * {
            font-family: 'Prompt', 'Sarabun', sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: var(--paper-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        /* ── กล่องการ์ดเข้าสู่ระบบแบบแบ่ง 2 ฝั่ง (Split Card) ── */
        .login-card {
            width: 100%;
            max-width: 880px;
            min-height: 520px;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--border-clr);
            overflow: hidden;
            box-shadow: 0 10px 40px -10px rgba(10, 37, 64, 0.12);
            display: flex;
        }

        /* ── ฝั่งซ้าย: แถบสี Navy แสดงข้อมูลระบบและภาพลักษณ์ ── */
        .panel-visual {
            flex: 1.05;
            background: linear-gradient(145deg, var(--navy-dark) 0%, var(--navy-main) 60%, var(--navy-light) 100%);
            color: #ffffff;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .panel-visual::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, transparent 70%);
            top: -40px;
            right: -40px;
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #ffffff;
        }

        .brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin: 0;
            line-height: 1.2;
            color: #ffffff;
        }

        .brand-sub {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.65);
            margin: 0;
        }

        .visual-content {
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        .visual-content h1 {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 0.85rem;
            color: #ffffff;
        }

        .visual-content p {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
            margin: 0;
            font-weight: 300;
        }

        .visual-footer {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.55);
        }

        .visual-footer i {
            color: #38BDF8;
            font-size: 1rem;
        }

        /* ── ฝั่งขวา: ฟอร์มกรอกข้อมูลล็อกอิน ── */
        .panel-form {
            flex: 1;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .badge-portal {
            display: inline-block;
            align-self: flex-start;
            background: #EFF6FF;
            color: #2563EB;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            margin-bottom: 0.75rem;
            border: 1px solid #DBEAFE;
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--navy-main);
            margin-bottom: 0.35rem;
        }

        .form-subtitle {
            font-size: 0.88rem;
            color: #64748B;
            margin-bottom: 2rem;
        }

        .panel-form label {
            font-size: 0.85rem;
            color: var(--text-main);
            font-weight: 500;
            margin-bottom: 0.4rem;
            display: block;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 1.05rem;
            pointer-events: none;
        }

        .panel-form .form-control {
            border-radius: 10px;
            padding: 0.7rem 1rem;
            border: 1.5px solid var(--border-clr);
            font-size: 0.92rem;
            padding-left: 2.75rem;
            color: var(--text-main);
            transition: all 0.2s ease;
            width: 100%;
        }

        .panel-form .form-control.has-toggle {
            padding-right: 2.75rem;
        }

        .panel-form .form-control:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .btn-toggle-pw {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1.1rem;
            line-height: 1;
            transition: color 0.2s;
        }

        .btn-toggle-pw:hover {
            color: #475569;
        }

        .btn-user-login {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.98rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 0.5rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-user-login:hover {
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 100%);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        .btn-user-login:active {
            transform: translateY(0);
        }

        /* ── Responsive Mobile/Tablet ── */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .login-card {
                flex-direction: column;
                max-width: 440px;
                min-height: auto;
            }

            .panel-visual {
                padding: 2.25rem 1.75rem;
            }

            .visual-content {
                margin: 1.25rem 0;
            }

            .visual-content h1 {
                font-size: 1.35rem;
            }

            .panel-form {
                padding: 2.25rem 1.75rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">

        <!-- ── Left Visual Panel: ฝั่งซ้ายแสดงข้อมูลระบบและแบรนด์ ── -->
        <div class="panel-visual">

            <!-- Brand Header -->
            <div class="brand-header">
                <div class="brand-icon-box">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h2 class="brand-name">CS-DocProject</h2>
                    <p class="brand-sub">Maejo University</p>
                </div>
            </div>

            <!-- Main Visual Message -->
            <div class="visual-content">
                <h1>ระบบบริหารจัดการ<br>คลังโครงงานคอมพิวเตอร์</h1>
                <p>ศูนย์ควบคุมและจัดทำสารสนเทศโครงงานและสหกิจศึกษา สาขาวิชาวิทยาการคอมพิวเตอร์ มหาวิทยาลัยแม่โจ้</p>
            </div>

            <!-- Footer Security Note -->
            <div class="visual-footer">
                <i class="bi bi-shield-check"></i>
                <span>Portal for Teacher & Officer · CS MJU</span>
            </div>

        </div>

        <!-- ── Right Form Panel: ฝั่งขวาฟอร์มเข้าสู่ระบบ ── -->
        <div class="panel-form">

            <span class="badge-portal">Portal Login</span>
            <h2 class="form-title">เข้าสู่ระบบ</h2>
            <p class="form-subtitle">สำหรับอาจารย์และเจ้าหน้าที่ประจำสาขาวิชา</p>

            <!-- ฟอร์มเข้าสู่ระบบสำหรับ Teacher & Officer -->
            <form action="{{ route('login.post') }}" method="POST" id="userLoginForm">
                @csrf

                <!-- Username or Email -->
                <div class="mb-3">
                    <label for="username">ชื่อผู้ใช้งาน (Username) หรืออีเมล</label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control" 
                               autocomplete="username" 
                               placeholder="Username หรือ Email"
                               value="{{ old('username', Cookie::get('user_remember_username')) }}"
                               required 
                               autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password">รหัสผ่าน (Password)</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control has-toggle" 
                               autocomplete="current-password" 
                               placeholder="กรอกรหัสผ่านของคุณ"
                               value="{{ old('password', Cookie::get('user_remember_password')) }}"
                               required>
                        <button type="button" 
                                class="btn-toggle-pw" 
                                onclick="togglePasswordVisibility('password', 'eyeIcon')" 
                                aria-label="แสดง/ซ่อนรหัสผ่าน"
                                title="แสดง/ซ่อนรหัสผ่าน">
                            <i class="fa-solid fa-eye-slash" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="mb-4 form-check">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember" 
                           class="form-check-input" 
                           style="cursor: pointer;"
                           {{ old('remember') || Cookie::has('user_remember_username') || Cookie::has('user_remember_password') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember" style="cursor: pointer; user-select: none;">
                        จำการเข้าสู่ระบบบนอุปกรณ์นี้ (Remember me)
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-user-login" id="btnLogin">
                    <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
                </button>

            </form>

        </div>

    </div>

    <!-- ระบบแจ้งเตือนกลาง (iziToast Notifications) -->
    @include('theme.notify')

</body>
</html>
