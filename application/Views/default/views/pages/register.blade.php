@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <h1>Create an account</h1>
    <p class="muted">Registration uses <code>Auth::register()</code> — the guard hashes the password and whitelists columns.</p>

    @if(!empty($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <form method="post" action="{{ $base_url }}register">
        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">

        <label for="name">Name</label>
        <input id="name" type="text" name="name" required autofocus>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required placeholder="you@example.com">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required minlength="8">

        <label for="password_confirm">Confirm password</label>
        <input id="password_confirm" type="password" name="password_confirm" required minlength="8">

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p class="muted" style="margin-top:16px">
        Already registered? <a href="{{ $base_url }}login">Sign in</a> ·
        <a href="{{ $base_url }}forgot-password">Forgot password?</a>
    </p>
</div>

<style>
.auth-card { max-width: 420px; margin: 60px auto; background: #fff; border: 1px solid var(--border-color);
             border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
.auth-card h1 { font-size: 24px; margin-bottom: 4px; }
.auth-card label { display: block; margin: 14px 0 4px; font-weight: 500; }
.auth-card input[type=text], .auth-card input[type=email], .auth-card input[type=password] {
    width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; }
.btn { display: inline-block; margin-top: 20px; width: 100%; padding: 12px; border: 0; border-radius: 8px;
       font-size: 16px; font-weight: 600; cursor: pointer; }
.btn-primary { background: var(--primary-color); color: #fff; }
.alert { padding: 10px 14px; border-radius: 8px; margin: 14px 0; font-size: 14px; }
.alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.muted { color: var(--text-secondary); font-size: 14px; }
</style>
@endsection
