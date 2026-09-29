@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <h1>Forgot password</h1>
    <p class="muted"><code>Auth::sendPasswordReset()</code> stores only a SHA-256 hash of a single-use token.</p>

    @if(!empty($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif
    @if(!empty($success))
        <div class="alert alert-success">{{ $success }}</div>
    @endif
    @if(!empty($reset_link))
        <div class="alert alert-info">Demo mode — no mailer configured. Your reset link:
            <a href="{{ $reset_link }}">{{ $reset_link }}</a>
        </div>
    @endif

    <form method="post" action="{{ $base_url }}forgot-password">
        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required autofocus>

        <button type="submit" class="btn btn-primary">Send reset link</button>
    </form>

    <p class="muted" style="margin-top:16px"><a href="{{ $base_url }}login">Back to login</a></p>
</div>

<style>
.auth-card { max-width: 420px; margin: 60px auto; background: #fff; border: 1px solid var(--border-color);
             border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
.auth-card h1 { font-size: 24px; margin-bottom: 4px; }
.auth-card label { display: block; margin: 14px 0 4px; font-weight: 500; }
.auth-card input[type=email] {
    width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; }
.btn { display: inline-block; margin-top: 20px; width: 100%; padding: 12px; border: 0; border-radius: 8px;
       font-size: 16px; font-weight: 600; cursor: pointer; }
.btn-primary { background: var(--primary-color); color: #fff; }
.alert { padding: 10px 14px; border-radius: 8px; margin: 14px 0; font-size: 14px; word-break: break-all; }
.alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.alert-info { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.muted { color: var(--text-secondary); font-size: 14px; }
</style>
@endsection
