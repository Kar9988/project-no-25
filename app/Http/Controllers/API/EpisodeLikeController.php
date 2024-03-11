<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EpisodeLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EpisodeLikeController extends Controller
{
    /**
     * @param EpisodeLikeService $service
     */

    public function __construct(protected EpisodeLikeService $service)
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
                'message' => 'Episode successfully unliked',
                'type' => 'success'
            ]);
        }

        return response()->json([
            'success' => false,
            'type' => 'error'
        ]);
    }
}
