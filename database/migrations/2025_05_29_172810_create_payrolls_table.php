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
            $table->enum('STATUS', ['PENDING', 'PROCESSED', 'DENIED'])->default('PENDING');
            $table->foreignId('approval_id')->nullable()->constrained('employees')->onDelete('cascade'); // approver mostly the payroll manager
            $table->date('approval_date')->nullable();
            $table->decimal('gross_pay', 10, 2)->nullable();         // salary + benefits
            $table->decimal('total_deductions', 10, 2)->nullable();  // deductions
            $table->decimal('net_pay', 10, 2)->nullable();           // gross - deductions
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
