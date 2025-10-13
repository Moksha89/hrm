<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_working_days', function (Blueprint $table) {
            $table->boolean('deduct_emi')->default(true)->after('working_days');
        });
    }

    public function down(): void
    {
        Schema::table('employee_working_days', function (Blueprint $table) {
            $table->dropColumn('deduct_emi');
        });
    }
};
