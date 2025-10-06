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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->required();
            $table->integer('company_id')->required();
            $table->integer('customer_id')->required();
            $table->date('due_date')->required();
            $table->decimal('payment_amount',20,2)->default(0);
            $table->decimal('paid_amount',20,2)->default(0);
            $table->decimal('discount_amount',20,2)->default(0);
            $table->decimal('interest_amount',20,2)->default(0);
            $table->decimal('interest_paid_amount',20,2)->default(0);
            $table->decimal('late_amount',20,2)->default(0);
            $table->decimal('late_paid_amount',20,2)->default(0);
            $table->integer('closed')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
