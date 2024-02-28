<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EpisodeService
{

    private FileManagerService $fileManagerService;

    /**
     * @param FileManagerService $fileManagerService
     */
    public function __construct(FileManagerService $fileManagerService)
    {

        $this->fileManagerService = $fileManagerService;
    }

    /**
     * @throws \Exception
     */
    public function createEpisodes(Video $video, array $data): bool
    {
        try {
            DB::beginTransaction();
            $episodesData = [];
            $filePaths = [];
            foreach ($data as $datum) {
                $episodeCreateData = [
                    'name' => $datum['name'],
                    'duration' => $datum['duration'],
                    'is_new_arrival' => $datum['is_new_arrival'],
                    'is_top_rated' => $datum['is_top_rated']
                ];
                if ($datum['cover_img']) {
                    $coverPath = $this->fileManagerService->storeCover("videos/$video->id/episodes/cover", $datum['cover_img']);
                    $episodeCreateData['cover_img'] = $coverPath;
                    $filePaths[] = $coverPath;
                }
                if ($datum['video_path']) {
                    $videoPath = $this->fileManagerService->storeVideo("videos/$video->id/episodes", $datum['video_path']);
                    $episodeCreateData['video_path'] = $videoPath;
                    $filePaths[] = $videoPath;
                }
                $episodesData[] = $episodeCreateData;
            }
            $video->episodes()->createMany($episodesData);
            DB::commit();

            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
            DB::rollBack();
            if (isset($filePaths)) {
                $this->fileManagerService->deleteFiles($filePaths);
            }

            throw new \Exception($exception);
        }
    }
}
