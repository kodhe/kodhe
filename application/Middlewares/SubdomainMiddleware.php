<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Http\Request;
use Kodhe\Framework\Http\Response;

class SubdomainMiddleware extends Middleware
{
    protected $subdomain = null;
    protected $wildcard = false;
    protected $allowedSubdomains = [];
    protected $redirectTo = null;
    
    public function setParameters(array $params)
    {
        if (!empty($params)) {
            $firstParam = $params[0] ?? null;
            
            if ($firstParam === 'wildcard' || $firstParam === '*') {
                $this->wildcard = true;
                log_message('debug', 'SubdomainMiddleware: Wildcard mode enabled');
            } elseif ($firstParam) {
                $this->subdomain = $firstParam;
                log_message('debug', 'SubdomainMiddleware: Set subdomain constraint: ' . $firstParam);
            }
            
            // Parameter kedua bisa berupa redirect URL
            if (isset($params[1])) {
                $this->redirectTo = $params[1];
                log_message('debug', 'SubdomainMiddleware: Redirect to: ' . $this->redirectTo);
            }
            
            // Parameter ketiga bisa berupa allowed subdomains list
            if (isset($params[2])) {
                $this->allowedSubdomains = explode('|', $params[2]);
                log_message('debug', 'SubdomainMiddleware: Allowed subdomains: ' . implode(', ', $this->allowedSubdomains));
            }
        }
    }
    
    public function before(Request $request, Response $response, $arguments = null)
    {
        $currentSubdomain = $this->extractSubdomain($request);
        
        log_message('debug', 'SubdomainMiddleware: Current subdomain: ' . ($currentSubdomain ?: 'none'));
        log_message('debug', 'SubdomainMiddleware: Required subdomain: ' . ($this->subdomain ?: 'any'));
        
        // Jika wildcard mode, cek apakah ada subdomain
        if ($this->wildcard) {
            if ($currentSubdomain === null) {
                log_message('debug', 'SubdomainMiddleware: Wildcard mode but no subdomain found');
                
                // Redirect ke default jika di-configure
                if ($this->redirectTo) {
                    log_message('debug', 'SubdomainMiddleware: Redirecting to: ' . $this->redirectTo);
                    return $this->redirect($this->redirectTo);
                }
                
                // Jika tidak ada subdomain dan wildcard, mungkin kita di domain utama
                // Biarkan request berlanjut
                return null;
            }
            
            // Wildcard subdomain ditemukan, lanjutkan
            log_message('debug', 'SubdomainMiddleware: Wildcard subdomain accepted: ' . $currentSubdomain);
            
            // Simpan subdomain ke request untuk digunakan nanti
            $request->setAttribute('subdomain', $currentSubdomain);
            return null;
        }
        
        // Jika ada subdomain constraint
        if ($this->subdomain !== null) {
            // Cek apakah subdomain cocok
            if ($currentSubdomain === $this->subdomain) {
                log_message('debug', 'SubdomainMiddleware: Subdomain match');
                $request->setAttribute('subdomain', $currentSubdomain);
                return null;
            }
            
            // Cek jika di allowed list
            if (!empty($this->allowedSubdomains) && in_array($currentSubdomain, $this->allowedSubdomains)) {
                log_message('debug', 'SubdomainMiddleware: Subdomain in allowed list');
                $request->setAttribute('subdomain', $currentSubdomain);
                return null;
            }
            
            // Subdomain tidak cocok
            log_message('warning', 'SubdomainMiddleware: Subdomain mismatch. Expected: ' . 
                $this->subdomain . ', Got: ' . ($currentSubdomain ?: 'none'));
            
            // Redirect jika di-configure
            if ($this->redirectTo) {
                log_message('debug', 'SubdomainMiddleware: Redirecting due to subdomain mismatch');
                return $this->redirect($this->redirectTo);
            }
            
            // Atau return 403
            return $this->abort('Access denied: Invalid subdomain', 403);
        }
        
        // Tidak ada constraint, lanjutkan
        return null;
    }
    
    /**
     * Extract subdomain dari request
     */
    protected function extractSubdomain(Request $request): ?string
    {
        $host = $request->getUri()->getHost();
        
        // Remove port jika ada
        if (strpos($host, ':') !== false) {
            $host = explode(':', $host)[0];
        }
        
        // Skip localhost dan IP addresses
        if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }
        
        $parts = explode('.', $host);
        
        // Jika hanya 1 part (localhost tanpa domain), return null
        if (count($parts) === 1) {
            return null;
        }
        
        // Untuk www.example.com, www bukan subdomain
        if (count($parts) === 2) {
            if ($parts[0] === 'www') {
                return null;
            }
            return $parts[0];
        }
        
        // Untuk subdomain.example.com
        if (count($parts) >= 3) {
            // Jika part pertama adalah www, part kedua adalah subdomain
            if ($parts[0] === 'www') {
                return $parts[1];
            }
            return $parts[0];
        }
        
        return null;
    }
    
    /**
     * After hook untuk logging
     */
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        $subdomain = $request->getAttribute('subdomain');
        
        if ($subdomain) {
            // Tambahkan header untuk debugging
            $response->setHeader('X-Subdomain', $subdomain);
            
            // Log subdomain usage
            log_message('info', 'Request served for subdomain: ' . $subdomain);
        }
        
        return $response;
    }
}