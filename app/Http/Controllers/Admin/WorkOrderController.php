<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWorkOrderRequest;
use App\Models\WorkOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Schema::hasTable('work_orders'), 500, 'Please run migrations first.');

        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(WorkOrder::STATUSES))],
            'q' => ['nullable', 'string', 'max:190'],
        ]);

        $query = WorkOrder::query()->latestFirst();
        $status = $filters['status'] ?? null;
        $search = trim((string) ($filters['q'] ?? ''));

        if (filled($status) && array_key_exists($status, WorkOrder::STATUSES)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('work_title', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return view('admin.work-orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'statuses' => WorkOrder::STATUSES,
            'selectedStatus' => $status,
            'search' => $search,
            'totalCount' => WorkOrder::query()->count(),
            'newCount' => WorkOrder::query()->where('status', 'new')->count(),
            'unviewedCount' => WorkOrder::query()->whereNull('viewed_at')->count(),
        ]);
    }

    public function show(WorkOrder $workOrder): View
    {
        if ($workOrder->isUnviewed()) {
            $workOrder->forceFill(['viewed_at' => now()])->save();
        }

        return view('admin.work-orders.show', [
            'order' => $workOrder,
            'statuses' => WorkOrder::STATUSES,
        ]);
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->update($request->validated());

        return back()->with('status', 'Work order updated successfully.');
    }

    public function destroy(WorkOrder $workOrder): RedirectResponse
    {
        $workOrder->delete();

        return redirect()
            ->route('admin.work-orders.index')
            ->with('status', 'Work order deleted.');
    }
}
