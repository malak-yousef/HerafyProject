<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انضم الآن - حرفي</title>

    <!-- الخطوط -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- ملف CSS الخاص بنا -->
    <link rel="stylesheet" href="{{ asset('cms/css/auth.css') }}">

    <style>
        /* ===== تحسينات التصميم فقط - بدون تغيير منطق الصفحة ===== */
        :root {
            --herafy-yellow: #ffc107;
            --herafy-yellow-dark: #e0a800;
            --herafy-dark: #1f2937;
            --herafy-muted: #6b7280;
            --herafy-bg: #f7f8fa;
            --herafy-border: #e9ecef;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background:
                radial-gradient(circle at 8% 12%, rgba(255, 193, 7, .10), transparent 28%),
                radial-gradient(circle at 92% 88%, rgba(255, 193, 7, .08), transparent 30%),
                var(--herafy-bg);
            color: var(--herafy-dark);
        }

        .navbar {
            min-height: 74px;
            border-bottom: 1px solid rgba(0, 0, 0, .04);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            letter-spacing: -.5px;
            transition: transform .25s ease;
        }

        .navbar-brand:hover {
            transform: translateY(-1px);
        }

        .navbar-brand i {
            filter: drop-shadow(0 4px 7px rgba(255, 193, 7, .25));
        }

        .nav-link {
            font-weight: 500;
            color: #4b5563 !important;
            margin: 0 4px;
            padding: 10px 13px !important;
            border-radius: 10px;
            transition: all .25s ease;
        }

        .nav-link:hover {
            color: #111827 !important;
            background: rgba(255, 193, 7, .10);
        }

        .navbar .btn {
            border-radius: 10px;
            padding: 8px 17px;
            font-weight: 700;
            transition: all .25s ease;
        }

        .navbar .btn-warning:hover {
            background: var(--herafy-yellow-dark);
            border-color: var(--herafy-yellow-dark);
            transform: translateY(-1px);
        }

        .navbar .btn-outline-warning:hover {
            color: #212529;
        }

        .auth-section {
            position: relative;
            min-height: calc(100vh - 150px);
            padding-top: 35px !important;
            padding-bottom: 35px !important;
        }

        .register-card {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, .97);
            border: 1px solid rgba(255, 193, 7, .10) !important;
            box-shadow: 0 18px 55px rgba(31, 41, 55, .10) !important;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .register-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #ffc107, #ffda6a, #ffc107);
        }

        .register-card:hover {
            box-shadow: 0 22px 65px rgba(31, 41, 55, .13) !important;
        }

        .auth-section .register-card {
            padding: 28px 30px !important;
        }

        .auth-icon {
            width: 56px !important;
            height: 56px !important;
            font-size: 1.4rem !important;
            box-shadow: 0 10px 24px rgba(255, 193, 7, .25);
            border: 5px solid #fff;
        }

        .register-card h3 {
            font-weight: 800 !important;
            letter-spacing: -.4px;
        }

        .register-card>div:first-child p {
            margin-top: 7px;
        }

        .account-type-card {
            position: relative;
            min-height: 118px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid var(--herafy-border) !important;
            cursor: pointer;
            overflow: hidden;
            transition: all .28s ease;
        }

        .account-type-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--herafy-yellow);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform .28s ease;
        }

        .account-type-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 193, 7, .65) !important;
            box-shadow: 0 10px 25px rgba(31, 41, 55, .08);
        }

        .account-type-card.active {
            border-color: var(--herafy-yellow) !important;
            background: linear-gradient(135deg, rgba(255, 193, 7, .12), rgba(255, 255, 255, 1)) !important;
            box-shadow: 0 10px 28px rgba(255, 193, 7, .14);
        }

        .account-type-card.active::before {
            transform: scaleY(1);
        }

        .type-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 193, 7, .13);
            color: #c58f00;
            font-size: 1.2rem;
            transition: all .25s ease;
        }

        .account-type-card:hover .type-icon,
        .account-type-card.active .type-icon {
            background: var(--herafy-yellow);
            color: #212529;
            transform: scale(1.05);
        }

        .account-type-card h6 {
            font-weight: 800;
            color: #252b33;
        }

        .account-type-card small {
            font-size: .78rem;
        }

        .type-check {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--herafy-yellow);
            color: #212529;
            font-size: .7rem;
            opacity: 0;
            transform: scale(.7);
            transition: all .25s ease;
        }

        .account-type-card.active .type-check {
            opacity: 1;
            transform: scale(1);
        }

        .form-label {
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-label i {
            width: 20px;
            text-align: center;
        }

        .form-control,
        .form-select {
            min-height: 44px;
            border-radius: 11px;
            border: 1px solid #dfe3e8;
            background-color: #fff;
            padding: 10px 14px;
            font-family: inherit;
            transition: all .22s ease;
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--herafy-yellow);
            box-shadow: 0 0 0 .22rem rgba(255, 193, 7, .16);
        }

        .input-group .form-control {
            border-radius: 0 11px 11px 0;
        }

        .input-group .btn {
            min-width: 52px;
            border-radius: 11px 0 0 11px;
            border-color: #dfe3e8;
        }

        .input-group .btn:hover {
            background: #f8f9fa;
        }

        .craftsman-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 12px 0 12px;
            padding: 9px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(255, 193, 7, .13), rgba(255, 193, 7, .04));
            border: 1px solid rgba(255, 193, 7, .25);
        }

        .divider-icon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--herafy-yellow);
            color: #212529;
            font-size: .9rem;
            box-shadow: 0 7px 18px rgba(255, 193, 7, .20);
        }

        .divider-text h6 {
            margin: 0 0 3px;
            font-weight: 800;
        }

        .divider-text small {
            color: var(--herafy-muted);
        }

        .form-check {
            padding: 13px 15px;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .form-check-input {
            cursor: pointer;
            border-color: #cfd4da;
        }

        .form-check-input:checked {
            background-color: var(--herafy-yellow);
            border-color: var(--herafy-yellow);
        }

        .form-check-label {
            color: #6b7280;
        }

        .text-warning {
            color: #d99d00 !important;
        }

        .btn-warning {
            color: #212529;
            background: linear-gradient(135deg, #ffc107, #ffcf3f);
            border: none;
            border-radius: 12px;
            box-shadow: 0 9px 22px rgba(255, 193, 7, .22);
            transition: all .25s ease;
        }

        .btn-warning:hover,
        .btn-warning:focus {
            color: #212529;
            background: linear-gradient(135deg, #eeb000, #ffc107);
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(255, 193, 7, .28);
        }

        .register-card .btn-warning {
            min-height: 44px;
        }

        .register-card .mb-3 {
            margin-bottom: 12px !important;
        }

        .register-card .mb-4 {
            margin-bottom: 20px !important;
        }

        .register-card .btn-outline-danger,
        .register-card .btn-outline-primary {
            min-height: 44px;
            border-radius: 10px;
            font-weight: 600;
            transition: all .25s ease;
        }

        .register-card .btn-outline-danger:hover,
        .register-card .btn-outline-primary:hover {
            transform: translateY(-1px);
        }

        .auth-side-panel {
            position: sticky;
            top: 95px;
            padding: 28px 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, .88);
            border: 1px solid rgba(255, 193, 7, .15);
            box-shadow: 0 18px 45px rgba(31, 41, 55, .08);
            text-align: center;
            overflow: hidden;
        }

        .auth-side-panel::before {
            content: "";
            position: absolute;
            width: 170px;
            height: 170px;
            border-radius: 50%;
            background: rgba(255, 193, 7, .08);
            top: -90px;
            right: -70px;
        }

        .side-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(255, 193, 7, .13);
            color: #c58f00;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(255, 193, 7, .13);
        }

        .auth-side-panel h4 {
            font-weight: 800 !important;
        }

        .features-list {
            margin-top: 18px;
            text-align: right;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 0;
            border-bottom: 1px solid #f0f1f2;
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-icon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 193, 7, .12);
            color: #c58f00;
        }

        .feature-item strong {
            display: block;
            font-size: .92rem;
            margin-bottom: 2px;
        }

        .feature-item small {
            display: block;
            color: var(--herafy-muted);
            line-height: 1.5;
            font-size: .76rem;
        }

        footer {
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        footer p {
            opacity: .85;
        }

        #passwordStrength {
            margin-top: 7px !important;
            border-radius: 7px !important;
            min-height: 20px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991.98px) {
            .auth-section {
                padding-top: 30px !important;
            }

            .register-card {
                margin: 0 auto;
            }
        }

        @media (max-width: 575.98px) {
            .navbar {
                min-height: 65px;
            }

            .auth-section .register-card {
                padding: 20px 16px !important;
                border-radius: 18px !important;
            }

            .account-type-card {
                min-height: 110px;
                padding: 12px !important;
            }

            .auth-icon {
                width: 60px !important;
                height: 60px !important;
            }

            .craftsman-divider {
                padding: 12px;
            }

            .form-control,
            .form-select {
                min-height: 44px;
            }
        }
    </style>

