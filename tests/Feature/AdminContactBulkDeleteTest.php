<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminContactBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_multiple_selected_responses_in_one_query(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $first = $this->submission('First');
        $second = $this->submission('Second');
        $untouched = $this->submission('Keep');

        $deletes = [];
        DB::listen(function ($query) use (&$deletes): void {
            if (preg_match('/^delete from ["`]?contact_submissions/i', $query->sql)) {
                $deletes[] = $query->sql;
            }
        });

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->delete(route('admin.contact-submissions.bulk-destroy'), [
                'ids' => [$first->id, $second->id],
            ])
            ->assertRedirect(route('admin.contact-submissions.index'))
            ->assertSessionHas('status', '2 contact responses deleted.');

        $this->assertDatabaseMissing('contact_submissions', ['id' => $first->id]);
        $this->assertDatabaseMissing('contact_submissions', ['id' => $second->id]);
        $this->assertDatabaseHas('contact_submissions', ['id' => $untouched->id]);
        $this->assertCount(1, $deletes);
    }

    public function test_admin_must_select_at_least_one_response(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $item = $this->submission('Keep');

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->delete(route('admin.contact-submissions.bulk-destroy'), ['ids' => []])
            ->assertSessionHasErrors('ids');

        $this->assertDatabaseHas('contact_submissions', ['id' => $item->id]);
    }

    public function test_admin_cannot_submit_invalid_or_duplicate_ids(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $item = $this->submission('Keep');

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->delete(route('admin.contact-submissions.bulk-destroy'), ['ids' => [$item->id, $item->id]])
            ->assertSessionHasErrors('ids.1');

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->delete(route('admin.contact-submissions.bulk-destroy'), ['ids' => ['not-an-id']])
            ->assertSessionHasErrors('ids.0');

        $this->assertDatabaseHas('contact_submissions', ['id' => $item->id]);
    }

    public function test_only_authorized_admins_can_bulk_delete(): void
    {
        $item = $this->submission('Protected');

        $this->delete(route('admin.contact-submissions.bulk-destroy'), ['ids' => [$item->id]])
            ->assertRedirect(route('admin.login'));

        $visitor = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        $this->actingAs($visitor)
            ->delete(route('admin.contact-submissions.bulk-destroy'), ['ids' => [$item->id]])
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('contact_submissions', ['id' => $item->id]);
    }

    public function test_bulk_selection_is_available_on_the_list_without_changing_individual_actions(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $item = $this->submission('Visible');

        $this->actingAs($admin)
            ->withSession(['admin_last_activity_at' => now()->timestamp])
            ->get(route('admin.contact-submissions.index'))
            ->assertOk()
            ->assertSee('Select all contact responses on this page')
            ->assertSee('name="ids[]"', false)
            ->assertSee('form="contact-bulk-form"', false)
            ->assertSee('Delete selected')
            ->assertSee(route('admin.contact-submissions.show', $item))
            ->assertSee(route('admin.contact-submissions.destroy', $item));
    }

    private function submission(string $name): ContactSubmission
    {
        return ContactSubmission::query()->create([
            'name' => $name,
            'email' => strtolower($name).'@example.com',
            'message' => 'Test inquiry message.',
        ]);
    }
}
