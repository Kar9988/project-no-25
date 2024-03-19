<?php

namespace App\Services;

use App\Http\Resources\DiscoverResource;
use App\Http\Resources\VideoResource;
use App\Jobs\CreateEpisodeJob;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            $videoData = Arr::except($data, ['episodes', 'cover_img']);

            $video = Video::create($videoData);
            if (isset($data['cover_img'])) {
                $coverPath = $this->fileManagerService->storeCover("videos/$video->id/cover", $data['cover_img']);
                $video->update(['cover_img' => $coverPath]);
            }
            if (isset($data['episodes'])) {
                foreach ($data['episodes'] as $episode) {
                    $episode['thumb'] = Storage::disk('public')->putFile('tmp', $episode['thumb']);
                    $episode['source'] = Storage::disk('public')->putFile('tmp', $episode['source']);
                    CreateEpisodeJob::dispatch($video, $episode);
                }
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

    public function updateVideo(int $id, array $data)
    {
        try {
            DB::beginTransaction();
            $videoData = Arr::except($data, ['episodes', 'cover_img', 'likes_count','category']);

            if (isset($data['cover_img'])) {
                $coverPath = $this->fileManagerService->storeCover("videos/$id/cover", $data['cover_img']);
                $videoData['cover_img'] = $coverPath;
            }
            $videoUpdated = Video::where('id', $id)->update($videoData);
            if (isset($data['episodes'])) {
                $this->episodeService->updateEpisodes($id, array_filter($data['episodes'], function ($item) {
                    return isset($item['id']);
                }));
                $newEpisodes = array_filter($data['episodes'], function ($item) {
                    return !isset($item['id']);
                });
                if (count($newEpisodes)) {
                    $video = Video::where('id', $id)->first();
                    $this->episodeService->createEpisodes($video, $newEpisodes);
                }
            }
            DB::commit();

            return $videoUpdated;
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
     * @return array
     */
    public function paginateVideos(): array
    {

        $videos = Video::select('videos.*')
//            ->join('episodes', 'episodes.video_id', 'videos.id')
            ->with(['episodes' => function ($query) {
                $query->withCount(['views', 'likes']);
            }])
            ->distinct()
            ->paginate();

        return [
            'data'          => VideoResource::collection($videos),
            'per_page'      => $videos->perPage(),
            'total'         => $videos->total(),
            'current_page'  => $videos->currentPage(),
            'last_page'     => $videos->lastPage(),
            'next_page_url' => $videos->nextPageUrl(),
            'prev_page_url' => $videos->previousPageUrl(),
        ];
    }

    /**
     * @param array $data
     * @param int $page
     * @param int $take
     * @return ResourceCollection
     */
    public function randomVideos(array $data, int $page = 1, int $take = 10): ResourceCollection
    {
        $id = (int)$data['id'] ?? null;
        $videos = Video::select('videos.*')
            ->join('episodes', 'episodes.video_id', 'videos.id')
            ->with(['episodes' => function ($query) {
                $query->withCount(['views', 'likes']);
            }])
            ->when($id && $page != 1 && $page != 0, function ($query) use ($id, $page) {
                $query->where("videos.id", '!=', $id);
            })
            ->when($id && ($page === 1 || $page === 0), function ($query) use ($id) {
                $query->orderByRaw("CASE WHEN videos.id = $id THEN 1 ELSE 0 END DESC");
            })
            ->groupBy('videos.id')
//            ->orderBy(DB::raw('RAND()'))
            ->skip($page * $take - $take)
            ->take($take)
            ->get();

        return VideoResource::collection($videos);
    }

    /**
     * @return ResourceCollection
     */
    public function discover(): ResourceCollection
    {
        $categories = Category::select('categories.*')
            ->join('videos', function ($join) {
                $join->on('videos.category_id', 'categories.id')
                    ->join('episodes', 'episodes.video_id', 'videos.id');
            })
            ->with(['videos' => function ($query) {
                $query->with(['episodes' => function ($q) {
                    $q->withCount(['views', 'likes']);
                }]);
            }])
            ->groupBy('categories.id')
            ->skip(request()->get('page', 1) * request()->get('take', 10) - request()->get('take', 10))
            ->take(request()->get('take', 10))
            ->get();

        return DiscoverResource::collection($categories);
    }

    /**
     * @param int $categoryId
     * @param int $page
     * @param int $take
     * @return ResourceCollection
     */
    public function getByCategoryId(int $categoryId, int $page = 1, int $take = 10): ResourceCollection
    {
        $videos = Video::select('videos.*')
            ->where('category_id', $categoryId)
            ->join('episodes', 'episodes.video_id', 'videos.id')
            ->with(['episodes' => function ($q) {
                $q->withCount(['views', 'likes']);
            }])
            ->groupBy('videos.id')
            ->skip($page * $take - $take)
            ->take($take)
            ->get();

        return VideoResource::collection($videos);
    }

    /**
     * @param $id
     * @return Video
     */
    public function getById($id): Video
    {
        return Video::select('videos.*')
            ->withCount('likes')
//            ->join('episodes', 'episodes.video_id', 'videos.id')
            ->where('videos.id', $id)
            ->with(['episodes' => function ($query) {
                $query->withCount('views');
                $query->withCount('likes');
            }])
            ->first();
    }

    /**
     * @param int $id
     * @return VideoResource
     */
    public function getVideo(int $id): VideoResource
    {
        $video = $this->getById($id);
        return new VideoResource($video);
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
