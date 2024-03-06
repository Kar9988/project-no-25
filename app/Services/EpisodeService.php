<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EpisodeService
{

    private FileManagerService $fileManagerService;
    private ViewService $viewService;

    /**
     * @param FileManagerService $fileManagerService
     */
    public function __construct(FileManagerService $fileManagerService, ViewService $viewService)
    {

        $this->fileManagerService = $fileManagerService;
    }

    /**
     * @param int $id
     * @return Episode
     */
    public function getById(int $id): Episode
    {
        return Episode::findOrFail($id);
    }

    /**
     * @param int $videoId
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function updateEpisodes(int $videoId, array $data): bool
    {
        try {
            DB::beginTransaction();
            $filePaths = [];
            foreach ($data as $datum) {
                $episodeUpdateData = [
                    'title'    => $datum['title'],
                    'duration' => $datum['duration'] ?? 0,
                    'position' => $datum['position'] ?? 1,
                    'price' => $datum['price'] ?? 1,
                ];

                if ($datum['thumb'] ?? false) {
                    $coverPath = $this->fileManagerService->storeCover("videos/$videoId/episodes/cover", $datum['thumb']);
                    $episodeUpdateData['thumb'] = $coverPath;
                    $filePaths[] = $coverPath;
                }
                if ($datum['source'] ?? false) {
                    $videoPath = $this->fileManagerService->storeVideo("videos/$videoId/episodes", $datum['source']);
                    $episodeUpdateData['source'] = $videoPath;
                    $filePaths[] = $videoPath;
                }
                Episode::where('id', $datum['id'])->update($episodeUpdateData);
            }
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
                    'title'    => $datum['title'],
                    'duration' => $datum['duration'] ?? 0,
                    'price' => $datum['price'] ?? 0
                ];

                if ($datum['thumb'] ?? null) {
                    $coverPath = $this->fileManagerService->storeCover("videos/$video->id/episodes/cover", $datum['thumb']);
                    $episodeCreateData['thumb'] = $coverPath;
                    $filePaths[] = $coverPath;
                }
                if ($datum['source'] ?? null) {
                    $videoPath = $this->fileManagerService->storeVideo("videos/$video->id/episodes", $datum['source']);
                    $episodeCreateData['source'] = $videoPath;
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
