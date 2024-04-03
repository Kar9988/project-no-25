<?php

namespace App\Console\Commands;


use App\Models\Invoice;
use App\Models\Plan;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use App\Services\UserBalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function __construct(InvoiceService $invoiceService, UserBalanceService $userBalanceService, SubscriptionService $subService,PaymentService $paymentService)
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
            $data = [];
            foreach ($invoices as $invoice) {
                $data = [
                    'amount' => $invoice->subscription->plan->price,
                ];
                $paymentable = [
                    'from_id' => 0,
                    'user_id' => $invoice->subscription->user->id,
                    'amount' => $invoice->subscription->plan->price,
                    'paymentable_type' => Plan::class,
                    'Paymentable_id' => $invoice->subscription->plan->id
                ];
                $this->paymentService->create($paymentable);
                $updateBalance = $this->userBalanceService->addBalance($data, $invoice->subscription->user_id, false);
                if ($updateBalance['success']) {
                    $type = $invoice->subscription->plan['type'];
                    $updateSub = [
                        'start_date' => Carbon::today()->toDateString(),
                        'end_date' => Carbon::today()->toDateString(),
                    ];

                    switch ($type) {
                        case 'Weekly':
                            $updateSub['end_date'] = Carbon::today()->addWeeks()->toDateString();
                            break;
                        case 'Yearly':
                            $updateSub['end_date'] = Carbon::today()->addYear()->toDateString();
                            break;
                        case 'One Time':
                            break;
                    }
                    $updateSubscription = $this->subService->update($invoice->subscription['id'], $updateSub);
                    if ($updateSubscription) {
                        $invoiceData = [
                            'user_id' => $invoice->subscription->user['id'],
                            'subscription_id' => $invoice->subscription['id'],
                            'next_attempt' => $updateSub['end_date'],
                            'amount' => $invoice->subscription->plan['price'],
                        ];
                        $this->invoiceService->addInvoice($invoiceData);
                        $this->invoiceService->delete($invoice->id);
                    }
                }
            }
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return [
                'success' => false,
                'type'    => 'error',
                'message' => $exception->getMessage()
            ];
        }
    }
}
