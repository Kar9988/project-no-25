<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    public function store($data)
    {
        return Plan::create($data);
    }

    public function index()
    {
        return Plan::all();
    }

    public function update($data, $id)
    {
        return Plan::query()->where('id', $id)->update($data);
    }

    public function getById($id)
    {
        return Plan::query()->findOrFail($id);
    }

    public function delete($id)
    {
        return Plan::query()->where('id', $id)->delete();
    }
}
