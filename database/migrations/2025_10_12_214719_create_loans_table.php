<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('monthly_emi', 10, 2);
            $table->integer('total_months');
            $table->integer('remaining_months');
            $table->decimal('remaining_balance', 10, 2);
            $table->date('start_date');
            $table->enum('status', ['pending', 'active', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
