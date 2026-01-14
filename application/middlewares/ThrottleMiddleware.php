<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Http\Request;
use Kodhe\Framework\Http\Response;
use Kodhe\Framework\Exceptions\TooManyRequestsException;

class ThrottleMiddleware extends Middleware
{
    protected $maxAttempts = 60;
    protected $decayMinutes = 1;
    protected $keyBy = 'ip';
    
    public function setParameters(array $params)
    {
        if (isset($params[0])) $this->maxAttempts = (int)$params[0];
        if (isset($params[1])) $this->decayMinutes = (int)$params[1];
        if (isset($params[2])) $this->keyBy = $params[2];
    }
    
    public function before(Request $request, Response $response, $arguments = null)
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->maxAttempts;
        $decaySeconds = $this->decayMinutes * 60;
        
        // Get rate limiter instance
        $limiter = kodhe()->rate_limiter ?? null;
        
        if ($limiter) {
            if ($limiter->tooManyAttempts($key, $maxAttempts)) {
                $retryAfter = $limiter->availableIn($key);
                
                $exception = new TooManyRequestsException();
                $exception->setHeaders($this->getHeaders(
                    $maxAttempts,
                    $limiter->retriesLeft($key, $maxAttempts),
                    $retryAfter
                ));
                
                throw $exception;
            }
            
            $limiter->hit($key, $decaySeconds);
            
            // Add headers
            $response->setHeader('X-RateLimit-Limit', $maxAttempts);
            $response->setHeader('X-RateLimit-Remaining', 
                $limiter->retriesLeft($key, $maxAttempts));
        }
        
        return null;
    }
    
    protected function resolveRequestSignature(Request $request)
    {
        switch ($this->keyBy) {
            case 'ip':
                return 'throttle:' . $request->ip();
            case 'user':
                $user = $request->user();
                return 'throttle:' . ($user ? $user->id : 'guest');
            case 'route':
                return 'throttle:' . $request->method() . ':' . $request->getUri()->getPath();
            default:
                return 'throttle:' . $this->keyBy;
        }
    }
    
    protected function getHeaders($maxAttempts, $remaining, $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remaining,
        ];
        
        if ($retryAfter !== null) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = time() + $retryAfter;
        }
        
        return $headers;
    }
}