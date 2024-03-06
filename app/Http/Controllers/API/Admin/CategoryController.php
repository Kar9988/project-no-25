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
        $result = $this->service->paginateVideos(10);

        return response()->json([
            'success' => true,
            'type' => 'success',
            'categories' => $result['data'],
            ...Arr::except($result, 'data')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(CategoryRequest $request)
    {
        $data = $this->service->store($request->all());

        return response()->json([
            'success' => true,
            'message' => 'category created successfully',
            'view' => new CategoryResource($data)
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * @param CategoryRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, string $id): JsonResponse
    {
//        dd($request->all());
        $updateData = $this->service->update($request->all(), $id);
        if ($updateData === 1) {
            return response()->json(['success' => true, 'type' => 'success']);
        }

        return response()->json(['success' => false, 'type' => 'error']);
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
