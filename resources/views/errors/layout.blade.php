<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - حرفي</title>

    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }
        .error-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 520px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .error-code {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1;
            color: #ffc107;
            margin-bottom: 0.75rem;
        }
        .error-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }
        .error-message {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-herafy-primary {
            background-color: #ffc107;
            color: #212529;
            font-weight: 600;
            border: none;
            padding: 0.6rem 1.4rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-herafy-primary:hover {
            background-color: #e6a800;
            color: #212529;
        }
        .btn-herafy-secondary {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            border: none;
            padding: 0.6rem 1.4rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-herafy-secondary:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="error-card">
            <div class="mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none fs-3 fw-bold text-dark">
                    <i class="fas fa-hammer text-warning" aria-hidden="true"></i> حرفي
                </a>
            </div>
            <div class="error-code">@yield('code')</div>
            <h1 class="error-title">@yield('heading')</h1>
            <p class="error-message">@yield('message')</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ url('/') }}" class="btn-herafy-primary">
                    <i class="fas fa-home" aria-hidden="true"></i> الرئيسية
                </a>
                <button onclick="window.history.back()" class="btn-herafy-secondary">
                    <i class="fas fa-arrow-right" aria-hidden="true"></i> العودة للخلف
                </button>
            </div>
        </div>
    </div>
</body>
</html>
