<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RcoratorioEvento24h extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorio:evento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar email recordatorio del evento dia antes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date_now = now()->format('Y-m-d');
        $visits = \DB::table('logs')->whereDate('created_at',$date_now)->get();
    }
}
