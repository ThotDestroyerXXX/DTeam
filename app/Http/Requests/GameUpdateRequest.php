<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GameUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure the user is a publisher
        return Auth::check() && Auth::user()->publisher;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'brief_description' => 'required|string',
            'full_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'release_date' => 'required|date',
            'discount' => 'required|integer|min:0|max:100',
            'age_rating_id' => 'required|exists:age_ratings,id',
            'genres' => 'sometimes|array|max:3',
            'genres.*' => 'exists:genres,id',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'sometimes|array',
            'delete_images.*' => 'exists:game_images,id',
        ];
    }
}
