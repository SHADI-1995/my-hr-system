<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول الموظف | ENG-SHADI HR</title>

    <meta http-equiv="refresh" content="0; url={{ route('unified-login', ['account' => 'employee']) }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(135deg, rgba(76, 87, 214, .92), rgba(112, 64, 180, .92)),
                #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            color: #111827;
        }

        .redirect-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 28px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(31, 41, 55, .22);
            border: 1px solid rgba(255, 255, 255, .35);
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
            box-shadow: 0 15px 35px rgba(103, 91, 205, .35);
        }

        .logo strong {
            font-size: 26px;
            font-weight: 900;
        }

        .logo span {
            font-size: 11px;
            margin-top: 5px;
            font-weight: 800;
        }

        h1 {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        p {
            color: #6b7280;
            font-weight: 800;
            line-height: 1.8;
            margin-bottom: 18px;
        }

        .loading {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #6d45c5;
            font-weight: 900;
            margin-bottom: 18px;
        }

        .spinner {
            width: 22px;
            height: 22px;
            border: 3px solid #e0e7ff;
            border-top-color: #6d45c5;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn {
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #6676de, #6d45c5);
            color: white;
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 15px 30px rgba(103, 91, 205, .35);
        }

        .btn i {
            margin-right: 8px;
        }

        @media (max-width: 520px) {
            .redirect-card {
                border-radius: 22px;
                padding: 26px 18px;
            }

            .logo {
                width: 78px;
                height: 78px;
                border-radius: 22px;
            }

            h1 {
                font-size: 24px;
            }

            p {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<div class="redirect-card">
    <div class="logo">
        <strong>ES</strong>
        <span>HR SYSTEM</span>
    </div>

    <h1>دخول الموظف</h1>

    <p>
        يتم تحويلك الآن إلى صفحة تسجيل الدخول الموحدة على تبويب الموظف.
    </p>

    <div class="loading">
        <span class="spinner"></span>
        <span>جاري التحويل...</span>
    </div>

    <a href="{{ route('unified-login', ['account' => 'employee']) }}" class="btn">
        الذهاب إلى صفحة الدخول الموحدة
        <i class="fas fa-arrow-left"></i>
    </a>
</div>

<script>
    window.location.replace("{{ route('unified-login', ['account' => 'employee']) }}");
</script>

</body>
</html>
