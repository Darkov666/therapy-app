<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_mxn', 8, 2)->default(0)->after('price');
            $table->decimal('price_usd', 8, 2)->default(0)->after('price_mxn');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_mxn', 'price_usd']);
        });
    }
};
