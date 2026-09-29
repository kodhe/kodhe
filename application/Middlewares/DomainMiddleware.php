<?php

// DomainMiddleware.php
class DomainMiddleware
{
    public function handle(Request $request, Closure $next, string $domain)
    {
        $host = $request->getHost();
        
        // Parse domain pattern
        $handler = new EnhancedGroupHandler();
        
        if (!$handler->domainMatchesPattern($host, $domain)) {
            abort(404, 'Domain not allowed');
        }
        
        // Check TLD jika ada constraint
        $domainInfo = $handler->parseDomainPattern($domain);
        if ($domainInfo['has_tld_constraint']) {
            $requestTld = $handler->extractTld($host);
            $requiredTld = $domainInfo['tld'];
            
            if ($requestTld !== $requiredTld) {
                abort(404, 'Invalid TLD');
            }
        }
        
        return $next($request);
    }
}