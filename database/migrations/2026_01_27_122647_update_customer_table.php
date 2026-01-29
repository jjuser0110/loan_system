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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nric_path')->after('nric_number')->nullable();
            $table->decimal('monthly_income',10,2)->default(0)->nullable()->change();
            $table->decimal('monthly_income_2',10,2)->after('monthly_income')->default(0);
            $table->renameColumn('2nd_salary_date', 'salary_date_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
