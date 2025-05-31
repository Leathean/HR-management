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
        Schema::create('deduction_attendance_rules', function (Blueprint $table) {
                $table->id();
                $table->enum('ATTENDANCE_TYPE', ['LATE', 'ABSENT', 'EARLY OUT']); // matches attendance flags
                $table->enum('DEDUCTION_METHOD', ['FIXED', 'PERCENTAGE'])->default('FIXED'); // how deduction is calculated
                $table->decimal('DEDUCTION_ATTENDANCE_AMOUNT', 10, 2); // amount or percent
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction_attendance_rules');
    }
};
