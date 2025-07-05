<?php

namespace StalkArtisan\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {name} 
                            {--api : Generate API CRUD}
                            {--web : Generate Web CRUD}
                            {--repo : Use repository pattern}
                            {--all : Generate both API and Web}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate complete CRUD operations';

    protected $files;
    protected $modelName;
    protected $variableName;
    protected $tableName;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $this->validateConfig();

        $this->modelName = Str::studly($this->argument('name'));
        $this->variableName = Str::camel($this->modelName);
        $this->tableName = Str::snake(Str::pluralStudly($this->modelName));
        $force = $this->option('force');

        $ran = false;

        if ($this->option('all')) {
            $this->generateApiCrud($force);
            $this->generateWebCrud($force, true);
            $ran = true;
        } elseif ($this->option('api')) {
            $this->generateApiCrud($force);
            $ran = true;
        } elseif ($this->option('web')) {
            $this->generateWebCrud($force, true);
            $ran = true;
        } else {
            // Default: web CRUD, root folders
            $this->generateWebCrud($force, false);
            $ran = true;
        }

        if ($ran) {
            if ($this->option('repo')) {
                $this->generateRepositoryFiles($force);
            }
            $this->generateMigration($force);

            $this->newLine(2);
            $this->info('CRUD generation completed!');
            $this->showNextSteps();
        }
    }

    protected function validateConfig()
    {
        $required = ['namespaces.model', 'namespaces.controller', 'paths.model'];

        foreach ($required as $key) {
            if (empty(config("crud-generator.{$key}"))) {
                throw new \RuntimeException("Missing required config: crud-generator.{$key}");
            }
        }
    }

    protected function generateApiCrud($force)
    {
        $this->generateApiBaseController();
        $this->generateModel($force);
        $this->generateApiController($force);
        $this->generateFormRequest('api', $force);
        $this->generateResource($force);
        $this->generateResourceCollection($force);
        $this->addApiRoutes();
    }

    protected function generateWebCrud($force, $inWebFolder = false)
    {
        $this->generateModel($force);
        $this->generateWebController($force, $inWebFolder);
        $this->generateFormRequest('web', $force, $inWebFolder);
        $this->generateViews($force);
        $this->addWebRoutes();
    }

    protected function generateRepositoryFiles($force)
    {
        $this->generateInterface($force);
        $this->generateRepository($force);
    }

    protected function generateModel($force)
    {
        $path = app_path('Models/' . $this->modelName . '.php');
        if (!$force && $this->files->exists($path)) {
            $this->error('Model already exists!');
            return;
        }
        $stub = $this->files->get($this->getStubPath('model.stub'));
        $stub = $this->replacePlaceholders($stub);
        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info('Model created: ' . $path);
    }

    protected function generateApiBaseController()
    {
        $path = app_path('Http/Controllers/Api/ApiBaseController.php');
        if ($this->files->exists($path)) {
            $this->info('API Base Controller already exists: ' . $path);
            return;
        }
        $stub = $this->files->get($this->getStubPath('api/base-controller.stub'));
        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info('API Base Controller created: ' . $path);
    }

    protected function generateApiController($force)
    {
        $path = app_path('Http/Controllers/Api/' . $this->modelName . 'Controller.php');

        if (!$force && $this->files->exists($path)) {
            $this->error('API Controller already exists!');
            return;
        }

        $stub = $this->files->get($this->getStubPath('api/controller.stub'));
        $stub = $this->replacePlaceholders($stub);

        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info('API Controller created: ' . $path);
    }

    protected function generateWebController($force, $inWebFolder = false)
    {
        // Use repository controller stub if repo flag is set
        $stubFile = $this->option('repo') ? 'repository/controller.stub' : 'web/controller.stub';

        if ($inWebFolder) {
            $path = app_path('Http/Controllers/Web/' . $this->modelName . 'Controller.php');
            $namespace = config('crud-generator.namespaces.controller') . '\\Web';
        } else {
            $path = app_path('Http/Controllers/' . $this->modelName . 'Controller.php');
            $namespace = config('crud-generator.namespaces.controller');
        }

        if (!$force && $this->files->exists($path)) {
            $this->error('Web Controller already exists!');
            return;
        }

        $stub = $this->files->get($this->getStubPath($stubFile));
        $stub = str_replace('{{namespace}}', $namespace, $stub);
        $stub = $this->replacePlaceholders($stub);

        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info('Web Controller created: ' . $path);
    }

    protected function generateFormRequest($type, $force, $inWebFolder = false)
    {
        $subfolder = '';
        $requestNamespace = config('crud-generator.namespaces.request');

        if ($type === 'api') {
            $subfolder = 'Api/';
            $requestNamespace .= '\\Api';
        } elseif ($type === 'web' && $inWebFolder) {
            $subfolder = 'Web/';
            $requestNamespace .= '\\Web';
        }

        $stubType = $type . '/request.stub';

        $path = app_path('Http/Requests/' . $subfolder . $this->modelName . 'Request.php');

        if (!$force && $this->files->exists($path)) {
            $this->error(ucfirst($type) . ' Request already exists!');
            return;
        }

        $stub = $this->files->get($this->getStubPath($stubType));
        $stub = str_replace('{{requestNamespace}}', $requestNamespace, $stub);
        $stub = $this->replacePlaceholders($stub);

        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info(ucfirst($type) . ' Request created: ' . $path);
    }

    protected function generateResource($force)
    {
        $path = app_path('Http/Resources/' . $this->modelName . 'Resource.php');

        if (!$force && $this->files->exists($path)) {
            $this->error('Resource already exists!');
            return;
        }

        $stub = $this->files->get($this->getStubPath('api/resource.stub'));
        $stub = $this->replacePlaceholders($stub);

        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info('Resource created: ' . $path);
    }

    protected function generateResourceCollection($force)
    {
        $path = app_path('Http/Resources/' . $this->modelName . 'Collection.php');

        if (!$force && $this->files->exists($path)) {
            $this->error('Resource Collection already exists!');
            return;
        }

        $stub = $this->files->get($this->getStubPath('api/resource-collection.stub'));
        $stub = $this->replacePlaceholders($stub);

        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info('Resource Collection created: ' . $path);
    }

    protected function generateViews($force)
    {
        $views = ['index', 'create', 'edit', 'show'];
        $viewDir = $this->tableName;

        foreach ($views as $view) {
            $path = resource_path("views/{$viewDir}/{$view}.blade.php");
            if (!$force && $this->files->exists($path)) {
                $this->warn("View already exists: {$path}");
                continue;
            }

            $stub = $this->files->get($this->getStubPath("web/{$view}.blade.stub"));
            $stub = $this->replacePlaceholders($stub);

            $this->ensureDirectoryExists(dirname($path));
            $this->files->put($path, $stub);
            $this->info("View created: {$path}");
        }
    }

    protected function generateInterface($force)
    {
        $path = app_path('Interfaces/' . $this->modelName . 'Interface.php');
        if (!$force && $this->files->exists($path)) {
            $this->error('Interface already exists!');
            return;
        }
        $stub = $this->files->get($this->getStubPath('repository/interface.stub'));
        $stub = $this->replacePlaceholders($stub);
        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info("Interface created: {$path}");
    }

    protected function generateRepository($force)
    {
        $path = app_path('Repositories/' . $this->modelName . 'Repository.php');
        if (!$force && $this->files->exists($path)) {
            $this->error('Repository already exists!');
            return;
        }
        $stub = $this->files->get($this->getStubPath('repository/repository.stub'));
        $stub = $this->replacePlaceholders($stub);
        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info("Repository created: {$path}");
    }

    protected function generateMigration($force)
    {
        $migrationName = 'create_' . $this->tableName . '_table';
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$migrationName}.php";
        $path = database_path("migrations/{$filename}");

        if (!$force && $this->files->exists($path)) {
            $this->error("Migration already exists: {$filename}");
            return;
        }

        $stubPath = $this->getStubPath('migration.stub');
        if (!$this->files->exists($stubPath)) {
            $this->error("Migration stub not found at: {$stubPath}");
            return;
        }

        $stub = $this->files->get($stubPath);
        $stub = str_replace(
            ['{{table}}', '{{migrationClass}}'],
            [$this->tableName, 'Create' . Str::studly(Str::plural($this->modelName)) . 'Table'],
            $stub
        );

        $this->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $stub);
        $this->info("Migration created: {$path}");
    }

    protected function addApiRoutes()
    {
        $route = "Route::apiResource('" . $this->tableName . "', " . $this->modelName . "Controller::class);";
        $this->addRouteToFile(base_path('routes/api.php'), $route, 'API');
    }

    protected function addWebRoutes()
    {
        $route = "Route::resource('" . $this->tableName . "', " . $this->modelName . "Controller::class);";
        $this->addRouteToFile(base_path('routes/web.php'), $route, 'Web');
    }

    protected function addRouteToFile($filePath, $route, $type)
    {
        if (!$this->files->exists($filePath)) {
            $this->error("{$type} routes file not found!");
            return;
        }

        $content = $this->files->get($filePath);

        if (str_contains($content, $route)) {
            $this->warn("{$type} route already exists!");
            return;
        }

        $this->files->append($filePath, PHP_EOL . $route);
        $this->info("{$type} route added: {$route}");
    }

    protected function showNextSteps()
    {
        $this->line('Next steps:');

        $this->line('- Run migrations: php artisan migrate');

        if ($this->option('repo')) {
            $this->line('- Bind interface in AppServiceProvider:');
            $this->line("  \$this->app->bind(\\App\\Interfaces\\{$this->modelName}Interface::class, \\App\\Repositories\\{$this->modelName}Repository::class);");
        }

        $this->line('- Customize generated files as needed');
    }

    protected function getStubPath($stubName)
    {
        $customStubPath = base_path(config('crud-generator.stubs.custom').'/'.$stubName);
        $packageStubPath = dirname(__DIR__).'/Console/Stubs/'.$stubName;
        return $this->files->exists($customStubPath) ? $customStubPath : $packageStubPath;
    }

    protected function replacePlaceholders($stub)
    {
        $replacements = [
            '{{namespace}}' => config('crud-generator.namespaces.controller'),
            '{{apiNamespace}}' => config('crud-generator.namespaces.api_controller'),
            '{{modelNamespace}}' => config('crud-generator.namespaces.model'),
            '{{requestNamespace}}' => config('crud-generator.namespaces.request'),
            '{{resourceNamespace}}' => config('crud-generator.namespaces.resource'),
            '{{interfaceNamespace}}'   => config('crud-generator.namespaces.interface'),
            '{{repositoryNamespace}}'  => config('crud-generator.namespaces.repository'),
            '{{model}}' => $this->modelName,
            '{{modelVariable}}' => $this->variableName,
            '{{modelPlural}}' => Str::plural($this->modelName),
            '{{modelPluralVariable}}' => Str::plural($this->variableName),
            '{{modelSnake}}' => Str::snake($this->modelName),
            '{{modelSnakePlural}}' => $this->tableName,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }


    protected function ensureDirectoryExists($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }
}
