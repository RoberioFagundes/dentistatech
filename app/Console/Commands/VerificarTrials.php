<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:verificar-trials')]
#[Description('Command description')]
class VerificarTrials extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $clinicas = Clinica::where('status_assinatura', 'trial')
            ->whereDate('trial_ends_at', '<', now())
            ->get();

        foreach ($clinicas as $clinica) {

            $clinica->status_assinatura = 'vencida';

            $clinica->save();
        }
    }
}
