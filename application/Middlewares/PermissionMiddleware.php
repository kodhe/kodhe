<?php

declare(strict_types=1);

namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Auth\Support\Facade as Auth;

/**
 * Permission gate on top of the 'auth' middleware.
 *
 * Registered as the 'permission' alias in application/config/middleware.php
 * and used per-route, e.g.:
 *
 *   Route::get('/admin/users', ...)->middleware('auth')->middleware('permission:user.manage');
 *   Route::post('/post/{id}/edit', ...)->middleware('permission:post.edit,own');
 *
 * Parameter format: "perm[.perm2,...][:contextKey]"
 *   - one or more permission names separated by ',' (ALL must pass — see
 *     Auth::hasAllPermissions(); use the ACL to widen grants instead);
 *   - optional trailing ":own" injects context ['own' => true] so
 *     scope-aware ACL rules ({"own":true}) can resolve ownership.
 *
 * The actual decision (direct grants + role hierarchy + multi-rule ACL)
 * lives entirely in kodhe/auth; this middleware only wires it into the
 * route pipeline.
 */
class PermissionMiddleware extends Middleware
{
    public function before($request, $response, $arguments = null)
    {
        // Not logged in at all -> login page (mirrors AuthMiddleware).
        if (!Auth::check()) {
            $config = require APPPATH . 'config/auth.php';
            return $this->redirect($config['redirect_unauthenticated'] ?? '/login', 302);
        }

        [$permissions, $context] = $this->resolveParameters($arguments);

        if ($permissions === []) {
            // Misconfiguration: fail closed rather than opening the route.
            log_message('error', 'PermissionMiddleware: no permission given (use permission:name[,name][:own]).');
            return $this->forbidden($request, 'No permission configured for this route.');
        }

        // Full authorization: grants + hierarchy + ACL with context.
        if (!Auth::hasAllPermissions($permissions, null, $context)) {
            log_message('debug', 'PermissionMiddleware: denied for user ' . var_export(Auth::id(), true)
                . ' permissions=' . implode(',', $permissions));
            return $this->forbidden($request, 'You do not have permission to access this resource.');
        }

        return null; // continue to the controller
    }

    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        return $controllerResult;
    }

    /**
     * Normalize middleware parameters into [permissions[], context[]].
     *
     * Parameters arrive either as an array (setParameters()/pipeline) or a
     * raw ":"-joined string, depending on how the router invoked us.
     *
     * @return array{0: string[], 1: array}
     */
    private function resolveParameters($arguments): array
    {
        $params = is_array($arguments) ? $arguments : (string) $arguments;
        $raw = trim(implode(':', array_map('strval', (array) $params)), ':');
        if ($raw === '') {
            return [[], []];
        }

        $parts = explode(':', $raw);
        $context = [];
        foreach (array_slice($parts, 1) as $flag) {
            if ($flag === 'own') {
                $context['own'] = true;
            } elseif (str_contains($flag, '=')) {
                [$k, $v] = explode('=', $flag, 2);
                $context[$k] = $v;
            }
        }

        $permissions = array_values(array_filter(array_map('trim', explode(',', $parts[0]))));

        return [$permissions, $context];
    }

    /**
     * 403 for API requests, redirect-with-flash for web requests.
     */
    private function forbidden($request, string $message)
    {
        $uri = (string) ($request->server('REQUEST_URI') ?? '');
        if (strpos($uri, '/api/') === 0) {
            return $this->json(['error' => $message], 403);
        }

        if (function_exists('get_instance')) {
            $ci = get_instance();
            if (isset($ci->session)) {
                $ci->session->set_flashdata('auth_error', $message);
            }
        }

        return $this->redirect('/dashboard', 302);
    }
}
