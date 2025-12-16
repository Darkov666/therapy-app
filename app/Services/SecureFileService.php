<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SecureFileService
{
    /**
     * Store a file in the private storage.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return string Path to the stored file
     */
    public function storeFile($file, $folder = 'documents')
    {
        // Store in storage/app/private/{folder}
        // Note: 'local' disk usually points to storage/app
        // We can use a custom disk 'private' or just store in a specific folder in 'local'
        // Default filesystem 'local' root is storage_path('app')

        // We will store in 'private/{folder}' relative to storage/app
        return $file->storeAs(
            'private/' . $folder,
            time() . '_' . $file->getClientOriginalName(),
            'local'
        );
    }

    /**
     * Generate a temporary signed URL to view the file.
     *
     * @param string $path The relative path in storage/app (e.g., private/docs/file.pdf)
     * @param int $minutes Validity duration
     * @return string
     */
    public function getTemporaryUrl($path, $minutes = 5)
    {
        if (!$path || !Storage::disk('local')->exists($path)) {
            return null;
        }

        // Generate a URL to a route that will serve the file
        // Route name: 'media.secure' (to be defined)
        return URL::temporarySignedRoute(
            'media.secure',
            now()->addMinutes($minutes),
            ['path' => $path]
        );
    }
}
