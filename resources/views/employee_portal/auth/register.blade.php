<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل موظف جديد | ENG-SHADI HR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Tahoma,Arial,sans-serif;background:#eef2ff;min-height:100vh;color:#111827;overflow:auto}
        .auth-page{min-height:100vh;display:grid;grid-template-columns:52% 48%;direction:rtl}
        .banner{background:linear-gradient(135deg,rgba(76,87,214,.93),rgba(109,69,197,.93)),url('/images/hr-login.png');background-size:cover;background-position:center;color:#fff;padding:55px;display:flex;align-items:center;justify-content:center}
        .banner-content{max-width:620px}.banner-content h1{font-size:44px;margin-bottom:16px;font-weight:900}.banner-content p{font-size:19px;line-height:1.9;margin-bottom:28px;opacity:.95}
        .flow-steps{display:grid;gap:14px}.flow-step{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);backdrop-filter:blur(10px);border-radius:18px;padding:15px 18px;display:flex;align-items:center;gap:14px;font-size:16px;font-weight:900}
        .flow-step i{width:42px;height:42px;border-radius:14px;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center}
        .form-side{background:#fff;display:flex;align-items:flex-start;justify-content:center;padding:24px 38px;overflow-y:auto;max-height:100vh;box-shadow:0 0 35px rgba(80,70,160,.12)}
        .auth-card{width:100%;max-width:500px;padding-bottom:28px}.logo{width:88px;height:88px;margin:0 auto 14px;border-radius:25px;background:linear-gradient(135deg,#6676de,#7b5cc8);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 15px 35px rgba(103,91,205,.35)}
        .logo strong{font-size:25px}.logo span{font-size:10px;margin-top:5px}.title{text-align:center;font-size:30px;margin-bottom:7px;font-weight:900}.subtitle{text-align:center;color:#6b7280;margin-bottom:20px;line-height:1.7;font-weight:800}
        .message{border-radius:15px;padding:12px 14px;margin-bottom:14px;font-weight:800;line-height:1.8}.message.success{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0}.message.error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}.message.info{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
        .form-grid{display:grid;gap:14px}.form-group label{display:block;font-weight:900;color:#374151;margin-bottom:8px;font-size:14px}.input-box{position:relative}
        .input-box input{width:100%;height:52px;border:1px solid #d8d6ff;border-radius:16px;padding:0 50px 0 46px;background:#f8f9ff;outline:none;font-size:15px;font-weight:800;color:#111827}
        .input-box input:focus{border-color:#6676de;box-shadow:0 0 0 4px rgba(102,118,222,.12);background:#fff}.input-box .main-icon{position:absolute;right:18px;top:17px;color:#6d5bd0}
        .toggle-password{position:absolute;left:14px;top:11px;width:30px;height:30px;border:0;background:transparent;color:#64748b;cursor:pointer;font-size:15px}
        .field-note{color:#6b7280;font-size:12px;font-weight:800;margin-top:7px;line-height:1.7}.field-error{color:#dc2626;font-size:12px;margin-top:7px;font-weight:900;line-height:1.7}
        .email-choice-box{border:1px solid #e0e7ff;background:#f8f9ff;border-radius:18px;padding:14px}.email-choice-title{font-size:14px;font-weight:900;margin-bottom:10px;line-height:1.7}.choice-row{display:grid;gap:9px}.choice-card{display:flex;align-items:center;gap:10px;background:#fff;border:1px solid #e0e7ff;border-radius:14px;padding:11px 12px;cursor:pointer;font-weight:900;color:#374151;line-height:1.6}.choice-card input{width:18px;height:18px;accent-color:#6676de}
        .submit-btn{width:100%;height:54px;border:none;border-radius:16px;background:linear-gradient(135deg,#6676de,#6d45c5);color:#fff;font-size:17px;font-weight:900;cursor:pointer;box-shadow:0 15px 30px rgba(103,91,205,.35);transition:.2s ease}.submit-btn:hover{transform:translateY(-1px)}
        .secondary-box{margin-top:14px;border:1px solid #e0e7ff;background:#f8f9ff;border-radius:16px;padding:13px;color:#6b7280;font-weight:800;line-height:1.8;text-align:center}.secondary-box a{color:#6d45c5;text-decoration:none;font-weight:900}.quick-login-link{display:inline-flex;align-items:center;justify-content:center;width:100%;height:46px;border-radius:14px;background:#f8f9ff;border:1px solid #e0e7ff;color:#6d45c5;text-decoration:none;font-weight:900;margin-bottom:14px}
        @media(max-width:1024px){.auth-page{display:block}.banner{padding:32px 24px;border-radius:0 0 28px 28px}.banner-content{text-align:center;max-width:760px}.banner-content h1{font-size:34px;margin-bottom:10px}.banner-content p{font-size:16px;margin-bottom:18px}.flow-steps{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.flow-step{font-size:14px;padding:12px}.form-side{max-height:none;padding:24px 18px 34px;box-shadow:none}.auth-card{max-width:560px}}
        @media(max-width:640px){body{background:#fff}.banner{display:none}.form-side{min-height:100vh;padding:18px 13px 26px}.auth-card{max-width:100%}.logo{width:78px;height:78px;border-radius:22px;margin-bottom:12px}.logo strong{font-size:23px}.title{font-size:27px}.subtitle{font-size:13px;margin-bottom:16px}.input-box input{height:50px;font-size:14px;padding:0 45px 0 42px}.input-box .main-icon{right:16px;top:16px;font-size:14px}.toggle-password{left:10px;top:10px}.submit-btn{height:50px;font-size:16px}.secondary-box{font-size:12px}}
        @media(orientation:landscape) and (max-height:520px) and (max-width:900px){.banner,.logo{display:none}.form-side{min-height:100vh;padding-top:12px}.title{font-size:24px}.subtitle{margin-bottom:10px}}
    </style>
</head>
<body>
<div class="auth-page">
    <section class="banner">
        <div class="banner-content">
            <h1>بوابة الموظف</h1>
            <p>أنشئ حسابك باستخدام رقم الإقامة والبريد الإلكتروني، ثم أكّد بريدك برمز التحقق للدخول إلى خدمات الموظف.</p>
            <div class="flow-steps">
                <div class="flow-step"><i class="fas fa-id-card"></i>إدخال رقم الإقامة</div>
                <div class="flow-step"><i class="fas fa-envelope"></i>تحديد البريد الإلكتروني</div>
                <div class="flow-step"><i class="fas fa-lock"></i>إنشاء كلمة مرور</div>
                <div class="flow-step"><i class="fas fa-shield-halved"></i>التحقق من البريد</div>
            </div>
        </div>
    </section>
    <section class="form-side">
        <div class="auth-card">
            <div class="logo"><strong>ES</strong><span>HR SYSTEM</span></div>
            <a href="{{ route('unified-login', ['account' => 'employee']) }}" class="quick-login-link"><i class="fas fa-right-to-bracket" style="margin-left:8px;"></i> لدي حساب - العودة لتسجيل الدخول</a>
            <h1 class="title">تسجيل موظف جديد</h1>
            <p class="subtitle">أدخل بياناتك لإنشاء حساب في بوابة الموظف</p>
            @if(session('success'))<div class="message success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="message error">{{ session('error') }}</div>@endif
            @if(session('info'))<div class="message info">{{ session('info') }}</div>@endif
            @if(session('existing_employee_email'))
                <div class="message info">يوجد بريد إلكتروني مسجل لهذا الموظف:<br><strong>{{ session('existing_employee_email') }}</strong><br>اختر استخدام البريد الحالي أو استبداله ببريد جديد.</div>
            @endif
            <form method="POST" action="{{ route('employee-portal.register.store') }}">
                @csrf
                @if(session('existing_employee_email'))<input type="hidden" name="confirm_email_choice" value="1">@endif
                <div class="form-grid">
                    <div class="form-group">
                        <label>رقم الإقامة</label>
                        <div class="input-box">
                            <i class="fas fa-id-card main-icon"></i>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" name="iqama_number" value="{{ old('iqama_number') }}" placeholder="أدخل رقم الإقامة" autocomplete="off" required>
                        </div>
                        @error('iqama_number')<div class="field-error">{{ $message }}</div>@enderror
                        <div class="field-note">يتم مطابقة رقم الإقامة مع بيانات الموظف المسجلة في النظام.</div>
                    </div>
                    @if(session('existing_employee_email'))
                        <div class="email-choice-box">
                            <div class="email-choice-title">اختيار البريد المستخدم للتحقق</div>
                            <div class="choice-row">
                                <label class="choice-card"><input type="radio" name="email_action" value="use_existing" {{ old('email_action', 'use_existing') === 'use_existing' ? 'checked' : '' }} onchange="toggleReplacementEmail()">استخدام البريد الحالي: {{ session('existing_employee_email') }}</label>
                                <label class="choice-card"><input type="radio" name="email_action" value="replace" {{ old('email_action') === 'replace' ? 'checked' : '' }} onchange="toggleReplacementEmail()">استبدال البريد ببريد جديد</label>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="email_action" value="replace">
                    @endif
                    <div class="form-group" id="replacementEmailGroup">
                        <label>البريد الإلكتروني</label>
                        <div class="input-box">
                            <i class="fas fa-envelope main-icon"></i>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="أدخل البريد الإلكتروني" autocomplete="email" {{ session('existing_employee_email') && old('email_action', 'use_existing') !== 'replace' ? '' : 'required' }}>
                        </div>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        <div class="field-note">سيتم إرسال رمز تحقق إلى هذا البريد قبل تفعيل الحساب.</div>
                    </div>
                    <div class="form-group">
                        <label>كلمة المرور</label>
                        <div class="input-box">
                            <i class="fas fa-lock main-icon"></i>
                            <input id="password" type="password" name="password" placeholder="أدخل كلمة المرور" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')"><i class="fas fa-eye"></i></button>
                        </div>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>تأكيد كلمة المرور</label>
                        <div class="input-box">
                            <i class="fas fa-lock main-icon"></i>
                            <input id="passwordConfirmation" type="password" name="password_confirmation" placeholder="أعد إدخال كلمة المرور" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('passwordConfirmation')"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">إنشاء الحساب وإرسال رمز التحقق <i class="fas fa-arrow-left" style="margin-right:8px;"></i></button>
                </div>
            </form>
            <div class="secondary-box">لديك حساب سابق؟ <a href="{{ route('unified-login', ['account' => 'employee']) }}">لدي حساب - تسجيل الدخول</a></div>
        </div>
    </section>
</div>
<script>
    const iqamaInput=document.querySelector('input[name="iqama_number"]');
    if(iqamaInput){iqamaInput.addEventListener('input',function(){this.value=this.value.replace(/[^0-9]/g,'');});}
    function togglePassword(inputId){const input=document.getElementById(inputId);if(input){input.type=input.type==='password'?'text':'password';}}
    function toggleReplacementEmail(){
        const selectedAction=document.querySelector('input[name="email_action"]:checked');
        const emailGroup=document.getElementById('replacementEmailGroup');
        const emailInput=emailGroup?emailGroup.querySelector('input[name="email"]'):null;
        if(!selectedAction||!emailGroup||!emailInput){return;}
        if(selectedAction.value==='replace'){emailGroup.style.display='block';emailInput.required=true;}
        else{emailGroup.style.display='none';emailInput.required=false;emailInput.value='';}
    }
    document.addEventListener('DOMContentLoaded',toggleReplacementEmail);
</script>
</body>
</html>
