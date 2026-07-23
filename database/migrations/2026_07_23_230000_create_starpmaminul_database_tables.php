<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureSqliteDatabaseExists();

        $schema = Schema::connection('starpmaminul');

        if (! $schema->hasTable('users')) {
            $schema->create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('portfolio_sections')) {
            $schema->create('portfolio_sections', function (Blueprint $table): void {
                $table->id();
                $table->string('section_key')->unique();
                $table->json('data');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        $schema = Schema::connection('starpmaminul');
        $schema->dropIfExists('portfolio_sections');
        $schema->dropIfExists('users');
    }

    private function ensureSqliteDatabaseExists(): void
    {
        if (config('database.connections.starpmaminul.driver') !== 'sqlite') {
            return;
        }

        $database = (string) config('database.connections.starpmaminul.database');

        if ($database === '' || $database === ':memory:' || File::exists($database)) {
            return;
        }

        File::ensureDirectoryExists(dirname($database));
        File::put($database, '');
    }
};
