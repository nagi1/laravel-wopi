<?php

use Illuminate\Support\Facades\Route;
use Nagi\LaravelWopi\Http\Controllers\CheckFileInfoController;
use Nagi\LaravelWopi\Http\Middleware\ValidateProof;

Route::group([
    'middleware' => [ValidateProof::class],
    'prefix'     => 'wopi',
], function () {
    Route::get('files/{file_id}', CheckFileInfoController::class)->name('checkFileInfo');
});
