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
use App\Services\PlanService;
use App\Services\SubscriptionService;
use App\Services\UserBalanceService;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    protected $service;
    protected $balanceService;
    protected $planService;
    protected $subscriptionService;
    /**
     * @param AdminService $service
     */
    public function __construct(AdminService $service, UserBalanceService $balanceService,SubscriptionService $subscriptionService,PlanService $planService)
    {   $this->subscriptionService = $subscriptionService;
        $this->balanceService = $balanceService;
        $this->service = $service;
        $this->planService = $planService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'users' => UserResource::collection(User::with('getActiveSubscription')
                ->where('role_id', '!=', Role::ADMIN_ID)
                ->get()),
            'type'    => 'success'
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {

        $user->load('userBalance','getActiveSubscription.plan');

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

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->subId) {
            $deleteSub = $this->subscriptionService->delete($request->subId);
        }
            $plan = $this->planService->getById($request->planId);
            if ($plan) {
                $nextAttempt = '';
                switch ($plan->type) {
                    case('one_time'):
                        $nextAttempt = Carbon::now()->now();
                        break;
                    case('weekly'):
                        $nextAttempt = Carbon::now()->addWeek();
                        break;
                    case('yearly'):
                        $nextAttempt = Carbon::now()->addYear();
                        break;
                    default:
                }
                $data = [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'start_date' => Carbon::now(),
                    'end_date' => $nextAttempt
                ];
                $subscription = $this->subscriptionService->store($data);
                $subscription->invoice()->create([
                    'user_id' => $user->id,
                    'next_attempt' => $nextAttempt,
                    'amount' => $plan->price,
                ]);
            }
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
