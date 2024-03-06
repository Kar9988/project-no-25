<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ViewResource;
use App\Models\View;
use App\Services\ViewService;
use http\Client\Curl\User;
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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        dd(88889898989898);
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
            ], 201);
        } else {
            $create = $this->service->store($request->all());
            return response()->json([
                'success' => true,
                'message' => 'View created successfully',
                'view' => new ViewResource($create)
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        if (isset($request->count) && $request->count != null) {
            $deletedRows = View::query()->where('episode_id', $id)
                ->where('user_id', auth()->id())
                ->take($request->count)
                ->delete();
            return response()->json(['message' => $deletedRows . ' records deleted successfully'], 200);
        }
        return response()->json(['message' => 'not deleted rows'], 200);
    }
}
