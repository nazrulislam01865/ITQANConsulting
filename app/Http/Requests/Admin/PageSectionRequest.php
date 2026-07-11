<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PageSectionRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:1500'],
            'lead' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:8000'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'button_route' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'string', 'max:3000'],
            'qr_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
