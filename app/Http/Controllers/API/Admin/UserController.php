<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    protected $service;

    /**
     * @param AdminService $service
     */
    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'users' => UserResource::collection(User::where('role_id', '!=', Role::ADMIN_ID)->get())
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $updateData = $this->service->update($request->all(), $user->id);
        if ($updateData === 1) {
            return response()->json(['success' => true, 'type' => 'success']);
        }
        return response()->json(['success' => false, 'type' => 'error']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        User::where('id', $id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'product was successfully deleted'
        ], 200);
    }
}
