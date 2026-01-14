<?php

// Subdomain routing
Route::domain('admin.example.com', function() {
    Route::get('/', 'Admin\DashboardController@index')
        ->middleware('auth')
        ->name('admin.dashboard');
});

Route::domain('api.example.com', function() {
    Route::apiVersion('1', function() {
        Route::get('/users', 'Api\v1\UserController@index')
            ->middleware('api.header:X-API-Key');
        
        // Deprecated endpoint dengan sunset date
        Route::get('/old/users', 'Api\v1\OldUserController@index')
            ->middleware('api.deprecated:2024-12-31,/api/v2/users|/api/v3/users,api@example.com,https://api.example.com/docs/migration')
            ->name('api.v1.users.deprecated');
    });
    
    Route::apiVersion('2', function() {
        Route::get('/users', 'Api\v2\UserController@index')
            ->middleware('api.header:X-API-Key,X-Client-ID');
    });
}, [
    'middleware' => 'subdomain.api'
]);

// Wildcard subdomain
Route::domain('{wildcard}.example.com', function() {
    Route::get('/', function($subdomain) {
        return response()->json([
            'subdomain' => $subdomain,
            'message' => 'Welcome to ' . $subdomain . ' subdomain'
        ]);
    })->middleware('subdomain:wildcard');
});

// API dengan versioning di path
Route::group(['prefix' => 'api', 'middleware' => 'api'], function() {
    Route::group(['prefix' => 'v1'], function() {
        Route::get('/products', 'Api\v1\ProductController@index')
            ->middleware('api.version:1');
        
        Route::get('/products/{id}', 'Api\v1\ProductController@show')
            ->middleware(['api.version:1', 'api.header:X-API-Version:1']);
    });
    
    Route::group(['prefix' => 'v2'], function() {
        Route::get('/products', 'Api\v2\ProductController@index')
            ->middleware(['api.version:2', 'api.deprecated:2024-06-30']);
    });
});