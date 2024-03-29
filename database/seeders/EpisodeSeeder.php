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
        $faker = Faker::create();
        for ($i = 0; $i < 5; $i++) {
            $videoData = [
                'title'       => $faker->name,
                'description' => $faker->text,
                'category_id' => rand(1, 4),
                'cover_img'   => glob(base_path('test_thumbs/*'))[rand(0, 2)],
                'episodes'    => []
            ];
            for ($x = 0; $x < 3; $x++) {
                $videoData['episodes'][] = [
                    'title'    => $faker->name,
                    'duration' => null,
                    'position' => $x + 1,
                    'price'    => 120,
                    'thumb'    => glob(base_path('test_thumbs/*'))[rand(0, 2)],
                    'source'   => new File(glob(base_path('test_videos/*'))[rand(0, 4)]),
                ];
            }
            $result = $this->videoService->createVideo($videoData);
            dump($result->id);
        }
    }
}
