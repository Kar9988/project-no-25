<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\VideoStoreRequest;
use App\Http\Requests\API\VideoUpdateRequest;
use App\Http\Resources\VideoResource;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

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
            'videos'  => $result['data'],
            ...Arr::except($result, 'data')
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
            'video'   => new VideoResource($this->videoService->getById($id))
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

    /**
     * @param $videoId
     * @param VideoUpdateRequest $updateRequest
     * @return JsonResponse
     */
    public function update($videoId, VideoUpdateRequest $updateRequest): JsonResponse
    {
        $video = $this->videoService->updateVideo($videoId, $updateRequest->all());
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
