<?php
namespace App\Services;
use App\Models\Payment;

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
}
