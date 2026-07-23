<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreWorkOrderRequest;
use App\Models\WorkOrder;
use App\Services\Frontend\ItqanFrontendContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class WorkOrderController extends Controller
{
    public function __construct(private readonly ItqanFrontendContentService $contentService)
    {
    }

    public function store(StoreWorkOrderRequest $request): RedirectResponse
    {
        abort_unless(Schema::hasTable('work_orders'), 500, 'Please run migrations first.');

        $validated = $request->validated();
        $work = collect($this->contentService->content()['collections']['works'] ?? [])
            ->first(fn (array $item): bool => hash_equals(
                (string) ($item['order_key'] ?? ''),
                (string) $validated['selected_work_key']
            ));

        if (! is_array($work)) {
            $exception = ValidationException::withMessages([
                'selected_work_key' => 'The selected work item is no longer available. Please choose it again.',
            ]);
            $exception->errorBag = 'workOrder';
            $exception->redirectTo(route('works').'#workGrid');

            throw $exception;
        }

        $order = WorkOrder::query()->create([
            'page_section_item_id' => $work['id'] ?? null,
            'work_key' => $work['order_key'],
            'work_title' => $work['title'] ?? 'Work request',
            'work_category' => $work['pill'] ?? null,
            'customer_name' => $validated['customer_name'],
            'company_name' => $validated['company_name'] ?: null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'preferred_contact_method' => $validated['preferred_contact_method'],
            'budget_range' => $validated['budget_range'],
            'timeline' => $validated['timeline'],
            'project_summary' => $validated['project_summary'],
            'requirements' => $validated['requirements'] ?: null,
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
        ]);

        return redirect()
            ->to(route('works').'#workGrid')
            ->with('work_order_status', 'Your order request has been received. ITQAN will review it and contact you soon.')
            ->with('work_order_reference', $order->reference_number);
    }
}
