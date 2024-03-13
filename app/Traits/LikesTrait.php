<?php

namespace App\Traits;

use App\Http\Resources\LikeResource;
use App\Models\Episode;
use App\Models\Like;
use App\Models\Video;
use App\Services\LIkeService;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\JsonResponse;

trait LikesTrait
{
    /**
     * @param LIkeService $service
     */

    public function __construct(protected LIkeService $service)
    {
    }

    /**
     * @param $datum
     * @return JsonResponse
     */
    public function like($datum): JsonResponse
    {
        if (isset($datum['likes_count'])) {
            $count = (int)$datum['likes_count'];
        }
        if (isset($count) && $count > 0) {
            $data = [];
            for ($i = 0; $i < $count; $i++) {
                if (isset($datum['video_id'])) {
                    $data[] = [
                        'user_id' => auth()->id(),
                        'likeable_type' => Video::class,
                        'likeable_id' => $datum['video_id'],
                    ];
                }
                if (isset($datum['episode_id'])) {
                    $data[] = [
                        'user_id' => auth()->id(),
                        'likeable_type' => Episode::class,
                        'likeable_id' => $datum['episode_id'],
                    ];
                }
            }
            $this->service->insert($data);
            return response()->json([
                'success' => true,
                'message' => 'Likes created successfully',
                'type'   =>  'success'
            ], 201);
        } else {
            $create = $this->service->store($datum->all());
            return response()->json([
                'success' => true,
                'message' => 'Likes created successfully',
                'likes' => new LikeResource($create),
                'type' => 'success',
            ], 201);
        } else {
            $this->service->store($datum);
        }
    }

    /**
     * @param $id
     * @param $data
     * @return JsonResponse
     */
    public function destroyLike($id, $data): JsonResponse
    {
        if (isset($data['episode_id'])) {
            if (isset($data['count']) && $data['count'] != null) {
                $deletedRows = Like::query()->where('likeable_id', $id)
                    ->where('user_id', auth()->id())
                    ->where('likeable_type', Episode::class)
                    ->take($data['count'])
                    ->delete();
                return response()->json([
                    'message' => $deletedRows . ' records deleted successfully',
                    'success' => true,
                    'type'    => 'success',
                    ]);
            }
            return response()->json([
                'message' => 'There is no line to delete',
                'type'    => 'error',
                'success' => false,
                ]);
        }
        if (isset($data['video_id'])) {
            if (isset($data['count']) && $data['count'] != null) {
                $deletedRows = Like::query()->where('likeable_id', $id)
                    ->where('user_id', auth()->id())
                    ->where('likeable_type', Video::class)
                    ->take($data['count'])
                    ->delete();
                return response()->json([
                    'message' => $deletedRows . ' records deleted successfully',
                    'success' => true,
                    'type'    => 'success'
                ]);
            }
            return response()->json([
                'message' => 'There is no line to delete',
                'type'    => 'error',
                'success' => false,
                ]);

            $this->deleteLike($id, $data['count'] ?? null, Episode::class);
        }
        if (isset($data['video_id'])) {
            $this->deleteLike($id, $data['count'] ?? null, Video::class);
        }
    }

    /**
     * @param int $likeAbleId
     * @param ?int $count
     * @param string $type
     * @return mixed
     */
    private function deleteLike(int $likeAbleId, ?int $count, string $type): mixed
    {
        $this->service->deleteLike($likeAbleId, $count, $type);
    }
}
