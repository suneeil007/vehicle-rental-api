<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'module:make {name}';

    protected $description = 'Create a complete module structure';

    public function handle()
    {
        $module = Str::studly($this->argument('name'));

        $basePath = app_path("Modules/{$module}");

        /*
        |--------------------------------------------------------------------------
        | Create Directories
        |--------------------------------------------------------------------------
        */

        $directories = [
            'Controllers',
            'Models',
            'Requests',
            'Resources',
            'Services',
            'Repositories/Contracts',
            'Enums',
            'Database/Migrations',
            'Database/Seeders',
            'Providers',
            'routes',
            'config',
            'Helpers',
        ];


        foreach ($directories as $directory) {

            File::ensureDirectoryExists(
                $basePath.'/'.$directory
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Create Module Files
        |--------------------------------------------------------------------------
        */


        $this->createFile(
            $basePath."/Controllers/{$module}Controller.php",
            $this->controllerStub($module)
        );


        $this->createFile(
            $basePath."/Models/{$module}.php",
            $this->modelStub($module)
        );


        $this->createFile(
            $basePath."/Services/{$module}Service.php",
            $this->serviceStub($module)
        );


        $this->createFile(
            $basePath."/Repositories/{$module}Repository.php",
            $this->repositoryStub($module)
        );


        $this->createFile(
            $basePath."/Repositories/Contracts/{$module}RepositoryInterface.php",
            $this->repositoryInterfaceStub($module)
        );


        $this->createFile(
            $basePath."/Requests/Store{$module}Request.php",
            $this->requestStub($module,"Store{$module}Request")
        );


        $this->createFile(
            $basePath."/Requests/Update{$module}Request.php",
            $this->requestStub($module,"Update{$module}Request")
        );


        $this->createFile(
            $basePath."/Resources/{$module}Resource.php",
            $this->resourceStub($module)
        );


        $this->createFile(
            $basePath."/routes/api.php",
            "<?php\n\nuse Illuminate\Support\Facades\Route;\n"
        );


        $this->createFile(
            $basePath."/Providers/{$module}ServiceProvider.php",
            $this->providerStub($module)
        );


        $this->createFile(
            $basePath."/config/".Str::snake($module).".php",
            "<?php\n\nreturn [];\n"
        );


        $this->createFile(
            $basePath."/Helpers/helpers.php",
            "<?php\n"
        );


        /*
        |--------------------------------------------------------------------------
        | Create README
        |--------------------------------------------------------------------------
        */

        $this->createFile(
            $basePath."/README.md",
            "# {$module} Module\n"
        );


        $this->info("✅ Module {$module} created successfully.");

    }



    private function createFile($path,$content)
    {
        if(File::exists($path)){
            $this->warn("Skipped: ".$path);
            return;
        }

        File::put($path,$content);
    }



    private function controllerStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Controllers;

use App\Http\Controllers\Controller;

class {$module}Controller extends Controller
{

}

PHP;
    }



    private function modelStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Models;

use Illuminate\Database\Eloquent\Model;

class {$module} extends Model
{

}

PHP;
    }



    private function serviceStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Services;

class {$module}Service
{

}

PHP;
    }



    private function repositoryStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Repositories;

use App\Modules\\{$module}\Repositories\Contracts\\{$module}RepositoryInterface;

class {$module}Repository implements {$module}RepositoryInterface
{

}

PHP;
    }



    private function repositoryInterfaceStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Repositories\Contracts;

interface {$module}RepositoryInterface
{

}

PHP;
    }



    private function requestStub($module,$class)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$class} extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [];
    }

}

PHP;
    }



    private function resourceStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {$module}Resource extends JsonResource
{

    public function toArray(Request \$request): array
    {
        return parent::toArray(\$request);
    }

}

PHP;
    }



    private function providerStub($module)
    {
        return <<<PHP
<?php

namespace App\Modules\\{$module}\Providers;

use Illuminate\Support\ServiceProvider;

class {$module}ServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        \$this->loadRoutesFrom(
            __DIR__.'/../routes/api.php'
        );


        \$this->loadMigrationsFrom(
            __DIR__.'/../Database/Migrations'
        );

    }

}

PHP;
    }
}