<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Services\VideoService;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class EpisodeSeeder extends Seeder
{

    private VideoService $videoService;

    /**
     * @param VideoService $videoService
     */
    public function __construct(VideoService $videoService)
    {

        $this->videoService = $videoService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    }
}
