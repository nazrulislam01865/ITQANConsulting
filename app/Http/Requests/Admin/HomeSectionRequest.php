<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:1200'],
            'lead' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'button_route' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'string', 'max:500'],
            'founder_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
