<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCard;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;

class PurchaseService
{

    private EpisodeService $episodeService;
    private UserBalanceService $userBalanceService;
    private PaymentService $paymentService;
    private PlanService $planService;
    private SubscriptionService $subscriptionService;

    /**
     * @param EpisodeService $episodeService
     * @param UserBalanceService $userBalanceService
     * @param PaymentService $paymentService
     * @param PlanService $planService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(EpisodeService      $episodeService, UserBalanceService $userBalanceService,
                                PaymentService      $paymentService, PlanService $planService,
                                SubscriptionService $subscriptionService)
    {
        $this->episodeService = $episodeService;
        $this->userBalanceService = $userBalanceService;
        $this->paymentService = $paymentService;
        $this->planService = $planService;
        $this->subscriptionService = $subscriptionService;
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
        $stripe->customers->create([
            'name' => 'Jenny Rosen',
            'email' => 'jennyrosen@example.com',
        ]);
        $intent = $stripe->paymentIntents->create([
            'amount'                    => $plan->price * 100,
            'currency'                  => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return $intent->client_secret;
    }

    public function createSubscription(int $userId, Plan $plan)
    {
        $nextAttempt = null;
        switch ($plan->type) {
            case('weekly'):
                $nextAttempt = Carbon::now()->addWeek();
                break;
            case('yearly'):
                $nextAttempt = Carbon::now()->addYear();
                break;
            default:
        }
        $data = [
            'user_id'    => $userId,
            'plan_id'    => $plan->id,
            'start_date' => Carbon::now(),
            'end_date'   => $nextAttempt
        ];
        $subscription = $this->subscriptionService->store($data);
        $subscription->invoice()->create([
            'user_id'      => $userId,
            'next_attempt' => $nextAttempt,
            'amount'       => $plan->price,
        ]);
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

            if ($plan->type !== 'one_time') {
                $this->createSubscription($userId, $plan);
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
                'payment_method'      => $card->id
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
    public function purchaseEpisode(int $episodeId, int $userId, string $type): array
    {
        try {
            DB::beginTransaction();
            $episode = $this->episodeService->getById($episodeId);
            $userBalance = $this->userBalanceService->getByUserId($userId);

            if (($type == 'bonus' && $userBalance->bonus < $episode->price)
                || $type == 'coin' && $userBalance->amount < $episode->price) {
                DB::rollBack();
                return [
                    'success' => false,
                    'type'    => 'error',
                    'message' => 'You dont have enough coins'
                ];
            }
            if ($type == 'coin') {
                $userBalance->update([
                    'amount' => $userBalance->amount - $episode->price
                ]);
            } else {
                $userBalance->update([
                    'bonus' => $userBalance->bonus - $episode->price
                ]);
            }

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

    /**
     * @param int $userId
     * @return mixed
     */
    public function getHistory(int $userId, $page, $take):mixed
    {
        return User::query()
            ->select('payments.created_at', 'plans.type', 'plans.points', 'user_cards.type')
            ->join('payments', 'payments.user_id', '=', 'users.id')
            ->join('plans', 'paymentable_id', '=', 'plans.id')
            ->join('user_cards', 'payments.payment_method', '=', 'user_cards.id')
            ->where('users.id', $userId)
            ->skip($page * $take - $take)
            ->take($take)->get();
    }

}
