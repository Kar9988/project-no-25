<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $service;

    public function __construct(PlanService $service)
    {
        $this->service = $service;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return response()->json(['data' => PlanResource::collection($this->service->index()), 'success' => true, 'type' => 'success']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PlanRequest $request)
    {
//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request)
    {
        $data = $this->service->store($request->all());
        return response()->json(['success' => true, 'type' => 'success', 'plan' => new PlanResource($data)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = $this->service->getById($id);
        return response()->json(['success' => true, 'type' => 'success', 'plan' => new PlanResource($plan)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Request $request)
    {
//
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $updateData = $this->service->update($request->all(), $id);
        if ($updateData === 1) {
            return response()->json(['success' => true, 'type' => 'success']);
        }
        return response()->json(['success' => false, 'type' => 'error']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->service->delete($id) === 1) {
            return response()->json(['success' => true, 'type' => 'success']);
        };
        return response()->json(['success' => false, 'type' => 'error']);

    }
}
