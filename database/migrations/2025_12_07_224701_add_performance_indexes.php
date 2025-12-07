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
        Schema::table('employees', function (Blueprint $table) {
            $table->index('status', 'idx_employees_status');
            $table->index('team_id', 'idx_employees_team_id');
            $table->index(['status', 'team_id'], 'idx_employees_status_team');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->index('status', 'idx_requests_status');
            $table->index('employee_id', 'idx_requests_employee_id');
            $table->index('requested_by', 'idx_requests_requested_by');
            $table->index(['status', 'employee_id'], 'idx_requests_status_employee');
            $table->index('created_at', 'idx_requests_created_at');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->index('status', 'idx_loans_status');
            $table->index('employee_id', 'idx_loans_employee_id');
            $table->index(['status', 'remaining_balance'], 'idx_loans_status_balance');
        });

        Schema::table('loan_payments', function (Blueprint $table) {
            $table->index('status', 'idx_loan_payments_status');
            $table->index('loan_id', 'idx_loan_payments_loan_id');
            $table->index('due_date', 'idx_loan_payments_due_date');
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->index('status', 'idx_salary_payments_status');
            $table->index('employee_id', 'idx_salary_payments_employee_id');
            $table->index(['month', 'year'], 'idx_salary_payments_month_year');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id', 'idx_notifications_user_id');
            $table->index('read_at', 'idx_notifications_read_at');
            $table->index(['user_id', 'read_at'], 'idx_notifications_user_read');
        });

        Schema::table('employee_activities', function (Blueprint $table) {
            $table->index('employee_id', 'idx_employee_activities_employee_id');
            $table->index('activity_type', 'idx_employee_activities_type');
            $table->index('created_at', 'idx_employee_activities_created_at');
        });

        Schema::table('employee_working_days', function (Blueprint $table) {
            $table->index(['employee_id', 'month', 'year'], 'idx_working_days_employee_month_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_status');
            $table->dropIndex('idx_employees_team_id');
            $table->dropIndex('idx_employees_status_team');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex('idx_requests_status');
            $table->dropIndex('idx_requests_employee_id');
            $table->dropIndex('idx_requests_requested_by');
            $table->dropIndex('idx_requests_status_employee');
            $table->dropIndex('idx_requests_created_at');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex('idx_loans_status');
            $table->dropIndex('idx_loans_employee_id');
            $table->dropIndex('idx_loans_status_balance');
        });

        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropIndex('idx_loan_payments_status');
            $table->dropIndex('idx_loan_payments_loan_id');
            $table->dropIndex('idx_loan_payments_due_date');
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropIndex('idx_salary_payments_status');
            $table->dropIndex('idx_salary_payments_employee_id');
            $table->dropIndex('idx_salary_payments_month_year');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_user_id');
            $table->dropIndex('idx_notifications_read_at');
            $table->dropIndex('idx_notifications_user_read');
        });

        Schema::table('employee_activities', function (Blueprint $table) {
            $table->dropIndex('idx_employee_activities_employee_id');
            $table->dropIndex('idx_employee_activities_type');
            $table->dropIndex('idx_employee_activities_created_at');
        });

        Schema::table('employee_working_days', function (Blueprint $table) {
            $table->dropIndex('idx_working_days_employee_month_year');
        });
    }
};
