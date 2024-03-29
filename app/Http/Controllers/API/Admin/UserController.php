<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserUpdateRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminService;
use App\Services\UserBalanceService;
use http\Env\Request;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    protected $service;
    protected $balanceService;
    /**
     * @param AdminService $service
     */
    public function __construct(AdminService $service, UserBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'users'   => UserResource::collection(User::where('role_id', '!=', Role::ADMIN_ID)->get()),
            'type'    => 'success'
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {

        $user->load('userBalance');
        return response()->json([
            'success' => true,
            'user' => $user,
            'type' => 'success'
        ], 200);
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $userData= [
            'name'=>$request->name,
            'email'=>$request->email,
        ];

        $updateData = $this->service->update($userData, $user['id']);
        if ($updateData){
            $balanceValue =[
                'amount' => $request->amount,
                'bonus'  => $request->bonus,
            ];
            $getBalance = $this->balanceService->getByUserId($user['id']);
            if (!$getBalance){
                $this->balanceService->store([
                    'amount' => $request->amount,
                    'bonus'  => $request->bonus,
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => true,
                    'type' => 'success'
                ]);
            }else{
                $updateBalance = $this->balanceService->update($balanceValue, $request->balanceId);
                if ($updateBalance === 1) {
                    return response()->json([
                        'success' => true,
                        'type' => 'success'
                    ]);
                }
            }
        }

        return response()->json([
            'success' => false,
            'type' => 'error'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        User::where('id', $id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'product was successfully deleted',
            'type'    => 'success'
        ], 200);
    }

    /**
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
       $this->service->create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'User successfully created',
            'type'    => 'success'
        ], 201);
    }
}
