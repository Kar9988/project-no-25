<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ImageKit\ImageKit;
use Imagick;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class FileManagerService
{

    private Filesystem $storage;

    private ImageKit $imageKitService;

    public function __construct()
    {
        $this->storage = Storage::disk('public');
        $this->imageKitService = new ImageKit(config('image-kit.public_key'),
            config('image-kit.private_key'), config('image-kit.url_endpoint'));
    }

    public function test()
    {
        $upload = $this->imageKitService->uploadFile([
//            'file' => '/var/www/html/project-no-25/storage/app/public/videos/11/episodes/ijs4ioYE8AQ2FSR4QUR15tlcPfkvPVs58uYQFhix.mp4',
            'file' => base64_encode('/var/www/html/project-no-25/test_thumbs/1709999637.webp'),
            'fileName' => '1709999637.webp'
        ]);
//        $imageURL = $this->imageKitService->url(array(
//            'path'        => '/var/www/html/project-no-25/storage/app/public/videos/11/episodes/ijs4ioYE8AQ2FSR4QUR15tlcPfkvPVs58uYQFhix.mp4',
//            'urlEndpoint' => 'https://ik.imagekit.io/your_imagekit_id',
//
//            'transformation' => [
//                [
//                    'height' => '300',
//                    'width'  => '400',
//                    'raw'    => "l-image,i-ik_canvas,bg-FF0000,w-300,h-100,l-end"
//                ]
//            ]
//        ));
        dd($upload);
    }

    /**
     * @param string $filePath
     * @param UploadedFile $file
     * @return bool|string
     */
//    public function storeCover(string $filePath, $file): bool|string
//    {
//        $file = ImageManager::imagick()->read($file)->toWebp(30)->toString();
//        $fileName = time().'.webp';
//        $this->storage->put("$filePath/$fileName", $file);
//
//        return $fileName;
//    }
    public function storeCover(string $fileName, $file): bool|string
    {
        return $this->storage->putFile($fileName, $file);
    }

    /**
     * @param string $fileName
     * @param UploadedFile $file
     * @return bool|string
     */
    public function storeVideo(string $fileName, $file): bool|string
    {

        return $this->storage->putFile($fileName, $file);
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
