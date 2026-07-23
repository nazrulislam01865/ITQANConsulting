<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_submit_a_work_order_request(): void
    {
        $response = $this->post(route('work-orders.store'), [
            'selected_work_key' => 'catalog-fleetman-plus-1',
            'customer_name' => 'Test Client',
            'company_name' => 'Example Company',
            'email' => 'client@example.com',
            'phone' => '+8801700000000',
            'preferred_contact_method' => 'whatsapp',
            'budget_range' => 'BDT 100,000 – 300,000',
            'timeline' => 'Within 1–3 months',
            'project_summary' => 'We need a fleet operations system with clearer reporting and daily control.',
            'requirements' => 'Vehicle, driver, fuel, and contract management.',
            'consent' => '1',
            'website' => '',
        ]);

        $response
            ->assertRedirect(route('works').'#workGrid')
            ->assertSessionHas('work_order_status')
            ->assertSessionHas('work_order_reference');

        $this->assertDatabaseHas('work_orders', [
            'work_key' => 'catalog-fleetman-plus-1',
            'work_title' => 'Fleetman PLUS',
            'customer_name' => 'Test Client',
            'email' => 'client@example.com',
            'status' => 'new',
        ]);

        $this->assertNotNull(WorkOrder::query()->first()?->reference_number);
    }

    public function test_work_order_request_requires_valid_contact_and_project_details(): void
    {
        $response = $this->from(route('works'))->post(route('work-orders.store'), [
            'selected_work_key' => 'catalog-fleetman-plus-1',
            'customer_name' => '',
            'email' => 'not-an-email',
            'phone' => '',
            'preferred_contact_method' => 'invalid',
            'budget_range' => '',
            'timeline' => '',
            'project_summary' => 'Too short',
        ]);

        $response
            ->assertRedirect(route('works'))
            ->assertSessionHasErrors([
                'customer_name',
                'email',
                'phone',
                'preferred_contact_method',
                'budget_range',
                'timeline',
                'project_summary',
                'consent',
            ], null, 'workOrder');

        $this->assertDatabaseCount('work_orders', 0);
    }

    public function test_admin_can_review_and_update_a_work_order(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $order = WorkOrder::query()->create([
            'work_key' => 'catalog-fleetman-plus-1',
            'work_title' => 'Fleetman PLUS',
            'work_category' => 'Software / Fleet',
            'customer_name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '+8801700000000',
            'preferred_contact_method' => 'whatsapp',
            'budget_range' => 'Not decided yet',
            'timeline' => 'Flexible / not decided',
            'project_summary' => 'A sufficiently detailed project request for admin testing.',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->get(route('admin.work-orders.show', $order))
            ->assertOk()
            ->assertSee($order->reference_number)
            ->assertSee('Test Client');

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->put(route('admin.work-orders.update', $order), [
                'status' => 'quoted',
                'internal_notes' => 'Quotation preparation started.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('work_orders', [
            'id' => $order->id,
            'status' => 'quoted',
            'internal_notes' => 'Quotation preparation started.',
        ]);
    }
}
