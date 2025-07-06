# Laravel CRUD Generator

[![Packagist Version](https://img.shields.io/packagist/v/stalkdeveloper/laravel-crud-generator)](https://packagist.org/packages/stalkdeveloper/laravel-crud-generator)
[![License](https://img.shields.io/packagist/l/stalkdeveloper/laravel-crud-generator)](https://github.com/stalkdeveloper/laravel-crud-generator/blob/master/LICENSE)
[![Stars](https://img.shields.io/github/stars/stalkdeveloper/laravel-crud-generator)](https://github.com/stalkdeveloper/laravel-crud-generator)

A powerful CRUD generator for Laravel that supports API, Web, and Repository pattern.

## ✨ Features

- Generate CRUD for Web/API/Both
- Repository Pattern support
- Customizable stubs
- Auto route registration
- PSR-4 structure
- Laravel 9+ compatible

## Requirements
    Laravel >= 9.0
    PHP >= 8.0.0

## 🚀 Installation

1. **Install via Composer**

    ```
    composer require stalkdeveloper/laravel-crud-generator
    ```

2. **Publish Configuration and Stubs**

    ```
    php artisan vendor:publish --tag=crud-generator-config
    ```

3. **(Optional) Customize Configuration**

    Edit `config/crud-generator.php` to customize namespaces, paths, and stub locations as per your project structure.

💻 Usage
*To generate a basic Web CRUD:*

# Web CRUD
php artisan make:crud Post --web

# API CRUD 
php artisan make:crud Post --api

# Both Web & API
php artisan make:crud Post --all

# With Repository
php artisan make:crud Post --repo

# Force overwrite
php artisan make:crud Post --force

🏗 Repository Setup
Add to AppServiceProvider.php:

public function register()
{
    $this->app->bind(
        \App\Interfaces\PostInterface::class,
        \App\Repositories\PostRepository::class
    );
}

<details>
<summary>📂 Generated Structure (click to expand)</summary>

app/
├── Models/
│   └── Post.php
├── Http/
│   ├── Controllers/
│   │   ├── PostController.php
│   │   ├── Web/
│   │   │   └── PostController.php
│   │   └── Api/
│   │       ├── PostController.php
│   │       └── ApiBaseController.php
│   ├── Requests/
│   │   ├── PostRequest.php
│   │   ├── Web/
│   │   │   └── PostRequest.php
│   │   └── Api/
│   │       └── PostRequest.php
│   └── Resources/
│       ├── PostResource.php
│       └── PostCollection.php
├── Interfaces/
│   └── PostInterface.php
├── Repositories/
│   └── PostRepository.php
resources/
└── views/
    └── posts/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── show.blade.php
database/
└── migrations/
    └── 202x_xx_xx_xxxxxx_create_posts_table.php

</details>

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/stalkdeveloper/laravel-crud-generator/blob/main/LICENSE.txt) file for details.

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!  
Feel free to check [issues page](https://github.com/stalkdeveloper/laravel-crud-generator/issues) or submit a pull request.

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 📬 Support

If you have any questions or need help, feel free to open an issue or contact me on [LinkedIn](https://www.linkedin.com/in/stalkdeveloper/).

---

Happy Coding! 🚀  
Built with ❤️ by [Sunny Kumar](https://www.linkedin.com/in/stalkdeveloper/)

🌐 [Portfolio](https://stalkdeveloper.github.io/stalkdeveloper/)


