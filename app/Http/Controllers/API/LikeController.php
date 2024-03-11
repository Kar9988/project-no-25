<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\UserLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * @param UserLikeService $service
     */

    public function __construct(protected UserLikeService $service)
    {
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->service->store($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Like successfully created',
            'type' => 'success'
        ], 201);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        if ($this->service->delete($id) === 1) {

            return response()->json([
                'success' => true,
                'message' => 'Video successfully unliked',
                'type' => 'success'
            ]);
        }

        return response()->json([
            'success' => false,
            'type' => 'error'
        ]);

    }
}
