<?php

use Illuminate\Support\Facades\Route;
use Nagi\LaravelWopi\Http\Controllers\CheckFileInfoController;
use Nagi\LaravelWopi\Http\Controllers\GetFileController;
use Nagi\LaravelWopi\Http\Controllers\PutFileController;
use Nagi\LaravelWopi\Http\Middleware\ValidateProof;

Route::group([
    'middleware' => [ValidateProof::class],
    'prefix'     => 'wopi',
    'as' => 'wopi.',
], function () {
    Route::get('files/{file_id}', CheckFileInfoController::class)->name('checkFileInfo');
    Route::get('files/{file_id}/contents', GetFileController::class)->name('getFile');
    Route::post('files/{file_id}/contents', PutFileController::class)->name('putFile');
});
