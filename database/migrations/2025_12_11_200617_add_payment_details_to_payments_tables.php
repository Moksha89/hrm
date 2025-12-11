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
        // Add screenshot column to payments table if not exists
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'screenshot')) {
                $table->string('screenshot')->nullable()->after('remarks');
            }
        });

        // Add UTR, remarks, and screenshot columns to salary_payments table
        Schema::table('salary_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_payments', 'utr')) {
                $table->string('utr')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('salary_payments', 'remarks')) {
                $table->text('remarks')->nullable()->after('utr');
            }
            if (!Schema::hasColumn('salary_payments', 'screenshot')) {
                $table->string('screenshot')->nullable()->after('remarks');
            }
        });

        // Add UTR, remarks, and screenshot columns to loan_payments table
        Schema::table('loan_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_payments', 'utr')) {
                $table->string('utr')->nullable()->after('paid_date');
            }
            if (!Schema::hasColumn('loan_payments', 'remarks')) {
                $table->text('remarks')->nullable()->after('utr');
            }
            if (!Schema::hasColumn('loan_payments', 'screenshot')) {
                $table->string('screenshot')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'screenshot')) {
                $table->dropColumn('screenshot');
            }
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            if (Schema::hasColumn('salary_payments', 'utr')) {
                $table->dropColumn('utr');
            }
            if (Schema::hasColumn('salary_payments', 'remarks')) {
                $table->dropColumn('remarks');
            }
            if (Schema::hasColumn('salary_payments', 'screenshot')) {
                $table->dropColumn('screenshot');
            }
        });

        Schema::table('loan_payments', function (Blueprint $table) {
            if (Schema::hasColumn('loan_payments', 'utr')) {
                $table->dropColumn('utr');
            }
            if (Schema::hasColumn('loan_payments', 'remarks')) {
                $table->dropColumn('remarks');
            }
            if (Schema::hasColumn('loan_payments', 'screenshot')) {
                $table->dropColumn('screenshot');
            }
        });
    }
};
