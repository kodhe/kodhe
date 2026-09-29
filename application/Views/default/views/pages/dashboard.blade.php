@extends('layouts.dashboard')

@section('content')
<div class="auth-card">
    @if(!empty($success))
        <div class="alert alert-success">{{ $success }}</div>
    @endif
    @if(!empty($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <h1>Dashboard</h1>
    <p class="muted">You can only see this page while authenticated (protected by the <code>auth</code> middleware).</p>

    <table class="user-table">
        <tr><th>ID</th><td>{{ $user['id'] ?? '-' }}</td></tr>
        <tr><th>Name</th><td>{{ $user['name'] ?? '-' }}</td></tr>
        <tr><th>Email</th><td>{{ $user['email'] ?? '-' }}</td></tr>
        <tr><th>Email verified</th><td>{{ !empty($user['email_verified_at']) ? 'yes (' . $user['email_verified_at'] . ')' : 'no' }}</td></tr>
        <tr><th>Session re-validated</th><td>every request (anti stale-role)</td></tr>
    </table>

    @if(empty($user['email_verified_at']))
        <form method="post" action="{{ $base_url }}email/verification-notification" style="margin-top:12px">
            <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
            <button type="submit" class="btn btn-secondary">Resend verification link</button>
        </form>
    @endif

    <h2 style="margin-top:28px; font-size:18px">Connected accounts</h2>
    @if(empty($socialProviders))
        <p class="muted">No social providers are configured yet. Add credentials in
            <code>application/config/socialite.php</code> (or the matching environment
            variables) to enable "Continue with Google / GitHub / …".</p>
    @else
        <table class="user-table social-accounts">
            @foreach($socialProviders as $sp)
                <tr>
                    <th>{{ $sp['label'] }}</th>
                    <td>
                        @if(!empty($sp['connected']))
                            <span class="tag tag-ok">Connected</span>
                            <form method="post" action="{{ $base_url }}auth/socialite/{{ $sp['name'] }}/disconnect" style="display:inline">
                                <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
                                <button type="submit" class="link-btn" onclick="return confirm('Disconnect {{ $sp['label'] }}?')">Disconnect</button>
                            </form>
                        @else
                            <a class="link-btn link-connect" href="{{ $base_url }}auth/socialite/{{ $sp['name'] }}/connect">Connect</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <p class="muted" style="font-size:13px">
            Connecting lets you sign in to this account through that provider too —
            same profile, same roles, one session system. Disconnecting never locks
            you out: it is refused when no other login method would remain.
        </p>
    @endif

    <h2 style="margin-top:28px; font-size:18px">Change password</h2>
    <form method="post" action="{{ $base_url }}password">
        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
        <label for="current_password">Current password</label>
        <input id="current_password" type="password" name="current_password" required>
        <label for="new_password">New password</label>
        <input id="new_password" type="password" name="password" required minlength="8">
        <label for="new_password_confirm">Confirm new password</label>
        <input id="new_password_confirm" type="password" name="password_confirm" required minlength="8">
        <button type="submit" class="btn btn-primary">Change password</button>
    </form>

    <form method="post" action="{{ $base_url }}logout" style="margin-top:20px">
        {{-- CSRF token supplied by AuthController (read-only; verified by legacy Input pipeline) --}}
        <input type="hidden" name="{{ $csrf_token_name ?? 'csrf_test_name' }}" value="{{ $csrf_hash ?? '' }}">
        <button type="submit" class="btn btn-danger">Log out</button>
    </form>
</div>

<style>
.user-table { width: 100%; border-collapse: collapse; margin-top: 18px; }
.user-table th { text-align: left; color: var(--text-secondary); font-weight: 500; padding: 8px 12px 8px 0; width: 200px; }
.user-table td { padding: 8px 0; border-bottom: 1px solid var(--border-color); }
.auth-card label { display: block; margin: 14px 0 4px; font-weight: 500; }
.auth-card input[type=password] {
    width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; }
.btn { display: inline-block; margin-top: 20px; width: 100%; padding: 12px; border: 0; border-radius: 8px;
       font-size: 16px; font-weight: 600; cursor: pointer; }
.btn-primary { background: var(--primary-color); color: #fff; }
.btn-secondary { background: var(--border-color); color: inherit; width: auto; padding: 10px 24px; }
.btn-danger { background: var(--danger-color); color: #fff; width: auto; padding: 10px 24px; }
.tag { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 13px; font-weight: 600; }
.tag-ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.link-btn { background: none; border: 0; padding: 0 6px; font: inherit; font-size: 14px; color: var(--primary-color);
            cursor: pointer; text-decoration: underline; }
.link-connect { text-decoration: none; font-weight: 600; }
.alert { padding: 10px 14px; border-radius: 8px; margin: 14px 0; font-size: 14px; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
</style>
@endsection
