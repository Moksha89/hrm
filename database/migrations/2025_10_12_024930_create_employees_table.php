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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('loan', 10, 2)->nullable();
            $table->decimal('emi', 10, 2)->nullable();
            $table->string('aadhar', 12)->nullable();
            $table->string('pan', 10)->nullable();
            $table->date('dob')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
