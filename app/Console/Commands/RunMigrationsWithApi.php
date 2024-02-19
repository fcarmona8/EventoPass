<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RunMigrationsWithApi extends Command
{
    protected $signature = 'api:migrate:refresh';
    protected $description = 'Run the API migrations and then this project migrations with seeders';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $apiPath = env('API_PATH');

        if (empty($apiPath)) {
            $this->error('La variable de entorno API_PATH no está definida.');
            return;
        }

        $apiCommand = "cd $apiPath && php artisan migrate:fresh";
        
        $process = Process::fromShellCommandline($apiCommand);
        try {
            $process->mustRun();

            echo $process->getOutput();
            
            $this->call('migrate:fresh', [
                '--seed' => true,
            ]);
        } catch (ProcessFailedException $exception) {
            $this->error('El comando de la API falló.');
            echo $exception->getMessage();
        }
    }
}
