<?php

namespace App\Console\Commands;


use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UserCard;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use App\Services\UserBalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentMethod;

class PayInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(InvoiceService $invoiceService, UserBalanceService $userBalanceService, SubscriptionService $subService, PaymentService $paymentService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
        $this->userBalanceService = $userBalanceService;
        $this->subService = $subService;
        $this->paymentService = $paymentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $invoices = $this->invoiceService->getByDate(Carbon::today()->toDateString());

            foreach ($invoices as $invoice) {
                $paymentable = [
                    'from_id'          => 0,
                    'user_id'          => $invoice->subscription->user->id,
                    'amount'           => $invoice->subscription->plan->price,
                    'paymentable_type' => Plan::class,
                    'Paymentable_id'   => $invoice->subscription->plan->id
                ];
                $this->paymentService->create($paymentable);
                $paymentMethod = UserCard::where('user_id', $invoice->subscription->user->id)->orderBy('id', 'DESC')->first();
                $stripe = new \Stripe\StripeClient(config('stripe.secret_key'));
                $paymentIntent = $stripe->paymentIntents->create([
                    'amount'                    => $invoice->subscription->plan->price * 100,
                    'currency'                  => 'usd',
                    'customer'                  => $invoice->subscription->user->customer_id,
                    'payment_method'            => $paymentMethod->payment_method,
                    'automatic_payment_methods' => [
                        'enabled'         => true,
                        'allow_redirects' => 'never'
                    ]
                ]);
                $paymentResult = $stripe->paymentIntents->confirm(
                    $paymentIntent->id,
                    [
                        'payment_method' => $paymentMethod->payment_method,
                    ]
                );
                if ($paymentResult->status !== 'succeeded') {
                    \Illuminate\Support\Facades\Log::error('Payment Intent details: ', [$paymentResult]);
                    $invoice->subscription()->delete();
                    $invoice->delete();

                    return [
                        'type'    => 'error',
                        'message' => 'Payment failed: ' . $paymentResult->last_payment_error->message,
                        'success' => false
                    ];
                }
                $this->updateSubscriptionAndInvoice($invoice->subscription);
                $invoice->delete();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
        }
    }

    private function updateSubscriptionAndInvoice(Subscription $subscription)
    {
        $nextAttempt = null;
        switch ($subscription->plan->type) {
            case('weekly'):
                $nextAttempt = \Carbon\Carbon::now()->addWeek();
                break;
            case('yearly'):
                $nextAttempt = Carbon::now()->addYear();
                break;
            default:
        }
        $subscription->update([
            'start_date' => Carbon::now(),
            'end_date'   => $nextAttempt,
        ]);
        $subscription->invoice()->create([
            'user_id'      => $subscription->user_id,
            'next_attempt' => $nextAttempt,
            'amount'       => $subscription->plan->price,
        ]);
    }
}
