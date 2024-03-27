<?php

namespace App\Services;

use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class UserBalanceService
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data): UserBalance
    {
        return UserBalance::create($data);
    }

    /**
     * @return mixed
     */
    public function index(): UserBalance
    {
        return UserBalance::all();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): UserBalance
    {
        return UserBalance::query()->findOrFail($id);
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getByUserId(int $userId): mixed
    {
        return UserBalance::query()->where('user_id', $userId)->first();
    }


    /**
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(array $data, int $id): int
    {
        return UserBalance::query()->where('id', $id)->update($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): UserBalance
    {
        return UserBalance::query()->where('id', $id)->delete();
    }

    /**
     * @param array $data
     * @param int $userId
     * @return array
     */
    public function addBalance(array $data, int $userId, bool $invoicePay = false): array
    {
        try {
            DB::beginTransaction();
            $userBalance = UserBalance::query()->where('user_id', $userId)->first();
            $userBalanceAmount = (int)$userBalance['amount'] + (int)$data['amount'];
            $updateData = [
                'amount' => $userBalanceAmount
            ];
            $update = UserBalance::query()->where('id', $userBalance['id'])->update($updateData);
            if ($update && !$invoicePay) {
                $paymentable = [
                    'from_id'          => auth()->id(),
                    'user_id'          => $userId,
                    'amount'           => $data['amount'],
                    'paymentable_type' => UserBalance::class,
                    'Paymentable_id'   => $userBalance['id']
                ];
                $created = $this->paymentService->create($paymentable);
            }
            if ($created) {
                DB::commit();

                return [
                    'success' => true,
                    'type'    => 'success',
                ];
            } else {

                return [
                    'success' => false,
                    'type'    => 'error',
                ];
            }
        } catch (\Exception $exception) {
            DB::rollBack();

            return [
                'success' => false,
                'type'    => 'error',
                'message' => $exception->getMessage()
            ];
        }
    }

}
