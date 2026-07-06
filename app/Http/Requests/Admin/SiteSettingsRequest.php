<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'mark_text' => ['nullable', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:500'],
            'primary_cta_text' => ['nullable', 'string', 'max:80'],
            'primary_cta_route' => ['nullable', 'string', 'max:80'],
            'footer_bottom_left' => ['nullable', 'string', 'max:255'],
            'copyright' => ['nullable', 'string', 'max:120'],
        ];
    }
}
