<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_book_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->date('date');
            $table->text('description')->nullable();
            $table->decimal('loan_top_up', 20, 2)->nullable();
            $table->integer('payment')->default(1);
            $table->decimal('expenses', 20, 2)->default(0);
            $table->integer('account_total_amount')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_book_reports');
    }
};
