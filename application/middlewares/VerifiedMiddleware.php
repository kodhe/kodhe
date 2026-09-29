<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Auth\Facade as Auth;

class VerifiedMiddleware extends Middleware
{
    /**
     * Check if the authenticated user's e-mail is verified.
     *
     * Uses the kodhe/auth guard (Auth::isVerified()), which reads the
     * configurable "verify_column" (default: email_verified_at) from the
     * fresh user record — not an ad-hoc session flag.
     */
    public function before($request, $response, $arguments = null)
    {
        log_message('debug', 'VerifiedMiddleware::before() executed');

        if (!Auth::check()) {
            // Not logged in at all: send them to the login page.
            return $this->redirect('/login', 302);
        }

        if (!Auth::isVerified()) {
            log_message('debug', 'User email not verified');

            // Check if this is an API request
            if (strpos($request->server('REQUEST_URI'), '/api/') === 0) {
                return $this->json(['error' => 'Email not verified'], 403);
            }

            return $this->redirect('/email/verify', 302);
        }

        return null;
    }
}
