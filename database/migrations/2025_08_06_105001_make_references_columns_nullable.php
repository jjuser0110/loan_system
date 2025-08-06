<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('references', function (Blueprint $table) {
            $table->integer('customer_id')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('race')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('mobile')->nullable()->change();
            $table->string('telephone')->nullable()->change();
            $table->string('house_ownership')->nullable()->change();
            $table->string('warganegara')->nullable()->change();
            $table->text('address1')->nullable()->change();
            $table->text('address2')->nullable()->change();
            $table->string('postcode')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('company_name')->nullable()->change();
            $table->string('biz_type')->nullable()->change();
            $table->string('designation')->nullable()->change();
            $table->string('monthly_income')->nullable()->change();
            $table->text('company_address1')->nullable()->change();
            $table->text('company_address2')->nullable()->change();
            $table->string('company_postcode')->nullable()->change();
            $table->string('company_city')->nullable()->change();
            $table->string('company_state')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('references', function (Blueprint $table) {
            // Revert back if needed (optional)
            $table->integer('customer_id')->nullable(false)->change();
            // Repeat for others if rollback is needed
        });
    }
};
