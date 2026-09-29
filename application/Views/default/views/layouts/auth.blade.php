<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Authentication' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style type="text/css">
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-bg: #f1f5f9;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            background: linear-gradient(135deg, #eff6ff 0%, var(--light-bg) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-shell { width: 100%; max-width: 460px; }

        .auth-brand { text-align: center; margin-bottom: 20px; }
        .auth-brand a {
            font-size: 20px; font-weight: 700; color: var(--primary-dark);
            text-decoration: none; letter-spacing: -0.5px;
        }
        .auth-brand p { color: var(--text-secondary); font-size: 13px; margin-top: 4px; }

        .alert { padding: 10px 14px; border-radius: 8px; margin: 0 0 16px; font-size: 14px; }
        .alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

        .footer { text-align: center; color: var(--text-secondary); font-size: 12px; margin-top: 24px; }
        .footer a { color: var(--primary-color); text-decoration: none; }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-brand">
            <a href="{{ $base_url ?? '/' }}">Kodhe</a>
            <p>Example application of the <code>kodhe/auth</code> session guard.</p>
        </div>

        @if(!empty($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @endif
        @if(!empty($success))
            <div class="alert alert-success">{{ $success }}</div>
        @endif

        @yield('content')

        <div class="footer">&copy; {{ date('Y') }} Kodhe Framework</div>
    </div>
</body>
</html>
