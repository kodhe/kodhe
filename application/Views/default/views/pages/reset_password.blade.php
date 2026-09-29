@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <h1>Reset password</h1>
    <p class="muted">Setting a new password for <strong>{{ $email }}</strong>.</p>

    @if(!empty($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <form method="post" action="{{ $base_url }}reset-password">
        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="password">New password</label>
        <input id="password" type="password" name="password" required minlength="8" autofocus>

        <label for="password_confirm">Confirm password</label>
        <input id="password_confirm" type="password" name="password_confirm" required minlength="8">

        <button type="submit" class="btn btn-primary">Reset password</button>
    </form>

    <p class="muted" style="margin-top:16px"><a href="{{ $base_url }}login">Back to login</a></p>
</div>

<style>
.auth-card { max-width: 420px; margin: 60px auto; background: #fff; border: 1px solid var(--border-color);
             border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
.auth-card h1 { font-size: 24px; margin-bottom: 4px; }
.auth-card label { display: block; margin: 14px 0 4px; font-weight: 500; }
.auth-card input[type=password] {
    width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; }
.btn { display: inline-block; margin-top: 20px; width: 100%; padding: 12px; border: 0; border-radius: 8px;
       font-size: 16px; font-weight: 600; cursor: pointer; }
.btn-primary { background: var(--primary-color); color: #fff; }
.alert { padding: 10px 14px; border-radius: 8px; margin: 14px 0; font-size: 14px; }
.alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.muted { color: var(--text-secondary); font-size: 14px; }
</style>
@endsection
