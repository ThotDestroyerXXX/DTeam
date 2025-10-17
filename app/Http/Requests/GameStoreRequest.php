<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GameStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === Role::PUBLISHER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'brief_description' => 'required|string|max:500',
            'full_description' => 'required|string',
            'release_date' => 'required|date',
            'price' => 'required|decimal:2|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'age_rating_id' => 'required|exists:age_ratings,id',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
            'images' => 'required|array|min:4|max:12',
            'images.*' => 'image|max:5012', // Max 5MB per image
        ];
    }
}
