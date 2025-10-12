<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('gross_salary', 10, 2);
            $table->integer('working_days');
            $table->decimal('net_pay', 10, 2);
            $table->decimal('total_emi', 10, 2)->default(0);
            $table->decimal('final_pay', 10, 2);
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
