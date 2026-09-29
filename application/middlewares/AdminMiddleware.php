<?php

declare(strict_types=1);

namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Auth\Support\Facade as Auth;

/**
 * Gate for administrative areas.
 *
 * Built on top of kodhe/auth's authorization layer instead of an ad-hoc
 * session flag: the user must hold the "admin" (or "superadmin") role —
 * resolved through the provider / role column and the role hierarchy —
 * or pass the equivalent permission check via the ACL ("admin.access").
 *
 * Usage (see config/middleware.php group 'admin'):
 *   ->middleware('auth')->middleware('admin')
 */
class AdminMiddleware extends Middleware
{
    /** Roles considered administrator (hierarchy-aware via Auth::hasRole). */
    protected array $adminRoles = ['superadmin', 'admin'];

    /** Permission name the ACL may grant to other roles for admin access. */
    protected string $adminPermission = 'admin.access';

    public function before($request, $response, $arguments = null)
    {
        // Not logged in at all: send them to the login page first.
        if (!Auth::check()) {
            $config = require APPPATH . 'config/auth.php';
            return $this->redirect($config['redirect_unauthenticated'] ?? '/login', 302);
        }

        // Role check OR explicit permission/ACL grant — either one passes.
        if (!Auth::anyRole($this->adminRoles) && !Auth::allows($this->adminPermission)) {
            log_message('debug', 'AdminMiddleware: access denied for user ' . var_export(Auth::id(), true));

            if (strpos((string) $request->server('REQUEST_URI'), '/api/') === 0) {
                return $this->json(['error' => 'Forbidden: administrator access required'], 403);
            }

            if (function_exists('get_instance')) {
                $ci = get_instance();
                if (isset($ci->session)) {
                    $ci->session->set_flashdata('auth_error', 'Administrator access required.');
                }
            }

            return $this->redirect('/dashboard', 302);
        }

        return null; // continue to the controller
    }

    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        return $controllerResult;
    }
}
