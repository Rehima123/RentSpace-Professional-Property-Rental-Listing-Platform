<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesImageUpload
{
    /**
     * Store an uploaded image in the public disk under properties/.
     * Returns the relative storage path (e.g. "properties/abc123.jpg").
     */
    protected function storePropertyImage(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('properties', $filename, 'public');
    }

    /**
     * Delete a stored image from the public disk.
     */
    protected function deletePropertyImage(string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
