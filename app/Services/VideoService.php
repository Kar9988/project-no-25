<?php

namespace App\Services;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoService
{

    private FileManagerService $fileManagerService;
    private EpisodeService $episodeService;

    /**
     * @param FileManagerService $fileManagerService
     * @param EpisodeService $episodeService
     */
    public function __construct(FileManagerService $fileManagerService, EpisodeService $episodeService)
    {

        $this->fileManagerService = $fileManagerService;
        $this->episodeService = $episodeService;
    }

    /**
     * @param array $data
     * @return Video|false
     */
    public function createVideo(array $data): Video|false
    {
        try {
            DB::beginTransaction();
            $videoData = Arr::except($data['video'], 'cover_img');
            $video = Video::create($videoData);
            if (isset($data['video']['cover_img'])) {
                $coverPath = $this->fileManagerService->storeCover("videos/$video->id/cover", $data['video']['cover_img']);
                $video->update(['cover_img' => $coverPath]);
            }
            if (isset($data['episodes'])) {
                $this->episodeService->createEpisodes($video, $data['episodes']);
            }
            DB::commit();

            return $video;
        } catch (\Exception $exception) {
            Log::error($exception);
            DB::rollBack();
            if (isset($coverPath)) {
                $this->fileManagerService->deleteFiles([$coverPath]);
            }

            return false;
        }
    }

    /**
     * @param $page
     * @return AnonymousResourceCollection
     */
    public function paginateVideos($page): AnonymousResourceCollection
    {
        $videos = Video::paginate($page);

        return VideoResource::collection($videos);
    }

    /**
     * @param $id
     * @return Video
     */
    public function getById($id): Video
    {
        return Video::where('id', $id)->first();
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy($id): bool
    {
        return Video::where('id', $id)->delete();
    }
}
