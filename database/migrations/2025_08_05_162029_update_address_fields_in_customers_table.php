<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('address', 'address1');
            $table->renameColumn('company_address', 'company_address1');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->text('address2')->nullable()->after('address1');
            $table->text('company_address2')->nullable()->after('company_address1');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('address1', 'address');
            $table->dropColumn('address2');
            $table->renameColumn('company_address1', 'company_address');
            $table->dropColumn('company_address2');
        });
    }
};
