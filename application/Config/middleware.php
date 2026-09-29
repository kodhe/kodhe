<?php
// File: application/config/Middleware.php

return [
    /**
     * Middleware aliases
     * Format: 'alias' => 'Full\Class\Name'
     */
    'aliases' => [
        'auth' => 'App\Middlewares\AuthMiddleware',
        'session' => 'App\Middlewares\SessionMiddleware',
        'verified' => 'App\Middlewares\VerifiedMiddleware',
        'domain' => 'App\Middlewares\DomainMiddleware',
        'admin' => 'App\Middlewares\AdminMiddleware',
        'permission' => 'App\Middlewares\PermissionMiddleware',
        'csrf' => 'App\Middlewares\CsrfMiddleware',
        'cors' => 'App\Middlewares\CorsMiddleware',
        'throttle' => 'App\Middlewares\ThrottleMiddleware',
        'api' => 'App\Middlewares\ApiMiddleware',
        'subdomain' => 'App\Middlewares\SubdomainMiddleware',
        'api.version' => 'App\Middlewares\ApiVersionMiddleware',
        'api.header' => 'App\Middlewares\ApiHeaderMiddleware',
        'api.deprecated' => 'App\Middlewares\ApiDeprecatedMiddleware',
    ],
    
    /**
     * Global middlewares
     * Dijalankan untuk semua request
     */
    'global' => [
        //'cors',
    ],
    
    /**
     * Middleware groups
     * Bisa dipanggil dengan nama group
     */
    'groups' => [
        'web' => [
            'csrf',
            'session', // Anda bisa buat SessionMiddleware
        ],
        
        'api' => [
            'api',
            'throttle:60,1',
        ],
        
        'guru' => [
            'auth',
        ],
        
        'admin' => [
            'auth',
            'admin',
        ],
        
        'api.v1' => [
            'api',
            'api.version:1',
            'throttle:100,1',
        ],
    ],
    
    /**
     * Middleware priority
     * Middleware dengan priority tinggi dijalankan lebih dulu
     */
    'priority' => [
        'session',
        'cors',
        'throttle',
        'auth',
        'admin',
        'csrf',
        'api',
        'json',
    ],
];