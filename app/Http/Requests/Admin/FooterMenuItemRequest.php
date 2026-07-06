<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FooterMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'group_key' => ['required', 'string', 'max:50'],
            'group_title' => ['required', 'string', 'max:80'],
            'label' => ['required', 'string', 'max:100'],
            'route_name' => ['nullable', 'string', 'max:80'],
            'url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
