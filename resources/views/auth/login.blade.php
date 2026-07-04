<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | ENG-SHADI HR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Tahoma,Arial,sans-serif;background:#eef2ff;min-height:100vh;overflow:auto;color:#111827}
        .login-page{min-height:100vh;display:grid;grid-template-columns:58% 42%;direction:rtl}
        .banner{background:linear-gradient(135deg,rgba(76,87,214,.92),rgba(112,64,180,.92)),url('/images/hr-login.png');background-size:cover;background-position:center;color:#fff;padding:70px;display:flex;align-items:center;justify-content:center}
        .banner-content{max-width:620px}.banner-content h1{font-size:48px;margin-bottom:20px;font-weight:900}.banner-content p{font-size:21px;line-height:1.8;margin-bottom:35px}
        .features{display:grid;gap:15px}.feature{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.2);backdrop-filter:blur(10px);border-radius:18px;padding:16px 20px;display:flex;align-items:center;gap:14px;font-size:18px;font-weight:bold}
        .feature i{width:42px;height:42px;border-radius:14px;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center}
        .form-side{background:#fff;display:flex;align-items:flex-start;justify-content:center;padding:22px 38px;box-shadow:0 0 35px rgba(80,70,160,.12);overflow-y:auto;max-height:100vh}
        .login-card{width:100%;max-width:460px;padding-bottom:28px}.logo{width:92px;height:92px;margin:0 auto 16px;border-radius:28px;background:linear-gradient(135deg,#6676de,#7b5cc8);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 15px 35px rgba(103,91,205,.35)}
        .logo strong{font-size:26px}.logo span{font-size:11px;margin-top:5px}.title{text-align:center;font-size:30px;margin-bottom:6px;font-weight:900}.subtitle{text-align:center;color:#6b7280;margin-bottom:18px;line-height:1.6;font-weight:700}
        .account-label{display:block;font-weight:900;color:#374151;margin-bottom:10px}.account-type-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:16px}
        .account-type{border:2px solid #e0e7ff;background:#f8f9ff;border-radius:18px;padding:12px;min-height:68px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:.2s ease;position:relative;color:#111827}
        .account-type:hover{border-color:rgba(102,118,222,.55);transform:translateY(-1px)}.account-type.active{border-color:#3478f6;background:#edf4ff;box-shadow:0 14px 30px rgba(52,120,246,.13)}
        .account-type .check{position:absolute;top:9px;left:9px;width:23px;height:23px;border-radius:50%;background:#3478f6;color:#fff;display:none;align-items:center;justify-content:center;font-size:12px}.account-type.active .check{display:flex}
        .account-text strong{display:block;font-size:16px;font-weight:900;margin-bottom:5px}.account-text span{color:#6b7280;font-size:12px;font-weight:800}
        .account-icon{width:44px;height:44px;border-radius:15px;background:#3478f6;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
        .employee-tabs{display:none;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:0 0 14px}.employee-tabs.show{display:grid}
        .employee-tab{border:1px solid #dbe3ff;background:#fff;color:#6b7280;border-radius:15px;height:44px;cursor:pointer;font-weight:900;transition:.2s ease}.employee-tab.active{background:linear-gradient(135deg,#6676de,#6d45c5);color:#fff;border-color:transparent;box-shadow:0 12px 24px rgba(103,91,205,.22)}
        .panel{display:none}.panel.active{display:block;animation:fadeUp .2s ease}@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .form-group{margin-bottom:14px}label{display:block;font-weight:bold;color:#374151;margin-bottom:8px}.input-box{position:relative}
        .input-box input{width:100%;height:52px;border:1px solid #d8d6ff;border-radius:16px;padding:0 50px 0 46px;background:#f8f9ff;outline:none;font-size:15px;font-weight:800}
        .input-box input:focus{border-color:#6676de;box-shadow:0 0 0 4px rgba(102,118,222,.12);background:#fff}.input-box .main-icon{position:absolute;right:18px;top:17px;color:#6d5bd0}
        .toggle-password{position:absolute;left:14px;top:11px;width:30px;height:30px;border:0;background:transparent;color:#64748b;cursor:pointer;font-size:15px}
        .remember-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px}.remember{display:flex;align-items:center;gap:8px;color:#4b5563;margin:0;font-weight:800}.remember input{width:18px;height:18px}
        .forgot-link{color:#6d45c5;text-decoration:none;font-size:13px;font-weight:900}.login-btn{width:100%;height:54px;border:none;border-radius:16px;background:linear-gradient(135deg,#6676de,#6d45c5);color:#fff;font-size:18px;font-weight:bold;cursor:pointer;box-shadow:0 15px 30px rgba(103,91,205,.35);transition:.2s ease}
        .login-btn:hover{transform:translateY(-1px);box-shadow:0 18px 38px rgba(103,91,205,.42)}.secondary-box{margin-top:12px;border:1px solid #e0e7ff;background:#f8f9ff;border-radius:16px;padding:14px;color:#6b7280;font-weight:800;line-height:1.8;text-align:center}.secondary-box a{color:#6d45c5;text-decoration:none;font-weight:900}
        .register-card{border:1px solid #e0e7ff;background:#f8f9ff;border-radius:20px;padding:18px;text-align:center}.register-card i{width:58px;height:58px;border-radius:18px;background:#edf4ff;color:#3478f6;display:inline-flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:12px}.register-card h3{margin-bottom:8px;font-size:18px;font-weight:900}.register-card p{color:#6b7280;line-height:1.8;font-weight:800;font-size:13px;margin-bottom:14px}
        .register-link-btn{display:inline-flex;align-items:center;justify-content:center;width:100%;height:54px;border-radius:15px;background:linear-gradient(135deg,#6676de,#6d45c5);color:#fff;text-decoration:none;font-weight:900;box-shadow:0 12px 26px rgba(103,91,205,.25)}
        .error{color:#dc2626;font-size:13px;margin-top:8px;font-weight:800}.status-message{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0;border-radius:14px;padding:12px 14px;margin-bottom:16px;font-weight:800;line-height:1.7}
        @media(max-width:900px){body{overflow:auto}.login-page{grid-template-columns:1fr}.banner{display:none}.form-side{min-height:100vh;padding:25px 16px}}
        @media(max-width:480px){.title{font-size:29px}.account-type-grid{grid-template-columns:1fr}.logo{width:94px;height:94px;border-radius:24px}}

        @media(max-height:780px){
            .form-side{padding-top:14px;padding-bottom:14px}
            .logo{width:78px;height:78px;border-radius:22px;margin-bottom:10px}
            .logo strong{font-size:23px}
            .title{font-size:26px}
            .subtitle{margin-bottom:12px;font-size:13px}
            .account-type{min-height:62px;padding:10px}
            .account-icon{width:40px;height:40px}
            .input-box input{height:48px}
            .input-box .main-icon{top:15px}
            .toggle-password{top:9px}
            .login-btn{height:50px}
            .secondary-box{font-size:12px;padding:10px}
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive - Tablet & Mobile
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1180px) {
            .login-page {
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
                overflow: auto;
                background: #ffffff;
            }

            .login-page {
                min-height: 100vh;
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

            .login-card {
                max-width: 560px;
                margin: 0 auto;
                background: #fff;
            }

            .logo {
                width: 86px;
                height: 86px;
                border-radius: 24px;
                margin-bottom: 14px;
            }

            .title {
                font-size: 30px;
            }

            .subtitle {
                font-size: 14px;
                margin-bottom: 18px;
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

            .account-type-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .account-type {
                min-height: 64px;
                border-radius: 16px;
            }

            .account-icon {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                font-size: 18px;
            }

            .employee-tabs {
                grid-template-columns: 1fr 1fr;
            }

            .input-box input {
                height: 52px;
                font-size: 14px;
                border-radius: 15px;
            }

            .login-btn {
                height: 52px;
                font-size: 16px;
            }

            .secondary-box {
                font-size: 12px;
                padding: 11px;
            }
        }

        @media (max-width: 520px) {
            body {
                background: #ffffff;
            }

            .banner {
                display: none;
            }

            .form-side {
                min-height: 100vh;
                padding: 18px 13px 26px;
            }

            .login-card {
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

            .account-label {
                font-size: 13px;
            }

            .account-type {
                padding: 11px 12px;
            }

            .account-text strong {
                font-size: 15px;
            }

            .account-text span {
                font-size: 11px;
            }

            .employee-tab {
                height: 42px;
                font-size: 13px;
            }

            .form-group {
                margin-bottom: 13px;
            }

            label {
                font-size: 13px;
            }

            .input-box input {
                height: 50px;
                padding: 0 45px 0 42px;
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

            .remember-row {
                align-items: flex-start;
                gap: 9px;
                margin-bottom: 13px;
            }

            .remember,
            .forgot-link {
                font-size: 12px;
            }

            .login-btn {
                height: 50px;
                border-radius: 15px;
            }

            .register-card {
                padding: 15px;
                border-radius: 18px;
            }

            .register-card i {
                width: 50px;
                height: 50px;
                font-size: 21px;
            }

            .register-link-btn {
                height: 50px;
            }
        }

        @media (max-width: 380px) {
            .form-side {
                padding-inline: 10px;
            }

            .title {
                font-size: 24px;
            }

            .subtitle {
                font-size: 12px;
            }

            .input-box input {
                font-size: 13px;
            }
        }

        @media (orientation: landscape) and (max-height: 520px) and (max-width: 900px) {
            .banner {
                display: none;
            }

            .form-side {
                min-height: 100vh;
                padding-top: 12px;
                padding-bottom: 20px;
            }

            .logo {
                display: none;
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
<div class="login-page">
    <section class="banner">
        <div class="banner-content">
            <h1>ENG-SHADI HR</h1>
            <p>منصة موحدة لإدارة الموظفين، الحضور، الإجازات، الرواتب، وطلبات الموظفين من مكان واحد.</p>
            <div class="features">
                <div class="feature"><i class="fas fa-users"></i>إدارة الموظفين والأقسام</div>
                <div class="feature"><i class="fas fa-clock"></i>متابعة الحضور والانصراف</div>
                <div class="feature"><i class="fas fa-calendar-days"></i>إدارة الإجازات والطلبات</div>
                <div class="feature"><i class="fas fa-money-bill-wave"></i>إدارة الرواتب والمستحقات</div>
                <div class="feature"><i class="fas fa-chart-line"></i>تقارير وإحصائيات تفصيلية</div>
            </div>
        </div>
    </section>

    <section class="form-side">
        <div class="login-card">
            <div class="logo"><strong>ES</strong><span>HR SYSTEM</span></div>
            <h1 class="title">تسجيل الدخول</h1>
            <p class="subtitle" id="loginSubtitle">اختر نوع الحساب ثم أدخل بياناتك للمتابعة</p>

            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif

            <span class="account-label">نوع الحساب</span>
            <div class="account-type-grid">
                <button type="button" class="account-type active" data-account="admin">
                    <span class="check"><i class="fas fa-check"></i></span>
                    <span class="account-text"><strong>أدمن</strong><span>لوحة التحكم</span></span>
                    <span class="account-icon"><i class="fas fa-users-gear"></i></span>
                </button>
                <button type="button" class="account-type" data-account="employee">
                    <span class="check"><i class="fas fa-check"></i></span>
                    <span class="account-text"><strong>موظف</strong><span>بوابة الموظف</span></span>
                    <span class="account-icon"><i class="fas fa-id-card"></i></span>
                </button>
            </div>

            <div id="employeeTabs" class="employee-tabs">
                <button type="button" class="employee-tab active" data-tab="login">لدي حساب</button>
                <button type="button" class="employee-tab" data-tab="register">تسجيل جديد</button>
            </div>

            <form id="adminPanel" class="panel active" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>اسم المستخدم أو رقم الإقامة</label>
                    <div class="input-box">
                        <i class="fas fa-user main-icon"></i>
                        <input type="text" name="email" value="{{ old('email') }}" placeholder="أدخل اسم المستخدم أو البريد الإلكتروني" required autofocus>
                    </div>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>كلمة المرور</label>
                    <div class="input-box">
                        <i class="fas fa-lock main-icon"></i>
                        <input id="adminPassword" type="password" name="password" placeholder="أدخل كلمة المرور" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('adminPassword')"><i class="fas fa-eye"></i></button>
                    </div>
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="remember-row">
                    <label class="remember"><input type="checkbox" name="remember">تذكرني</label>
                    @if (Route::has('password.request'))
                    @endif
                </div>

                <button type="submit" class="login-btn">تسجيل الدخول <i class="fas fa-arrow-left" style="margin-right:8px;"></i></button>
                <div class="secondary-box">هذا الدخول مخصص للأدمن ومستخدمي النظام مثل الموارد البشرية، المديرين، والمحاسبين حسب الصلاحيات.</div>
            </form>

            <form id="employeeLoginPanel" class="panel" method="POST" action="{{ route('employee-portal.login.store') }}">
                @csrf
                <div class="form-group">
                    <label>رقم الإقامة</label>

                    <div class="input-box">
                        <i class="fas fa-id-card main-icon"></i>
                        <input
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            name="iqama_number"
                            value="{{ old('iqama_number') }}"
                            placeholder="أدخل رقم الإقامة"
                            autocomplete="off"
                            required>
                    </div>

                    @error('iqama_number')
                    <div class="error">{{ $message }}</div>
                    @enderror

                    <div style="color:#6b7280;font-size:12px;font-weight:800;margin-top:7px;">
                        أدخل رقم الإقامة بالأرقام فقط.
                    </div>
                </div>

                <div class="form-group">
                    <label>كلمة المرور</label>
                    <div class="input-box">
                        <i class="fas fa-lock main-icon"></i>
                        <input id="employeePassword" type="password" name="password" placeholder="أدخل كلمة المرور" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('employeePassword')"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="remember"><input type="checkbox" name="remember">تذكرني</label>
                    @if (Route::has('employee-portal.forgot-password'))
                        <a class="forgot-link" href="{{ route('employee-portal.forgot-password') }}">نسيت كلمة المرور؟</a>
                    @endif
                </div>

                <button type="submit" class="login-btn">دخول بوابة الموظف <i class="fas fa-arrow-left" style="margin-right:8px;"></i></button>
                <div class="secondary-box">ليس لديك حساب؟ <a href="#" onclick="switchEmployeeTab('register'); return false;">تسجيل موظف جديد</a></div>
            </form>

            <div id="employeeRegisterPanel" class="panel">
                <div class="register-card">
                    <i class="fas fa-user-plus"></i>
                    <h3>تسجيل موظف جديد</h3>
                    <p>التسجيل يتم برقم الإقامة ثم البريد الإلكتروني وكلمة المرور، وبعدها يتم إرسال رمز تحقق للبريد.</p>
                    <a href="{{ route('employee-portal.register') }}" class="register-link-btn">الذهاب إلى تسجيل موظف جديد <i class="fas fa-arrow-left" style="margin-right:8px;"></i></a>
                </div>
                <div class="secondary-box">لديك حساب سابق؟ <a href="#" onclick="switchEmployeeTab('login'); return false;">العودة لتسجيل الدخول</a></div>
            </div>
        </div>
    </section>
</div>

<script>
    const accountButtons=document.querySelectorAll('.account-type');
    const employeeTabs=document.getElementById('employeeTabs');
    const adminPanel=document.getElementById('adminPanel');
    const employeeLoginPanel=document.getElementById('employeeLoginPanel');
    const employeeRegisterPanel=document.getElementById('employeeRegisterPanel');
    const loginSubtitle=document.getElementById('loginSubtitle');

    accountButtons.forEach(button=>{
        button.addEventListener('click',function(){
            accountButtons.forEach(item=>item.classList.remove('active'));
            this.classList.add('active');
            if(this.dataset.account==='admin'){
                employeeTabs.classList.remove('show');
                showPanel(adminPanel);
                loginSubtitle.textContent='دخول الأدمن ومستخدمي النظام حسب الصلاحيات';
            }else{
                employeeTabs.classList.add('show');
                switchEmployeeTab('login');
                loginSubtitle.textContent='دخول الموظف برقم الإقامة أو إنشاء حساب جديد في بوابة الموظف';
            }
        });
    });

    document.querySelectorAll('.employee-tab').forEach(tab=>{
        tab.addEventListener('click',function(){switchEmployeeTab(this.dataset.tab);});
    });

    function switchEmployeeTab(tab){
        document.querySelectorAll('.employee-tab').forEach(item=>item.classList.toggle('active',item.dataset.tab===tab));
        showPanel(tab==='register'?employeeRegisterPanel:employeeLoginPanel);
    }

    function showPanel(panel){
        [adminPanel,employeeLoginPanel,employeeRegisterPanel].forEach(item=>item.classList.remove('active'));
        panel.classList.add('active');
    }

    function togglePassword(inputId){
        const input=document.getElementById(inputId);
        if(input){input.type=input.type==='password'?'text':'password';}
    }
    const iqamaInput = document.querySelector('input[name="iqama_number"]');
    if (iqamaInput) {
        iqamaInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const account = params.get('account');

        if (account === 'employee') {
            const employeeButton = document.querySelector('[data-account="employee"]');
            if (employeeButton) {
                employeeButton.click();
            }
        }
    });
</script>
</body>
</html>
