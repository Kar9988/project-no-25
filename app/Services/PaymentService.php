<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    private Payment $model;

    /**
     * @param Payment $model
     */
    public function __construct(Payment $model)
    {

        $this->model = $model;
    }

    /**
     * @param $data
     * @return Payment
     */
    public function create($data): Payment
    {
        return $this->model->create($data);
    }

    public function index($user_id, $page, $take)
    {
        return Payment::query()
            ->select('payments.id', 'users.name as userName', 'users.id as userId', 'payments.created_at', 'plans.name as planName', 'plans.price as planPrice', 'plans.points as planPoints', 'payments.user_id', 'plans.type as plan_type', 'plans.points', 'user_cards.type')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->join('plans', 'paymentable_id', '=', 'plans.id')
            ->join('user_cards', 'payments.payment_method', '=', 'user_cards.id')
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('users.id', $user_id);
            })
            ->groupBy('payments.id')
            ->skip($page * $take - $take)
            ->take($take)
            ->get();
    }
}
