<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class UpdateEpisodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-episodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $videos = Video::with('episodes')->get();
        foreach ($videos as $video) {
            foreach ($video->episodes as $key => $episode) {
                $episode->update([
                    'position' => $key+1
                ]);
            }
        }
    }
}
