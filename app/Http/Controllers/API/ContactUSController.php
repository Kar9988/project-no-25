<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\EmailRequest;
use App\Mail\sendMail;
use App\Services\FileManagerService;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUSController extends Controller
{
    public function __construct(protected FileManagerService $fileService)
    {

    }


    /**
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function contactUSPost(EmailRequest $request): JsonResponse
    {
        $baseUrl = '';
        if (isset($request->file)) {
            $imagePath = $this->fileService->storeCover('uploads/' . $request->file('file')->getFilename() . now(), $request->file('file'));
            $baseUrl = config('app.url') . '/' . $imagePath;
        }
        $user = [
            'image' => $baseUrl,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'message' => $request->message,
        ];

        $sendMail = Mail::to($user['email'])->send(new sendMail($user));
        if ($sendMail) {
            return response()->json([
                'success' => true,
                'message' => 'email send successfully',
                'type' => 'success',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something was wrong',
            'type' => 'error',
        ]);
    }
}
