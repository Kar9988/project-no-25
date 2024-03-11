<?php

namespace App\Services;

use App\Models\Like;
use Illuminate\Support\Facades\DB;

class LIkeService
{
    /**
     * @param $data
     * @return Like
     */
    public function store($data): Like
    {
        return Like::create($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($datum): mixed
    {
        return DB::table('likes')->insert($datum);
    }
}
