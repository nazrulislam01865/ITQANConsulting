<?php

namespace App\Http\Requests\Admin;

use App\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdmin() === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(WorkOrder::STATUSES))],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
