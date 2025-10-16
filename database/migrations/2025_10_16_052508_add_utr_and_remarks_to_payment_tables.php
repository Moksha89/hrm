<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('utr')->nullable()->after('notes');
            $table->text('remarks')->nullable()->after('utr');
        });
        
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->string('utr')->nullable()->after('notes');
            $table->text('remarks')->nullable()->after('utr');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['utr', 'remarks']);
        });
        
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropColumn(['utr', 'remarks']);
        });
    }
};
