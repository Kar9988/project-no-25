<?php

namespace App\Services;

use App\Models\Episode;
use Illuminate\Support\Facades\DB;

class PurchaseService
{

    private EpisodeService $episodeService;
    private UserBalanceService $userBalanceService;
    private PaymentService $paymentService;

    /**
     * @param EpisodeService $episodeService
     * @param UserBalanceService $userBalanceService
     */
    public function __construct(EpisodeService $episodeService, UserBalanceService $userBalanceService,
                                PaymentService $paymentService)
    {
        $this->episodeService = $episodeService;
        $this->userBalanceService = $userBalanceService;
        $this->paymentService = $paymentService;
    }

    /**
     * @param int $episodeId
     * @param int $userId
     * @return array
     */
    public function purchaseEpisode(int $episodeId, int $userId): array
    {
        try {
            DB::beginTransaction();
            $episode = $this->episodeService->getById($episodeId);
            $userBalance = $this->userBalanceService->getByUserId($userId);
            if ($userBalance->amount < $episode->price) {
                DB::rollBack();
                return [
                    'success' => false,
                    'type'    => 'error',
                    'message' => 'You dont have enough coins'
                ];
            }
            $userBalance->update([
                'amount' => $userBalance->amount - $episode->price
            ]);
            // todo:: for now it's only payment with coin, but in future will be with payment provider
            $paymentableData = [
                'type' => Episode::class,
                'id'   => $episodeId
            ];
            $this->paymentService->create([
                'user_id'             => auth()->id(),
                'amount'              => $episode->price,
                'external_payment_id' => null, //null when payment is completed via coins otherwise it should be payment provider side id
                'paymentable_type'    => $paymentableData['type'],
                'paymentable_id'      => $paymentableData['id']
            ]);

            DB::commit();

            return [
                'success' => true,
                'type'    => 'success',
                'message' => 'You have successfully '
            ];
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
