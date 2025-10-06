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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code');
            $table->integer('company_id')->required();
            $table->integer('customer_id')->required();
            $table->date('year_month')->required();
            $table->string('interest_group')->required();
            $table->decimal('loan_amount',20,2)->required();
            $table->decimal('interest_rate',8,4)->default(0);
            $table->integer('loan_term')->nullable();
            $table->decimal('installment',20,2)->default(0);
            $table->decimal('first_payment',20,2)->default(0);
            $table->decimal('last_payment',20,2)->default(0);
            $table->decimal('processing_fee',20,2)->default(0);
            $table->decimal('stamp_fee',20,2)->default(0);
            $table->decimal('capital',20,2)->required();
            $table->decimal('payment',20,2)->default(0);
            $table->decimal('paid',20,2)->default(0);
            $table->decimal('balance',20,2)->default(0);
            $table->decimal('interest',20,2)->default(0);
            $table->decimal('interest_paid',20,2)->default(0);
            $table->decimal('interest_balance',20,2)->default(0);
            $table->decimal('late',20,2)->default(0);
            $table->decimal('late_paid',20,2)->default(0);
            $table->decimal('late_balance',20,2)->default(0);
            $table->decimal('discount',20,2)->default(0);
            $table->decimal('outstanding',20,2)->default(0);
            $table->date('next_due_date')->nullable();
            $table->decimal('next_due_amount',20,2)->default(0);
            $table->string('alternate_code')->nullable();
            $table->string('receipt_no')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->required();
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
         Schema::dropIfExists('loans');
    }
};
