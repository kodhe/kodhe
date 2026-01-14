<?php

use Kodhe\Framework\Routing\Route;

Route::get('/', 'App\Controllers\Welcome@index')->name('welcome');
Route::group(['prefix' => 'welcome'], function() {
    Route::get('/', 'App\Controllers\Welcome@index')->name('welcome.index');
    Route::get('/{method}/{param}', 'App\Controllers\Welcome@{method}');
});

Route::fallback('Kodhe\Controllers\Error\FileNotFound@index');

