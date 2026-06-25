<div align="center">
  <img src="/.github/art/kernery-logo-full-no-bg.png" width="50%" alt="Logo Kernery" style="max-width: 100%;">
</div>

<h3><a href="#requirements">Requirements</a> | <a href="#installation">Installation</a> | <a href="#quick-start">Quick Start</a> | <a href="#project-structure">Project Structure</a> </h3>

---

## Overview

**Kernery Mobile** is a lightweight battery-included core for building mobile apps with NativePHP. Built on top of Laravel, it provides a comprehensive framework for creating cross-platform mobile applications with features like:

- **Mobile UI Management** - Flexible UI rendering with layout composition and asset management
- **NativePHP Integration** - Cross-platform mobile app capabilities
- **Laravel Powerhouse** - Full access to Laravel's ecosystem (Eloquent, Blade, Events, etc.)
- **Modular Architecture** - Composer-based package system for extensibility

---

## Requirements

Before installing Kernery Mobile, ensure your development environment meets these requirements:

### System Requirements

| Requirement | Version | Notes |
|-------------|---------|-------|
| **PHP** | >= 8.2.0 | Required by updater configuration |
| **Composer** | ^2.0+ | For dependency management |
| **XAMPP** | Latest | Recommended for PHP development |
| **cURL extension** | enabled | PHP extension required |
| **GD extension** | enabled | PHP extension required |
| **JSON extension** | enabled | PHP extension required (built-in) |

### Verify Your Environment

Run these commands to verify your setup:

```bash
# Check PHP version
php -v

# Check Composer version
composer --version

# Verify required extensions
php -m | grep -E "curl|gd|json"
```

---

## Installation

### Step 1: Create New Laravel Project

Create a new laravel project

```bash
laravel new example-app

# Read the laravel documentation for proper setup
https://laravel.com/docs/13.x/installation
```
### Step 2: Clone Kernery Source Code

Create a new folder in your Laravel directory:

```bash
# Use this specific folder name because it has been configured in the source code

mkdir mobile
cd mobile
git clone https://github.com/
```

### Step 3: Require Kernery Mobile and Install Dependencies via Composer

The core dependencies are automatically installed when you require the main package but before doing that, you have to setup the project repositories in composer.json<br>

Require the project package

```php
"require": {
    "php": "^8.3",
    "kernery/mobile": "*@dev", // add this line
    ...
}
```

You also need to setup the project use this entry config

```php
...
"minimum-stability": "dev",
    "prefer-stable": true,
    "repositories": [
        {
            "type": "path",
            "url": "./mobile/core"
        },
        {
            "type": "path",
            "url": "./mobile/package/*"
        }
    ]
```
After you're done with the setup above, update your project using the command below. This registers Kernery Mobile into laravel and bootstraps all the necessary files needed to start the project.

```php
composer update
``` 

This will install:
- `nativephp/mobile` - NativePHP mobile support (^3.0)
- `tightenco/ziggy` - Laravel-to-JS route sharing (2.5.3)
- `intervention/image` - Image manipulation library (^3.4)
- `kernery/assets` - Assets manager (@dev)
- `kernery/mobile-ui` - Mobile UI management (@dev)
- `kernery/dev-tool` - CLI development tools (@dev)

### Step 3: Environment Configuration

Create your `.env` file from the example configuration if missing

```bash
# Create environment file (if not already present)
cp .env.example .env 2>/dev/null || touch .env
```

Key environment variables to configure:

```ini
# Application Settings
APP_NAME="My Mobile App"
APP_ENV=local|testing|production
APP_DEBUG=true|false
APP_URL=http://localhost

# Mobile UI Configuration
APP_MOBILE_UI_PUBLIC_NAME=myapp
APPLY_SAFE_AREA_IN_MOBILE=false|true

```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

---

## Quick Start

### Initialize the Application

After installation, run the install command to set up migrations and default UI:

```bash
# Or specify a custom UI name
php artisan kernery:mobile:install myapp
```

