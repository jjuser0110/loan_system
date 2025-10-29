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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->string('branch_id');
            $table->string('bank_id');
            $table->string('account_no')->nullable();
            $table->string('owner_name')->nullable();
            $table->integer('is_active')->default(1);
            $table->decimal('amount',20,2)->default(0);
            $table->integer('created_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
