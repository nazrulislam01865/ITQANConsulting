<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() ?? false;
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:80'],
            'route_name' => ['nullable', 'string', 'max:80', 'required_without:url'],
            'url' => ['nullable', 'string', 'max:255', 'required_without:route_name'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
