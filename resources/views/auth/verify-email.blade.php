<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد البريد الإلكتروني | ENG-SHADI HR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Tahoma,Arial,sans-serif;background:#eef2ff;min-height:100vh;overflow:auto;color:#111827}
        .auth-page{min-height:100vh;display:grid;grid-template-columns:58% 42%;direction:rtl}
        .banner{background:linear-gradient(135deg,rgba(76,87,214,.92),rgba(112,64,180,.92)),url('/images/hr-login.png');background-size:cover;background-position:center;color:#fff;padding:70px;display:flex;align-items:center;justify-content:center}
        .banner-content{max-width:620px}.banner-content h1{font-size:48px;margin-bottom:18px;font-weight:900}.banner-content p{font-size:21px;line-height:1.9;margin-bottom:34px}
        .features{display:grid;gap:15px}.feature{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.2);backdrop-filter:blur(10px);border-radius:18px;padding:16px 20px;display:flex;align-items:center;gap:14px;font-size:18px;font-weight:bold}
        .feature i{width:42px;height:42px;border-radius:14px;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center}
        .form-side{background:#fff;display:flex;align-items:flex-start;justify-content:center;padding:24px 38px;box-shadow:0 0 35px rgba(80,70,160,.12);overflow-y:auto;max-height:100vh}
        .auth-card{width:100%;max-width:460px;padding-bottom:28px}.logo{width:92px;height:92px;margin:0 auto 16px;border-radius:26px;background:linear-gradient(135deg,#6676de,#7b5cc8);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 15px 35px rgba(103,91,205,.35)}
        .logo strong{font-size:26px}.logo span{font-size:11px;margin-top:5px}
        .back-link{display:inline-flex;align-items:center;justify-content:center;width:100%;min-height:46px;border-radius:14px;background:#f8f9ff;border:1px solid #e0e7ff;color:#6d45c5;text-decoration:none;font-weight:900;margin-bottom:14px;padding:12px}.back-link i{margin-left:8px}
        .title{text-align:center;font-size:30px;margin-bottom:7px;font-weight:900}.subtitle{text-align:center;color:#6b7280;margin-bottom:20px;line-height:1.7;font-weight:800}
        .message{border-radius:15px;padding:12px 14px;margin-bottom:14px;font-weight:800;line-height:1.8}.message.success{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0}
        .verify-box{border:1px solid #e0e7ff;background:#f8f9ff;border-radius:20px;padding:18px;margin-bottom:16px;text-align:center}
        .verify-icon{width:64px;height:64px;border-radius:20px;background:#edf4ff;color:#6676de;display:inline-flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:12px}
        .verify-box h3{font-size:18px;font-weight:900;margin-bottom:8px}.verify-box p{color:#6b7280;line-height:1.9;font-weight:800;font-size:14px}
        .actions{display:grid;gap:12px}.submit-btn{width:100%;min-height:54px;border:none;border-radius:16px;background:linear-gradient(135deg,#6676de,#6d45c5);color:#fff;font-size:16px;font-weight:900;cursor:pointer;box-shadow:0 15px 30px rgba(103,91,205,.35);padding:12px}
        .logout-btn{width:100%;min-height:50px;border:1px solid #fecaca;border-radius:16px;background:#fef2f2;color:#991b1b;font-size:15px;font-weight:900;cursor:pointer;padding:12px}
        .secondary-box{margin-top:14px;border:1px solid #e0e7ff;background:#f8f9ff;border-radius:16px;padding:13px;color:#6b7280;font-weight:800;line-height:1.8;text-align:center}.secondary-box a{color:#6d45c5;text-decoration:none;font-weight:900}
        @media(max-width:1024px){body{background:#fff}.auth-page{display:block}.banner{padding:32px 24px;border-radius:0 0 28px 28px}.banner-content{max-width:760px;width:100%;text-align:center}.banner-content h1{font-size:34px;margin-bottom:10px}.banner-content p{font-size:16px;margin-bottom:18px}.features{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.feature{font-size:14px;padding:12px;border-radius:16px}.form-side{min-height:auto;max-height:none;padding:24px 18px 34px;box-shadow:none}.auth-card{max-width:560px;margin:0 auto}}
        @media(max-width:720px){.banner{padding:26px 16px}.banner-content h1{font-size:28px}.banner-content p{font-size:14px}.features{grid-template-columns:1fr}.feature{font-size:13px}.form-side{padding:20px 14px 30px}}
        @media(max-width:520px){.banner{display:none}.form-side{min-height:100vh;padding:18px 13px 26px}.auth-card{max-width:100%}.logo{width:78px;height:78px;border-radius:22px;margin-bottom:12px}.logo strong{font-size:23px}.title{font-size:27px}.subtitle{font-size:13px}.verify-box p,.secondary-box{font-size:12px}.submit-btn{min-height:50px;border-radius:15px}}
    </style>
</head>
<body>
<div class="auth-page">
    <section class="banner">
        <div class="banner-content">
            <h1>تأكيد البريد الإلكتروني</h1>
            <p>قبل الدخول إلى النظام، يرجى تأكيد بريدك الإلكتروني. يمكنك إعادة إرسال رابط التحقق إذا لم يصلك البريد.</p>
            <div class="features">
                <div class="feature"><i class="fas fa-envelope-circle-check"></i>التحقق من البريد الإلكتروني</div>
                <div class="feature"><i class="fas fa-shield-halved"></i>حماية حساب المستخدم</div>
                <div class="feature"><i class="fas fa-paper-plane"></i>إعادة إرسال رابط التحقق</div>
                <div class="feature"><i class="fas fa-right-to-bracket"></i>الرجوع لتسجيل الدخول</div>
            </div>
        </div>
    </section>

    <section class="form-side">
        <div class="auth-card">
            <div class="logo"><strong>ES</strong><span>HR SYSTEM</span></div>

            <a href="{{ route('unified-login') }}" class="back-link">
                <i class="fas fa-right-to-bracket"></i>
                الرجوع لصفحة الدخول الموحدة
            </a>

            <h1 class="title">تأكيد البريد الإلكتروني</h1>
            <p class="subtitle">تم إرسال رابط تحقق إلى بريدك الإلكتروني المسجل</p>

            @if (session('status') == 'verification-link-sent')
                <div class="message success">
                    تم إرسال رابط تحقق جديد إلى البريد الإلكتروني الذي استخدمته أثناء التسجيل.
                </div>
            @endif

            <div class="verify-box">
                <div class="verify-icon"><i class="fas fa-envelope-open-text"></i></div>
                <h3>تحقق من بريدك الإلكتروني</h3>
                <p>
                    شكرًا لتسجيلك في النظام. قبل المتابعة، افتح بريدك الإلكتروني واضغط على رابط التحقق.
                    إذا لم يصلك الرابط، يمكنك طلب إرسال رابط جديد.
                </p>
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="submit-btn">
                        إعادة إرسال رابط التحقق
                        <i class="fas fa-paper-plane" style="margin-right:8px;"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        تسجيل الخروج والعودة للدخول
                        <i class="fas fa-arrow-left" style="margin-right:8px;"></i>
                    </button>
                </form>
            </div>

            <div class="secondary-box">
                بعد تأكيد البريد، يمكنك الرجوع إلى
                <a href="{{ route('unified-login') }}">صفحة الدخول الموحدة</a>
                وتسجيل الدخول إلى حسابك.
            </div>
        </div>
    </section>
</div>
</body>
</html>
