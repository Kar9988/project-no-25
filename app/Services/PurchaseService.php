<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Plan;
use App\Models\UserCard;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;

class PurchaseService
{

    private EpisodeService $episodeService;
    private UserBalanceService $userBalanceService;
    private PaymentService $paymentService;
    private PlanService $planService;

    /**
     * @param EpisodeService $episodeService
     * @param UserBalanceService $userBalanceService
     * @param PaymentService $paymentService
     * @param PlanService $planService
     */
    public function __construct(EpisodeService $episodeService, UserBalanceService $userBalanceService,
                                PaymentService $paymentService, PlanService $planService)
    {
        $this->episodeService = $episodeService;
        $this->userBalanceService = $userBalanceService;
        $this->paymentService = $paymentService;
        $this->planService = $planService;
    }

    /**
     * @param int $planId
     * @return string|null
     * @throws ApiErrorException
     */
    public function createPaymentIntent(int $planId): ?string
    {
        $plan = $this->planService->getById($planId);
        $stripe = new \Stripe\StripeClient(config('stripe.secret_key'));
        $intent = $stripe->paymentIntents->create([
            'amount'                    => $plan->price * 100,
            'currency'                  => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return $intent->client_secret;
    }

    /**
     * @param string $paymentMethod
     * @param string $paymentId
     * @param int $userId
     * @return void
     */
    public function purchasePlan(string $paymentMethod, string $paymentId, int $userId, int $planId, string $type): array
    {
        try {
            DB::beginTransaction();

            $plan = $this->planService->getById($planId);

            UserTransaction::query()->create([
                'user_id' => $userId,
                'type'    => 'increase',
                'amount'  => $plan->price,
                'note'    => 'Plan purchase'
            ]);
            $balance = $this->userBalanceService->getByUserId($userId);
            if ($balance) {
                $balance->update([
                    'amount' => $balance->amount + $plan->points
                ]);
            } else {
                $this->userBalanceService->store([
                    'user_id' => $userId,
                    'amount'  => $plan->points
                ]);
            }
            $card = UserCard::query()->create([
                'user_id'        => $userId,
                'payment_method' => $paymentMethod,
                'type'           => $type
            ]);
            $this->paymentService->create([
                'user_id'             => auth()->id(),
                'amount'              => $plan->price,
                'external_payment_id' => $paymentId,
                'paymentable_type'    => Plan::class,
                'paymentable_id'      => $planId,
                'payment_method'      => $card
            ]);
            DB::commit();

            return [
                'success' => true,
                'type'    => 'success',
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
