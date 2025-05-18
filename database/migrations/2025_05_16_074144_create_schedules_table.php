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
            Schema::create('schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employees_id')->constrained('employees')->onDelete('cascade');
                $table->string('NAME');
                $table->time('STARTTIME')->nullable();
                $table->time('ENDTIME')->nullable();
                $table->enum('SCHEDULE_TYPE', ['WORKDAY', 'ONLEAVE', 'ABSENT'])->default('WORKDAY');
                $table->date('DATE');
                $table->timestamps();
                                                                    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
