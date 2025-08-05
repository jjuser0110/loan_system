<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('company_id')->nullable()->after('profile_image');
        });
    }

    public function down(): void
    {
        // Reverse logic (optional)
    }
};
