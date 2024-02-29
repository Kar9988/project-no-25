<?php

namespace App\Services;

use App\Models\View;

class ViewService
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($data): View
    {
        return View::create($data);
    }
}
