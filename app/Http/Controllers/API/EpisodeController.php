<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EpisodeService;
use App\Services\VideoStream;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EpisodeController extends Controller
{

    private EpisodeService $episodeService;

    /**
     * @param EpisodeService $episodeService
     */
    public function __construct(EpisodeService $episodeService)
    {

        $this->episodeService = $episodeService;
    }

    public function getVideoStream($episodeId)
    {
                $episode = $this->episodeService->getById($episodeId);

        $videoPath = $episode->source; // Replace with the actual path to your video file
        $fileSize = Storage::disk('public')->size($videoPath);

        $response = new StreamedResponse(function () use ($videoPath, $fileSize) {
            $file = Storage::disk('public')->get($videoPath);
            $chunkSize = 1048576/2; // 1 MB chunk size, adjust as needed

            // Split the file into chunks and send them to the client
            for ($start = 0; $start < $fileSize; $start += $chunkSize) {
                $end = min($start + $chunkSize - 1, $fileSize - 1);

                echo substr($file, $start, $chunkSize);

                ob_flush();
                flush();
            }
        });

        $response->headers->set('Content-Type', 'video/mp4');
        $response->headers->set('Content-Length', $fileSize);
        $response->headers->set('Accept-Ranges', 'bytes');

        return $response;
    }
//
//    public function getVideoStream($episodeId)
//    {
//
//        $episode = $this->episodeService->getById($episodeId);
//
//        $videoPath = public_path("storage/$episode->source");
//
//        $stream = new VideoStream($videoPath);
//        $response = Response::stream(function () use ($stream) {
//            $stream->start();
//        }, 200, [
//            'Content-Type' => 'video/mp4',
//            'Cache-Control' => 'no-cache, no-store, must-revalidate',
//            'Pragma' => 'no-cache',
//            'Expires' => '0',
//            'Content-Range' => 1000,
//            'Content-Disposition' => 'inline',
//            'X-Content-Type-Options' => 'nosniff',
//        ]);
//
//        $response->send();
//    }

}
