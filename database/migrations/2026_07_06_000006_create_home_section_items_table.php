<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_section_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('home_section_id')->constrained('home_sections')->cascadeOnDelete();
            $table->string('item_type')->default('card');
            $table->string('badge')->nullable();
            $table->string('title')->nullable();
            $table->longText('text')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_route')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_class')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['home_section_id', 'item_type', 'is_active', 'sort_order'], 'home_section_items_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_section_items');
    }
};
