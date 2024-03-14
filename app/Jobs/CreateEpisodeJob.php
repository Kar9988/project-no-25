<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\EpisodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class CreateEpisodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Video $video;
    private array $data;

    /**
     * Create a new job instance.
     * @param Video $video
     * @param array $data
     */
    public function __construct(Video $video, array $data)
    {

        $this->video = $video;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $episodeService = App::make(EpisodeService::class);
            $episodeService->store($this->video, $this->data);
        } catch (\Exception $exception) {
            dd($exception);
        }
    }
}
