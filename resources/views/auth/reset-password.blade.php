<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور | ENG-SHADI HR</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #eef2ff;
            min-height: 100vh;
            overflow: auto;
            color: #111827;
        }

        .auth-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 58% 42%;
            direction: rtl;
        }

        .banner {
            background:
                linear-gradient(135deg, rgba(76, 87, 214, .92), rgba(112, 64, 180, .92)),
                url('/images/hr-login.png');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .banner-content {
            max-width: 620px;
        }

        .banner-content h1 {
            font-size: 48px;
            margin-bottom: 18px;
            font-weight: 900;
        }

        .banner-content p {
            font-size: 21px;
            line-height: 1.9;
            margin-bottom: 34px;
        }

        .features {
            display: grid;
            gap: 15px;
        }

        .feature {
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.2);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 18px;
            font-weight: bold;
        }

        .feature i {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(255,255,255,.22);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-side {
            background: white;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 24px 38px;
            box-shadow: 0 0 35px rgba(80,70,160,.12);
            overflow-y: auto;
            max-height: 100vh;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            padding-bottom: 28px;
        }

        .logo {
            width: 92px;
            height: 92px;
            margin: 0 auto 16px;
            border-radius: 26px;
            background: linear-gradient(135deg, #6676de, #7b5cc8);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(103,91,205,.35);
        }

        .logo strong {
            font-size: 26px;
        }

        .logo span {
            font-size: 11px;
            margin-top: 5px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 46px;
            border-radius: 14px;
            background: #f8f9ff;
            border: 1px solid #e0e7ff;
            color: #6d45c5;
            text-decoration: none;
            font-weight: 900;
            margin-bottom: 14px;
            padding: 12px;
        }

        .back-link i {
            margin-left: 8px;
        }

        .title {
            text-align: center;
            font-size: 30px;
            color: #111827;
            margin-bottom: 7px;
            font-weight: 900;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 20px;
            line-height: 1.7;
            font-weight: 800;
        }

        .message {
            border-radius: 15px;
            padding: 12px 14px;
            margin-bottom: 14px;
            font-weight: 800;
            line-height: 1.8;
        }

        .message.success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .message.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        .form-group label {
            display: block;
            font-weight: 900;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-box {
            position: relative;
        }

        .input-box input {
            width: 100%;
            height: 52px;
            border: 1px solid #d8d6ff;
            border-radius: 16px;
            padding: 0 50px 0 46px;
            background: #f8f9ff;
            outline: none;
            font-size: 15px;
            font-weight: 800;
            color: #111827;
        }

        .input-box input:focus {
            border-color: #6676de;
            box-shadow: 0 0 0 4px rgba(102,118,222,.12);
            background: white;
        }

        .input-box .main-icon {
            position: absolute;
            right: 18px;
            top: 17px;
            color: #6d5bd0;
        }

        .toggle-password {
            position: absolute;
            left: 14px;
            top: 11px;
            width: 30px;
            height: 30px;
            border: 0;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            font-size: 15px;
        }

        .field-error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 7px;
            font-weight: 900;
            line-height: 1.7;
        }

        .field-note {
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
            margin-top: 7px;
            line-height: 1.7;
        }

        .submit-btn {
            width: 100%;
            min-height: 54px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #6676de, #6d45c5);
            color: white;
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 15px 30px rgba(103,91,205,.35);
            transition: .2s ease;
            padding: 12px;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 38px rgba(103,91,205,.42);
        }

        .secondary-box {
            margin-top: 14px;
            border: 1px solid #e0e7ff;
            background: #f8f9ff;
            border-radius: 16px;
            padding: 13px;
            color: #6b7280;
            font-weight: 800;
            line-height: 1.8;
            text-align: center;
        }

        .secondary-box a {
            color: #6d45c5;
            text-decoration: none;
            font-weight: 900;
        }

        @media (max-width: 1180px) {
            .auth-page {
                grid-template-columns: 52% 48%;
            }

            .banner {
                padding: 45px;
            }

            .banner-content h1 {
                font-size: 40px;
            }

            .banner-content p {
                font-size: 18px;
            }

            .feature {
                font-size: 16px;
                padding: 14px 16px;
            }

            .form-side {
                padding: 22px 28px;
            }
        }

        @media (max-width: 1024px) {
            body {
                background: #ffffff;
            }

            .auth-page {
                display: block;
            }

            .banner {
                display: flex;
                min-height: auto;
                padding: 32px 24px;
                border-radius: 0 0 28px 28px;
            }

            .banner-content {
                max-width: 760px;
                width: 100%;
                text-align: center;
            }

            .banner-content h1 {
                font-size: 34px;
                margin-bottom: 10px;
            }

            .banner-content p {
                font-size: 16px;
                margin-bottom: 18px;
            }

            .features {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .feature {
                justify-content: flex-start;
                text-align: right;
                font-size: 14px;
                padding: 12px;
                border-radius: 16px;
            }

            .feature i {
                width: 36px;
                height: 36px;
                border-radius: 12px;
                flex-shrink: 0;
            }

            .form-side {
                min-height: auto;
                max-height: none;
                padding: 24px 18px 34px;
                align-items: flex-start;
                box-shadow: none;
            }

            .auth-card {
                max-width: 560px;
                margin: 0 auto;
            }
        }

        @media (max-width: 720px) {
            .banner {
                padding: 26px 16px;
            }

            .banner-content h1 {
                font-size: 28px;
            }

            .banner-content p {
                font-size: 14px;
                line-height: 1.7;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .feature {
                font-size: 13px;
            }

            .form-side {
                padding: 20px 14px 30px;
            }
        }

        @media (max-width: 520px) {
            .banner {
                display: none;
            }

            .form-side {
                min-height: 100vh;
                padding: 18px 13px 26px;
            }

            .auth-card {
                max-width: 100%;
                padding-bottom: 18px;
            }

            .logo {
                width: 78px;
                height: 78px;
                border-radius: 22px;
                margin-bottom: 12px;
            }

            .logo strong {
                font-size: 23px;
            }

            .logo span {
                font-size: 10px;
            }

            .title {
                font-size: 27px;
                margin-bottom: 6px;
            }

            .subtitle {
                font-size: 13px;
                line-height: 1.6;
                margin-bottom: 16px;
            }

            .form-group label {
                font-size: 13px;
            }

            .input-box input {
                height: 50px;
                padding: 0 45px 0 42px;
                font-size: 14px;
            }

            .input-box .main-icon {
                right: 16px;
                top: 16px;
                font-size: 14px;
            }

            .toggle-password {
                left: 10px;
                top: 10px;
            }

            .submit-btn {
                min-height: 50px;
                border-radius: 15px;
            }

            .secondary-box {
                font-size: 12px;
            }
        }

        @media (orientation: landscape) and (max-height: 520px) and (max-width: 900px) {
            .banner,
            .logo {
                display: none;
            }

            .form-side {
                min-height: 100vh;
                padding-top: 12px;
                padding-bottom: 20px;
            }

            .title {
                font-size: 24px;
            }

            .subtitle {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="auth-page">

    <section class="banner">
        <div class="banner-content">
            <h1>إعادة تعيين كلمة المرور</h1>

            <p>
                أدخل البريد الإلكتروني وكلمة المرور الجديدة لإكمال عملية إعادة تعيين كلمة المرور.
            </p>

            <div class="features">
                <div class="feature">
                    <i class="fas fa-envelope"></i>
                    التحقق من البريد الإلكتروني
                </div>

                <div class="feature">
                    <i class="fas fa-lock"></i>
                    إنشاء كلمة مرور جديدة
                </div>

                <div class="feature">
                    <i class="fas fa-shield-halved"></i>
                    حماية حساب المستخدم
                </div>

                <div class="feature">
                    <i class="fas fa-right-to-bracket"></i>
                    العودة لتسجيل الدخول
                </div>
            </div>
        </div>
    </section>

    <section class="form-side">
        <div class="auth-card">

            <div class="logo">
                <strong>ES</strong>
                <span>HR SYSTEM</span>
            </div>

            <a href="{{ route('unified-login') }}" class="back-link">
                <i class="fas fa-right-to-bracket"></i>
                الرجوع لصفحة الدخول الموحدة
            </a>

            <h1 class="title">إعادة تعيين كلمة المرور</h1>
            <p class="subtitle">أدخل كلمة المرور الجديدة لحسابك</p>

            @if(session('status'))
                <div class="message success">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="message error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>

                        <div class="input-box">
                            <i class="fas fa-envelope main-icon"></i>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $request->email) }}"
                                placeholder="example@email.com"
                                autocomplete="username"
                                required
                                autofocus>
                        </div>

                        @error('email')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة المرور الجديدة</label>

                        <div class="input-box">
                            <i class="fas fa-lock main-icon"></i>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="أدخل كلمة المرور الجديدة"
                                autocomplete="new-password"
                                required>

                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        @error('password')
                        <div class="field-error">{{ $message }}</div>
                        @enderror

                        <div class="field-note">يفضل أن تحتوي كلمة المرور على أحرف وأرقام ورموز.</div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">تأكيد كلمة المرور الجديدة</label>

                        <div class="input-box">
                            <i class="fas fa-lock main-icon"></i>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="أعد إدخال كلمة المرور الجديدة"
                                autocomplete="new-password"
                                required>

                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        @error('password_confirmation')
                        <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="submit-btn">
                        حفظ كلمة المرور الجديدة
                        <i class="fas fa-arrow-left" style="margin-right:8px;"></i>
                    </button>
                </div>
            </form>

            <div class="secondary-box">
                تذكرت كلمة المرور؟
                <a href="{{ route('unified-login') }}">العودة لتسجيل الدخول</a>
            </div>

        </div>
    </section>

</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);

        if (!input) {
            return;
        }

        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
</html>
