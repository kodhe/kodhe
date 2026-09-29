<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Http\Request;
use Kodhe\Framework\Http\Response;

class ApiMiddleware extends Middleware
{
    public function before(Request $request, Response $response, $arguments = null)
    {
        // Set default headers untuk API
        $response->setHeader('Content-Type', 'application/json; charset=UTF-8');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'DENY');
        
        // Handle CORS jika diperlukan
        if ($request->hasHeader('Origin')) {
            $this->handleCors($request, $response);
        }
        
        return null;
    }
    
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        // Ensure API response is JSON
        $body = $response->getBody();
        
        if (!is_array($body) && !is_object($body)) {
            // Try to decode jika bukan array/object
            $decoded = json_decode($body, true);
            if ($decoded !== null) {
                $response->setBody(json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }
        
        return $response;
    }
    
    protected function handleCors(Request $request, Response $response)
    {
        $allowedOrigins = config_item('cors.allowed_origins') ?? ['*'];
        $origin = $request->getHeader('Origin');
        
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            
            if ($request->method() === 'OPTIONS') {
                $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
                $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-Key');
                $response->setHeader('Access-Control-Max-Age', '86400');
                $response->setStatus(204);
                return $response;
            }
        }
        
        return null;
    }
}