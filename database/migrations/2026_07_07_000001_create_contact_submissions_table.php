<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contact_submissions')) {
            return;
        }

        Schema::create('contact_submissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('need')->nullable();
            $table->json('areas')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->text('message');
            $table->string('status', 30)->default('unread')->index();
            $table->string('ip_address', 80)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};
