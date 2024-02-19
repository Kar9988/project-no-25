<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\PlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        $plans = Plan::all();
//        return response()->json(['data' => PlanResource::collection($plans), 'success' => true, 'type' => 'success']);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PlanRequest $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlanRequest $request, PlanService $service)
    {
        $data = $service->store($request->all());
        return response()->json(['success' => true, 'type' => 'success','plan' => new PlanResource($data)]);
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
        dd($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
