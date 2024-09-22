<?php

use SirCoolMind\UploadedFiles\app\Http\Controllers\UploadedFileController;

// Public
Route::get('files/download/{id}', [UploadedFileController::class, 'download'])->name('files.download');
