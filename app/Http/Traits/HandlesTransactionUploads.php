<?php

namespace App\Http\Traits;

use App\Http\Requests\UploadFileRequest;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesTransactionUploads
{
    protected function uploadTransactionFiles(Asset $asset, Request $request, string $note): void
    {
        if (! $request->hasFile('file')) {
            return;
        }

        if (! Storage::exists('private_uploads/assets')) {
            Storage::makeDirectory('private_uploads/assets', 775);
        }

        $uploadRequest = app(UploadFileRequest::class);

        foreach ($request->file('file', []) as $file) {
            $filename = $uploadRequest->handleFile(
                'private_uploads/assets/',
                'transaction-'.$asset->id,
                $file
            );

            $asset->logUpload($filename, $note);
        }
    }
}
