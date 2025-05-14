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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('salaries_id')->nullable()->constrained('salaries')->onDelete('cascade');
            $table->date('PAYDATE');
            $table->enum('STATUS', ['PENDING', 'PROCESSED'])->default('PENDING');
            $table->foreignId('approval_id')->constrained('employees')->onDelete('cascade');
            $table->date('approval_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
