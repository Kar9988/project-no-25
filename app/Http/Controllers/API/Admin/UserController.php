<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\UserUpdateRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\AdminService;
use App\Services\UserBalanceService;
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
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $userData= [
            'name' => $request->name,
            'email' => $request->email,
        ];

        $updateData = $this->service->update($userData, $user['id']);
        if ($updateData){
            $balanceValue =[
                'amount' => $request->amount??0,
                'bonus'  => $request->bonus??0,
            ];
            $getBalance = $this->balanceService->getByUserId($user['id']);
            if (!$getBalance){
                if ($balanceValue['amount']) {
                    UserTransaction::query()->create([
                        'user_id' => $user['id'],
                        'type'    => 'increase',
                        'amount'  => $balanceValue['amount'],
                        'note'    => 'Admin add balance'
                    ]);
                }
                if ($balanceValue['bonus']) {
                    UserTransaction::query()->create([
                        'user_id' => $user['id'],
                        'type'    => 'increase',
                        'amount'  => $balanceValue['bonus'],
                        'note'    => 'Admin add bonus'
                    ]);
                }
                $this->balanceService->store([
                    'amount' => $balanceValue['amount'],
                    'bonus'  => $balanceValue['bonus'],
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'type' => 'success'
                ]);
            } else {
                $updateBalance = $this->balanceService->update($balanceValue, $user['id']);
                if ($updateBalance === 1) {
                    if ($getBalance->bonus != $balanceValue['bonus']) {
                        $type = $balanceValue['bonus'] > $getBalance->bonus ? 'increase' : 'decrease';
                        UserTransaction::query()->create([
                            'user_id' => $user['id'],
                            'type'    => $type,
                            'amount'  => abs($getBalance->bonus - $balanceValue['bonus']),
                            'note'    => "Admin $type bonus"
                        ]);
                    }
                    if ($getBalance->amount != $balanceValue['amount']) {
                        $type = $balanceValue['amount'] > $getBalance->amount ? 'increase' : 'decrease';
                        UserTransaction::query()->create([
                            'user_id' => $user['id'],
                            'type'    => $type,
                            'amount'  => abs($getBalance->amount - $balanceValue['amount']),
                            'note'    => "Admin $type amount"
                        ]);
                    }
                    return response()->json([
                        'success' => true,
                        'type' => 'success'
                    ]);
                }
            }
        }
        return response()->json([
            'success' => false,
            'type' => 'error',
            'message' => 'Something went wrong'
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
            'type'    => 'success'
        ], 200);
    }

    /**
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
      $create = $this->service->create($request->all());
       if ($create){
           return response()->json([
               'success' => true,
               'type'    => 'success'
           ], 201);
       }
        return response()->json([
          'success' => false,
          'type' => 'error',
          'message' => 'Something went wrong'
        ]);
    }
}
