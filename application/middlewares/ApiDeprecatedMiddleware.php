<?php namespace App\Middlewares;

use Kodhe\Framework\Middleware\Middleware;
use Kodhe\Framework\Http\Request;
use Kodhe\Framework\Http\Response;

class ApiDeprecatedMiddleware extends Middleware
{
    protected $deprecated = true;
    protected $sunsetDate = null;
    protected $alternatives = [];
    protected $contact = null;
    protected $link = null;
    
    public function setParameters(array $params)
    {
        if (!empty($params)) {
            // Parameter pertama bisa sunset date
            if (isset($params[0])) {
                $this->sunsetDate = $params[0];
                log_message('debug', 'ApiDeprecatedMiddleware: Sunset date: ' . $this->sunsetDate);
            }
            
            // Parameter kedua bisa alternatives (pipe separated)
            if (isset($params[1])) {
                $this->alternatives = explode('|', $params[1]);
                log_message('debug', 'ApiDeprecatedMiddleware: Alternatives: ' . implode(', ', $this->alternatives));
            }
            
            // Parameter ketiga bisa contact email
            if (isset($params[2])) {
                $this->contact = $params[2];
                log_message('debug', 'ApiDeprecatedMiddleware: Contact: ' . $this->contact);
            }
            
            // Parameter keempat bisa documentation link
            if (isset($params[3])) {
                $this->link = $params[3];
                log_message('debug', 'ApiDeprecatedMiddleware: Documentation link: ' . $this->link);
            }
        }
    }
    
    public function before(Request $request, Response $response, $arguments = null)
    {
        log_message('warning', 'ApiDeprecatedMiddleware: Deprecated API endpoint accessed: ' . 
            $request->getUri()->getPath());
        
        // Set warning headers
        $this->setDeprecationHeaders($response);
        
        // Log usage untuk analytics
        $this->logDeprecatedUsage($request);
        
        // Check jika sudah sunset
        if ($this->isSunset()) {
            log_message('error', 'ApiDeprecatedMiddleware: Sunset API endpoint accessed after sunset date');
            return $this->sunsetResponse();
        }
        
        // Return warning (tapi biarkan request berlanjut)
        return null;
    }
    
    /**
     * Set deprecation headers
     */
    protected function setDeprecationHeaders(Response $response): void
    {
        $response->setHeader('Deprecation', 'true');
        
        if ($this->sunsetDate) {
            $response->setHeader('Sunset', $this->sunsetDate);
        }
        
        if ($this->link) {
            $response->setHeader('Link', '<' . $this->link . '>; rel="deprecation"');
        }
        
        // Build warning message
        $warningMessage = $this->buildWarningMessage();
        $response->setHeader('Warning', '299 - "Deprecated API: ' . $warningMessage . '"');
        
        // Custom header untuk informasi lebih detail
        $response->setHeader('X-API-Deprecated', 'true');
        
        if (!empty($this->alternatives)) {
            $response->setHeader('X-API-Alternatives', implode(', ', $this->alternatives));
        }
        
        if ($this->contact) {
            $response->setHeader('X-API-Contact', $this->contact);
        }
    }
    
    /**
     * Build warning message untuk header
     */
    protected function buildWarningMessage(): string
    {
        $parts = ['This API endpoint is deprecated'];
        
        if ($this->sunsetDate) {
            $parts[] = 'and will be sunset on ' . $this->sunsetDate;
        }
        
        if (!empty($this->alternatives)) {
            $parts[] = 'use ' . implode(' or ', $this->alternatives) . ' instead';
        }
        
        if ($this->link) {
            $parts[] = 'see ' . $this->link . ' for details';
        }
        
        return implode(', ', $parts);
    }
    
    /**
     * Check jika sudah melewati sunset date
     */
    protected function isSunset(): bool
    {
        if (!$this->sunsetDate) {
            return false;
        }
        
        try {
            $sunsetTime = strtotime($this->sunsetDate);
            $currentTime = time();
            
            return $currentTime > $sunsetTime;
        } catch (\Exception $e) {
            log_message('error', 'ApiDeprecatedMiddleware: Invalid sunset date format: ' . $this->sunsetDate);
            return false;
        }
    }
    
    /**
     * Response untuk sunset endpoint
     */
    protected function sunsetResponse(): Response
    {
        $data = [
            'error' => [
                'code' => 'api_sunset',
                'message' => 'This API endpoint has been sunset',
                'sunset_date' => $this->sunsetDate,
                'alternatives' => $this->alternatives,
                'contact' => $this->contact,
                'documentation' => $this->link,
            ]
        ];
        
        $response = $this->json($data, 410); // 410 Gone
        $response->setHeader('Deprecation', 'sunset');
        $response->setHeader('Sunset', $this->sunsetDate);
        
        return $response;
    }
    
    /**
     * Log deprecated API usage
     */
    protected function logDeprecatedUsage(Request $request): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'endpoint' => $request->getUri()->getPath(),
            'method' => $request->method(),
            'client_ip' => $request->ip(),
            'user_agent' => $request->getHeader('User-Agent'),
            'sunset_date' => $this->sunsetDate,
            'alternatives' => $this->alternatives,
        ];
        
        // Log ke file khusus deprecated APIs
        $logMessage = json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // Simpan ke file log khusus
        $logDir = WRITEPATH . 'logs/api/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . 'deprecated_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        log_message('info', 'Deprecated API usage logged: ' . $request->getUri()->getPath());
    }
    
    /**
     * After hook untuk logging tambahan
     */
    public function after($request, $response, $arguments = null, $controllerResult = null)
    {
        // Tambahkan deprecation notice ke response body jika JSON
        $contentType = $response->getHeader('Content-Type');
        
        if (strpos($contentType, 'application/json') !== false) {
            $body = $response->getBody();
            
            try {
                $data = json_decode($body, true);
                
                if (is_array($data) && !isset($data['warning'])) {
                    $data['warning'] = [
                        'deprecated' => true,
                        'message' => $this->buildWarningMessage(),
                        'sunset_date' => $this->sunsetDate,
                        'alternatives' => $this->alternatives,
                        'contact' => $this->contact,
                        'documentation' => $this->link,
                    ];
                    
                    $response->setBody(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            } catch (\Exception $e) {
                // Jika bukan JSON, skip
            }
        }
        
        return $response;
    }
}