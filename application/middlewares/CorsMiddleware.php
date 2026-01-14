<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;

class CorsMiddleware extends Middleware
{
    /**
     * Handle CORS headers
     */
    public function before($request, $response, $arguments = null)
    {
        // Allow from any origin
        $response->setHeader('Access-Control-Allow-Origin', '*');
        
        // Allow methods
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        
        // Allow headers
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        
        // Handle preflight requests
        if ($request->method() === 'OPTIONS') {
            $response->setStatus(200);
            $response->setBody('');

            $response->sendAndExit();
            return $response;
        }
        
        return null;
    }
}