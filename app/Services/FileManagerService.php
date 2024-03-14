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
        $fileName = $this->storage->putFile($filePath, new File(storage_path("app/public/$file")), 'public');
        Storage::disk('public')->delete($file);

        return $fileName;
    }

    /**
     * @param string $filePath
     * @param $file
     * @return string
     * @throws \ImagickException
     */
    public function storeThumb(string $filePath, $file): string
    {
        chmod(storage_path("app/public/$file"), 0777);
        $image = new Imagick(storage_path("app/public/$file"));
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(80);
        $image->setOption('webp:progressive', 'true');
        $image->writeImage(storage_path("app/public/$file"));
        $fileName = time().'.webp';
        $this->storage->put("$filePath/$fileName", $file, 'public');
        Storage::disk('public')->delete($file);

        return $fileName;
    }

    /**
     * @param string $fileName
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeVideo(string $fileName,  $filePath): bool|string
    {
        $file = new File(storage_path("app/public/$filePath"));
        $fileName = $this->storage->putFile($fileName, $file, 'public');
        Storage::disk('public')->delete($filePath);

        return $fileName;
    }

    /**
     * @param array $paths
     * @return bool
     */
    public function deleteFiles(array $paths):bool
    {
        return $this->storage->delete($paths);
    }
}
