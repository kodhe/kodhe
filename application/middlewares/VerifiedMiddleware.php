<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;

class VerifiedMiddleware extends Middleware
{
    /**
     * Check if user email is verified
     */
    public function before($request, $response, $arguments = null)
    {
        log_message('debug', 'VerifiedMiddleware::before() executed');
        
        $ci = $this->getCI();
        
        // Check if user is verified
        $isVerified = $this->session('email_verified');
        
        if (!$isVerified) {
            log_message('debug', 'User email not verified');
            
            // Check if this is an API request
            if (strpos($request->server('REQUEST_URI'), '/api/') === 0) {
                return $this->json(['error' => 'Email not verified'], 403);
            }
            
            return $this->redirect('/verify-email');
        }
        
        return null;
    }
}