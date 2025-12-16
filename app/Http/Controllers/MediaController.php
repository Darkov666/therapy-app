<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Serve a private file if the signature is valid.
     */
    public function serve(Request $request)
    {
        // The 'signed' middleware should be applied to the route definition.
        // But we double check signature just in case middleware is missed (though middleware is safer).
        if (!$request->hasValidSignature()) {
            abort(403, 'Enlace expirado o inválido.');
        }

        $path = $request->query('path');

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        // Check if path is within 'private' folder to prevent traversing?
        // Storage::disk('local') is rooted at storage/app. 
        // We probably want to restrict to 'private/' prefix explicitly for this route.
        if (!str_starts_with($path, 'private/')) {
            abort(403, 'Acceso denegado a esta ubicación.');
        }

        return Storage::disk('local')->response($path);
    }
}
