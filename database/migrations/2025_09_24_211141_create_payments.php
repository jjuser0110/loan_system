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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->required();
            $table->integer('customer_id')->required();
            $table->integer('loan_id')->required();
            $table->decimal('payment_amount',20,2)->default(0);
            $table->decimal('late_paid_amount',20,2)->default(0);
            $table->decimal('interest_paid_amount',20,2)->default(0);
            $table->decimal('discount_amount',20,2)->default(0);
            $table->string('bank')->nullable();
            $table->string('cheque',20,2)->nullable();
            $table->string('collection_type')->nullable();
            $table->integer('created_by')->required();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
