<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CategoryController extends Controller
{
    /**
     * @param CategoryService $service
     */

    public function __construct(protected CategoryService $service)
    {
    }

    /**
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        return response()->json([
            'data'    => CategoryResource::collection($this->service->index()),
            'success' => true,
            'type'   => 'success'
        ], 200);
    }

    /**
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $data = $this->service->store($request->all());

        return response()->json([
            'success' => true,
            'message' => 'category created successfully',
            'view' => new CategoryResource($data),
            'type' => 'success'
        ], 201);

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'type' => 'success',
            'categories' => new CategoryResource($this->service->getById($id))
        ]);
    }
    /**
     * @param CategoryRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, string $id): JsonResponse
    {
        $updateData = $this->service->update($request->all(), $id);
        if ($updateData === 1) {
            return response()->json([
                'success' => true,
                'type' => 'success'
            ]);
        }

        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => 'category update failed'
        ]);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */

    public function destroy(string $id)
    {
        if ($this->service->delete($id) === 1) {

            return response()->json([
                'success' => true,
                'type' => 'success'
            ]);
        }

        return response()->json([
            'success' => false,
            'type' => 'error'
        ]);
    }
}