This will:
1. Run database migrations
2. Migrate app settings
3. Clean up unnecessary entries
4. Create new Mobile UI scaffold
5. Activate the specified UI
6. Publish all assets

### Start Development Server

```bash
# Using Laravel's built-in server
php artisan serve

# Or using XAMPP Apache
# Navigate to http://localhost/k-mobile/mobile in your browser
```

---

## Project Structure

```
mobile/
├── core/                          # Core framework components
│   ├── main/src/                  # Main application source
│   │   ├── Contracts/            # Interface definitions
│   │   ├── Enums/                # Enumerations
│   │   ├── Events/               # Event classes
│   │   ├── Exceptions/           # Custom exceptions
│   │   ├── Facades/              # Facade classes
│   │   ├── Helpers/              # Helper functions
│   │   ├── Http/                 # HTTP layer (Controllers, Middleware)
│   │   ├── Listeners/            # Event listeners
│   │   ├── Models/               # Eloquent models
│   │   ├── Providers/            # Service providers
│   │   ├── Services/             # Business logic services
│   │   ├── Supports/             # Support classes
│   │   └── Traits/               # Reusable traits
│   ├── setting/src/              # Settings management
│   │   ├── Facades/              # SecureSetting facade
│   │   ├── Http/                 # HTTP handlers for settings
│   │   ├── Models/               # Setting models
│   │   ├── Providers/            # Service provider
│   │   ├── Repositories/         # Data repositories
│   │   └── Supports/             # Support classes
│   ├── main/updater.json         # Application updater configuration
│   └── support/src/              # Support utilities
│       ├── Http/                 # HTTP utilities
│       ├── Providers/            # Service providers
│       ├── Repositories/         # Data repositories
│       └── Services/             # Utility services
├── package/                       # Third-party packages
│   ├── assets/                   # Assets manager package
│   │   └── src/
│   │       ├── Assets.php        # Main asset class
│   │       ├── HtmlBuilder.php   # HTML builder helper
│   │       └── Facades/          # Asset facade
│   ├── dev-tool/                 # Development CLI tools package
│   └── mobile-ui/                # Mobile UI management package
├── ui/                            # Custom UI templates (your apps and codes go here)
│   └── {mobileUi}/               # Each app has its own directory
│       ├── assets/               # Source assets (JS, SASS)
│       ├── views/                # Blade templates
│       │   ├── layouts/          # Layout files
│       │   ├── partials/         # Reusable components
│       │   └── {view}.blade.php  # View templates
│       ├── mobile-ui.json        # UI template metadata
│       └── routes/               # Custom route definitions
├── database/                      # Database files
│   └── database.sqlite            # SQLite database (default)
└── config/                        (generated after artisan commands)
```

---

## Package Details

### Kernery Assets (`kernery/assets`)

An assets manager for handling CSS, JavaScript, and other static resources.

**Features:**
- Asset container system with named groups
- Dependency management for scripts
- Automatic versioning and caching
- Blade directive support

**Usage:**
```php
// Add a stylesheet
MobileUi::asset()->container('styles')->style('main', 'css/main.css');

// Add JavaScript with dependencies
MobileUi::asset()
    ->script('jquery', 'js/jquery.js')
    ->script('app', 'js/app.js', ['jquery']);

// Render assets in your layout
echo MobileUi::asset()->styles();
echo MobileUi::asset()->scripts();
```

### Kernery Mobile UI (`kernery/mobile-ui`)

A flexible UI management system for mobile applications.

**Features:**
- Multiple UI templates (themes) with inheritance
- Region-based content composition
- Layout and partial system
- Asset containers
- Event hooks for customization

**Basic Usage:**
```php
// Render a page with specific UI and layout
return MobileUi::uses('myapp')
    ->layout('default')
    ->scope('index')
    ->render();

// Set region content
MobileUi::set('header', '<h1>Welcome</h1>');
MobileUi::append('sidebar', '<div>Sidebar</div>');

// Check if region exists
if (MobileUi::has('footer')) { ... }
```

### Kernery Dev Tool (`kernery/dev-tool`)

