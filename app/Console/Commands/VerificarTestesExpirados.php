<?php

namespace App\Console\Commands;

use App\Models\Clinica;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class VerificarTestesExpirados extends Command
{
    protected $signature = 'verificar:testes-expirados';
    protected $description = 'Atualiza status de teste vencido e avisa 3 dias antes de acabar';

    public function handle()
    {
        // 1️⃣ Avisa quem tem 3 dias restantes
        $clinicasAviso = Clinica::where('status_assinatura', 'trial')
            ->whereDate('trial_ends_at', '=', now()->addDays(3))
            ->get();

        foreach ($clinicasAviso as $clinica) {
            // Envia e-mail de aviso
            Mail::raw("Olá! Seu teste de 15 dias acaba em 3 dias. Assine agora no sistema para não perder acesso.", function ($msg) use ($clinica) {
                $msg->to($clinica->user->email)
                    ->subject('Aviso: Seu teste acaba em 3 dias')
                    ->from('sistema@dentistatech.com.br', 'DentistaTech');
            });

            $this->info("📧 Aviso enviado para: {$clinica->razao_social}");
        }

        // 2️⃣ Marca como vencido quem já passou do prazo
        Clinica::where('status_assinatura', 'trial')
            ->where('trial_ends_at', '<', now())
            ->update(['status_assinatura' => 'vencida']);

        $this->info('✅ Verificação concluída!');
        return Command::SUCCESS;
    }
}