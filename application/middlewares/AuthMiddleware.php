<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;

// MIDDLEWARE (System kamu)
class AuthMiddleware extends Middleware
{
    public function before($request, $response, $arguments = null)
    {
        if (!$this->session('user_id')) {
            // Bisa menghentikan di sini dengan return Response
            return $this->redirect('/auth/login');
        }
        return null; // Lanjutkan
    }
    
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        // Bisa modifikasi response dari controller
        $response->setHeader('X-Custom-Header', 'value');
        return $controllerResult;
    }
}