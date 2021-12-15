<?php

use Illuminate\Support\Facades\Route;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\Http\Controllers\CheckFileInfoController;
use Nagi\LaravelWopi\Http\Controllers\GetFileController;
use Nagi\LaravelWopi\Http\Controllers\PutFileController;
use Nagi\LaravelWopi\Http\Controllers\WopiPostRequestRouter;

$middleware = app(ConfigRepositoryInterface::class)->getMiddleware();

Route::group([
    'middleware' => $middleware,
    'prefix'     => 'wopi',
    'as' => 'wopi.',
], function () {

    // Something that I wanted to contribute to laravel (Header-based routing)
    // Route::whereHasHeader('X-HEADER-VALUE')->get('files/123', SomeController::class);
    // Route::whereHasHeader('X-HEADER-ANOTHER-VALUE')->get('files/123', AnotherController::class);

    // Route::whereHeader('X-HEADER-VALUE', '🔥🔥🔥')->post('files/123/contents', SomeController::class);
    // Route::whereHeader('X-HEADER-ANOTHER-VALUE', '🚀🚀🚀')->post('files/123/contents', AnotherController::class);

    Route::get('files/{file_id}', CheckFileInfoController::class)->name('checkFileInfo');
    Route::get('files/{file_id}/contents', GetFileController::class)->name('getFile');
    Route::post('files/{file_id}/contents', PutFileController::class)->name('putFile');

    Route::post('files/{file_id}', WopiPostRequestRouter::class)->name('post-router');
});
