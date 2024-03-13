<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ViewResource;
use App\Models\Episode;
use App\Models\View;
use App\Services\ViewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewController extends Controller
{
    /**
     * @param ViewService $service
     */

    public function __construct(protected ViewService $service)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $count = (int)$request->views_count;
        if ($count > 0) {
            $data = [];
            for ($i = 0; $i < $count; $i++) {
                $data[] = [
                    'user_id' => auth()->id(),
                    'episode_id' => $request->episode_id
                ];
            }
            $this->service->insert($data);
            return response()->json([
                'success' => true,
                'message' => 'Views created successfully',
                'type'    => 'success',
            ], 201);
        } else {
            $create = $this->service->store($request->all());
            return response()->json([
                'success' => true,
                'message' => 'View created successfully',
                'view' => new ViewResource($create),
                'type' => 'success'
            ], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request): JsonResponse
    {
        if (isset($request->count) && $request->count != null) {
            $deletedRows = View::query()->where('episode_id', $id)
                ->where('user_id', auth()->id())
                ->take($request->count)
                ->delete();
            return response()->json([
                'message' => $deletedRows . ' records deleted successfully',
                'type'    => 'success',
                'success' => true
                ]);
        }
        return response()->json([
            'message' => 'There is no line to delete',
            'success' => true,
            'type'   => 'error',
            ]);
    }
}
