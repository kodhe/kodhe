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

    // RESTful resource. CATATAN: jangan dirantai `->middleware([...])` di sini —
    // pada implementasi saat ini Route::resource() mengembalikan null (bukan objek
    // Route), sehingga pemanggilan fluent berikutnya memicu error fatal:
    // "Call to a member function middleware() on null".
    // Perlindungan auth untuk 7 verb resource dideklarasikan lewat atribut group:
    Route::group(['middleware' => ['auth']], function () {
        Route::resource('posts', 'Post');
    });

    Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
        Route::get('/', ['as' => 'blog.admin.index', 'uses' => 'Admin@index']);
    });
});
