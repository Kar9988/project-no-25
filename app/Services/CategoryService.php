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

    public function paginateVideos($page): array
    {
        $categories = Category::paginate();

        return [
            'data' => CategoryResource::collection($categories),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'next_page_url' => $categories->nextPageUrl(),
            'prev_page_url' => $categories->previousPageUrl(),
        ];
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
