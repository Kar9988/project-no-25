<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class FileManagerService
{

    private Filesystem $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('public');
    }

    /**
     * @param string $filePath
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeCover(string $filePath, $file): bool|string
    {
        $file = ImageManager::imagick()->read($file)->toWebp(30)->toString();
        $fileName = time().'.webp';
        $this->storage->put("$filePath/$fileName", $file);

        return $fileName;
    }

    /**
     * @param string $fileName
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeVideo(string $fileName,  $file): bool|string
    {

        return $this->storage->putFile($fileName, $file);
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
