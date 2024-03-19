<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Intervention\Image\ImageManager;

class FileManagerService
{

    private Filesystem $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('spaces');
    }

    /**
     * @param string $filePath
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeCover(string $filePath, $file): bool|string
    {
        $file = Storage::disk('public')->putFile('tmp', $file);
        $image = new Imagick(storage_path("app/public/$file"));
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(80);
        $image->setOption('webp:progressive', 'true');
        $image->writeImage(storage_path("app/public/$file"));
        $fileName = time() . '.webp';
        $this->storage->put("$filePath/$fileName", file_get_contents(new File(storage_path("app/public/$file"))), 'public');
        Storage::disk('public')->delete($file);

        return "$filePath/$fileName";
    }

    /**
     * @param string $filePath
     * @param $file
     * @return string
     * @throws \ImagickException
     */
    public function storeThumb(string $filePath, $file): string
    {
        $filepath = $file->store('public/tmp');
        $image = new Imagick(storage_path('app/' . $filepath));
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(80);
        $image->setOption('webp:progressive', 'true');
        $image->writeImage(storage_path('app/' . $filepath));

        $fileName = time() . '.webp';
        $this->storage->put("$filePath/$fileName", file_get_contents(new File(storage_path('app/' . $filepath))), 'public');
        Storage::disk('public')->delete('app/' . $filepath);
        return "$filePath/$fileName";
    }

    /**
     * @param string $fileName
     * @param $filePath
     * @return bool|string
     */
    public function storeVideo(string $fileName, $filePath): bool|string
    {
        $storedFilePath = "videos/$fileName";
        try {
            Storage::disk('public')->put($storedFilePath, file_get_contents($filePath));
        } catch (\Exception $e) {
            return $e;
        }

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        return $storedFilePath;
    }

    /**
     * @param array $paths
     * @return bool
     */
    public function deleteFiles(array $paths): bool
    {
        return $this->storage->delete($paths);
    }
}
