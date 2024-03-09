<?php

namespace App\Console\Commands;

use App\Services\VideoService;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class CreateEpisodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-episodes';
    public $timeout = 0;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private VideoService $videoService;

    /**
     * @param VideoService $videoService
     */
    public function __construct(VideoService $videoService)
    {
        parent::__construct();
        $this->videoService = $videoService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);
        DB::connection()->disableQueryLog();
//        $categoryData = [
//            [
//                'name'   => 'New Arrival',
//                'active' => true
//            ],
//            [
//                'name'   => 'Top Rated',
//                'active' => true
//            ],
//            [
//                'name'   => 'Trading This week',
//                'active' => true
//            ],
//            [
//                'name'   => 'Test',
//                'active' => true
//            ]
//        ];
        $faker = Faker::create();
//        for ($i = 0; $i < 5; $i++) {
            $videoData = [
                'title'       => $faker->name,
                'description' => $faker->text,
                'category_id' => rand(1, 4),
                'cover_img'   => glob(base_path('test_thumbs/*'))[rand(0, 2)],
                'episodes'    => []
            ];
            for ($x = 0; $x < 20; $x++) {
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
            $this->info($result->id);
//        }
    }
}
