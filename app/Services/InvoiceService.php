<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Subscription;

class InvoiceService
{

  public function __construct(public Invoice $model)
  {
  }

    /**
     * @return mixed
     */
    public function index(): mixed
    {

       return $this->model->query()->get();

    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function deleteByIds(array $ids): mixed
    {
       return $this->model->query()->wherein('id', $ids)->delete();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function getByDate($date):mixed
    {
        return $this->model::with(['user', 'subscription', 'subscription.plan'])
            ->whereHas('subscription.plan')
            ->whereNull('canceled_at')
            ->where('next_attempt', $date)->get();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function addInvoice(array $data): mixed
    {
        return $this->model->query()->create($data);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id): mixed
    {
        return $this->model->query()->where('id',$id)->delete();
    }
}
