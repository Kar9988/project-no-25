<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\HistoryResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\ViewResource;
use App\Policies\UserPolicy;
use App\Services\EpisodeService;
use App\Services\VideoStream;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EpisodeController extends Controller
{

    private EpisodeService $episodeService;

    /**
     * @param EpisodeService $episodeService
     */

    public function __construct(EpisodeService $episodeService)
    {
        $this->episodeService = $episodeService;
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): JsonResponse
    {
        $history = $this->episodeService->index(request()->get('page', 1));
        return response()->json([
            'success' => true,
            'message' => 'This is user video history',
            'history' => ViewResource::collection($history),
            'type'    => 'success'

        ], 200);
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function randomEpisodes(): JsonResponse
    {
        $episodes = $this->episodeService->randomEpisodes();

        return response()->json([
            'success'  => true,
            'episodes' => LibraryResource::collection($episodes),
            'type'     => 'success'

        ]);
    }


    /**
     * @param $episodeId
     * @return void
     */
    public function getVideoStream($episodeId)
    {

        $episode = $this->episodeService->getById($episodeId);

        $videoPath = public_path("storage/$episode->source");
        $userPolicy = UserPolicy::canViewEpisode(auth()->user(), $episodeId);
        if ($userPolicy) {
            $stream = new VideoStream($videoPath);
            $response = Response::stream(function () use ($stream) {
                $stream->start();
            }, 200, [
                'Content-Type'           => 'video/mp4',
                'Cache-Control'          => 'no-cache, no-store, must-revalidate',
                'Pragma'                 => 'no-cache',
                'Expires'                => '0',
                'Content-Range'          => 1000,
                'Content-Disposition'    => 'inline',
                'X-Content-Type-Options' => 'nosniff',
            ]);
            $response->send();
        } else {
            return response()->json([
                'success' => false,
                'type'    => 'error',
                'message' => 'Please buy episode',
            ], 403);
        }

    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function library(): JsonResponse
    {
        $userId = auth()->id();
        $episodes = $this->episodeService->getLibrary($userId, request()->get('page', 1));

        return response()->json([
            'success' => true,
            'library' => LibraryResource::collection($episodes),
            'type'    => 'success'
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function storeHistory(Request $request,): JsonResponse
    {
        $data = [
            'user_id'    => auth()->id(),
            'episode_id' => $request->episode_id
        ];
        $episodes = $this->episodeService->storeHistory($data);
        if ($episodes) {
            return response()->json([
                'success' => $episodes,
                'type'    => 'success',
                'message' => 'History saved successful'
            ]);
        }
        return response()->json([
            'success' => $episodes,
            'type'    => 'error',
            'message' => 'Failed to save history',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyHistory(Request $request): JsonResponse
    {
        $destroy = $this->episodeService->destroyHistory($request->ids);
        if ($destroy) {
            return response()->json([
                'success' => true,
                'type'    => 'success',
                'message' => 'History deleted successful',
            ]);
        }
        return response()->json([
            'success' => false,
            'type'    => 'error',
            'message' => 'Failed to delete history',
        ]);
    }


    public function showAllHistory()
    {
        $userHistory = $this->episodeService->showAllHistory(\request()->get('page', 1));
        if ($userHistory) {
            return response()->json([
                'success' => true,
                'type'    => 'success',
                'data'    => HistoryResource::collection($userHistory),
            ]);
        }
        return response()->json([
            'success' => false,
            'type'    => 'error',
            'message' => 'You dont have any history yet',
        ]);

    }

}
