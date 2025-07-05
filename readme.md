src/
├── Commands/
│   └── MakeCrudCommand.php
├── Console/
│   └── Stubs/
│       ├── api/
│       │   ├── controller.stub
│       │   ├── request.stub
│       │   ├── resource.stub
│       │   └── resource-collection.stub
│       ├── repository/
│       │   ├── controller.stub
│       │   ├── interface.stub
│       │   └── repository.stub
│       ├── web/
│       │   ├── controller.stub
│       │   ├── request.stub
│       │   ├── index.blade.stub
│       │   ├── create.blade.stub
│       │   ├── edit.blade.stub
│       │   └── show.blade.stub
│       ├── request.stub       <-- New request.stub for common use
│       ├── model.stub         <-- New model.stub for common use
│       └── migration.stub     <-- New migration.stub for common use
├── Providers/
│   └── CrudGeneratorServiceProvider.php
├── Config/
│   └── crud-generator.php
└── helpers.php


php artisan vendor:publish --tag=crud-generator-config

