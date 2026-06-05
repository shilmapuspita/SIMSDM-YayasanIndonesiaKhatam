<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'address' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,png', 'max:2048'],
        ];
    }
}
