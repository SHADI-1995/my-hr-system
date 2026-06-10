<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | ENG-SHADI HR</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #eef2ff;
            min-height: 100vh;
            overflow: hidden;
        }

        .login-page {
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
            margin-bottom: 20px;
        }

        .banner-content p {
            font-size: 22px;
            line-height: 1.8;
            margin-bottom: 35px;
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
            align-items: center;
            justify-content: center;
            padding: 45px;
            box-shadow: 0 0 35px rgba(80,70,160,.12);
        }

        .login-card {
            width: 100%;
            max-width: 430px;
        }

        .logo {
            width: 110px;
            height: 110px;
            margin: 0 auto 25px;
            border-radius: 28px;
            background: linear-gradient(135deg, #6676de, #7b5cc8);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(103,91,205,.35);
        }

        .logo strong {
            font-size: 28px;
        }

        .logo span {
            font-size: 12px;
            margin-top: 5px;
        }

        .title {
            text-align: center;
            font-size: 36px;
            color: #111827;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 35px;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
        }

        .input-box input {
            width: 100%;
            height: 58px;
            border: 1px solid #d8d6ff;
            border-radius: 16px;
            padding: 0 50px 0 15px;
            background: #f8f9ff;
            outline: none;
            font-size: 15px;
        }

        .input-box input:focus {
            border-color: #6676de;
            box-shadow: 0 0 0 4px rgba(102,118,222,.12);
        }

        .input-box i {
            position: absolute;
            right: 18px;
            top: 20px;
            color: #6d5bd0;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4b5563;
            margin-bottom: 22px;
        }

        .remember input {
            width: 18px;
            height: 18px;
        }

        .login-btn {
            width: 100%;
            height: 58px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #6676de, #6d45c5);
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 15px 30px rgba(103,91,205,.35);
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
        }

        @media (max-width: 900px) {
            body { overflow: auto; }

            .login-page {
                grid-template-columns: 1fr;
            }

            .banner {
                display: none;
            }

            .form-side {
                min-height: 100vh;
            }
        }
    </style>
</head>
<body>

<div class="login-page">

    <section class="banner">
        <div class="banner-content">
            <h1>ENG-SHADI HR</h1>

            <p>
            </p>

            <div class="features">
                <div class="feature">
                    <i class="fas fa-users"></i>
                    إدارة الموظفين والأقسام
                </div>

                <div class="feature">
                    <i class="fas fa-clock"></i>
                    متابعة الحضور والانصراف
                </div>

                <div class="feature">
                    <i class="fas fa-calendar-days"></i>
                    إدارة الإجازات والطلبات
                </div>

                <div class="feature">
                    <i class="fas fa-money-bill-wave"></i>
                    إدارة الرواتب والمستحقات
                </div>

                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    تقارير وإحصائيات تفصيلية
                </div>
            </div>
        </div>
    </section>

    <section class="form-side">
        <div class="login-card">

            <div class="logo">
                <strong>ES</strong>
                <span>HR SYSTEM</span>
            </div>

            <h1 class="title">تسجيل الدخول</h1>
            <p class="subtitle">أدخل بياناتك للوصول إلى نظام الموارد البشرية</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label>اسم المستخدم أو البريد الإلكتروني</label>

                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <input
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="أدخل اسم المستخدم أو البريد الإلكتروني"
                            required
                            autofocus>
                    </div>

                    @error('email')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>كلمة المرور</label>

                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            name="password"
                            placeholder="أدخل كلمة المرور"
                            required>
                    </div>

                    @error('password')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <label class="remember">
                    <input type="checkbox" name="remember">
                    تذكرني
                </label>

                <button type="submit" class="login-btn">
                    تسجيل الدخول
                </button>
            </form>

        </div>
    </section>

</div>

</body>
</html>
