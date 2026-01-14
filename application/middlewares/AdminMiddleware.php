<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;

class AdminMiddleware extends Middleware
{
    /**
     * Check if user is authenticated
     */
    public function before($request, $response, $arguments = null)
    {
            return $this->redirect('auth/login');
    }
}
