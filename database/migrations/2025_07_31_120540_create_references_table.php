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
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('type');
            $table->string('new_ic');
            $table->string('name');
            $table->string('gender');
            $table->string('race');
            $table->date('date_of_birth');
            $table->string('mobile');
            $table->string('telephone');
            $table->string('house_ownership');
            $table->string('warganegara');
            $table->text('address');
            $table->string('postcode');
            $table->string('city');
            $table->string('state');
            $table->string('company_name');
            $table->string('biz_type');
            $table->string('designation');
            $table->string('monthly_income');
            $table->text('company_address');
            $table->string('company_postcode');
            $table->string('company_city');
            $table->string('company_state');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
