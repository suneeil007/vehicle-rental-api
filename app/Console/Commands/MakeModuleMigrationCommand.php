<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeModuleMigrationCommand extends Command
{
    protected $signature = 'module:make-migration 
                            {module}
                            {name}';

    protected $description = 'Create migration inside module database folder';


    public function handle()
    {
        $module = $this->argument('module');

        $name = $this->argument('name');


        $path = "app/Modules/{$module}/Database/Migrations";


        Artisan::call('make:migration', [
            'name' => $name,
            '--path' => $path,
        ]);


        $this->info(
            Artisan::output()
        );
    }
}