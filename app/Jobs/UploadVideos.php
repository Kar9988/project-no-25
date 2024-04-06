<?php

namespace App\Jobs;

use App\Models\Episode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            if (Storage::disk('public')->exists("uploads/$this->source")) {
                $source = Storage::disk('spaces')->putFile($this->path, new File(storage_path("app/public/uploads/$this->source")));
                Episode::query()->where('id', $this->episodeId)->update([
                    'source' => $source
                ]);
                Storage::disk('public')->delete("uploads/$this->source");
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
