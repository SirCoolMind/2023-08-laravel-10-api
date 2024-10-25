<?php

namespace SirCoolMind\UploadedFiles\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class UploadedFileController extends \App\Http\Controllers\Controller
{
    public function download(Request $request, $id)
    {
        // Validate the signature
        if (!$request->hasValidSignature()) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid or expired download link.');
        }

        // Fetch the file record
        $file = UploadedFile::findOrFail($id);

        // Check if file exists on the disk
        if (!Storage::disk('public')->exists($file->path)) {
            abort(Response::HTTP_NOT_FOUND, 'File not found.');
        }

        // Serve the file securely
        return Storage::disk('public')->download($file->path, $file->original_filename);
    }
}
