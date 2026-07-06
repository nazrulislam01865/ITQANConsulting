<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'platform' => ['required', 'string', 'max:80'],
            'label' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:500'],
            'icon_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:512'],
            'remove_icon' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
