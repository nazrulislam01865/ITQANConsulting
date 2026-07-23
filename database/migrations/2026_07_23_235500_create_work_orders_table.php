<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('work_orders')) {
            return;
        }

        Schema::create('work_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_number', 40)->nullable()->unique();
            $table->foreignId('page_section_item_id')
                ->nullable()
                ->constrained('page_section_items')
                ->nullOnDelete();
            $table->string('work_key', 190);
            $table->string('work_title');
            $table->string('work_category')->nullable();
            $table->string('customer_name');
            $table->string('company_name')->nullable();
            $table->string('email');
            $table->string('phone', 80);
            $table->string('preferred_contact_method', 30);
            $table->string('budget_range', 120);
            $table->string('timeline', 120);
            $table->text('project_summary');
            $table->text('requirements')->nullable();
            $table->string('status', 30)->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->string('ip_address', 80)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['work_key', 'created_at']);
            $table->index(['email', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