</head>

<body>

    <!-- ============================================ -->
    <!-- NAVBAR -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="{{ url('/') }}">
                <i class="fas fa-hammer text-warning"></i> حرفي
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#about">من نحن</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#craftsmen">الحرفيون</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-warning btn-sm">دخول</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-warning btn-sm">انضم الآن</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================ -->
    <!-- REGISTER SECTION -->
    <!-- ============================================ -->
    <section class="auth-section py-5">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-7">
                    <div class="card shadow-lg border-0 rounded-4 register-card">
                        <div class="text-center mb-3">
                            <div class="auth-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-user-plus text-dark"></i>
                            </div>
                            <h3 class="fw-bold">انضم إلى <span class="text-warning">حرفي</span></h3>
                            <p class="text-muted">أنشئ حسابك وابدأ رحلتك معنا</p>
                        </div>

                        <!-- اختيار نوع الحساب -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="account-type-card p-3 text-center border rounded-3 cursor-pointer active"
                                    data-type="user" onclick="selectAccountType('user')">
                                    <div class="type-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h6 class="mt-2 mb-0">مستخدم</h6>
                                    <small class="text-muted">أبحث عن خدمات</small>
                                    <div class="type-check"><i class="fas fa-check"></i></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="account-type-card p-3 text-center border rounded-3 cursor-pointer"
                                    data-type="craftsman" onclick="selectAccountType('craftsman')">
                                    <div class="type-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <h6 class="mt-2 mb-0">حرفي</h6>
                                    <small class="text-muted">أقدم خدمات</small>
                                    <div class="type-check"><i class="fas fa-check"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- نموذج الاشتراك -->
                        <form id="registerForm" onsubmit="handleRegister(event)">
                            <!-- حقل مخفي لنوع الحساب -->
                            <input type="hidden" id="accountType" value="user">

                            <div class="row">
                                <!-- الاسم الأول -->
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">
                                        <i class="fas fa-user text-warning"></i> الاسم الأول
                                    </label>
                                    <input type="text" class="form-control" id="firstName"
                                        placeholder="أحمد" required>
                                </div>

                                <!-- الاسم الأخير -->
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">
                                        <i class="fas fa-user text-warning"></i> الاسم الأخير
                                    </label>
                                    <input type="text" class="form-control" id="lastName"
                                        placeholder="محمد" required>
                                </div>

                                <!-- اسم المستخدم -->
                                <div class="col-md-6 mb-3">
                                    <label for="user_name" class="form-label">
                                        <i class="fas fa-at text-warning"></i> اسم المستخدم
                                    </label>
                                    <input type="text" class="form-control" id="user_name"
                                        placeholder="ahmed123" required>
                                </div>

                                <!-- الجنس -->
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">
                                        <i class="fas fa-venus-mars text-warning"></i> الجنس
                                    </label>
                                    <select class="form-select" id="gender">
                                        <option value="">اختر الجنس</option>
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                </div>
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-warning"></i> البريد الإلكتروني
                                </label>
                                <input type="email" class="form-control" id="email"
                                    placeholder="example@email.com" required>
                            </div>

                            <div class="row">
                                <!-- رقم الهاتف -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone text-warning"></i> رقم الهاتف
                                    </label>
                                    <input type="tel" class="form-control" id="phone"
                                        placeholder="0599 123 456">
                                </div>

                                <!-- العنوان -->
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt text-warning"></i> العنوان
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                        placeholder="غزة، شارع عمر المختار">
                                </div>
                            </div>

                            <!-- كلمة المرور -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock text-warning"></i> كلمة المرور
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password"
                                        placeholder="********" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- تأكيد كلمة المرور -->
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">
                                    <i class="fas fa-check-circle text-warning"></i> تأكيد كلمة المرور
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword"
                                        placeholder="********" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('confirmPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- حقل إضافي للحرفي (يظهر فقط عند اختيار حرفي) -->
                            <div id="craftsmanFields" style="display: none;">
                                <!-- فاصل بيانات الحرفي -->
                                <div class="craftsman-divider">
                                    <div class="divider-icon">
                                        <i class="fas fa-hard-hat"></i>
                                    </div>
                                    <div class="divider-text">
                                        <h6>بيانات الحرفي</h6>
                                        <small>أكمل بياناتك المهنية لتظهر في نتائج البحث</small>
                                    </div>
                                </div>

                                <!-- رقم الهوية -->
                                <div class="mb-3">
                                    <label for="id_number" class="form-label">
                                        <i class="fas fa-id-card text-warning"></i> رقم الهوية
                                    </label>
                                    <input type="text" class="form-control" id="id_number"
                                        placeholder="مثال: 123456789" required>
                                </div>

                                <div class="mb-3">
                                    <label for="craft" class="form-label">
                                        <i class="fas fa-tools text-warning"></i> الحرفة
                                    </label>
                                    <select class="form-select" id="craft"
                                        onchange="handleCraftChange()">
                                        <option value="">اختر حرفتك</option>
                                        @foreach($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                                        @endforeach
                                        <option value="other">أخرى</option>
                                    </select>
                                </div>

                                <!-- حقل الحرفة المخصصة (يظهر فقط عند اختيار "أخرى") -->
                                <div id="customCraftField" style="display: none;">
                                    <div class="mb-3">
                                        <label for="customCraft" class="form-label">
                                            <i class="fas fa-pen text-warning"></i> اكتب حرفتك
                                        </label>
                                        <input type="text" class="form-control" id="customCraft"
                                            placeholder="مثال: حدادة، زجاج، رخام، ...">
                                        <small class="text-muted">يرجى كتابة اسم الحرفة التي تمارسها</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="experience" class="form-label">
                                        <i class="fas fa-star text-warning"></i> سنوات الخبرة
                                    </label>
                                    <input type="number" class="form-control" id="experience"
                                        placeholder="عدد سنوات الخبرة" min="0">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="hourly_rate" class="form-label">
                                            <i class="fas fa-clock text-warning"></i> السعر بالساعة (₪)
                                        </label>
                                        <input type="number" class="form-control" id="hourly_rate"
                                            placeholder="50" min="0" step="0.5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="daily_rate" class="form-label">
                                            <i class="fas fa-calendar-day text-warning"></i> السعر باليوم (₪)
                                        </label>
                                        <input type="number" class="form-control" id="daily_rate"
                                            placeholder="350" min="0" step="0.5">
                                    </div>
                                </div>
                            </div>

                            <!-- الموافقة على الشروط -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label small" for="terms">
                                        أوافق على
                                        <a href="#" class="text-warning">شروط الخدمة</a>
                                        و
                                        <a href="#" class="text-warning">سياسة الخصوصية</a>
                                    </label>
                                </div>
                            </div>

                            <!-- زر إنشاء الحساب -->
                            <button type="submit" class="btn btn-warning w-100 fw-bold">
                                <i class="fas fa-user-plus"></i> إنشاء الحساب
                            </button>

                            <!-- فصل -->
                            <div class="my-3 text-center">
                                <span class="text-muted small">أو</span>
                            </div>

                            <!-- أزرار التسجيل عبر وسائل التواصل -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="socialRegister('google')">
                                    <i class="fab fa-google"></i> التسجيل عبر Google
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="socialRegister('facebook')">
                                    <i class="fab fa-facebook"></i> التسجيل عبر Facebook
                                </button>
                            </div>

                            <!-- رابط تسجيل الدخول -->
                            <div class="text-center mt-3">
                                <p class="mb-0 small">
                                    لديك حساب بالفعل؟
                                    <a href="{{ route('login') }}" class="text-warning fw-bold">سجل دخولك</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- اللوحة الجانبية -->
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="auth-side-panel">
                        <div class="side-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4 class="fw-bold">ابدأ الآن مع <span class="text-warning">حرفي</span></h4>
                        <p class="text-muted">اختر نوع حسابك وابدأ رحلتك معنا</p>

                        <div class="features-list">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div>
                                    <strong>تواصل مباشر</strong>
                                    <small>اربط مع أفضل الحرفيين في منطقتك</small>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <strong>حساب آمن</strong>
                                    <small>بياناتك محمية ومشفرة بالكامل</small>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-badge-check"></i>
                                </div>
                                <div>
                                    <strong>حرفيون موثوقون</strong>
                                    <small>نتحقق من كل حرفي قبل انضمامه</small>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <strong>بدون رسوم</strong>
                                    <small>انضم مجاناً وابدأ فوراً</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="text-center">
                <p class="text-white small mb-0">
                    &copy; 2026 حرفي. جميع الحقوق محفوظة
                </p>
            </div>
        </div>
    </footer>

    <!-- ============================================ -->
    <!-- Bootstrap JS -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================================ -->
    <!-- SweetAlert2 -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- ============================================ -->
    <!-- ملف JS الخاص بنا -->
    <!-- ============================================ -->
    <script src="{{ asset('js/crud.js') }}"></script>
    <script src="{{asset('cms/plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{ asset('cms/js/main.js') }}"></script>

    <script>
        // ============================================
        // 1. تبديل إظهار/إخفاء كلمة المرور
        // ============================================
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // ============================================
        // 2. اختيار نوع الحساب
        // ============================================
        function selectAccountType(type) {
            // إزالة التحديد من الكل
            document.querySelectorAll('.account-type-card').forEach(card => {
                card.classList.remove('active', 'border-warning', 'bg-warning', 'bg-opacity-10');
                card.style.borderColor = '';
                card.style.backgroundColor = '';
            });

            // تحديد البطاقة المختارة
            const selectedCard = document.querySelector(`.account-type-card[data-type="${type}"]`);
            selectedCard.classList.add('active', 'border-warning', 'bg-warning', 'bg-opacity-10');

            // تحديث الحقل المخفي
            document.getElementById('accountType').value = type;

            // إظهار/إخفاء حقول الحرفي
            const craftsmanFields = document.getElementById('craftsmanFields');
            if (type === 'craftsman') {
                craftsmanFields.style.display = 'block';
                // إضافة تأثير انزلاقي
                craftsmanFields.style.animation = 'slideDown 0.4s ease forwards';
            } else {
                craftsmanFields.style.display = 'none';
                // إعادة تعيين الحقول
                document.getElementById('craft').value = '';
                document.getElementById('customCraftField').style.display = 'none';
                document.getElementById('experience').value = '';
                document.getElementById('id_number').value = '';
                document.getElementById('hourly_rate').value = '';
                document.getElementById('daily_rate').value = '';
            }
        }

        // ============================================
        // 3. معالجة تغيير حقل الحرفة
        // ============================================
        function handleCraftChange() {
            const craftSelect = document.getElementById('craft');
            const customCraftField = document.getElementById('customCraftField');
            const customCraftInput = document.getElementById('customCraft');

            if (craftSelect.value === 'other') {
                // إظهار حقل الحرفة المخصصة مع تأثير
                customCraftField.style.display = 'block';
                customCraftField.style.animation = 'slideDown 0.3s ease forwards';
                customCraftInput.required = true;
                setTimeout(() => customCraftInput.focus(), 300);
            } else {
                // إخفاء حقل الحرفة المخصصة
                customCraftField.style.display = 'none';
                customCraftInput.required = false;
                customCraftInput.value = '';
                customCraftInput.classList.remove('is-invalid');
                const customCraftFeedback = document.getElementById('customCraftFeedback');
                if (customCraftFeedback) {
                    customCraftFeedback.style.display = 'none';
                }
            }
        }

        // ============================================
        // 4. التحقق من صحة الحرفة المخصصة
        // ============================================
        function validateCustomCraft() {
            const input = document.getElementById('customCraft');
            const feedback = document.getElementById('customCraftFeedback');

            if (!input || !feedback) {
                return true;
            }

            if (input.value.trim().length < 2) {
                input.classList.add('is-invalid');
                feedback.style.display = 'block';
                return false;
            } else {
                input.classList.remove('is-invalid');
                feedback.style.display = 'none';
                return true;
            }
        }

        // ============================================
        // 5. معالجة التسجيل
        // ============================================
        function handleRegister(event) {
            event.preventDefault();

            // جلب البيانات من النموذج
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const accountType = document.getElementById('accountType').value;
            const phone = document.getElementById('phone').value.trim();
            const terms = document.getElementById('terms').checked;

            // ============================================
            // التحقق من صحة البيانات
            // ============================================

            // التحقق من الحقول المطلوبة
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ حقول ناقصة',
                    text: 'يرجى ملء جميع الحقول المطلوبة',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // التحقق من صحة البريد الإلكتروني
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ بريد إلكتروني غير صحيح',
                    text: 'يرجى إدخال بريد إلكتروني صحيح (مثال: name@domain.com)',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // التحقق من تطابق كلمة المرور
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ كلمات المرور غير متطابقة',
                    text: 'تأكد من كتابة كلمة المرور بشكل صحيح في كلا الحقلين',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // التحقق من قوة كلمة المرور (على الأقل 6 أحرف)
            if (password.length < 6) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ كلمة مرور ضعيفة',
                    text: 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // التحقق من الموافقة على الشروط
            if (!terms) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ الموافقة على الشروط',
                    text: 'يرجى الموافقة على شروط الخدمة وسياسة الخصوصية',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // ============================================
            // التحقق من بيانات الحرفي
            // ============================================
            let craft = '';
            let craftDisplay = '';
            let experience = 0;

            if (accountType === 'craftsman') {
                const craftSelect = document.getElementById('craft');
                const customCraft = document.getElementById('customCraft');
                const experienceInput = document.getElementById('experience');

                // التحقق من اختيار الحرفة
                if (craftSelect.value === '') {
                    Swal.fire({
                        icon: 'error',
                        title: '⚠️ الحرفة مطلوبة',
                        text: 'يرجى اختيار حرفتك من القائمة',
                        confirmButtonColor: '#FFC107'
                    });
                    craftSelect.focus();
                    return;
                }

                // التحقق من الحرفة المخصصة
                if (craftSelect.value === 'other') {
                    if (!customCraft.value.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: '⚠️ الحرفة مطلوبة',
                            text: 'يرجى كتابة اسم حرفتك',
                            confirmButtonColor: '#FFC107'
                        });
                        customCraft.focus();
                        return;
                    }
                    if (customCraft.value.trim().length < 2) {
                        Swal.fire({
                            icon: 'error',
                            title: '⚠️ اسم الحرفة قصير',
                            text: 'يرجى كتابة اسم حرفة واضح (أكثر من حرفين)',
                            confirmButtonColor: '#FFC107'
                        });
                        customCraft.focus();
                        return;
                    }
                    craft = customCraft.value.trim();
                    craftDisplay = craft;
                } else {
                    craft = craftSelect.value;
                    // تحويل الاسم إلى نص مفهوم
                    const craftNames = {
                        'carpentry': 'نجارة',
                        'plumbing': 'سباكة',
                        'tiling': 'تبليط',
                        'electricity': 'كهرباء',
                        'painting': 'دهان',
                        'welding': 'لحام',
                        'masonry': 'بناء',
                        'carpet': 'سجاد وموكيت',
                        'glass': 'زجاج',
                        'ac': 'تكييف وتبريد'
                    };
                    craftDisplay = craftNames[craft] || craft;
                }

                // التحقق من سنوات الخبرة
                if (!experienceInput.value) {
                    Swal.fire({
                        icon: 'error',
                        title: '⚠️ سنوات الخبرة مطلوبة',
                        text: 'يرجى إدخال عدد سنوات الخبرة',
                        confirmButtonColor: '#FFC107'
                    });
                    experienceInput.focus();
                    return;
                }

                experience = parseFloat(experienceInput.value);
                if (isNaN(experience) || experience < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: '⚠️ سنوات خبرة غير صحيحة',
                        text: 'يرجى إدخال عدد صحيح أو عشري (مثال: 2.5)',
                        confirmButtonColor: '#FFC107'
                    });
                    experienceInput.focus();
                    return;
                }
            }

            // ============================================
            // إعداد بيانات المستخدم (أسماء الحقول تطابق قاعدة البيانات)
            // ============================================
            const formData = new FormData();
            formData.append('name', firstName + ' ' + lastName);
            formData.append('user_name', document.getElementById('user_name').value.trim());
            formData.append('email', email);
            formData.append('password', password);
            formData.append('password_confirmation', confirmPassword);
            formData.append('mobile', phone);
            formData.append('address', document.getElementById('address').value.trim());
            formData.append('gender', document.getElementById('gender').value);
            formData.append('account_type', accountType);

            if (accountType === 'craftsman') {
                formData.append('id_number', document.getElementById('id_number').value.trim());
                formData.append('profession_id', document.getElementById('craft').value);
                formData.append('experience_years', experience);
                formData.append('hourly_rate', document.getElementById('hourly_rate').value || 0);
                formData.append('daily_rate', document.getElementById('daily_rate').value || 0);
                if (document.getElementById('craft').value === 'other') {
                    formData.append('custom_craft', document.getElementById('customCraft').value.trim());
                }
            }

            console.log('📝 بيانات التسجيل:', Object.fromEntries(formData));

            // ============================================
            // إرسال البيانات إلى السيرفر عبر axios
            // ============================================
            const button = event.target.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إنشاء الحساب...';
            button.disabled = true;

            axios.post('register', formData)
                .then(function(response) {
                    const accountTypeText = accountType === 'craftsman' ? 'حرفي' : 'مستخدم';
                    let message = 'مرحباً بك ' + firstName + ' في منصة حرفي! 🎉';
                    message += '\n\nنوع الحساب: ' + accountTypeText;

                    Swal.fire({
                        icon: 'success',
                        title: response.data.title || 'تم إنشاء الحساب بنجاح',
                        html: message.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#FFC107',
                        confirmButtonText: 'تسجيل الدخول'
                    }).then(() => {
                        window.location.href = response.data.redirect || '/login';
                    });
                })
                .catch(function(error) {
                    // إعادة تفعيل الزر
                    button.innerHTML = originalText;
                    button.disabled = false;

                    if (error.response && error.response.data) {
                        if (error.response.data.errors !== undefined) {
                            const errors = error.response.data.errors;
                            const firstKey = Object.keys(errors)[0];
                            Swal.fire({
                                icon: 'error',
                                title: '⚠️ خطأ في البيانات',
                                text: errors[firstKey],
                                confirmButtonColor: '#FFC107'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '⚠️ خطأ',
                                text: error.response.data.title || 'حدث خطأ أثناء إنشاء الحساب',
                                confirmButtonColor: '#FFC107'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '⚠️ خطأ',
                            text: 'تعذر الاتصال بالخادم، حاول مرة أخرى',
                            confirmButtonColor: '#FFC107'
                        });
                    }
                });

        }

        // ============================================
        // 6. تسجيل الدخول عبر وسائل التواصل
        // ============================================
        function socialRegister(provider) {
            // محاكاة تسجيل الدخول عبر وسائل التواصل
            const providerNames = {
                'google': 'Google',
                'facebook': 'Facebook',
                'apple': 'Apple'
            };

            Swal.fire({
                icon: 'info',
                title: '🔄 جاري التوجيه',
                text: `سيتم توجيهك للتسجيل عبر ${providerNames[provider] || provider}`,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // محاكاة نجاح التسجيل عبر وسائل التواصل
                const randomName = ['أحمد', 'محمد', 'سارة', 'فاطمة', 'خالد', 'نور'][Math.floor(Math.random() * 6)];
                const randomLastName = ['النجار', 'السباك', 'المبلط', 'الكهربائي', 'الدهان'][Math.floor(Math.random() * 5)];

                Swal.fire({
                    icon: 'success',
                    title: '✅ تم التسجيل بنجاح',
                    text: `مرحباً بك ${randomName} ${randomLastName} في منصة حرفي`,
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    // تخزين بيانات المستخدم في localStorage
                    const userData = {
                        id: Date.now(),
                        firstName: randomName,
                        lastName: randomLastName,
                        fullName: `${randomName} ${randomLastName}`,
                        email: `${randomName.toLowerCase()}@${provider}.com`,
                        accountType: 'user',
                        provider: provider,
                        createdAt: new Date().toISOString(),
                        isActive: true
                    };
                    localStorage.setItem('currentUser', JSON.stringify(userData));

                    // تحويل إلى لوحة التحكم
                    window.location.href = 'dashboard-user.html';
                });
            });
        }

        // ============================================
        // 7. التحقق من البريد الإلكتروني أثناء الكتابة
        // ============================================
        function validateEmailOnInput() {
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailInput.value.length > 0) {
                if (emailRegex.test(emailInput.value)) {
                    emailInput.classList.remove('is-invalid');
                    emailInput.classList.add('is-valid');
                } else {
                    emailInput.classList.remove('is-valid');
                    emailInput.classList.add('is-invalid');
                }
            } else {
                emailInput.classList.remove('is-valid', 'is-invalid');
            }
        }

        // ============================================
        // 8. التحقق من قوة كلمة المرور
        // ============================================
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthIndicator = document.getElementById('passwordStrength');

            if (!strengthIndicator) {
                // إنشاء مؤشر قوة كلمة المرور إذا لم يكن موجوداً
                createPasswordStrengthIndicator();
                return;
            }

            let strength = 0;
            let message = '';
            let color = '';

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            if (password.length === 0) {
                message = '';
                color = 'transparent';
                strengthIndicator.style.width = '0%';
            } else if (strength <= 1) {
                message = 'ضعيفة';
                color = '#dc3545';
                strengthIndicator.style.width = '20%';
            } else if (strength <= 2) {
                message = 'ضعيفة';
                color = '#ffc107';
                strengthIndicator.style.width = '40%';
            } else if (strength <= 3) {
                message = 'متوسطة';
                color = '#ffc107';
                strengthIndicator.style.width = '60%';
            } else if (strength <= 4) {
                message = 'قوية';
                color = '#28a745';
                strengthIndicator.style.width = '80%';
            } else {
                message = 'قوية جداً';
                color = '#28a745';
                strengthIndicator.style.width = '100%';
            }

            strengthIndicator.style.backgroundColor = color;
            strengthIndicator.textContent = message;
            strengthIndicator.style.color = 'white';
            strengthIndicator.style.padding = '2px 8px';
            strengthIndicator.style.borderRadius = '4px';
            strengthIndicator.style.fontSize = '12px';
            strengthIndicator.style.textAlign = 'center';
            strengthIndicator.style.transition = 'all 0.3s ease';
        }

        function createPasswordStrengthIndicator() {
            const passwordField = document.getElementById('password');
            const parentDiv = passwordField.closest('.mb-3');

            const indicatorDiv = document.createElement('div');
            indicatorDiv.id = 'passwordStrength';
            indicatorDiv.style.marginTop = '5px';
            indicatorDiv.style.height = '20px';
            indicatorDiv.style.borderRadius = '4px';
            indicatorDiv.style.transition = 'all 0.3s ease';
            indicatorDiv.style.overflow = 'hidden';
            indicatorDiv.style.display = 'flex';
            indicatorDiv.style.alignItems = 'center';
            indicatorDiv.style.justifyContent = 'center';

            parentDiv.appendChild(indicatorDiv);
        }

        // ============================================
        // 9. عرض رسالة ترحيبية عند تحميل الصفحة
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 منصة حرفي - صفحة الاشتراك جاهزة');

            // إضافة مستمعين للأحداث
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', validateEmailOnInput);
            }

            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('input', checkPasswordStrength);
            }

            // إضافة تأثير عند تحميل الصفحة
            const cards = document.querySelectorAll('.account-type-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 200 + (index * 150));
            });
        });

        // ============================================
        // 10. معالجة الأخطاء العامة
        // ============================================
        window.onerror = function(msg, url, line, col, error) {
            console.error('❌ خطأ في الصفحة:', msg, error);
            return false;
        };

        console.log('✅ جميع الوظائف جاهزة للاستخدام');
    </script>
</body>

</html>