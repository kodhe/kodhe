<?php

use Kodhe\Framework\Http\Routing\Route;

Route::get('/', 'App\Controllers\Welcome@index')->name('welcome');
Route::group(['prefix' => 'welcome'], function() {
    Route::get('/', 'App\Controllers\Welcome@index')->name('welcome.index');
    Route::get('/switch_language/{param}', 'App\Controllers\Welcome@switch_language');
});

/*
|--------------------------------------------------------------------------
| Authentication example (kodhe/auth)
|--------------------------------------------------------------------------
*/

// Guest routes
Route::get('/login', 'App\Controllers\AuthController@index')->name('login');
Route::post('/login', 'App\Controllers\AuthController@attempt')->name('login.attempt');
Route::post('/logout', 'App\Controllers\AuthController@logout')->name('logout')
    ->middleware('auth');

// Registration (guest)
Route::get('/register', 'App\Controllers\AuthController@registerForm')->name('register');
Route::post('/register', 'App\Controllers\AuthController@registerAttempt')->name('register.attempt');

// Password reset (guest)
Route::get('/forgot-password', 'App\Controllers\AuthController@forgotForm')->name('password.forgot');
Route::post('/forgot-password', 'App\Controllers\AuthController@forgotAttempt')->name('password.email');
Route::get('/reset-password/{email}/{token}', 'App\Controllers\AuthController@resetForm')->name('password.reset');
Route::post('/reset-password', 'App\Controllers\AuthController@resetAttempt')->name('password.update');

// E-mail verification (token link is reachable by guests too)
Route::get('/verify-email/{email}/{token}', 'App\Controllers\AuthController@verify')->name('verify');
Route::post('/email/verification-notification', 'App\Controllers\AuthController@resendVerification')
    ->name('verification.resend')->middleware('auth');

// Change password (authenticated)
Route::post('/password', 'App\Controllers\AuthController@changePassword')
    ->name('password.change')->middleware('auth');

// Protected route — 'auth' alias => App\Middlewares\AuthMiddleware
Route::get('/dashboard', 'App\Controllers\AuthController@dashboard')->name('dashboard')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Social login (kodhe/socialite, unified with kodhe/auth)
|--------------------------------------------------------------------------
|
| Same guard/session as the classic login above: a social sign-in produces
| an identical authenticated session (roles, permissions, remember-me).
| Provider credentials live in application/config/socialite.php.
|
*/

// Start the flow -> redirects to the provider's consent screen.
Route::get('/auth/socialite/{provider}', 'App\Controllers\SocialiteAuthController@redirect')
    ->name('socialite.redirect');

// "Connect to my profile" (logged-in users only) — one-time nonce protected.
Route::get('/auth/socialite/{provider}/connect', 'App\Controllers\SocialiteAuthController@connect')
    ->name('socialite.connect')->middleware('auth');

// Finish the flow (both URI shapes are registered by providers/console):
Route::get('/auth/socialite/{provider}/callback', 'App\Controllers\SocialiteAuthController@callback')
    ->name('socialite.callback');
Route::get('/auth/socialite/callback/{provider}', 'App\Controllers\SocialiteAuthController@callback')
    ->name('socialite.callback.legacy');

// Unlink a social identity from the current account.
Route::post('/auth/socialite/{provider}/disconnect', 'App\Controllers\SocialiteAuthController@disconnect')
    ->name('socialite.disconnect')->middleware('auth');

Route::fallback('Kodhe\Framework\Controllers\Error\FileNotFound@index');
