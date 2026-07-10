<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('habito_saudavels', function (Blueprint $table) {
            $table->string('consumo_alcool')->nullable()->after('habito_fumo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habito_saudavels', function (Blueprint $table) {
            $table->dropColumn('consumo_alcool');
        });
    }
};
