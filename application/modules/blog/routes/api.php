<?php

declare(strict_types=1);

/**
 * Endpoint JSON stateless milik modul blog (URL: /api/v1/blog/...).
 */

use Kodhe\Framework\Http\Routing\Route;

Route::apiVersion('v1', function () {
    Route::group(['prefix' => 'blog'], function () {
        Route::get('posts', static function () {
            return service('PostService', 'blog')->all();
        });

        Route::get('posts/(:num)', static function (int $id) {
            return service('PostService', 'blog')->find($id);
        });
    });
});
