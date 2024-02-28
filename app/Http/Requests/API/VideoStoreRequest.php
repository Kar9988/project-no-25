<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class VideoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'video.name'            => 'required|string',
            'video.cover_img'       => 'required|image',
            'video.is_new_arrival'  => 'boolean',
            'video.is_top_rated'    => 'boolean',
            'episodes.*.name'       => 'sometimes|required|string',
            'episodes.*.duration'   => 'sometimes',
            'episodes.*.cover_img'  => 'sometimes|image',
            'episodes.*.video_path' => 'sometimes|required|mimes:mp4,mov,ogg,qt'
        ];
    }

    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'Invalid data send',
            'details' => $errors->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
