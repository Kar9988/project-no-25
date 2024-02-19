<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    public function store($data)
    {
        return Plan::create($data);
    }
}
