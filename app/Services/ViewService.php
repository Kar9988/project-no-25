<?php

namespace App\Services;

use App\Models\View;
use Illuminate\Support\Facades\DB;

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

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data): mixed
    {
      return  DB::table('views')->insert($data);
    }
}
