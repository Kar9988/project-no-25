<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\Video;
use FFMpeg\Filters\Video\VideoFilters;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class UploadVideos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $source;
    private string $path;
    private int $episodeId;
    public $timeout = 1800; // Timeout in seconds (30 minutes)

    /**
     * Create a new job instance.
     */
    public function __construct(string $source, string $path, int $episodeId)
    {
        $this->source = $source;
        $this->path = $path;
        $this->episodeId = $episodeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            set_time_limit(0);
            if (Storage::disk('public')->exists("uploads/$this->source")) {
                $uniqueName = uniqid().'.mp4';
                $uniqueFileName = storage_path("app/public/uploads/$uniqueName");
                $fmpegFile = FFmpeg::fromDisk('local')
                    ->open("public/uploads/$this->source")
                    ->export()
                    ->addFilter(function (VideoFilters $filters) {
                        $filters->resize(new \FFMpeg\Coordinate\Dimension(1280, 720));
                    })
                    ->resize(1280, 720)
                    ->inFormat(new \FFMpeg\Format\Video\X264('aac', 'libx264'))
                    ->save("public/uploads/$uniqueName");
                $source = Storage::disk('spaces')->putFile($this->path, new File($uniqueFileName));
                DB::table('episodes')
                    ->where('id', $this->episodeId)
                    ->update([
                        'source'     => $source,
                        'deleted_at' => null
                    ]);
                $video = DB::table('videos')
                    ->select('videos.*')
                    ->join('episodes', 'episodes.video_id', '=', 'videos.id')
                    ->where('episodes.id', $this->episodeId)
                    ->whereNotNull('videos.deleted_at')
                    ->first();
                if ($video) {
                    Video::query()->where('id', $video->id)->restore();
                }
                Storage::disk('public')->delete("uploads/$uniqueName");
                Storage::disk('public')->delete("uploads/$this->source");
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