CLI tools for managing Mobile UI templates and database operations.

**Available Commands:**

| Command | Description |
|---------|-------------|
| `kernery:ui:create <name> [type]` | Create new Mobile UI template |
| `kernery:ui:activate [name]` | Activate existing UI |
| `kernery:ui:assets:publish [--name=...]` | Publish assets to public |
| `kernery:ui:assets:remove <name>` | Remove published assets |
| `kernery:ui:remove <name> [--force]` | Delete UI template |
| `kernery:ui:rename <old> <new>` | Rename UI template |
| `kernery:mobile:install [name]` | Fresh installation |
| `kernery:db:cleanup [--force]` | Clean database |

---

## Configuration

### Mobile UI Configuration (`config/general.php`)

```php
return [
    // Default UI template to use
    'mobileUiDefault' => 'default',

    // Default layout name
    'layoutDefault' => 'default',

    // Root path for UI templates
    'mobileUiDir' => 'ui',

    // Directory structure within each UI template
    'containerDir' => [
        'layout' => 'views/layouts',
        'asset' => '',
        'partial' => 'views/partials',
        'view' => 'views',
    ],

    // Event hooks for customization
    'events' => [],

    // Public UI name override (via environment)
    'public_mobile_ui_name' => env('APP_MOBILE_UI_PUBLIC_NAME'),

    // Apply safe area styles to mobile
    'apply_safe_area_in_mobile' => env('APPLY_SAFE_AREA_IN_MOBILE', false),
];
```

### UI Template Configuration (`ui/{template}/config.php`)

Each UI template can have its own configuration:

```php
return [
    // Inherit from parent UI
    'inherit' => 'parent-ui-name',

    // Event listeners
    'events' => [
        'before' => function ($mobileUi) {
            $mobileUi->share('appName', 'My App');
        },
        'asset' => function ($asset) {
            $asset->style('reset', 'css/reset.css');
        },
    ],
];
```

---

## Development Tips

### Debug Mode

Enable debug badge to show current UI, layout, and view information:

```php
APP_DEBUG=true
```

The badge only appears when not running in console mode.

### Custom Routes

Register custom routes for your Mobile UI:

```php
use Kernery\MobileUi\Facades\MobileUi;
use Illuminate\Support\Facades\Route;

MobileUi::registerRoutes(function (): void {
    Route::get('hello', 'getHello');
});

// Or use the default Mobile UI routes
MobileUi::routes();
```

### Inheritance Model

Child UIs can inherit from parent UIs:

1. Create child with `--parent=parent-ui-name`
2. Set `'inherit' => 'parent-ui-name'` in config
3. Child shares assets and configurations unless overridden

**Note:** Parent UIs cannot have children if configured with `'inherit' => true`.

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| **"Mobile Ui is already exists"** | Use a different name or remove existing UI first |
| **"Parent ui does not exist"** | Verify parent UI name in the `--parent` option |
| **"Parent ui does not support child ui"** | Parent has inheritance enabled (`'inherit' => true`) |
| **Only alphabetic characters allowed** | Use only lowercase letters, numbers, and hyphens |
| **Assets not publishing** | Run `php artisan kernery:ui:assets:publish` |

### Checking Installed Packages

```bash
composer show | grep kernery
composer show | grep nativephp
```

### Clearing Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Contributing

Please read the [CONTRIBUTING.md](CONTRIBUTING.md) file for guidelines on:

- Code style (PSR-2, enforced via PHP Code Sniffer)
- Adding tests before submitting patches
- Documenting behavioral changes
- Creating focused pull requests per feature

---

## License

This project is licensed under the [MIT License](LICENSE.md). See the license file for full details.

---

## Additional Resources

- **Official Documentation**: https://docs.kernery.com
- **Package Versions**: Check individual README files in `package/` directory
- **Changelog**: See [CHANGELOG.md](CHANGELOG.md) and sub-package CHANGELOGs
- **Security Issues**: Report to contact-jade@kernery.com

---

*Built with ❤️ using Laravel and NativePHP*
