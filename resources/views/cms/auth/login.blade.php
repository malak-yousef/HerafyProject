<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - حرفي</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- <link rel="stylesheet" href="{{ asset('cms/css/style.css') }}"> -->

    <style>
        /* ===== تصميم حرفي - تنسيقات فقط ===== */
        :root {
            --primary: #FFC107;
            --primary-dark: #e5a900;
            --dark: #252525;
            --muted: #777;
            --border: #eeeeee;
            --soft: #fffaf0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: #fff;
            color: var(--dark);
            font-family: "Tajawal", "Cairo", Arial, sans-serif;
        }

        /* ===== Navbar ===== */
        .navbar {
            min-height: 70px;
            background: rgba(255, 255, 255, .96) !important;
            border-bottom: 1px solid #f2f2f2;
            box-shadow: 0 4px 22px rgba(0, 0, 0, .04) !important;
            position: relative;
            z-index: 10;
        }

        .navbar .navbar-brand {
            color: var(--dark);
            font-size: 1.55rem !important;
            letter-spacing: -.5px;
        }

        .navbar .navbar-brand i {
            color: var(--primary) !important;
        }

        .navbar .nav-link {
            color: #444;
            font-weight: 600;
            font-size: 14px;
            padding: 9px 14px !important;
            border-radius: 10px;
            transition: .25s ease;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #b47c00;
            background: #fff8df;
        }

        .navbar .btn-warning,
        .navbar .btn-outline-warning {
            border-radius: 9px;
            font-weight: 700;
            padding: 7px 15px;
            transition: .25s ease;
        }

        .navbar .btn-warning {
            background: var(--primary);
            border-color: var(--primary);
            color: #222;
        }

        .navbar .btn-outline-warning {
            border: 1px solid var(--primary);
            color: #9a6b00;
        }

        .navbar .btn-warning:hover,
        .navbar .btn-outline-warning:hover {
            background: #ffca28;
            border-color: #ffca28;
            color: #222;
            transform: translateY(-1px);
        }

        /* ===== Login Section ===== */
        .auth-section {
            min-height: calc(100vh - 130px);
            padding: 55px 0 !important;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 13% 35%, rgba(255, 193, 7, .08) 0 100px, transparent 101px),
                radial-gradient(circle at 86% 72%, rgba(255, 193, 7, .07) 0 150px, transparent 151px),
                linear-gradient(135deg, #fff 0%, #fffdf8 100%);
        }

        .auth-section:before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border: 35px solid rgba(255, 193, 7, .055);
            border-radius: 50%;
            left: -120px;
            top: 80px;
        }

        .auth-section .container {
            position: relative;
            z-index: 1;
        }

        .auth-section .row {
            min-height: 650px !important;
            gap: 10px 0;
        }

        /* ===== Login Card ===== */
        .auth-section .card {
            border: 1px solid #f0f0f0 !important;
            border-radius: 24px !important;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 18px 55px rgba(0, 0, 0, .09) !important;
            padding: 38px 40px !important;
            max-width: 500px;
            margin: auto;
            transition: .3s ease;
        }

        .auth-section .card:hover {
            box-shadow: 0 22px 65px rgba(0, 0, 0, .12) !important;
        }

        .auth-section .card .auth-icon {
            width: 68px !important;
            height: 68px !important;
            background: #fff3c4 !important;
            color: #222 !important;
            border: 5px solid #fff8df;
            font-size: 1.65rem !important;
            box-shadow: 0 8px 20px rgba(255, 193, 7, .18);
        }

        .auth-section .card h3 {
            font-size: 1.65rem;
            color: #222;
            margin-bottom: 7px;
        }

        .auth-section .card .text-muted {
            color: #888 !important;
            font-size: 13px;
        }

        /* ===== Labels & Inputs ===== */
        .auth-section .form-label {
            color: #333;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .auth-section .form-label i {
            margin-left: 4px;
        }

        .auth-section .form-control {
            height: 48px;
            border: 1px solid #e4e4e4;
            border-radius: 10px !important;
            background: #fff;
            padding: 10px 14px;
            font-size: 13px;
            color: #333;
            box-shadow: none;
            transition: .2s ease;
        }

        .auth-section .form-control::placeholder {
            color: #b5b5b5;
        }

        .auth-section .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 193, 7, .13);
        }

        .auth-section .input-group .form-control {
            border-radius: 10px 0 0 10px !important;
        }

        .auth-section .input-group .btn-outline-secondary {
            width: 48px;
            border: 1px solid #e4e4e4;
            border-right: 0;
            border-radius: 0 10px 10px 0;
            background: #fff;
            color: #888;
        }

        .auth-section .input-group .btn-outline-secondary:hover {
            color: #555;
            background: #fffaf0;
            border-color: var(--primary);
        }

        /* ===== Main Login Button ===== */
        .auth-section .btn-warning {
            height: 48px;
            background: var(--primary);
            border: 0;
            border-radius: 10px;
            color: #222;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 7px 18px rgba(255, 193, 7, .18);
            transition: .25s ease;
        }

        .auth-section .btn-warning:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 193, 7, .28);
        }

        .auth-section .form-check-label,
        .auth-section .small {
            font-size: 12px !important;
        }

        .auth-section .form-check-input {
            border-color: #d5d5d5;
            width: 16px;
            height: 16px;
        }

        .auth-section .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .auth-section .text-warning {
            color: #d99a00 !important;
            font-weight: 700;
        }

        /* ===== Social Buttons ===== */
        .auth-section .btn-outline-danger,
        .auth-section .btn-outline-primary {
            height: 42px;
            border-radius: 9px;
            background: #fff;
            font-size: 12px;
            font-weight: 600;
            border-width: 1px;
            transition: .25s ease;
        }

        .auth-section .btn-outline-danger:hover,
        .auth-section .btn-outline-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 14px rgba(0, 0, 0, .07);
        }

        .auth-section .btn-outline-danger:hover {
            background: #fff7f7;
        }

        .auth-section .btn-outline-primary:hover {
            background: #f7faff;
        }

        /* ===== Side Illustration ===== */
        .auth-section .col-lg-5:last-child {
            padding: 25px 35px;
        }

        .auth-image {
            width: 100%;
            max-width: 500px;
            max-height: 440px;
            object-fit: contain;
            border-radius: 22px !important;
            box-shadow: none !important;
            transition: .3s ease;
        }

        .auth-image:hover {
            transform: translateY(-3px);
        }

        .auth-section .col-lg-5:last-child h4 {
            font-size: 22px;
            color: #252525;
            margin-top: 8px;
        }

        .auth-section .col-lg-5:last-child p {
            font-size: 13px;
            color: #888 !important;
        }

        /* ===== Footer ===== */
        footer {
            background: #22272b !important;
            padding: 17px 0 !important;
        }

        footer p {
            color: rgba(255, 255, 255, .55) !important;
            font-size: 11px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 991px) {
            .auth-section {
                padding: 35px 0 !important;
            }

            .auth-section .row {
                min-height: auto !important;
            }

            .auth-section .card {
                max-width: 560px;
            }
        }

        @media (max-width: 768px) {
            .auth-section {
                min-height: auto;
                padding: 35px 12px !important;
            }

            .auth-section .card {
                padding: 28px 22px !important;
                border-radius: 18px !important;
            }

            .auth-section .card h3 {
                font-size: 1.4rem;
            }

            .navbar .nav-link {
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .auth-section .card {
                padding: 24px 18px !important;
            }

            .auth-section .d-flex.justify-content-between {
                gap: 8px;
            }

            .auth-section .col-lg-5:last-child {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.html">
                <i class="fas fa-hammer text-warning"></i> حرفي
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.html#about">من نحن</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.html#craftsmen">الحرفيون</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-warning btn-sm">دخول</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-warning btn-sm">انضم الآن</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== LOGIN SECTION ===== -->
    <section class="auth-section py-5">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-75">
                <div class="col-lg-5">
                    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="auth-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 70px; height: 70px; font-size: 2rem;">
                                <i class="fas fa-user text-dark"></i>
                            </div>
                            <h3 class="fw-bold">مرحباً بعودتك</h3>
                            <p class="text-muted">سجل دخولك للوصول إلى حسابك</p>
                        </div>

                        <form id="loginForm" onsubmit="handleLogin(event)">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">
                                    <i class="fas fa-envelope text-warning"></i> البريد الإلكتروني
                                </label>
                                <input type="email"
                                    class="form-control form-control-lg"
                                    id="email"
                                    name="email"
                                    placeholder="example@email.com"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">
                                    <i class="fas fa-lock text-warning"></i> كلمة المرور
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control form-control-lg"
                                        id="password"
                                        name="password"
                                        placeholder="********"
                                        required>
                                    <button class="btn btn-outline-secondary"
                                        type="button"
                                        onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember">
                                    <label class="form-check-label small" for="remember">تذكرني</label>
                                </div>
                                <a href="#" class="text-warning text-decoration-none small">نسيت كلمة المرور؟</a>
                            </div>

                            <button type="button" onclick="login()" class="btn btn-warning btn-lg w-100 fw-bold">
                                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                            </button>

                            <div class="my-3 text-center">
                                <span class="text-muted small">أو</span>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="socialLogin('google')">
                                    <i class="fab fa-google"></i> تسجيل الدخول عبر Google
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="socialLogin('facebook')">
                                    <i class="fab fa-facebook"></i> تسجيل الدخول عبر Facebook
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <p class="mb-0 small">
                                    ليس لديك حساب؟
                                    <a href="{{ route('register') }}" class="text-warning fw-bold">انضم الآن</a>
                                </p>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="auth-side-content">
                        <h4 class="fw-bold">أهلاً بك في <span class="text-warning">حرفي</span></h4>
                        <p class="text-muted">منصتك الموثوقة للوصول إلى أفضل الحرفيين</p>
                    </div>

                    <img src="{{ asset('cms/img/login.png') }}"
                        alt="تسجيل الدخول"
                        class="auth-image">
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="text-center">
                <p class=" small mb-0">
                    &copy; 2026 حرفي. جميع الحقوق محفوظة
                </p>
            </div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{asset('cms/plugins/toastr/toastr.min.js')}}"></script>
    <script src="{{ asset('js/crud.js') }}"></script>
    <script src="{{ asset('cms/js/main.js') }}"></script>

    <script>
        // ============================================
        // تسجيل الدخول
        // ============================================
        function login() {
            axios.post('login', {
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    // remember: document.getElementById('remember').checked
                })
                .then(function(response) {
                    window.location.href = response.data.redirect;
                    // window.location.href = route('cms/admin');

                })
                .catch(function(error) {
                    if (error.response.data.errors !== undefined) {
                        showErrorMessages(error.response.data.errors);
                    } else {
                        showMessage(error.response.data);
                    }
                });
        }


        // ============================================
        // تبديل إظهار/إخفاء كلمة المرور
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
        // معالجة تسجيل الدخول
        // ============================================
        function handleLogin(event) {
            event.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ حقول ناقصة',
                    text: 'يرجى إدخال البريد الإلكتروني وكلمة المرور',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // ✅ التحقق من كلمة المرور
            if (user.password && user.password !== password) {
                Swal.fire({
                    icon: 'error',
                    title: '⚠️ كلمة مرور خاطئة',
                    text: 'يرجى التحقق من كلمة المرور',
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            localStorage.setItem('currentUser', JSON.stringify(user));

            Swal.fire({
                icon: 'success',
                title: '✅ تم تسجيل الدخول بنجاح',
                text: `مرحباً بك ${user.firstName || 'مستخدم'}`,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                if (user.accountType === 'admin') {
                    window.location.href = 'dashboard-admin.html';
                } else if (user.accountType === 'craftsman') {
                    window.location.href = 'dashboard-craftsman.html';
                } else {
                    window.location.href = 'dashboard-user.html';
                }
            });
        }

        // ============================================
        // تسجيل الدخول عبر وسائل التواصل
        // ============================================
        function socialLogin(provider) {
            Swal.fire({
                icon: 'info',
                title: 'جاري التوجيه',
                text: `سيتم توجيهك لتسجيل الدخول عبر ${provider}`,
                timer: 1500,
                showConfirmButton: false
            });
        }

        // ============================================
        // تحميل الصفحة
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            ensureAdminExists();
            console.log('🔐 صفحة تسجيل الدخول جاهزة');
        });
    </script>
</body>

</html>