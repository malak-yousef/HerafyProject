<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card border-0 shadow-sm" style="max-width: 520px; width: 100%;">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 fw-bold mb-3">تعيين كلمة مرور جديدة</h1>

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">البريد الإلكتروني</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $email) }}"
                            class="form-control form-control-lg" required autocomplete="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">كلمة المرور الجديدة</label>
                        <input id="password" name="password" type="password"
                            class="form-control form-control-lg" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">تأكيد كلمة المرور</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="form-control form-control-lg" required autocomplete="new-password">
                    </div>
                    <button class="btn btn-warning btn-lg w-100 fw-bold" type="submit">تغيير كلمة المرور</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
