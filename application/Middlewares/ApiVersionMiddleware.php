<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Http\Request;
use Kodhe\Framework\Http\Response;

class ApiVersionMiddleware extends Middleware
{
    protected $requiredVersion = null;
    protected $defaultVersion = '1';
    protected $versionHeader = 'X-API-Version';
    protected $acceptHeader = 'Accept';
    protected $versionParam = 'api_version';
    
    public function setParameters(array $params)
    {
        if (!empty($params)) {
            $this->requiredVersion = $params[0] ?? null;
            log_message('debug', 'ApiVersionMiddleware: Required version: ' . $this->requiredVersion);
            
            if (isset($params[1])) {
                $this->versionHeader = $params[1];
            }
            
            if (isset($params[2])) {
                $this->acceptHeader = $params[2];
            }
            
            if (isset($params[3])) {
                $this->versionParam = $params[3];
            }
        }
    }
    
    public function before(Request $request, Response $response, $arguments = null)
    {
        // Dapatkan versi dari berbagai sumber
        $detectedVersion = $this->detectVersion($request);
        
        log_message('debug', 'ApiVersionMiddleware: Detected version: ' . $detectedVersion);
        log_message('debug', 'ApiVersionMiddleware: Required version: ' . $this->requiredVersion);
        
        // Jika ada required version, validasi
        if ($this->requiredVersion !== null) {
            if ($detectedVersion !== $this->requiredVersion) {
                log_message('warning', 'ApiVersionMiddleware: Version mismatch. Required: ' . 
                    $this->requiredVersion . ', Detected: ' . $detectedVersion);
                
                // Return error response
                return $this->versionMismatchResponse($detectedVersion);
            }
            
            log_message('debug', 'ApiVersionMiddleware: Version validation passed');
        }
        
        // Simpan versi ke request untuk digunakan controller
        $request->setAttribute('api_version', $detectedVersion);
        $request->setAttribute('api_version_header', $this->versionHeader);
        
        // Set version di response headers
        $response->setHeader($this->versionHeader, $detectedVersion);
        $response->setHeader('X-API-Version-Detected', $detectedVersion);
        
        return null;
    }
    
    /**
     * Detect API version dari berbagai sumber
     */
    protected function detectVersion(Request $request): string
    {
        // Priority 1: Custom header
        if ($request->hasHeader($this->versionHeader)) {
            $version = $request->getHeader($this->versionHeader);
            if ($this->isValidVersion($version)) {
                log_message('debug', 'ApiVersionMiddleware: Version from header: ' . $version);
                return $version;
            }
        }
        
        // Priority 2: Accept header (version/v1)
        if ($request->hasHeader($this->acceptHeader)) {
            $accept = $request->getHeader($this->acceptHeader);
            $version = $this->extractVersionFromAcceptHeader($accept);
            
            if ($version !== null) {
                log_message('debug', 'ApiVersionMiddleware: Version from Accept header: ' . $version);
                return $version;
            }
        }
        
        // Priority 3: Query parameter
        $query = $request->getQueryParams();
        if (isset($query[$this->versionParam])) {
            $version = $query[$this->versionParam];
            if ($this->isValidVersion($version)) {
                log_message('debug', 'ApiVersionMiddleware: Version from query param: ' . $version);
                return $version;
            }
        }
        
        // Priority 4: URL segment (api/v1/resource)
        $path = $request->getUri()->getPath();
        $version = $this->extractVersionFromPath($path);
        
        if ($version !== null) {
            log_message('debug', 'ApiVersionMiddleware: Version from URL path: ' . $version);
            return $version;
        }
        
        // Priority 5: Default version
        log_message('debug', 'ApiVersionMiddleware: Using default version: ' . $this->defaultVersion);
        return $this->defaultVersion;
    }
    
    /**
     * Extract version dari Accept header
     */
    protected function extractVersionFromAcceptHeader(string $accept): ?string
    {
        // Format: application/vnd.api.v1+json
        if (preg_match('/vnd\.api\.v(\d+)/i', $accept, $matches)) {
            return $matches[1];
        }
        
        // Format: application/json; version=1
        if (preg_match('/version\s*=\s*(\d+)/i', $accept, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Extract version dari URL path
     */
    protected function extractVersionFromPath(string $path): ?string
    {
        // Pattern: /api/v1/resource
        if (preg_match('#/api/v(\d+)(/|$)#i', $path, $matches)) {
            return $matches[1];
        }
        
        // Pattern: /v1/api/resource
        if (preg_match('#/v(\d+)/api#i', $path, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Validasi format version
     */
    protected function isValidVersion(string $version): bool
    {
        return preg_match('/^\d+(\.\d+)*$/', $version) === 1;
    }
    
    /**
     * Response untuk version mismatch
     */
    protected function versionMismatchResponse(string $detectedVersion): Response
    {
        $data = [
            'error' => [
                'code' => 'api_version_mismatch',
                'message' => 'Invalid API version',
                'required_version' => $this->requiredVersion,
                'provided_version' => $detectedVersion,
                'documentation_url' => $this->getDocumentationUrl(),
            ]
        ];
        
        $response = $this->json($data, 400);
        $response->setHeader($this->versionHeader, $this->requiredVersion);
        $response->setHeader('X-API-Version-Required', $this->requiredVersion);
        
        return $response;
    }
    
    /**
     * Get documentation URL untuk error response
     */
    protected function getDocumentationUrl(): string
    {
        // Bisa di-override di config
        return config_item('api.documentation_url') ?? 'https://api.example.com/docs';
    }
    
    /**
     * After hook untuk logging dan headers tambahan
     */
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        $apiVersion = $request->getAttribute('api_version', $this->defaultVersion);
        
        // Tambahkan version info headers
        $response->setHeader('X-API-Version-Served', $apiVersion);
        $response->setHeader('X-API-Version-Status', 'active');
        
        // Log API version usage
        log_message('info', 'API request served with version: ' . $apiVersion . 
            ' for path: ' . $request->getUri()->getPath());
        
        return $response;
    }
}