<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'بوابة الموظف')</title>
    <style>
        *{box-sizing:border-box;font-family:Tahoma,Arial,sans-serif}
        body{margin:0;background:#f5f6fb;color:#111827}
        a{text-decoration:none}
        .portal-wrapper{min-height:100vh;display:grid;grid-template-columns:330px 1fr}
        .portal-side{background:linear-gradient(160deg,#4c3b91,#7c3aed);color:#fff;padding:30px;display:flex;flex-direction:column;justify-content:space-between}
        .portal-brand{display:flex;align-items:center;gap:12px}
        .portal-brand-icon{width:52px;height:52px;border-radius:18px;background:rgba(255,255,255,.16);display:inline-flex;align-items:center;justify-content:center;font-weight:900}
        .portal-brand h1{margin:0 0 4px;font-size:22px;font-weight:900}
        .portal-brand p{margin:0;font-size:13px;opacity:.85;font-weight:700}
        .portal-side-card{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);border-radius:24px;padding:22px;margin-top:26px}
        .portal-side-card h2{margin:0 0 10px;font-size:21px}
        .portal-side-card p{margin:0;line-height:1.8;opacity:.92;font-size:14px;font-weight:700}
        .portal-content{padding:28px;overflow-x:hidden}
        .portal-topbar{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:18px 22px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 16px 40px rgba(76,59,145,.07)}
        .portal-title h2{margin:0 0 5px;color:#4c3b91;font-size:23px;font-weight:900}
        .portal-title p{margin:0;color:#6b7280;font-weight:700;font-size:13px}
        .portal-card{background:#fff;border:1px solid #eeeafc;border-radius:24px;padding:24px;box-shadow:0 16px 40px rgba(76,59,145,.07);margin-bottom:20px}
        .portal-btn{height:46px;border:none;border-radius:15px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:8px;background:#6d5bd0;color:#fff;font-weight:900;cursor:pointer;box-shadow:0 12px 28px rgba(109,91,208,.22)}
        .portal-btn.secondary{background:#f3f4f6;color:#374151;box-shadow:none}
        .portal-btn.danger{background:#fee2e2;color:#991b1b;box-shadow:none}
        .alert{padding:14px 16px;border-radius:16px;margin-bottom:18px;font-weight:800;line-height:1.7}
        .alert-success{background:#ecfdf5;color:#166534;border:1px solid #bbf7d0}
        .alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
        .form-group.full{grid-column:1/-1}
        .form-group label{display:block;margin-bottom:8px;color:#4c3b91;font-weight:900;font-size:13px}
        .form-group input,.form-group select,.form-group textarea{width:100%;border:1px solid #ddd6fe;border-radius:15px;padding:0 13px;outline:none;font-size:14px;font-weight:800;color:#111827;background:#fff}
        .form-group input,.form-group select{height:48px}
        .form-group textarea{min-height:115px;padding-top:12px;line-height:1.8;resize:vertical}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#6d5bd0;box-shadow:0 0 0 4px rgba(109,91,208,.12)}
        .field-error{color:#dc2626;font-size:12px;font-weight:800;margin-top:7px}
        .status-pill{display:inline-flex;padding:6px 11px;border-radius:999px;font-size:12px;font-weight:900;line-height:1.5}
        .status-pending{background:#fef3c7;color:#92400e}
        .status-processing{background:#dbeafe;color:#1d4ed8}
        .status-approved{background:#dcfce7;color:#166534}
        .status-rejected{background:#fee2e2;color:#991b1b}
        .status-cancelled{background:#e5e7eb;color:#374151}
        @media(max-width:950px){.portal-wrapper{grid-template-columns:1fr}.portal-side{border-radius:0 0 28px 28px}}
        @media(max-width:650px){.portal-content{padding:16px}.portal-topbar{flex-direction:column;align-items:stretch}.form-grid{grid-template-columns:1fr}.portal-btn{width:100%}}
    </style>
</head>
<body>
<div class="portal-wrapper">
    <aside class="portal-side">
        <div>
            <div class="portal-brand">
                <div class="portal-brand-icon">HR</div>
                <div>
                    <h1>بوابة الموظف</h1>
                    <p>الخدمات الذاتية للموارد البشرية</p>
                </div>
            </div>
            <div class="portal-side-card">
                <h2>إدارة الإجازات</h2>
                <p>يمكنك تقديم طلب إجازة ومتابعة حالة الطلب من المدير المباشر ثم الموارد البشرية.</p>
            </div>
        </div>
        <div style="opacity:.8;font-size:12px;line-height:1.8">يعمل على الجوال، الآيباد، واللابتوب.</div>
    </aside>
    <main class="portal-content">@yield('content')</main>
</div>
</body>
</html>

