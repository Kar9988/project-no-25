<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
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

    /**
     * @param int $categoryId
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function filter(int $categoryId): JsonResponse
    {
        $result = $this->videoService->getByCategoryId($categoryId, request()->get('page', 1));

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'videos'  => $result
        ]);
    }
}
