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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees')->onDelete('cascade');
            $table->date('date')->default(now());
            $table->time('time_in')->now()->nullable();
            $table->time('time_out')->now()->nullable();
            $table->enum('time_in_status', ['ON TIME', 'LATE'])->nullable();
            $table->enum('time_out_status', ['ON TIME', 'EARLY OUT'])->nullable();
            $table->enum('status_day', ['PRESENT', 'ABSENT', ])->nullable()->default('PRESENT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
