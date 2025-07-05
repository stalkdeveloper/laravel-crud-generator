<?php

namespace StalkArtisan\LaravelCrudGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use StalkArtisan\LaravelCrudGenerator\Commands\MakeCrudCommand;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCrudCommand::class,
            ]);

            $this->publishes([
                $this->getConfigPath() => config_path('crud-generator.php'),
            ], 'crud-generator-config');

            $this->publishes([
                $this->getStubsPath() => base_path('/src/Config/crud-generator'),
            ], 'crud-generator-stubs');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigPath(),
            'crud-generator'
        );
    }

    protected function getConfigPath()
    {
        return dirname(__DIR__, 2).'/src/Config/crud-generator.php';
    }

    protected function getStubsPath()
    {
        return dirname(__DIR__, 2).'/src/Console/Stubs';
    }
    
}