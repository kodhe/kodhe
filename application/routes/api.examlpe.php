<?php

use Kodhe\Framework\Http\Routing\Route;

// ============================================
// API VERSION 2 ROUTES ONLY
// ============================================

// Clear existing routes
Route::clear();

// 1. API VERSION 2 GROUP (ONLY v2)
Route::apiVersion('2', function() {
    
    // A. AUTH Routes
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
        Route::post('login', 'Api\V2\AuthController@login')->name('login');
        Route::post('register', 'Api\V2\AuthController@register')->name('register');
        Route::post('logout', 'Api\V2\AuthController@logout')->middleware('auth:api')->name('logout');
        Route::post('refresh', 'Api\V2\AuthController@refresh')->name('refresh');
        Route::get('me', 'Api\V2\AuthController@me')->middleware('auth:api')->name('me');
    });
    
    // B. USERS Resource dengan nested routes
    Route::apiResource('users', 'Api\V2\UserController',['only' => ['index', 'show', 'update', 'destroy']]);
    
    // Users additional routes
    Route::group(['prefix' => 'users/{user}', 'where' => ['user' => '[0-9]+']], function() {
        Route::get('profile', 'Api\V2\UserController@profile')->name('users.profile');
        Route::put('change-password', 'Api\V2\UserController@changePassword')->name('users.change-password');
        Route::post('avatar', 'Api\V2\UserController@uploadAvatar')->name('users.upload-avatar');
        
        // User's posts
        Route::get('posts', 'Api\V2\UserPostController@index')->name('users.posts.index');
        Route::get('posts/{post}', 'Api\V2\UserPostController@show')->name('users.posts.show');
    });
    
    // C. POSTS Resource dengan advanced features
    Route::apiResource('posts', 'Api\V2\PostController');
    
    // Posts additional routes
    Route::group(['prefix' => 'posts/{post}', 'where' => ['post' => '[0-9]+']], function() {
        Route::post('like', 'Api\V2\PostController@like')->middleware('auth:api')->name('posts.like');
        Route::post('comment', 'Api\V2\PostController@comment')->middleware('auth:api')->name('posts.comment');
        Route::get('comments', 'Api\V2\PostController@comments')->name('posts.comments');
        Route::post('share', 'Api\V2\PostController@share')->middleware('auth:api')->name('posts.share');
        Route::get('analytics', 'Api\V2\PostController@analytics')->middleware(['auth:api', 'role:admin'])->name('posts.analytics');
    });
    
    // D. CATEGORIES dengan tree structure
    Route::apiResource('categories', 'Api\V2\CategoryController');
    
    Route::group(['prefix' => 'categories/{category}', 'where' => ['category' => '[0-9]+']], function() {
        Route::get('posts', 'Api\V2\CategoryController@posts')->name('categories.posts');
        Route::get('subcategories', 'Api\V2\CategoryController@subcategories')->name('categories.subcategories');
    });
    
    // E. FILE UPLOAD Routes
    Route::group(['prefix' => 'files', 'middleware' => ['auth:api']], function() {
        Route::post('upload', 'Api\V2\FileController@upload')->name('files.upload');
        Route::get('{file}/download', 'Api\V2\FileController@download')->name('files.download');
        Route::delete('{file}', 'Api\V2\FileController@destroy')->name('files.delete');
    });
    
    // F. SETTINGS Routes
    Route::group(['prefix' => 'settings', 'middleware' => ['auth:api']], function() {
        Route::get('/', 'Api\V2\SettingController@index')->name('settings.index');
        Route::put('profile', 'Api\V2\SettingController@updateProfile')->name('settings.profile');
        Route::put('notifications', 'Api\V2\SettingController@updateNotifications')->name('settings.notifications');
        Route::put('privacy', 'Api\V2\SettingController@updatePrivacy')->name('settings.privacy');
    });
    
    // G. SEARCH Routes
    Route::group(['prefix' => 'search'], function() {
        Route::get('global', 'Api\V2\SearchController@global')->name('search.global');
        Route::get('users', 'Api\V2\SearchController@users')->name('search.users');
        Route::get('posts', 'Api\V2\SearchController@posts')->name('search.posts');
        Route::get('categories', 'Api\V2\SearchController@categories')->name('search.categories');
    });
    
    // H. NOTIFICATIONS Routes
    Route::group(['prefix' => 'notifications', 'middleware' => ['auth:api']], function() {
        Route::get('/', 'Api\V2\NotificationController@index')->name('notifications.index');
        Route::get('unread', 'Api\V2\NotificationController@unread')->name('notifications.unread');
        Route::put('{notification}/read', 'Api\V2\NotificationController@markAsRead')->name('notifications.read');
        Route::put('read-all', 'Api\V2\NotificationController@markAllAsRead')->name('notifications.read-all');
    });
    
    // I. ADMIN Routes (hanya untuk admin)
    Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'role:admin'], 'as' => 'admin.'], function() {
        
        // Admin dashboard
        Route::get('dashboard', 'Api\V2\Admin\DashboardController@index')->name('dashboard');
        
        // Admin users management
        Route::group(['prefix' => 'users', 'as' => 'users.'], function() {
            Route::get('stats', 'Api\V2\Admin\UserController@stats')->name('stats');
            Route::post('bulk-delete', 'Api\V2\Admin\UserController@bulkDelete')->name('bulk-delete');
            Route::post('{user}/ban', 'Api\V2\Admin\UserController@ban')->name('ban');
            Route::post('{user}/unban', 'Api\V2\Admin\UserController@unban')->name('unban');
            Route::post('{user}/assign-role', 'Api\V2\Admin\UserController@assignRole')->name('assign-role');
        });
        
        // Admin posts moderation
        Route::group(['prefix' => 'posts', 'as' => 'posts.'], function() {
            Route::get('pending', 'Api\V2\Admin\PostController@pending')->name('pending');
            Route::post('{post}/approve', 'Api\V2\Admin\PostController@approve')->name('approve');
            Route::post('{post}/reject', 'Api\V2\Admin\PostController@reject')->name('reject');
            Route::post('{post}/feature', 'Api\V2\Admin\PostController@feature')->name('feature');
        });
        
        // Admin reports
        Route::apiResource('reports', 'Api\V2\Admin\ReportController',['onlu'=>['index', 'show', 'update']]);
        
        // Admin system settings
        Route::group(['prefix' => 'system', 'as' => 'system.'], function() {
            Route::get('config', 'Api\V2\Admin\SystemController@getConfig')->name('config');
            Route::put('config', 'Api\V2\Admin\SystemController@updateConfig')->name('update-config');
            Route::get('logs', 'Api\V2\Admin\SystemController@getLogs')->name('logs');
            Route::get('analytics', 'Api\V2\Admin\SystemController@analytics')->name('analytics');
        });
    });
    
    // J. WEBHOOKS Routes (tanpa auth)
    Route::group(['prefix' => 'webhooks'], function() {
        Route::post('stripe', 'Api\V2\WebhookController@stripe')->name('webhooks.stripe');
        Route::post('paypal', 'Api\V2\WebhookController@paypal')->name('webhooks.paypal');
        Route::post('github', 'Api\V2\WebhookController@github')->name('webhooks.github');
    });
    
    // K. EXPORT Routes
    Route::group(['prefix' => 'export', 'middleware' => ['auth:api']], function() {
        Route::get('my-data', 'Api\V2\ExportController@myData')->name('export.my-data');
        Route::get('posts', 'Api\V2\ExportController@posts')->middleware('role:admin')->name('export.posts');
        Route::get('users', 'Api\V2\ExportController@users')->middleware('role:admin')->name('export.users');
    });
    
    // L. STATISTICS Routes
    Route::group(['prefix' => 'stats'], function() {
        Route::get('overview', 'Api\V2\StatisticController@overview')->name('stats.overview');
        Route::get('user-growth', 'Api\V2\StatisticController@userGrowth')->middleware('role:admin')->name('stats.user-growth');
        Route::get('post-activity', 'Api\V2\StatisticController@postActivity')->name('stats.post-activity');
    });
    
}, [
    'default' => true, // Jadikan v2 sebagai default API
    'headers' => [
        'X-API-Version' => '2.0',
        'X-API-Status' => 'stable',
    ],
    'deprecated' => false, // v2 masih aktif
]);

