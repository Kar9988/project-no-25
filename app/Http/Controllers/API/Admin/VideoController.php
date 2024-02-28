<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\VideoStoreRequest;
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
            'videos'   => $result
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'type'    => 'success',
            'video'   => $this->videoService->getById($id)
        ]);
    }

    /**
     * @param VideoStoreRequest $request
     * @return JsonResponse
     */
    public function store(VideoStoreRequest $request): JsonResponse
    {
        $video = $this->videoService->createVideo($request->all());
        if ($video) {

            return response()->json([
                'success' => true,
                'type'    => 'success',
                'video'   => $video
            ]);
        }

        return response()->json([
            'success' => false,
            'type'    => 'error',
            'message' => 'Something went wrong'
        ]);
    }
}
