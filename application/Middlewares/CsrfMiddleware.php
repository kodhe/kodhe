<?php
namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;

class CsrfMiddleware extends Middleware
{
    public function before($request, $response, $arguments = null)
    {
        log_message('debug', 'CsrfMiddleware::before() executed');
        
        // Skip untuk GET, HEAD, OPTIONS requests
        if ($request->isGet() || $request->isHead() || $request->isOptions()) {
            log_message('debug', 'Skipping CSRF check for safe method');
            
            // Tetap generate token untuk form
            $this->generateCsrfToken();
            return null;
        }
        
        // Skip untuk API routes (biasanya menggunakan token auth lain)
        if (strpos($request->server('REQUEST_URI'), '/api/') === 0) {
            log_message('debug', 'Skipping CSRF check for API route');
            return null;
        }
        
        // Get CSRF token dari POST atau header
        $token = $request->post('csrf_token') ?: $request->header('X-CSRF-Token');
        
        log_message('debug', 'CSRF token from request: ' . ($token ? 'present' : 'missing'));
        
        if (!$this->validateCsrfToken($token)) {
            log_message('error', 'CSRF token validation failed');
            
            if ($request->isAjax()) {
                return $this->json(['error' => 'Invalid CSRF token'], 403);
            }
            
            // Show error page
            $response->setStatus(403);
            $response->setBody('Invalid CSRF token. Please refresh the page and try again.');
            return $response;
        }
        
        log_message('debug', 'CSRF token validated successfully');
        
        // Generate new token untuk request berikutnya
        $this->generateCsrfToken();
        
        return null;
    }
    
    protected function validateCsrfToken($token)
    {
        // Gunakan CI session jika available
        $ci = $this->getCI();
        
        if ($ci && isset($ci->session)) {
            // Gunakan CI Session library
            $sessionToken = $ci->session->userdata('csrf_token');
            
            // Jika token belum ada di session, generate baru
            if (!$sessionToken) {
                $sessionToken = $this->generateToken();
                $ci->session->set_userdata('csrf_token', $sessionToken);
            }
            
        } else {
            // Fallback ke native session dengan pengecekan status
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            } elseif (session_status() === PHP_SESSION_DISABLED) {
                log_message('error', 'Sessions are disabled');
                return false;
            }
            
            $sessionToken = $_SESSION['csrf_token'] ?? null;
            
            // Jika token belum ada, generate baru
            if (!$sessionToken) {
                $sessionToken = $this->generateToken();
                $_SESSION['csrf_token'] = $sessionToken;
            }
        }
        
        if (!$sessionToken || !$token) {
            log_message('debug', 'Missing token: session=' . ($sessionToken ? 'yes' : 'no') . ', request=' . ($token ? 'yes' : 'no'));
            return false;
        }
        
        // Gunakan timing attack safe comparison
        $isValid = hash_equals($sessionToken, $token);
        
        if (!$isValid) {
            log_message('debug', 'Token mismatch: session=' . substr($sessionToken, 0, 10) . '..., request=' . substr($token, 0, 10) . '...');
        }
        
        return $isValid;
    }
    
    protected function generateCsrfToken()
    {
        $ci = $this->getCI();
        $newToken = $this->generateToken();
        
        if ($ci && isset($ci->session)) {
            // Update token di CI session
            $ci->session->set_userdata('csrf_token', $newToken);
            log_message('debug', 'Generated new CSRF token in CI session');
        } else {
            // Update token di native session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['csrf_token'] = $newToken;
                log_message('debug', 'Generated new CSRF token in native session');
            } else {
                log_message('error', 'Cannot generate CSRF token: session not active');
            }
        }
        
        // Tambahkan token ke response header untuk AJAX
        $response = $this->response;
        $response->setHeader('X-CSRF-Token', $newToken);
    }
    
    /**
     * Generate cryptographically secure token
     */
    protected function generateToken()
    {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(32));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            // Fallback untuk sistem yang tidak support cryptographically secure random
            $bytes = '';
            for ($i = 0; $i < 32; $i++) {
                $bytes .= chr(mt_rand(0, 255));
            }
            return bin2hex($bytes);
        }
    }
    
    /**
     * After hook - inject token ke view
     */
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        $contentType = $response->getHeader('Content-Type');
        
        // Hanya inject ke HTML responses
        if (strpos($contentType ?? '', 'text/html') !== false || $contentType === null) {
            $body = $response->getBody();
            
            if (empty($body)) {
                return $response;
            }
            
            // Get current token
            $token = $this->getCurrentToken();
            
            if ($token) {
                // 1. Inject hidden input ke semua forms
                if (strpos($body, '</form>') !== false) {
                    $csrfField = '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
                    $body = str_replace('</form>', $csrfField . '</form>', $body);
                }
                
                // 2. Inject meta tag untuk JavaScript
                if (strpos($body, '</head>') !== false) {
                    $metaTag = '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
                    $body = str_replace('</head>', $metaTag . "\n</head>", $body);
                }
                
                $response->setBody($body);
            }
        }
        
        return $response;
    }
    
    /**
     * Get current CSRF token
     */
    protected function getCurrentToken()
    {
        $ci = $this->getCI();
        
        if ($ci && isset($ci->session)) {
            return $ci->session->userdata('csrf_token');
        }
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            return $_SESSION['csrf_token'] ?? null;
        }
        
        return null;
    }
}