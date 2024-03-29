<?php

namespace App\Console\Commands;

use App\Services\VideoService;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $faker = Faker::create();
        for ($i = 0; $i < 5; $i++) {
            $videoData = [
                'title'       => $faker->name,
                'description' => $faker->text,
                'category_id' => rand(1, 4),
                'cover_img'   => glob(base_path('test_thumbs/*'))[rand(0, 6)],
                'episodes'    => []
            ];
            for ($x = 0; $x < 10; $x++) {
                $videoData['episodes'][] = [
                    'title'    => $faker->name,
                    'duration' => null,
                    'position' => $x + 1,
                    'price'    => rand(20, 500),
                    'thumb'    => glob(base_path('test_thumbs/*'))[rand(0, 2)],
                    'source'   => new File(glob(base_path('test_videos/*'))[rand(0, 1)])
                ];
            }
            $result = $this->videoService->createVideo($videoData);
            $this->info($result->id);
        }
    }
}
