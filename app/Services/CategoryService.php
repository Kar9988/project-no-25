<?php

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryService
{

    /**
     * @param $data
     * @return mixed
     */
    public function store($data): Category
    {
        return Category::create($data);
    }

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return Category::all();
    }

    /**
     * @param int $id
     * @return mixed
     */

    public function delete(int $id): mixed
    {
        return Category::query()->where('id', $id)->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return Category::where('id', $id)->first();
    }

    /**
     * @param array $data
     * @param int $id
     * @return int
     */
    public function update(array $data, int $id): int
    {
        return Category::query()->where('id', $id)->update($data);
    }

}
