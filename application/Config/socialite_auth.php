<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Auth <-> Socialite bridge configuration (kodhe project)
|--------------------------------------------------------------------------
|
| This file is picked up automatically by Kodhe\Framework\Auth\SocialiteAuth
| (see SocialiteAuth::readConfigFile() -> APPPATH.'config/socialite_auth.php').
|
| Provider credentials themselves live in application/config/socialite.php
| (the kodhe/socialite component config). Here we only configure the
| *integration* between password auth and social login: which providers are
| offered in the UI, where users land after login, how accounts are matched
| and linked, and whether OAuth tokens are stored for later API calls.
|
*/

return [

    // Path the {provider} routes use (SocialiteAuthController registers
    // both variants; keep in sync with 'callback_uri' in socialite.php).
    'login_route' => '/auth/socialite',

    // Where to send users after a successful social login when there is no
    // "intended" URL remembered from the start of the flow.
    'redirect_after_login' => '/dashboard',

    // Where to bounce users when the flow fails (unknown provider, bad
    // state, cancelled consent, ...). The controller flashes the reason as
    // 'auth_error' so the login page can show it.
    'redirect_on_error' => '/login',

    // Create a local account automatically when the e-mail returned by the
    // provider does not exist yet? Set false to require an existing account
    // (or registration) before social login works.
    'create_users' => true,

    // When a social identity's e-mail matches an existing account, link the
    // two automatically instead of creating a duplicate user.
    'match_by_email' => true,

    // Store access/refresh tokens per provider on the user record
    // ('social_accounts' column / array key) so the app can call provider
    // APIs later via accessTokenFor() / refreshAccessToken().
    'store_tokens' => true,

    // Never throw out of handle(); always redirect to redirect_on_error.
    'throw_on_error' => false,

    // Session slot holding the one-time "connect to my profile" nonce.
    'connect_key' => 'socialite_connect_nonce',

    /*
    |--------------------------------------------------------------------------
    | Providers shown in the UI
    |--------------------------------------------------------------------------
    |
    | Only providers that are BOTH listed here AND enabled + configured with a
    | client_id in socialite.php appear as buttons on the login page and the
    | dashboard "Connected accounts" panel. Order defines button order.
    |
    */
    'providers' => ['google', 'github', 'facebook', 'gitlab', 'linkedin', 'twitter'],

    // Human labels + brand colours used by the Blade views.
    'labels' => [
        'google'   => 'Google',
        'github'   => 'GitHub',
        'facebook' => 'Facebook',
        'gitlab'   => 'GitLab',
        'linkedin' => 'LinkedIn',
        'twitter'  => 'X / Twitter',
    ],

    'colors' => [
        'google'   => '#4285F4',
        'github'   => '#24292f',
        'facebook' => '#1877F2',
        'gitlab'   => '#FC6D26',
        'linkedin' => '#0A66C2',
        'twitter'  => '#000000',
    ],
];
