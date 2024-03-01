<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EpisodeService;

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

        \Iman\Streamer\VideoStreamer::streamFile(public_path("storage/$episode->video_path"));
    }

}
