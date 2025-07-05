<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default CSS Framework
    |--------------------------------------------------------------------------
    |
    | This option controls the default css framework that will be
    | used when generating web CRUD views. You can set this to
    | either "bootstrap" or "tailwind".
    |
    */
    'css_framework' => 'bootstrap',
    
    /*
    |--------------------------------------------------------------------------
    | Default Namespace
    |--------------------------------------------------------------------------
    |
    | This option controls the default namespace for generated
    | classes. You can override this in the make:crud command.
    |
    */
    'namespaces' => [
        'model'          => 'App\Models',
        'controller'     => 'App\Http\Controllers',
        'api_controller' => 'App\Http\Controllers\Api',
        'request'        => 'App\Http\Requests',
        'resource'       => 'App\Http\Resources',
        'interface'      => 'App\Interfaces',
        'repository'     => 'App\Repositories',
    ],

    'paths' => [
        'model'          => 'app/Models',
        'controller'     => 'app/Http/Controllers',
        'api_controller' => 'app/Http/Controllers/Api',
        'request'        => 'app/Http/Requests',
        'resource'       => 'app/Http/Resources',
        'interface'      => 'app/Interfaces',
        'repository'     => 'app/Repositories',
        'views'          => 'resources/views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Stubs
    |--------------------------------------------------------------------------
    |
    | These options allow you to customize the stubs used for generation.
    |
    */
    'stubs' => [
        'source' => 'vendor/your-package/stubs',
        'custom' => 'stubs/crud-generator',
    ],
];
