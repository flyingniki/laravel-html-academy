<?php

namespace App\Http\Requests;

use App\Models\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFilmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'poster_image' => 'nullable|string|max:255',
            'preview_image' => 'nullable|string|max:255',
            'background_image' => 'nullable|string|max:255',
            'background_color' => ['nullable', 'regex:/^#\d{6}$/'],
            'video_link' => 'nullable|string|max:255',
            'preview_video_link' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'director' => 'nullable|string|max:255',
            'starring' => 'nullable|array',
            'run_time' => 'nullable|numeric',
            'released' => 'nullable|numeric',
            'imdb_id' => ['required', 'regex:/^tt\d+$/', Rule::unique('films', 'imdb_id')->ignoreModel($this->film)],
            'status' => [Rule::in([Film::STATUS_PENDING, Film::STATUS_ON_MODERATION, Film::STATUS_READY])],
        ];
    }

    public function messages()
    {
        return [
            'background_color.regex' => 'Цвет должен быть задан в формате #NNNNNN',
            'imdb_id.regex' => 'imdb id должен быть передан в формате ttNNNN',
            'imdb_id.unique' => 'Такой фильм уже есть'
        ];
    }
}
