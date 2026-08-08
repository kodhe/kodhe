<?php

use Kodhe\Framework\Http\Routing\Route;

Route::get('/', 'App\Controllers\Welcome@index')->name('welcome');
Route::group(['prefix' => 'welcome'], function() {
    Route::get('/', 'App\Controllers\Welcome@index')->name('welcome.index');
    Route::get('/switch_language/{param}', 'App\Controllers\Welcome@switch_language');
});

Route::fallback('Kodhe\Controllers\Error\FileNotFound@index');

