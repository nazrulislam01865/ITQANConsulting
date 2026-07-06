<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_section_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 80)->default('card');
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

            $table->index(['page_section_id', 'item_type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_items');
    }
};
