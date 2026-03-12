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
        Schema::table('cash_book_reports', function (Blueprint $table) {
            $table->string('customer_name')->after('description')->nullable();
            $table->string('expenses_name')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_book_reports', function (Blueprint $table) {
            //
        });
    }
};
