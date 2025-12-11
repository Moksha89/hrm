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
        Schema::table('employee_working_days', function (Blueprint $table) {
            $table->decimal('emi_override_amount', 10, 2)->nullable()->after('deduct_emi');
            $table->text('emi_override_reason')->nullable()->after('emi_override_amount');
            $table->foreignId('emi_override_by')->nullable()->after('emi_override_reason')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_working_days', function (Blueprint $table) {
            $table->dropForeign(['emi_override_by']);
            $table->dropColumn(['emi_override_amount', 'emi_override_reason', 'emi_override_by']);
        });
    }
};
