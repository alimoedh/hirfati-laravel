<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public static function upload(UploadedFile $file, string $folder = 'uploads'): ?string
    {
        $allowedExtensions = config('hirfati.uploads.allowed_extensions', []);
        $maxSize           = config('hirfati.uploads.max_size', 5 * 1024 * 1024);

        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
            return null;
        }

        if ($file->getSize() > $maxSize) {
            return null;
        }

        $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($folder, $filename, 'public');
    }
}
