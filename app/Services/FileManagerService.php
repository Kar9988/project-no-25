<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManagerService
{

    private Filesystem $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('public');
    }

    /**
     * @param string $fileName
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeCover(string $fileName, UploadedFile $file): bool|string
    {
        return $this->storage->putFile($fileName, $file);
    }

    /**
     * @param string $fileName
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeVideo(string $fileName, UploadedFile $file): bool|string
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
