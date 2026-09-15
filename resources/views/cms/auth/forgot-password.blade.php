<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نسيت كلمة المرور - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card border-0 shadow-sm" style="max-width: 520px; width: 100%;">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold mb-3">استعادة كلمة المرور</h1>
                <p class="text-muted">أدخل بريدك الإلكتروني وسنرسل إليك رابطاً آمناً لإعادة تعيين كلمة المرور.</p>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="form-control form-control-lg" required autofocus autocomplete="email">
                    </div>
                    <button class="btn btn-warning btn-lg w-100 fw-bold" type="submit">إرسال رابط الاستعادة</button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-warning text-decoration-none">العودة إلى تسجيل الدخول</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
