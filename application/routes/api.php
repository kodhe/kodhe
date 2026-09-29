<?php

use Kodhe\Framework\Http\JsonResponse;
use Kodhe\Framework\Http\Routing\Route;

/*
|--------------------------------------------------------------------------
| API Routes (application/routes/api.php)
|--------------------------------------------------------------------------
|
| File ini dibaca oleh Router/UnifiedRouter SEBELUM web.php dan khusus
| dipakai untuk mendaftarkan endpoint REST/JSON. Seluruh rute di bawah
| grup ini memakai prefix "api" dan middleware group "api"
| (App\Middlewares\ApiMiddleware + throttle 60 req/menit), sehingga
| request non-JSON dapat dibalas 406 dan request berlebihan 429.
|
| Contoh:
|   curl http://localhost:8000/api/ping
|   curl http://localhost:8000/api/articles
|   curl -X POST http://localhost:8000/api/articles \
|        -H "Content-Type: application/json" \
|        -d '{"title":"Hello","body":"..."}'
|
*/

Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {

    // Health check sederhana — berguna untuk monitoring / smoke test.
    Route::get('/ping', function () {
        return JsonResponse::ok(['pong' => true, 'time' => date('c')]);
    })->name('api.ping');

    // Contoh REST resource: App\Controllers\Api\ArticleRestController
    // (extend Kodhe\Framework\Http\Controllers\RESTController).
    // Menghasilkan:
    //   GET    /api/articles              -> index   (?per_page&page)
    //   POST   /api/articles              -> store
    //   GET    /api/articles/{article}    -> show
    //   PUT    /api/articles/{article}    -> update
    //   DELETE /api/articles/{article}    -> destroy
    Route::apiResource('articles', 'App\Controllers\Api\ArticleRestController');
});
