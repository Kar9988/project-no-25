<?php

namespace App\Services;

use App\Jobs\UploadVideos;
use App\Models\Episode;
use App\Models\User;
use App\Models\UserEpisodesHistory;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * @param int $page
     * @param int $take
     * @return mixed
     */
    public function index(int $page = 1, int $take = 10): mixed
    {
        return Episode::join('views', 'views.episode_id', '=', 'episodes.id')
            ->where('views.user_id', auth()->user()->id)
            ->select('episodes.title', 'episodes.thumb', 'episodes.id', 'episodes.created_at')
            ->orderByDesc('episodes.created_at')
            ->groupBy('episodes.id')
            ->skip($page * $take - $take)
            ->take($take)
            ->get();

    }

    /**
     * @param int $userId
     * @param int $page
     * @return Collection|array
     */
    public function getLibrary(int $userId, int $page = 1): Collection
    {
        return Episode::query()
            ->select('episodes.*')
            ->join('payments', function ($join) use ($userId) {
                $join->on('payments.paymentable_id', 'episodes.id')
                    ->where('payments.paymentable_type', Episode::class)
                    ->where('user_id', $userId);
            })
            ->withCount(['episodes', 'likes'])
            ->skip($page * 10 - 10)
            ->take(10)
            ->orderByDesc('payments.id')
            ->get();
    }

    /**
     * @return Episode|Builder[]|Collection
     */
    public function randomEpisodes(): Collection|Episode|array
    {
        return Episode::query()
            ->withCount(['likes', 'views', 'episodes'])
            ->orderBy(DB::raw('RAND()'))
            ->take(6)
            ->get();
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
                $episode = Episode::where('id', $datum['id'])->first();
                $episodeUpdateData = [
                    'title' => $datum['title'],
                    'duration' => $datum['duration'] ?? 0,
                    'position' => $datum['position'] ?? 1,
                    'price' => $datum['price'] ?? 0,
                ];

                if ($datum['thumb'] ?? false) {
                    $fileName = Storage::disk('public')->putFile('tmp', $datum['thumb']);
                    $coverPath = $this->fileManagerService->storeThumb("videos/$videoId/episodes/cover", $fileName);
                    $episodeUpdateData['thumb'] = $coverPath;
                    $filePaths[] = $coverPath;
                }
                if ($datum['source'] ?? false) {
                    UploadVideos::dispatch($datum['source'], "videos/$videoId/episodes", $datum['id'], $episode->source);
                }
                $episode->update($episodeUpdateData);
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
     * @param int $video_id
     * @return mixed
     */
    public function deleteEpisodes(int $video_id):mixed
    {
        $episodeSources = Episode::query()->where('video_id', $video_id)->pluck('source')->toArray();
        $episodeThumb = Episode::query()->where('video_id', $video_id)->pluck('thumb')->toArray();
        $episodeIds = Episode::query()->where('video_id', $video_id)->pluck('id')->toArray();
        $this->fileManagerService->deleteFiles($episodeThumb);
        $this->fileManagerService->deleteFiles($episodeSources);
        UserEpisodesHistory::query()
            ->whereIn('episode_id', $episodeIds)
            ->delete();

       return Episode::query()->where('video_id', $video_id)->delete();
    }

    /**
     * @param Video $video
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function store(Video $video, array $data): bool
    {
        try {
            DB::beginTransaction();
            $episodesData = [];
            $filePaths = [];
            $episodeCreateData = [
                'title' => $data['title'],
                'duration' => $data['duration'] ?? 0,
                'price' => $data['price'] ?? 0
            ];

            if ($data['thumb'] ?? null) {
                $coverPath = $this->fileManagerService->storeThumb("videos/$video->id/episodes/cover", $data['thumb']);
                $episodeCreateData['thumb'] = $coverPath;
                $filePaths[] = $coverPath;
            }
            if ($data['source'] ?? null) {
                $episodeCreateData['source'] = $data['source'];
                $filePaths[] = 'public/uploads/'.$data['source'];
                $episode = $video->episodes()->create($episodeCreateData);
                UploadVideos::dispatch($data['source'], "videos/$video->id/episodes", $episode->id);
                $episode->delete();
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
                    'title' => $datum['title'],
                    'duration' => $datum['duration'] ?? 0,
                    'position' => $datum['position'] ?? 1,
                    'price' => $datum['price'] ?? 0
                ];

                if ($datum['thumb'] ?? null) {
                    $fileName = Storage::disk('public')->putFile('tmp', $datum['thumb']);
                    $coverPath = $this->fileManagerService->storeThumb("videos/$video->id/episodes/cover", $fileName);
                    $episodeCreateData['thumb'] = $coverPath;
                    $filePaths[] = $coverPath;
                }
                if ($datum['source'] ?? null) {
                    $episode = $video->episodes()->create($episodeCreateData);
                    UploadVideos::dispatch($datum['source'], "videos/$video->id/episodes", $episode->id);
                    $episode->delete();
                }
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
     * @param array $data
     * @return bool
     */
    public function storeHistory(array $data): bool
    {
        $history = UserEpisodesHistory::where('user_id', $data['user_id'])
            ->where('episode_id', $data['episode_id'])->first();
        if ($history) {
            $history->update(['updated_at' => now()]);
            return true;

        } else {

            return UserEpisodesHistory::create($data) ? true : false;
        }
    }


    /**
     * @param array|int $ids
     * @return mixed
     */
    public function destroyHistory(array | int $ids): mixed
    {
        return UserEpisodesHistory::query()
            ->when(is_array($ids), function ($query) use ($ids) {
                return $query->whereIn('id', $ids);
            })
            ->when(is_int($ids), function ($query) use ($ids) {
                return $query->where('id', $ids);
            })
            ->delete();

    }

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function showAllHistory(int $page = 1, int $take = 10): mixed
    {
        return UserEpisodesHistory::where('user_id', auth()->id())
            ->with('episode')
            ->orderByDesc('updated_at')
            ->skip($page*$take-$take)
            ->take($take)
            ->get();

    }
}
