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
        $result = $this->videoService->paginateVideos();

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'videos'  => $result
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function discover(): JsonResponse
    {
        $result = $this->videoService->discover();

        return response()->json([
            'success'  => true,
            'type'     => 'success',
            'discover' => $result
        ]);
    }
}
