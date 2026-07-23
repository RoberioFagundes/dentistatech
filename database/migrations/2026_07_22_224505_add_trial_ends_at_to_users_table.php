<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            $table->date('trial_ends_at')->nullable();
            $table->boolean('trial_used')->default(false);
            $table->enum('status_assinatura', [
                'trial',
                'ativa',
                'vencida',
                'cancelada'
            ])->default('trial');

            $table->string('asaas_customer')->nullable();
            $table->string('asaas_subscription')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            // ✅ Adicione aqui para remover ao desfazer a migração
            $table->dropColumn([
                'trial_ends_at',
                'trial_used',
                'status_assinatura',
                'asaas_customer',
                'asaas_subscription'
            ]);
        });
    }
};

