<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageSectionItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'item_type' => ['required', 'string', Rule::in([
                'button',
                'card',
                'mission_card',
                'service_area',
                'faq',
                'work',
                'filter',
                'catalog_page',
                'step',
                'option',
            ])],
            'badge' => ['nullable', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:400'],
            'text' => ['nullable', 'string', 'max:8000'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'button_route' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'button_class' => ['nullable', 'string', 'max:50'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'media_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'media_video' => ['nullable', 'file', 'max:102400'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
