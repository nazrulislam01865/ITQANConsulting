<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkOrderRequest extends FormRequest
{
    protected $errorBag = 'workOrder';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'selected_work_key' => trim((string) $this->input('selected_work_key')),
            'customer_name' => trim((string) $this->input('customer_name')),
            'company_name' => trim((string) $this->input('company_name')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
            'project_summary' => trim((string) $this->input('project_summary')),
            'requirements' => trim((string) $this->input('requirements')),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'selected_work_key' => ['required', 'string', 'max:190'],
            'customer_name' => ['required', 'string', 'max:160'],
            'company_name' => ['nullable', 'string', 'max:180'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:80'],
            'preferred_contact_method' => ['required', Rule::in(['email', 'phone', 'whatsapp'])],
            'budget_range' => ['required', 'string', 'max:120'],
            'timeline' => ['required', Rule::in([
                'As soon as possible',
                'Within 1 month',
                'Within 1–3 months',
                'Within 3–6 months',
                'Flexible / not decided',
            ])],
            'project_summary' => ['required', 'string', 'min:20', 'max:3000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'selected_work_key.required' => 'Please select a work item before submitting the order request.',
            'project_summary.min' => 'Please provide a little more detail about what you want to achieve.',
            'consent.accepted' => 'Please confirm that ITQAN may contact you about this request.',
            'website.max' => 'The order request could not be submitted.',
        ];
    }
}
