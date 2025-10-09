<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'max:255', 'unique:users,nickname,' . $this->user()->id],
            'real_name' => ['required', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'bio' => ['required', 'string', 'max:1000'],
            'country_id' => ['required', 'exists:countries,id'],
        ];
    }
}
