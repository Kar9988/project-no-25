<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EpisodeService;
use App\Services\VideoStream;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

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

        $videoPath = public_path("storage/$episode->source");

        $stream = new VideoStream($videoPath);
        $response = Response::stream(function () use ($stream) {
            $stream->start();
        }, 200, [
            'Content-Type' => 'video/mp4',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Content-Range' => 1000,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
        ]);

        $response->send();
    }

}
