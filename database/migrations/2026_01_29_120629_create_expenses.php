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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_code')->required();
            $table->string('expense_title')->required();
            $table->text('expense_description')->required();
            $table->decimal('amount',10,2)->default(0);
            $table->integer('payment_method_id')->required();
            $table->integer('company_id')->required();
            $table->string('date')->required();
            $table->integer('updated_by')->required();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
