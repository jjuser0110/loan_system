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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('closing_date')->nullable();
            $table->integer('company_id')->nullable();
            $table->decimal('stock_a', 10, 2)->nullable();
            $table->decimal('stock_b', 10, 2)->nullable();
            $table->decimal('stock_bb', 10, 2)->nullable();
            $table->decimal('company_amount', 15, 2)->nullable();
            $table->decimal('loan_topup', 15, 2)->nullable();
            $table->decimal('payment', 15, 2)->nullable();
            $table->decimal('expenses', 15, 2)->nullable();
            $table->decimal('account_total_amount', 15, 2)->nullable();
            $table->date('created_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
