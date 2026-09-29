<?php

declare(strict_types=1);

namespace App\Controllers;

use Kodhe\Framework\Auth\Facade as Auth;
use Kodhe\Framework\Auth\SocialiteAuth;
use Kodhe\Framework\Socialite\Http\CurlClient;
use Kodhe\Framework\Socialite\Manager;
use Kodhe\Framework\Socialite\RedirectResponse;
use Kodhe\Framework\Socialite\Session\SessionStateStore;

/**
 * Social login controller for the kodhe project.
 *
 * Wires the native OAuth client (kodhe/socialite) into the shared auth
 * guard through Kodhe\Framework\Auth\SocialiteAuth, so a social login and
 * a password login produce exactly the same session, roles and events.
 *
 * Routes (see application/routes/web.php):
 *
 *   GET /auth/socialite/{provider}                     start    -> provider consent page
 *   GET /auth/socialite/{provider}/connect             link     -> provider (logged-in users only)
 *   GET /auth/socialite/{provider}/callback            finish   -> login + redirect home
 *   GET /auth/socialite/callback/{provider}            legacy alias of the above
 *   POST /auth/socialite/{provider}/disconnect         unlink   -> dashboard
 */
class SocialiteAuthController extends \CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url']);
    }

    /* ------------------------------------------------------------------
     | Route entry points
     * ------------------------------------------------------------------ */

    /**
     * GET /auth/socialite/{provider} — begin the social login flow.
     */
    public function redirect($provider = '')
    {
        return $this->emit(
            $this->bridge()->handle((string) $provider, 'start', [
                'intended' => $this->intendedUrl(),
            ])
        );
    }

    /**
     * GET /auth/socialite/{provider}/connect — link a social identity to
     * the CURRENTLY logged-in profile (nonce-protected one-shot flow).
     * Guests transparently fall back to a normal social login.
     */
    public function connect($provider = '')
    {
        return $this->emit(
            $this->bridge()->handle((string) $provider, 'start', [
                'connect'  => true,
                'intended' => '/dashboard',
            ])
        );
    }

    /**
     * GET /auth/socialite/{provider}/callback
     * GET /auth/socialite/callback/{provider} — resolve the identity,
     * find/create/link the local account, log in through the shared guard.
     */
    public function callback($provider = '', $legacy = null)
    {
        // Legacy URI shape appends the provider at the END of the callback
        // path (".../callback/google"), which lands in $legacy here.
        if ($provider === '' && is_string($legacy) && $legacy !== '') {
            $provider = $legacy;
        }

        $bridge   = $this->bridge();
        $before   = $bridge->guard()->id('email');
        $response = $bridge->handle((string) $provider, 'callback');

        if ($this->isSuccess($response, $bridge)) {
            $after = $bridge->guard()->id('email') ?? '(user)';
            $this->session->set_flashdata(
                'auth_success',
                $before !== null && $before === $after
                    ? "Your {$provider} account is now connected."
                    : "Signed in with " . ucfirst((string) $provider) . ". Welcome, {$after}!"
            );
        } else {
            $this->session->set_flashdata(
                'auth_error',
                "Social login via {$provider} failed. Please try again or sign in with your email."
            );
        }

        return $this->emit($response);
    }

    /**
     * POST /auth/socialite/{provider}/disconnect — unlink an identity.
     * Refuses (with a clear flash message) when it would lock the user out.
     */
    public function disconnect($provider = '')
    {
        $bridge = $this->bridge();

        try {
            $bridge->disconnect((string) $provider);
            $this->session->set_flashdata(
                'auth_success',
                ucfirst((string) $provider) . ' has been disconnected from your profile.'
            );
        } catch (\Throwable $e) {
            $this->session->set_flashdata('auth_error', $e->getMessage());
        }

        redirect('dashboard');
    }

    /* ------------------------------------------------------------------
     | Shared bridge instance (per request)
     * ------------------------------------------------------------------ */

    protected ?SocialiteAuth $bridgeInstance = null;

    /**
     * Build (once per request) the Auth<->Socialite bridge configured from
     * application/config/socialite_auth.php + socialite.php. The guard is
     * the SAME Auth facade instance the classic login uses, so sessions,
     * remember-me cookies and events are fully shared between both modes.
     */
    protected function bridge(): SocialiteAuth
    {
        if ($this->bridgeInstance instanceof SocialiteAuth) {
            return $this->bridgeInstance;
        }

        $authConfig = $this->readConfig('socialite_auth');
        $ssoConfig  = $this->readConfig('socialite');

        $manager = new Manager($ssoConfig);
        // State (CSRF) lives in $_SESSION — the same store CodeIgniter's
        // session object writes to, so it survives the provider round-trip.
        $manager->setStateStore(new SessionStateStore());
        $manager->setHttpClient(new CurlClient());

        // Same singleton guard the classic (password) login uses: identical
        // session payload, remember-me cookie, roles and events.
        $bridge = new SocialiteAuth($authConfig, Auth::guard());
        $bridge->setManager($manager);
        // Bridge reads/writes the one-time connect nonce via this session too.
        $bridge->setSession($this->session ?? null);

        return $this->bridgeInstance = $bridge;
    }

    /**
     * Read application/config/{name}.php directly (works whether or not the
     * legacy Config object already loaded it).
     */
    protected function readConfig(string $name): array
    {
        $path = defined('APPPATH') ? APPPATH . 'config/' . $name . '.php' : null;

        if ($path !== null && is_file($path)) {
            $cfg = require $path;
            if (is_array($cfg)) {
                return $cfg;
            }
        }

        return [];
    }

    /**
     * Remember where the user wanted to go (AuthMiddleware stashes it in
     * the session before bouncing to /login).
     */
    protected function intendedUrl(): ?string
    {
        $url = $this->session->userdata('url_intended');

        return is_string($url) && $url !== '' ? $url : null;
    }

    /**
     * handle() always returns a RedirectResponse; on failure it points at
     * config['redirect_on_error']. We detect success by comparing targets
     * AND by checking the guard actually holds a user after a callback.
     */
    protected function isSuccess(RedirectResponse $response, SocialiteAuth $bridge): bool
    {
        $errorTarget = (string) ($bridge->getConfigValue('redirect_on_error') ?? '/login');

        return rtrim($response->target, '/') !== rtrim($errorTarget, '/')
            || $bridge->guard()->check();
    }

    /**
     * Send the RedirectResponse (headers not yet emitted in this framework
     * pipeline at this point — same mechanism AuthController relies on).
     */
    protected function emit(RedirectResponse $response)
    {
        if (!headers_sent()) {
            header('Location: ' . $response->target, true, 302);
        }

        return null;
    }
}
