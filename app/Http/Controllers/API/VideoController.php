<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $data = [
            'page' => request()->get('page', 0),
            'take' => request()->get('take', 10),
            'id'   => request()->get('id', null)
        ];
        $result = $this->videoService->randomVideos($data, $data['page'], $data['take']);

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'videos'  => $result
        ]);
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function randomVideos(): JsonResponse
    {
        $videos = $this->videoService->random();

        return response()->json([
            'success'  => true,
            'episodes' => VideoResource::collection($videos),
            'type'     => 'success'
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
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $video = $this->videoService->getVideo($id);

        return response()->json([
            'success' => true,
            'type'    => 'success',
            'video'   => $video
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
