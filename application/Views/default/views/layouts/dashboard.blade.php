<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style type="text/css">
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            background-color: var(--light-bg);
            min-height: 100vh;
        }

        /* Top navigation bar */
        .topbar {
            background: #0f172a;
            color: #fff;
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar .brand { font-weight: 700; font-size: 16px; letter-spacing: -0.3px; }
        .topbar .brand a { color: #fff; text-decoration: none; }

        .topbar nav { display: flex; align-items: center; gap: 16px; font-size: 14px; }
        .topbar nav span.user-email { color: #94a3b8; }
        .topbar nav a.nav-link { color: #cbd5e1; text-decoration: none; }
        .topbar nav a.nav-link:hover { color: #fff; }
        .topbar form { display: inline; }
        .topbar .logout-btn {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            color: #fff; font-size: 13px; padding: 6px 14px;
            border-radius: 6px; cursor: pointer;
        }
        .topbar .logout-btn:hover { background: var(--danger-color); border-color: var(--danger-color); }

        /* Layout */
        .layout { display: flex; min-height: calc(100vh - 56px); }

        .sidebar {
            width: 220px;
            background: #fff;
            border-right: 1px solid var(--border-color);
            padding: 20px 12px;
            flex-shrink: 0;
        }

        .sidebar .section-title {
            font-size: 11px; text-transform: uppercase; letter-spacing: .08em;
            color: var(--text-secondary); padding: 0 12px; margin-bottom: 8px;
        }

        .sidebar a.side-link {
            display: block; padding: 9px 12px; border-radius: 8px;
            color: var(--text-primary); text-decoration: none; font-size: 14px;
        }
        .sidebar a.side-link:hover { background: var(--light-bg); }
        .sidebar a.side-link.active { background: #eff6ff; color: var(--primary-dark); font-weight: 600; }

        .main { flex: 1; padding: 28px clamp(16px, 4vw, 48px); max-width: 960px; }

        .alert { padding: 10px 14px; border-radius: 8px; margin: 0 0 16px; font-size: 14px; }
        .alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

        .verify-banner {
            background: #fffbeb; border: 1px solid #fde68a; color: #92400e;
            padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;
            font-size: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .verify-banner form { margin: 0; }
        .verify-banner button {
            background: var(--warning-color); border: 0; color: #fff; font-weight: 600;
            padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; white-space: nowrap;
        }

        .footer { color: var(--text-secondary); font-size: 12px; padding: 24px 0 8px; }

        @media (max-width: 720px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; border-right: 0; border-bottom: 1px solid var(--border-color); }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand"><a href="{{ $base_url ?? '/' }}">Kodhe</a></div>
        <nav>
            <span class="user-email">{{ $user['email'] ?? '' }}</span>
            <form method="post" action="{{ $base_url ?? '/' }}logout">
                <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
                <button type="submit" class="logout-btn">Log out</button>
            </form>
        </nav>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <div class="section-title">Account</div>
            <a class="side-link active" href="{{ $base_url ?? '/' }}dashboard">Dashboard</a>
            <a class="side-link" href="{{ $base_url ?? '/' }}">Home</a>
        </aside>

        <main class="main">
            @if(!empty($success))
                <div class="alert alert-success">{{ $success }}</div>
            @endif
            @if(!empty($error))
                <div class="alert alert-danger">{{ $error }}</div>
            @endif

            @if(isset($user) && empty($user['email_verified_at']))
                <div class="verify-banner">
                    <span>Your email address is not verified yet.</span>
                    <form method="post" action="{{ $base_url ?? '/' }}email/verification-notification">
                        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
                        <button type="submit">Resend verification link</button>
                    </form>
                </div>
            @endif

            @yield('content')

            <div class="footer">&copy; {{ date('Y') }} Kodhe Framework — authenticated area</div>
        </main>
    </div>
</body>
</html>
