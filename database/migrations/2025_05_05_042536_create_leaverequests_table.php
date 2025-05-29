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
        Schema::create('leaverequests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->date('LEAVEDATE');
            $table->date('RETURNDATE');
            $table->bigInteger('TOTAL_AMOUNT_LEAVE')->unsigned()->nullable();
            $table->string('REASONS')->nullable();
            $table->foreignId('approver_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->date('APPROVEDATE')->nullable();
            $table->enum('LEAVESTATUS', ['PENDING', 'ACCEPTED', 'DENY'])->default('PENDING');
            $table->enum('LEAVETYPE', ['NONE', 'SICK LEAVE', 'PATERNITY'])->default('NONE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaverequests');
    }
};
