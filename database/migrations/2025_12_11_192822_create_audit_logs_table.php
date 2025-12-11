<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // Store name in case user is deleted
            $table->string('action'); // created, updated, deleted, approved, rejected, etc.
            $table->string('module'); // employees, loans, requests, teams, payments, etc.
            $table->string('model_type'); // App\Models\Loan, App\Models\Employee, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description'); // Human-readable description of what happened
            $table->string('reason')->nullable(); // Why the change was made (optional)
            $table->json('old_values')->nullable(); // Previous values before change
            $table->json('new_values')->nullable(); // New values after change
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['module', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
