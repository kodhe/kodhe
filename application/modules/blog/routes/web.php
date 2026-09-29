<?php

declare(strict_types=1);

/**
 * Rute HTML milik modul blog — modul memiliki folder routes/ sendiri.
 * Lapisan service (setup.php + Services/) TIDAK pernah mendaftarkan rute.
 */

use Kodhe\Framework\Http\Routing\Route;

Route::module('blog', function () {
    Route::get('/', ['as' => 'blog.home', 'uses' => 'Post@index']);
    Route::get('post/(:num)', ['as' => 'blog.posts.show', 'uses' => 'Post@show/$1']);
    Route::resource('posts', 'Post')->middleware(['auth']);

    Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
        Route::get('/', ['as' => 'blog.admin.index', 'uses' => 'Admin@index']);
    });
});
