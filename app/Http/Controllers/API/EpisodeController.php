<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\ViewResource;
use App\Policies\UserPolicy;
use App\Services\EpisodeService;
use App\Services\VideoStream;
use Illuminate\Http\JsonResponse;
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
        $videoHistory = $this->episodeService->index(request()->get('page', 1));
        return response()->json([
            'success' => true,
            'message' => 'This is user video history',
            'video history' => ViewResource::collection($videoHistory)
        ], 200);
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
                'Content-Type' => 'video/mp4',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Content-Range' => 1000,
                'Content-Disposition' => 'inline',
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

}
