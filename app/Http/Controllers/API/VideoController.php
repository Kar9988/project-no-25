<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;

class VideoController extends Controller
{
    private VideoService $videoService;

    /**
     * @param VideoService $videoService
     */
    public function __construct(VideoService $videoService)
    {

        $this->videoService = $videoService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $result = $this->videoService->paginateVideos(10);

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'videos'  => $result
        ]);
    }
}
