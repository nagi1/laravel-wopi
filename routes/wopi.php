<?php

use Illuminate\Support\Facades\Route;
use Nagi\LaravelWopi\Http\Controllers\CheckFileInfoController;
use Nagi\LaravelWopi\Http\Controllers\DeleteFileController;
use Nagi\LaravelWopi\Http\Controllers\GetFileController;
use Nagi\LaravelWopi\Http\Controllers\GetLockController;
use Nagi\LaravelWopi\Http\Controllers\LockController;
use Nagi\LaravelWopi\Http\Controllers\PutFileController;
use Nagi\LaravelWopi\Http\Controllers\PutRelativeFileController;
use Nagi\LaravelWopi\Http\Controllers\RefreshLockController;
use Nagi\LaravelWopi\Http\Controllers\RenameFileController;
use Nagi\LaravelWopi\Http\Controllers\UnlockAndRelockController;
use Nagi\LaravelWopi\Http\Controllers\UnlockController;
use Nagi\LaravelWopi\Http\Middleware\ValidateProof;

Route::group([
    'middleware' => [ValidateProof::class],
    'prefix'     => 'wopi',
    'as' => 'wopi.',
], function () {
    Route::get('files/{file_id}', CheckFileInfoController::class)->name('checkFileInfo');
    Route::get('files/{file_id}/contents', GetFileController::class)->name('getFile');
    Route::post('files/{file_id}/contents', PutFileController::class)->name('putFile');
    Route::post('files/{file_id}', LockController::class)->name('lock');
    Route::post('files/{file_id}', GetLockController::class)->name('getLock');
    Route::post('files/{file_id}', RefreshLockController::class)->name('refreshLock');
    Route::post('files/{file_id}', UnlockController::class)->name('unlock');
    Route::post('files/{file_id}', UnlockAndRelockController::class)->name('unlockAndRelock');
    Route::post('files/{file_id}', PutRelativeFileController::class)->name('putRelativeFile');
    Route::post('files/{file_id}', RenameFileController::class)->name('rename');
    Route::post('files/{file_id}', DeleteFileController::class)->name('deleteFile');
});
