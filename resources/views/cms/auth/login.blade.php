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
        /* ===== تنسيق الصفحة ===== */
        .auth-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
        }

        .auth-section .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
        }

        .auth-section .card .auth-icon {
            background: linear-gradient(135deg, #FFC107, #e6a800);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #2d3436;
            transition: all 0.3s ease;
        }

        .auth-section .card .auth-icon:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(255, 193, 7, 0.4);
        }

        /* ===== تنسيق الحقول ===== */
        .auth-section .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 18px;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .auth-section .form-control:focus {
            border-color: #FFC107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .auth-section .form-control-lg {
            height: 52px;
        }

        .auth-section .input-group .btn-outline-secondary {
            border-radius: 0 12px 12px 0;
            border: 2px solid #e9ecef;
            border-left: none;
            background: #fff;
            transition: all 0.3s ease;
        }

        .auth-section .input-group .btn-outline-secondary:hover {
            background: #f8f9fa;
            border-color: #FFC107;
        }

        .auth-section .input-group .form-control {
            border-radius: 12px 0 0 12px;
            border-right: none;
        }

        .auth-section .input-group .form-control:focus {
            border-color: #FFC107;
        }

        .auth-section .input-group .form-control:focus+.btn-outline-secondary {
            border-color: #FFC107;
        }

        /* ===== تنسيق الأزرار ===== */
        .auth-section .btn-warning {
            background: #FFC107;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            color: #2d3436;
        }

        .auth-section .btn-warning:hover {
            background: #e6a800;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        }

        .auth-section .btn-outline-danger,
        .auth-section .btn-outline-primary {
            border-radius: 10px;
            padding: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            border-width: 2px;
        }

        .auth-section .btn-outline-danger:hover,
        .auth-section .btn-outline-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* ===== تنسيق الروابط ===== */
        .auth-section .form-check-label {
            font-size: 14px;
            color: #6c757d;
        }

        .auth-section .form-check-input:checked {
            background-color: #FFC107;
            border-color: #FFC107;
        }

        .auth-section .text-warning {
            color: #FFC107 !important;
            transition: all 0.3s ease;
        }

        .auth-section .text-warning:hover {
            color: #e6a800 !important;
        }

        /* ===== تنسيق الصورة الجانبية ===== */
        .auth-image {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .auth-image:hover {
            transform: scale(1.02);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
        }

        /* ===== تنسيق الجوال ===== */
        @media (max-width: 768px) {
            .auth-section {
                min-height: auto;
                padding: 40px 0;
            }

            .auth-section .card {
                padding: 20px !important;
                margin: 0 10px;
            }

            .auth-section .card .auth-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }

            .auth-section .form-control-lg {
                height: 45px;
                font-size: 14px;
            }

            .auth-section .btn-warning {
                padding: 12px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .auth-section .card {
                padding: 15px !important;
            }

            .auth-section .d-flex.justify-content-between {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start !important;
            }
        }

        /* ===== تنسيق الـ Navbar ===== */
        .navbar {
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05) !important;
        }

        .navbar .navbar-brand {
            color: #2d3436;
        }

        .navbar .navbar-brand i {
            color: #FFC107;
        }

        .navbar .nav-link {
            color: #2d3436;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }

        .navbar .nav-link:hover {
            color: #FFC107;
            background: rgba(255, 193, 7, 0.1);
        }

        .navbar .nav-link.active {
            color: #FFC107;
            background: rgba(255, 193, 7, 0.1);
        }

        .navbar .btn-warning {
            background: #FFC107;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #2d3436;
        }

        .navbar .btn-warning:hover {
            background: #e6a800;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        .navbar .btn-outline-warning {
            border: 2px solid #FFC107;
            border-radius: 8px;
            font-weight: 600;
            color: #FFC107;
            transition: all 0.3s ease;
        }

        .navbar .btn-outline-warning:hover {
            background: #FFC107;
            color: #2d3436;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }

        /* ===== تنسيق الفوتر ===== */
        footer {
            background: #1a1a2e !important;
        }

        footer p {
            color: rgba(255, 255, 255, 0.5) !important;
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
                    <li class="nav-item"><a class="nav-link" href="index.html">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.html#about">من نحن</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.html#craftsmen">الحرفيون</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="login.html" class="btn btn-warning btn-sm">دخول</a>
                    <a href="register.html" class="btn btn-outline-warning btn-sm">انضم الآن</a>
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
                                    <a href="register.html" class="text-warning fw-bold">انضم الآن</a>
                                </p>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="col-lg-5 d-none d-lg-block text-center">
                    <img src="https://via.placeholder.com/500x500/FFC107/FFFFFF?text=حرفي"
                        alt="تسجيل الدخول"
                        class="img-fluid rounded-4 shadow-lg">
                    <div class="mt-4">
                        <h4 class="fw-bold">أهلاً بك في <span class="text-warning">حرفي</span></h4>
                        <p class="text-muted">منصتك الموثوقة للوصول إلى أفضل الحرفيين</p>
                    </div>
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
                    remember: document.getElementById('remember').checked
                })
                .then(function(response) {
                    window.location.href = response.data.redirect;
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