<?php

declare(strict_types=1);

namespace App\Controllers;

use Kodhe\Framework\Auth\Facade as Auth;

/**
 * Example authentication controller for the kodhe/auth package.
 *
 * Demonstrates the full flow: login form, credential check via the
 * session guard, remember-me, logout, and a protected dashboard.
 */
class AuthController extends \CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // The shared layout (layouts.default) calls lang(), so the language
        // helper + welcome language file must be available on every action.
        $this->load->helper(['url', 'language']);
        $current_lang = ($this->session->userdata('language') ?? 'english');
        $this->lang->load('welcome', $current_lang === 'indonesian' ? 'indonesian' : 'english');
    }

    /**
     * GET /login — show the login form (or bounce to dashboard if logged in).
     */
    public function index()
    {
        if (Auth::check()) {
            redirect('dashboard');
        }

        // CSRF token: read-only accessor (never csrf_verify() here — that would
        // consume/regenerate the token on a GET request). The legacy Input
        // pipeline verifies it automatically on POST.
        $security = new \Kodhe\Framework\Support\Legacy\Security();

        return $this->load->view('pages.login', [
            'title'          => 'Login - Kodhe Auth Example',
            'lang'           => ($this->session->userdata('language') ?? 'english') === 'indonesian' ? 'id' : 'en',
            'error'          => $this->session->flashdata('auth_error'),
            'success'        => $this->session->flashdata('auth_success'),
            'base_url'       => base_url(),
            'csrf_token_name'  => $security->get_csrf_token_name(),
            'csrf_hash'      => $security->get_csrf_hash(),
            'current_year'   => date('Y'),
            // Social login buttons (kodhe/socialite, unified with this guard).
            'socialProviders' => $this->socialButtons(),
        ]);
    }

    /* ------------------------------------------------------------------
     | Social login UI helpers (shared by login + dashboard views)
     * ------------------------------------------------------------------ */

    /**
     * Build the provider button list for the views: every provider that is
     * enabled AND has a client_id in application/config/socialite.php.
     * When $withState is true each entry also carries 'connected' (already
     * linked to the current account) — used on the dashboard.
     *
     * @return array<int, array{name:string,label:string,color:string,connected?:bool}>
     */
    protected function socialButtons(bool $withState = false): array
    {
        static $configCache = null;

        if ($configCache === null) {
            $bridgeCfg = $this->readAppConfig('socialite_auth');
            $ssoCfg    = $this->readAppConfig('socialite');

            $configCache = [
                'providers' => $bridgeCfg['providers'] ?? array_keys($ssoCfg['providers'] ?? []),
                'labels'    => $bridgeCfg['labels'] ?? [],
                'colors'    => $bridgeCfg['colors'] ?? [],
                'sso'       => $ssoCfg['providers'] ?? [],
            ];
        }

        $linked = [];
        if ($withState && class_exists(\Kodhe\Framework\Auth\SocialiteAuth::class)) {
            try {
                $bridge = new \Kodhe\Framework\Auth\SocialiteAuth(
                    $this->readAppConfig('socialite_auth'),
                    Auth::guard()
                );
                $linked = $bridge->linkedProviders();
            } catch (\Throwable) {
                $linked = [];
            }
        }

        $buttons = [];
        foreach ((array) $configCache['providers'] as $name) {
            $cfg = $configCache['sso'][$name] ?? null;
            if (!is_array($cfg) || empty($cfg['enabled']) || empty($cfg['client_id'])) {
                continue; // not configured yet -> no half-working button
            }
            $buttons[] = [
                'name'      => $name,
                'label'     => $configCache['labels'][$name] ?? ucfirst($name),
                'color'     => $configCache['colors'][$name] ?? '#374151',
                'connected' => in_array(strtolower((string) $name), $linked, true),
            ];
        }

        return $buttons;
    }

    /**
     * Read application/config/{name}.php directly.
     */
    protected function readAppConfig(string $name): array
    {
        static $cache = [];
        if (isset($cache[$name])) {
            return $cache[$name];
        }

        $path = defined('APPPATH') ? APPPATH . 'config/' . $name . '.php' : null;
        $cfg  = ($path !== null && is_file($path)) ? require $path : [];

        return $cache[$name] = is_array($cfg) ? $cfg : [];
    }

    /**
     * POST /login — attempt authentication through the guard.
     */
    public function attempt()
    {
        $email    = trim((string) ($this->input->post('email') ?? ''));
        $password = (string) ($this->input->post('password') ?? '');
        $remember = !empty($this->input->post('remember'));

        if ($email === '' || $password === '') {
            $this->session->set_flashdata('auth_error', 'Email and password are required.');
            redirect('login');
        }

        // The guard handles: timing-safe lookup, bcrypt verify, transparent
        // rehash, session payload, and single-use remember-me tokens.
        if (Auth::attempt($email, $password, $remember)) {
            $this->session->set_flashdata('auth_success', 'Welcome back, ' . Auth::id('name') . '!');
            redirect('dashboard');
        }

        // Generic message on purpose: never reveal whether the email exists.
        $this->session->set_flashdata('auth_error', 'These credentials do not match our records.');
        redirect('login');
    }

    /**
     * POST /logout — clear session + revoke remember token.
     */
    public function logout()
    {
        Auth::logout();
        $this->session->set_flashdata('auth_success', 'You have been logged out.');
        redirect('login');
    }

    /**
     * GET /dashboard — protected by the 'auth' middleware (see routes).
     */
    public function dashboard()
    {
        $user     = Auth::user();
        $security = new \Kodhe\Framework\Support\Legacy\Security();

        return $this->load->view('pages.dashboard', [
            'title'          => 'Dashboard - Kodhe Auth Example',
            'lang'           => ($this->session->userdata('language') ?? 'english') === 'indonesian' ? 'id' : 'en',
            'user'           => $user,
            'success'        => $this->session->flashdata('auth_success'),
            'error'          => $this->session->flashdata('auth_error'),
            'base_url'       => base_url(),
            'csrf_token_name'  => $security->get_csrf_token_name(),
            'csrf_hash'      => $security->get_csrf_hash(),
            'current_year'   => date('Y'),
            // "Connected accounts" panel state (Auth <-> Socialite bridge).
            'socialProviders' => $this->socialButtons(withState: true),
        ]);
    }

    // ------------------------------------------------------------------
    // Registration
    // ------------------------------------------------------------------

    /**
     * GET /register — show the sign-up form.
     */
    public function registerForm()
    {
        if (Auth::check()) {
            redirect('dashboard');
        }

        $security = new \Kodhe\Framework\Support\Legacy\Security();

        return $this->load->view('pages.register', [
            'title'            => 'Register - Kodhe Auth Example',
            'error'            => $this->session->flashdata('auth_error'),
            'base_url'         => base_url(),
            'csrf_token_name'  => $security->get_csrf_token_name(),
            'csrf_hash'        => $security->get_csrf_hash(),
            'current_year'     => date('Y'),
        ]);
    }

    /**
     * POST /register — create the account via Auth::register().
     */
    public function registerAttempt()
    {
        $name     = trim((string) ($this->input->post('name') ?? ''));
        $email    = trim((string) ($this->input->post('email') ?? ''));
        $password = (string) ($this->input->post('password') ?? '');
        $confirm  = (string) ($this->input->post('password_confirm') ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $this->session->set_flashdata('auth_error', 'Name, email and password are required.');
            redirect('register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('auth_error', 'Please provide a valid email address.');
            redirect('register');
        }
        if (strlen($password) < 8 || $password !== $confirm) {
            $msg = strlen($password) < 8
                ? 'Password must be at least 8 characters.'
                : 'Password confirmation does not match.';
            $this->session->set_flashdata('auth_error', $msg);
            redirect('register');
        }

        try {
            // Guard handles: duplicate check, column whitelist (config
            // "register_columns"), bcrypt hashing and (optional) auto-login.
            $id = Auth::register([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ]);
        } catch (\Kodhe\Framework\Auth\AuthException $e) {
            $this->session->set_flashdata('auth_error', $e->getMessage());
            redirect('register');
        }

        // Kick off e-mail verification; in this demo the raw token is shown
        // once on-screen instead of being mailed (see sendVerificationMail()).
        $token = Auth::requestVerification($email);
        if ($token !== null) {
            $this->session->set_flashdata('verify_link', site_url("verify-email/{$email}/{$token}"));
        }

        $this->session->set_flashdata('auth_success', 'Account #' . $id . ' created. Welcome!');
        redirect(Auth::check() ? 'dashboard' : 'login');
    }

    // ------------------------------------------------------------------
    // E-mail verification
    // ------------------------------------------------------------------

    /**
     * GET /verify-email/{email}/{token} — flip the verified flag.
     */
    public function verify(string $email, string $token)
    {
        if (Auth::verifyEmail($email, $token)) {
            $this->session->set_flashdata('auth_success', 'Your email address has been verified.');
        } else {
            $this->session->set_flashdata('auth_error', 'That verification link is invalid or has expired.');
        }
        redirect(Auth::check() ? 'dashboard' : 'login');
    }

    /**
     * POST /email/verification-notification — resend the verification mail.
     */
    public function resendVerification()
    {
        $user = Auth::user();
        if ($user === null) {
            redirect('login');
        }

        $email = (string) ($user['email'] ?? '');
        $token = Auth::requestVerification($email);
        if ($token !== null) {
            $this->session->set_flashdata('verify_link', site_url("verify-email/{$email}/{$token}"));
            $this->session->set_flashdata('auth_success', 'A new verification link was generated.');
        }
        redirect('dashboard');
    }

    // ------------------------------------------------------------------
    // Password reset (forgot / reset)
    // ------------------------------------------------------------------

    /**
     * GET /forgot-password — request-a-link form.
     */
    public function forgotForm()
    {
        $security = new \Kodhe\Framework\Support\Legacy\Security();

        return $this->load->view('pages.forgot_password', [
            'title'            => 'Forgot Password - Kodhe Auth Example',
            'error'            => $this->session->flashdata('auth_error'),
            'success'          => $this->session->flashdata('auth_success'),
            'reset_link'       => $this->session->flashdata('reset_link'),
            'base_url'         => base_url(),
            'csrf_token_name'  => $security->get_csrf_token_name(),
            'csrf_hash'        => $security->get_csrf_hash(),
            'current_year'     => date('Y'),
        ]);
    }

    /**
     * POST /forgot-password — generate the single-use reset token.
     *
     * Always shows the same generic message so the form cannot be used to
     * enumerate registered addresses. The demo surfaces the raw token as a
     * link; a real app would mail it (sendResetMail()).
     */
    public function forgotAttempt()
    {
        $email = trim((string) ($this->input->post('email') ?? ''));

        if ($email !== '') {
            $token = Auth::sendPasswordReset($email);
            if ($token !== null) {
                $this->session->set_flashdata('reset_link', site_url("reset-password/{$email}/{$token}"));
            }
        }

        $this->session->set_flashdata('auth_success', 'If that address exists in our records, a reset link has been sent.');
        redirect('forgot-password');
    }

    /**
     * GET /reset-password/{email}/{token} — choose-a-new-password form.
     */
    public function resetForm(string $email, string $token)
    {
        $security = new \Kodhe\Framework\Support\Legacy\Security();

        return $this->load->view('pages.reset_password', [
            'title'            => 'Reset Password - Kodhe Auth Example',
            'email'            => $email,
            'token'            => $token,
            'error'            => $this->session->flashdata('auth_error'),
            'base_url'         => base_url(),
            'csrf_token_name'  => $security->get_csrf_token_name(),
            'csrf_hash'        => $security->get_csrf_hash(),
            'current_year'     => date('Y'),
        ]);
    }

    /**
     * POST /reset-password — consume the token and set the new password.
     */
    public function resetAttempt()
    {
        $email    = trim((string) ($this->input->post('email') ?? ''));
        $token    = (string) ($this->input->post('token') ?? '');
        $password = (string) ($this->input->post('password') ?? '');
        $confirm  = (string) ($this->input->post('password_confirm') ?? '');

        if (strlen($password) < 8 || $password !== $confirm) {
            $msg = strlen($password) < 8
                ? 'Password must be at least 8 characters.'
                : 'Password confirmation does not match.';
            $this->session->set_flashdata('auth_error', $msg);
            redirect("reset-password/{$email}/{$token}");
        }

        if (Auth::resetPassword($email, $token, $password)) {
            $this->session->set_flashdata('auth_success', 'Password reset — you can sign in with your new password.');
            redirect('login');
        }

        $this->session->set_flashdata('auth_error', 'This reset link is invalid or has expired.');
        redirect('forgot-password');
    }

    // ------------------------------------------------------------------
    // Change password (authenticated)
    // ------------------------------------------------------------------

    /**
     * POST /password — change the current user's password.
     */
    public function changePassword()
    {
        $current = (string) ($this->input->post('current_password') ?? '');
        $new     = (string) ($this->input->post('password') ?? '');
        $confirm = (string) ($this->input->post('password_confirm') ?? '');

        if (strlen($new) < 8 || $new !== $confirm) {
            $msg = strlen($new) < 8
                ? 'New password must be at least 8 characters.'
                : 'Password confirmation does not match.';
            $this->session->set_flashdata('auth_error', $msg);
            redirect('dashboard');
        }

        if (Auth::changePassword($current, $new)) {
            $this->session->set_flashdata('auth_success', 'Password changed successfully.');
        } else {
            $this->session->set_flashdata('auth_error', 'Your current password is incorrect.');
        }
        redirect('dashboard');
    }
}
