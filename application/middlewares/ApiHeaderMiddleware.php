<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Http\Request;
use Kodhe\Framework\Http\Response;

class ApiHeaderMiddleware extends Middleware
{
    protected $headers = [];
    protected $requiredHeaders = [];
    protected $defaultHeaders = [
        'Content-Type' => 'application/json; charset=UTF-8',
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];
    
    public function setParameters(array $params)
    {
        if (!empty($params)) {
            // Format: header:value atau header
            foreach ($params as $param) {
                if (strpos($param, ':') !== false) {
                    list($header, $value) = explode(':', $param, 2);
                    $this->headers[trim($header)] = trim($value);
                    log_message('debug', 'ApiHeaderMiddleware: Set header: ' . $header . ' = ' . $value);
                } else {
                    $this->requiredHeaders[] = trim($param);
                    log_message('debug', 'ApiHeaderMiddleware: Required header: ' . $param);
                }
            }
        }
    }
    
    public function before(Request $request, Response $response, $arguments = null)
    {
        log_message('debug', 'ApiHeaderMiddleware: Processing headers');
        
        // 1. Validasi required headers jika ada
        $missingHeaders = $this->validateRequiredHeaders($request);
        
        if (!empty($missingHeaders)) {
            log_message('warning', 'ApiHeaderMiddleware: Missing required headers: ' . 
                implode(', ', $missingHeaders));
            
            return $this->missingHeadersResponse($missingHeaders);
        }
        
        // 2. Set default headers
        $this->setDefaultHeaders($response);
        
        // 3. Set custom headers dari parameter
        $this->setCustomHeaders($response);
        
        // 4. Process request headers untuk analytics
        $this->processRequestHeaders($request);
        
        return null;
    }
    
    /**
     * Validate required headers
     */
    protected function validateRequiredHeaders(Request $request): array
    {
        $missing = [];
        
        foreach ($this->requiredHeaders as $header) {
            if (!$request->hasHeader($header)) {
                $missing[] = $header;
            }
        }
        
        return $missing;
    }
    
    /**
     * Set default headers
     */
    protected function setDefaultHeaders(Response $response): void
    {
        foreach ($this->defaultHeaders as $header => $value) {
            // Jangan override jika sudah ada
            if (!$response->hasHeader($header)) {
                $response->setHeader($header, $value);
                log_message('debug', 'ApiHeaderMiddleware: Set default header: ' . $header);
            }
        }
    }
    
    /**
     * Set custom headers dari parameter
     */
    protected function setCustomHeaders(Response $response): void
    {
        foreach ($this->headers as $header => $value) {
            $response->setHeader($header, $value);
            log_message('debug', 'ApiHeaderMiddleware: Set custom header: ' . $header . ' = ' . $value);
        }
    }
    
    /**
     * Process request headers untuk analytics
     */
    protected function processRequestHeaders(Request $request): void
    {
        // Simpan client info ke request attributes
        $clientInfo = [
            'user_agent' => $request->getHeader('User-Agent'),
            'accept' => $request->getHeader('Accept'),
            'accept_language' => $request->getHeader('Accept-Language'),
            'accept_encoding' => $request->getHeader('Accept-Encoding'),
        ];
        
        $request->setAttribute('client_info', array_filter($clientInfo));
        
        // Log client info
        if ($clientInfo['user_agent']) {
            log_message('debug', 'ApiHeaderMiddleware: User-Agent: ' . $clientInfo['user_agent']);
        }
    }
    
    /**
     * Response untuk missing headers
     */
    protected function missingHeadersResponse(array $missingHeaders): Response
    {
        $data = [
            'error' => [
                'code' => 'missing_headers',
                'message' => 'Required headers are missing',
                'missing_headers' => $missingHeaders,
                'documentation_url' => $this->getDocumentationUrl(),
            ]
        ];
        
        $response = $this->json($data, 400);
        
        // Set example headers di response
        foreach ($missingHeaders as $header) {
            $response->setHeader('X-Required-' . $header, 'required');
        }
        
        return $response;
    }
    
    /**
     * Get documentation URL
     */
    protected function getDocumentationUrl(): string
    {
        return config_item('api.headers_documentation_url') ?? 'https://api.example.com/docs/headers';
    }
    
    /**
     * After hook untuk headers tambahan
     */
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        // Add request ID header jika belum ada
        if (!$response->hasHeader('X-Request-ID')) {
            $requestId = $request->getAttribute('request_id', uniqid('req_', true));
            $response->setHeader('X-Request-ID', $requestId);
        }
        
        // Add processing time header
        if (defined('KOHANA_START_TIME')) {
            $processingTime = microtime(true) - KOHANA_START_TIME;
            $response->setHeader('X-Processing-Time', sprintf('%.3f', $processingTime));
        }
        
        // Add server info header
        $response->setHeader('X-Server', gethostname() ?: 'unknown');
        $response->setHeader('X-Powered-By', 'Kodhe Framework');
        
        return $response;
    }
}