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
        Schema::table('orcamento_pacientes', function (Blueprint $table) {
            //
            $table->decimal('desconto', 10, 2)->nullable()->after('total_orcamento');
            $table->decimal('valor_liquido', 10, 2)->nullable()->after('desconto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamento_pacientes', function (Blueprint $table) {
            //
            $table->dropColumn('desconto');
            $table->dropColumn('valor_liquido');
        });
    }
};
