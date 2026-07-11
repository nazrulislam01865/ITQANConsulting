<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeSectionItemRequest extends FormRequest
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
                'chip',
                'button',
                'social_link',
                'ticker',
                'paragraph',
                'card',
                'service_card',
                'step',
                'problem',
                'testimonial',
                'value',
                'work',
            ])],
            'badge' => ['nullable', 'string', 'max:80'],
            'title' => ['nullable', 'string', 'max:300'],
            'text' => ['nullable', 'string', 'max:2000'],
            'subtitle' => ['nullable', 'string', 'max:300'],
            'button_text' => ['nullable', 'string', 'max:80'],
            'button_route' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'button_class' => ['nullable', 'string', 'max:50'],
            'settings' => ['nullable', 'array'],
            'settings.*' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
