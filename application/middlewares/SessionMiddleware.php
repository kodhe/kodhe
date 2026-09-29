<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;

class SessionMiddleware extends Middleware
{
    /**
     * Initialize session dengan benar
     */
    public function before($request, $response, $arguments = null)
    {
        log_message('debug', 'SessionMiddleware::before() executed');
        
        $ci = $this->getCI();
        
        if ($ci) {
            // Pastikan session library sudah loaded
            if (!isset($ci->session)) {
                log_message('debug', 'Loading session library');
                $ci->load->library('session');
            }
            
            // Cek session status
            $sessionId = session_id();
            log_message('debug', 'Session ID: ' . ($sessionId ? $sessionId : 'Not started'));
            
            // Set default CSRF token jika belum ada
            if (!$ci->session->userdata('csrf_token')) {
                $this->generateCsrfToken();
            }
        } else {
            // Native session handling
            $this->initializeNativeSession();
        }
        
        return null;
    }
    
    /**
     * Initialize native PHP session
     */
    protected function initializeNativeSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            log_message('debug', 'Starting native PHP session');
            
            // Gunakan session name yang unik
            $sessionName = 'APP_' . substr(md5(__FILE__), 0, 8);
            
            if (session_name() !== $sessionName) {
                session_name($sessionName);
            }
            
            // Start session
            @session_start();
            
            // Generate CSRF token jika belum ada
            if (!isset($_SESSION['csrf_token'])) {
                $this->generateCsrfTokenNative();
            }
            
            log_message('debug', 'Native session started with ID: ' . session_id());
        }
    }
    
    /**
     * Generate CSRF token untuk CI session
     */
    protected function generateCsrfToken()
    {
        $ci = $this->getCI();
        if ($ci && isset($ci->session)) {
            $token = bin2hex(random_bytes(32));
            $ci->session->set_userdata('csrf_token', $token);
            log_message('debug', 'Generated CSRF token for CI session');
        }
    }
    
    /**
     * Generate CSRF token untuk native session
     */
    protected function generateCsrfTokenNative()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $token;
            log_message('debug', 'Generated CSRF token for native session');
        }
    }
}