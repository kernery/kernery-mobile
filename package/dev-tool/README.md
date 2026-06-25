# Kernery Dev Tool

A Laravel CLI tool providing commands for managing Mobile UI templates and database operations in Kernery Mobile applications.

## Installation

This package is automatically loaded when installed as a dependency of the Kernery Mobile application via Composer:

```bash
composer require kernery/mobile
```

The service provider will auto-register all available CLI commands.

## Available Commands

### Mobile UI Commands

#### Create New Mobile UI Template

Creates a new mobile UI template with your chosen starter kit.

```bash
php artisan kernery:ui:create <name> [type]
```

**Arguments:**
- `<name>` - The name of the UI template (required)
- `[type]` - Starter kit type: `vue` or `blade` (optional, prompts if omitted)

**Options:**
- `--parent=<ui-name>` - Create a child UI inheriting from an existing parent UI
- `--path=<path>` - Custom path for the UI directory

**Examples:**

Create a Blade-based UI:
```bash
php artisan kernery:ui:create myapp blade
```

Create a Vue.js Inertia-based UI:
```bash
php artisan kernery:ui:create myapp vue
```

Create a child UI inheriting from another:
```bash
php artisan kernery:ui:create myapp --parent=another-app
```

#### Activate Mobile UI

Activate an existing mobile UI template.

```bash
php artisan kernery:ui:activate [name]
```

**Arguments:**
- `[name]` - The name of the UI to activate (optional, uses currently active if omitted)

**Options:**
- `--path=<path>` - Custom path to the UI directory

#### Publish Assets

Publish mobile UI assets (CSS, JS, images) to the public directory.

```bash
php artisan kernery:ui:assets:publish [--name=<ui-name>]
```

**Options:**
- `--name=<ui-name>` - Specific UI name to publish
- `--path=<path>` - Custom path to the UI directory

#### Remove Assets

Remove published assets for a mobile UI.

```bash
php artisan kernery:ui:assets:remove <name>
```

**Arguments:**
- `<name>` - The name of the UI whose assets should be removed (required)

**Options:**
- `--path=<path>` - Custom path to the UI directory

#### Remove Mobile UI

Permanently delete a mobile UI template and its configuration.

```bash
php artisan kernery:ui:remove <name> [--force]
```

**Arguments:**
- `<name>` - The name of the UI to remove (required)

**Options:**
- `-f, --force` - Skip confirmation prompt

#### Rename Mobile UI

Rename an existing mobile UI template.

```bash
php artisan kernery:ui:rename <current-name> <new-name>
```

**Arguments:**
- `<current-name>` - Current name of the UI (required)
- `<new-name>` - New name for the UI (required)

### Database Commands

#### Install Application

Perform a fresh installation including migrations, settings migration, and Mobile UI setup.

```bash
php artisan kernery:mobile:install [name]
```

**Arguments:**
- `[name]` - UI template name to install (optional)

This command performs the following operations in sequence:
1. Runs database migrations with clashing migration handling
2. Migrates app settings
3. Cleans up unnecessary database entries
4. Creates a new Mobile UI scaffold
5. Activates the specified UI
6. Publishes all assets

#### Cleanup Database

Remove non-essential records from the database while preserving core system data.

```bash
php artisan kernery:db:cleanup [--force]
```

**Options:**
- `--force, -f` - Skip confirmation prompt and run immediately

## UI Template Structure

When creating a new Mobile UI, the following structure is generated:

```
ui/
├── {mobileUi}/
│   ├── assets/
│   │   ├── js/script.js
│   │   └── sass/style.scss
│   ├── config.stub
│   ├── lang/
│   │   └── en.json
│   ├── mobile-ui.json
│   ├── public/
│   │   ├── css/style.css
│   │   ├── js/script.js
│   │   └── libraries/jquery.min.js
│   ├── routes/
│   │   └── web.stub
│   ├── src/
│   │   └── Http/Controllers/{MobileUi}Controller.php
│   ├── utilities/
│   │   └── app-function.stub
│   └── views/
│       ├── 404.blade.php
│       ├── 500.blade.php
│       ├── 503.blade.php
│       ├── index.blade.php
│       ├── layouts/
│       │   └── default.blade.php
│       ├── partials/
│       │   ├── footer.blade.php
│       │   ├── header.blade.php
│       │   ├── navigation/
│       │   │   ├── bottom-nav.blade.php
│       │   │   └── navbar.blade.php
│       └── ...
```

## Configuration

### Mobile UI Config File

Each UI has a configuration file that defines:

1. **Inheritance** - Set parent UI to inherit from:
   ```php
   'inherit' => 'parent-ui-name',
   ```

2. **Event Listeners** - Hook into Mobile UI events:
   ```php
   'events' => [
       'before' => function ($mobileUi) { ... },
       'beforeRenderMobileUi' => function (MobileUi $mobileUi) { ... },
       'beforeRenderLayout' => ['default' => function ($mobileUi) { ... }],
   ]
   ```

Available events:
- `before` - Called before all operations
- `beforeRenderMobileUi` - Before rendering the UI (for assets, meta tags)
- `beforeRenderLayout` - Before rendering specific layouts

### Registering Custom Routes

Custom routes can be registered in `routes/web.stub`:

```php
use Kernery\MobileUi\Facades\MobileUi;
use Illuminate\Support\Facades\Route;

MobileUi::registerRoutes(function (): void {
    Route::get('hello', 'getHello');
});

// Or use the default Mobile UI routes:
MobileUi::routes();
```

## Inheritance Model

Child UIs can inherit from parent UIs using the `--parent` option when creating. The child will share assets and configurations from the parent unless explicitly overridden.

Note: Parent UIs cannot have children if they are configured with `'inherit' => true`.

## Error Handling

All commands validate input names to ensure they contain only alphanumeric characters and hyphens (`[a-z0-9\-]+`).

Common errors:
- **"Mobile Ui is already exists"** - The UI name you're using is taken
- **"Parent ui does not exist"** - Specified parent UI doesn't exist
- **"Parent ui does not support child ui"** - Parent has inheritance enabled
- **"Only alphabetic characters are allowed"** - Invalid name format

## License

This package is licensed under the MIT License. See [LICENSE](LICENSE) for details.
