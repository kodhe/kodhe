<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Authentication (kodhe/auth)
|--------------------------------------------------------------------------
|
| Configuration for the session-based auth guard shipped with the
| Kodhe framework. Values here are merged over the guard defaults
| (see Kodhe\Framework\Auth\Auth::defaultConfig()).
|
*/

return [

    // FQCN of your UserProvider implementation (must implement
    // Kodhe\Framework\Auth\Contracts\UserProviderInterface).
    'provider' => App\Providers\DatabaseUserProvider::class,

    // Column used to look up users at login (email / username).
    'identifier_column' => 'email',

    // Column holding the hashed password.
    'password_column' => 'password',

    // Primary key column of the users table.
    'id_column' => 'id',

    // Session slot that holds the auth payload.
    'session_key' => 'auth_user',

    // Remember-me cookie name, lifetime (seconds) and storage column.
    'remember_cookie'  => 'kodhe_remember',
    'remember_seconds' => 60 * 60 * 24 * 14, // two weeks
    'remember_column'  => 'remember_token',

    // Password hashing (passed to password_hash()).
    'algo'    => PASSWORD_BCRYPT,
    'options' => ['cost' => 11],

    // Store only SHA-256(token) server-side so a DB leak cannot be
    // replayed as a cookie. Leave true unless you have a good reason.
    'hash_remember' => true,

    /*
    |--------------------------------------------------------------------------
    | Registration / password reset / e-mail verification
    |--------------------------------------------------------------------------
    */

    // Extra input columns copied into the new record on Auth::register()
    // (whitelist — prevents mass-assignment). Password is never taken
    // from this list; it is hashed by the guard automatically.
    'register_columns'    => ['name'],

    // Log the user straight in after a successful register().
    'register_auto_login' => true,

    // Failed-login throttling (0 disables the lockout entirely).
    'max_attempts'        => 5,
    'lockout_seconds'     => 900,      // 15 minutes

    // Link lifetimes for the single-use tokens.
    'reset_ttl'           => 3600,     // password reset: 1 hour
    'verify_ttl'          => 86400 * 3,// e-mail verification: 3 days

    // Column flipped to a timestamp once the address is verified.
    'verify_column'       => 'email_verified_at',

    // Where AuthMiddleware redirects unauthenticated visitors.
    'redirect_unauthenticated' => '/login',

    // Route name used after successful login.
    'home_url' => '/dashboard',

    /*
    |--------------------------------------------------------------------------
    | Authorization (roles / permissions / hierarchy / ACL)
    |--------------------------------------------------------------------------
    |
    | Resolved by the guard through App\Providers\DatabaseUserProvider,
    | which implements AuthorizableProviderInterface on top of the
    | roles / permissions / acl tables (see database/migrations).
    | Use Auth::can('post.edit'), Auth::hasRole('admin'), etc.
    |
    */

    // Column on the user record holding a role name (fallback when the
    // provider has no role rows for the user). The demo app resolves
    // roles via users.role_id -> roles.name instead.
    'role_column'       => 'role',

    // Optional column with explicit per-user permission grants
    // (comma-separated or JSON array). Not present in the demo schema.
    'permission_column' => null,

    // Role hierarchy definitions. Left empty because the demo provider
    // loads roles/levels/inherits from the "roles" table; put static
    // definitions here (['admin' => ['level' => 10, 'inherits' => ['editor']]])
    // if you prefer config over storage.
    'roles'             => [],

    // Static ACL rules, merged with the rows loaded from the "acl" table.
    'acl_rules'         => [],

    // Decision when NO rule matches and no permission grant covers the
    // request: false = fail closed (recommended).
    'acl_default'       => false,

    // Hierarchical user groups (GroupTree definitions); rules can then
    // address subjects like '@root/editors'. Unused in the demo app.
    'groups'            => [],

];