// ============================================
// NON-API ROUTES (Optional)
// ============================================

// Jika perlu non-API routes juga
Route::group(['middleware' => ['web']], function() {
    
    // Documentation untuk API v2
    Route::get('api/v2/docs', 'DocumentationController@v2')->name('api.v2.docs');
    Route::get('api/v2/swagger.json', 'DocumentationController@swaggerV2')->name('api.v2.swagger');
    
    // API status
    Route::get('api/status', function() {
        return response()->json([
            'api' => [
                'version' => '2.0',
                'status' => 'stable',
                'documentation' => url('/api/v2/docs'),
                'endpoints' => [
                    'auth' => '/api/v2/auth',
                    'users' => '/api/v2/users',
                    'posts' => '/api/v2/posts',
                    'categories' => '/api/v2/categories',
                    'search' => '/api/v2/search',
                ]
            ]
        ]);
    })->name('api.status');
    
});

// ============================================
// FALLBACK untuk API v2
// ============================================

// API v2 fallback (jika route tidak ditemukan)
Route::fallback(['prefix'=>'api/v2'],function() {
    return response()->json([
        'error' => [
            'code' => 404,
            'message' => 'API endpoint not found',
            'documentation' => url('/api/v2/docs'),
            'available_versions' => ['2.0']
        ]
    ], 404);
});

// ============================================
// DEBUG OUTPUT
// ============================================

// Untuk melihat semua routes yang terdaftar
if (php_sapi_name() === 'cli') {
    echo "=== API VERSION 2 ROUTES ===\n\n";
    
    $stats = Route::getStats();
    echo "Total Routes: {$stats['total_routes']}\n";
    echo "API Routes: {$stats['api_routes']}\n";
    echo "Named Routes: {$stats['named_routes']}\n\n";
    
    echo "Routes by Method:\n";
    foreach ($stats['by_method'] as $method => $count) {
        if ($count > 0) {
            echo "  {$method}: {$count}\n";
        }
    }
    
    echo "\n=== NAMED ROUTES ===\n";
    foreach (Route::$namedRoutes as $name => $route) {
        echo "{$name} => {$route->getUri()} ({$route->getMethod()})\n";
    }
    
    echo "\n=== SAMPLE ENDPOINTS ===\n";
    $sampleEndpoints = [
        'POST /api/v2/auth/login',
        'GET /api/v2/users',
        'GET /api/v2/users/{id}',
        'GET /api/v2/users/{id}/profile',
        'GET /api/v2/posts',
        'POST /api/v2/posts',
        'POST /api/v2/posts/{id}/like',
        'GET /api/v2/search/global',
        'GET /api/v2/notifications',
        'GET /api/v2/admin/dashboard',
        'GET /api/v2/docs',
    ];
    
    foreach ($sampleEndpoints as $endpoint) {
        echo "✓ {$endpoint}\n";
    }
}
