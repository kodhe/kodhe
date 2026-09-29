@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <h1>Sign in</h1>
    <p class="muted">Example application of the <code>kodhe/auth</code> session guard.</p>

    @if(!empty($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif
    @if(!empty($success))
        <div class="alert alert-success">{{ $success }}</div>
    @endif

    <form method="post" action="{{ $base_url }}login">
        {{-- CSRF token supplied by AuthController (read-only; verified by legacy Input pipeline) --}}
        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required autofocus
               placeholder="demo@kodhe.test" value="demo@kodhe.test">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required
               placeholder="password">

        <label class="remember">
            <input type="checkbox" name="remember" value="1"> Remember me
        </label>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    {{-- Social login buttons (native OAuth, same guard/session as above) --}}
    @include('partials.social_buttons')

    <p class="muted" style="margin-top:16px">
        Demo credentials: <strong>demo@kodhe.test</strong> / <strong>password</strong><br>
        Not a member? <a href="{{ $base_url }}register">Register</a> ·
        <a href="{{ $base_url }}forgot-password">Forgot password?</a>
    </p>
</div>

<style>
.auth-card { max-width: 420px; margin: 60px auto; background: #fff; border: 1px solid var(--border-color);
             border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
.auth-card h1 { font-size: 24px; margin-bottom: 4px; }
.auth-card label { display: block; margin: 14px 0 4px; font-weight: 500; }
.auth-card input[type=email], .auth-card input[type=password] {
    width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; }
.auth-card .remember { display: flex; gap: 8px; align-items: center; margin-top: 14px; font-weight: 400; }
.btn { display: inline-block; margin-top: 20px; width: 100%; padding: 12px; border: 0; border-radius: 8px;
       font-size: 16px; font-weight: 600; cursor: pointer; }
.btn-primary { background: var(--primary-color); color: #fff; }
.alert { padding: 10px 14px; border-radius: 8px; margin: 14px 0; font-size: 14px; }
.alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.muted { color: var(--text-secondary); font-size: 14px; }
</style>
@endsection
