<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('processed_by')->nullable()->after('employee_id')->constrained('users')->onDelete('set null');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn('processed_by');
            $table->dropColumn('approval_status');
        });
    }
};
