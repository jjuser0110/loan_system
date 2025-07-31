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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('profile_image')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('company_code')->nullable();
            $table->string('customer_name');
            $table->string('nric_number');
            $table->string('gender')->nullable();
            $table->string('race')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('house_ownership')->nullable();
            $table->string('warganegara')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('remark')->nullable();
            $table->string('company_name')->nullable();
            $table->string('biz_type')->nullable();
            $table->string('designation')->nullable();
            $table->string('monthly_income')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_post_code')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_telephone')->nullable();
            $table->string('company_mobile')->nullable();
            $table->string('company_fax')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('employer')->nullable();
            $table->string('job_type')->nullable();
            $table->date('start_working_date')->nullable();
            $table->date('end_working_date')->nullable();
            $table->date('salary_date')->nullable();
            $table->date('2nd_salary_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
