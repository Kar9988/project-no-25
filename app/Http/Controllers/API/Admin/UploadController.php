<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $chunkIndex = $request->input('chunkIndex');
        $totalChunks = $request->input('totalChunks');
        $extension = $request->input('extension');
        $fileName = $request->input('filename', null);
        $file = $request->file('file');
        if (!$file->isValid() || !is_numeric($chunkIndex) || !is_numeric($totalChunks)) {
            return response()->json(['error' => 'Invalid file or chunk data'], 422);
        }
        DeleteVideo::dispatch($fileName)->delay(now()->addHours(12));
        $fileName = $fileName.".".$extension;
        $temporaryFilePath = $file->path();
        Storage::disk('public')->put('chunks/' . $fileName . '-' . $chunkIndex, file_get_contents($temporaryFilePath));
        if ($chunkIndex == $totalChunks - 1) {
            $fileContents = '';
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = 'chunks/' . $fileName . '-' . $i;
                if (Storage::disk('public')->exists($chunkPath)) {
                    $fileContents .= Storage::disk('public')->get($chunkPath);
                    Storage::disk('public')->delete($chunkPath);
                } else {
                    return response()->json(['error' => 'Missing chunk ' . $i], 400);
                }
            }
            $filePath = storage_path('app/public/uploads/' . $fileName);
            file_put_contents($filePath, $fileContents);

            return response()->json(['message' => 'File uploaded successfully!', 'fileName' => $fileName]);
        } else {
            return response()->json(['message' => $fileName.'Chunk uploaded successfully']);
        }
    }
}
