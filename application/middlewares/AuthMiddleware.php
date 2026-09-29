<?php

declare(strict_types=1);

namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Auth\Facade as Auth;

/**
 * Protects routes using the kodhe/auth session guard.
 *
 * Registered as the 'auth' alias in application/config/middleware.php
 * and applied per-route in application/routes/web.php.
 */
class AuthMiddleware extends Middleware
{
    public function before($request, $response, $arguments = null)
    {
        // The guard re-validates the session payload against storage on
        // every request, so deleted/revoked users are logged out here
        // automatically (and remember-me cookies are honoured).
        if (!Auth::check()) {
            $config = require APPPATH . 'config/auth.php';
            return $this->redirect($config['redirect_unauthenticated'] ?? '/login', 302);
        }

        return null; // continue to the controller
    }

    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        return $controllerResult;
    }
}
