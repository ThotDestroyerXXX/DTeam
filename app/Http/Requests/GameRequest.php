<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is a publisher
        return $this->user() && $this->user()->role->value === 'publisher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'brief_description' => 'required|string|max:500',
            'full_description' => 'required|string',
            'release_date' => 'required|date',
            'age_rating_id' => 'required|exists:age_ratings,id',
            'genres' => 'required|array|min:1|max:3',
            'genres.*' => 'exists:genres,id',
        ];

        // For store (create) requests
        if ($this->isMethod('post')) {
            $rules['images'] = 'required|array|min:1';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg|max:4096';
        }

        // For update requests
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['images'] = 'nullable|array';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg|max:4096';
            $rules['delete_images'] = 'nullable|array';
            $rules['delete_images.*'] = 'exists:game_images,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a title for your game.',
            'price.required' => 'Please enter a price for your game.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'discount.required' => 'Please enter a discount percentage.',
            'discount.integer' => 'Discount must be a whole number.',
            'discount.min' => 'Discount cannot be negative.',
            'discount.max' => 'Discount cannot be more than 100%.',
            'brief_description.required' => 'Please enter a brief description for your game.',
            'brief_description.max' => 'Brief description cannot exceed 500 characters.',
            'full_description.required' => 'Please enter a full description for your game.',
            'release_date.required' => 'Please enter a release date for your game.',
            'release_date.date' => 'Release date must be a valid date.',
            'age_rating_id.required' => 'Please select an age rating for your game.',
            'genres.required' => 'Please select at least one genre for your game.',
            'genres.min' => 'Please select at least one genre for your game.',
            'genres.max' => 'You can only select up to 3 genres for your game.',
            'images.required' => 'Please upload at least one image for your game.',
            'images.*.image' => 'Uploaded file must be an image.',
            'images.*.mimes' => 'Only JPEG, PNG, and JPG images are allowed.',
            'images.*.max' => 'Image size must not exceed 4MB.',
        ];
    }
}
