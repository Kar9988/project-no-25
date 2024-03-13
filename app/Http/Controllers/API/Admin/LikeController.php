<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\LikesTrait;

class LikeController extends Controller
{

    use LikesTrait;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->like($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Likes created successfully',
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request): JsonResponse
    {
        return $this->destroyLike($id, $request->all());
        return response()->json(['message' => 'records deleted successfully']);
    }
}
