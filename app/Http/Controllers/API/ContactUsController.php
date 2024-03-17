<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\EmailRequest;
use App\Mail\SendEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    /**
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function sendMail(EmailRequest $request): JsonResponse|string
    {
        $user = [
            'email' => $request['email'],
            'message' => $request['message'],
        ];

        $files = $request->file('files');

        if ($files != null) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $attachmentData[] = [
                        'file' => $file->getPathname()
                    ];
                } else {
                    return response()->json([
                        'message' => 'Failed to send email to address',
                        'success' => false,
                        'type' => 'error'
                    ]);
                }
            }
            Mail::to($user['email'])->send(new SendEmail($user, $attachmentData));
            return response()->json([
                'message' => 'Thank you for contacting us. Please wait for an email from our team.',
                'success' => true,
                'type' => 'success'
            ]);
        }
        Mail::to($user['email'])->queue(new SendEmail($user));
        return response()->json([
            'message' => 'Thank you for contacting us. Please wait for an email from our team.',
            'success' => true,
            'type' => 'success'
        ]);
    }
}
