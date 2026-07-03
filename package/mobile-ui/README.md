# Mobile UI Package

A Laravel-based mobile UI management system that enables flexible ui rendering, layout composition, and asset management for mobile applications.

## Overview

This package provides a robust framework for managing multiple UI templates (themes) with support for inheritance, regions, partials, and assets. It integrates seamlessly with Laravel's view and event systems.

## Installation

The package is installed via Composer:

```json
{
    "require": {
        "kernery/mobile-ui": "*@dev"
    }
}
```

After installation, the service providers will be automatically registered.

## Configuration

Publish or copy the config file from `config/general.php`:

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

## Project Structure

```
mobile-ui/
├── config/                 # Package configuration files
│   └── general.php        # Main configuration
├── resources/             # Views and assets
│   ├── errors/           # Error page templates (404, 500, 503)
│   ├── partials/         # Shared partials (header, footer)
│   ├── debug/            # Debug badge view
│   └── lang/             # Translations
├── routes/                # Route definitions
│   ├── public.php        # Public route bindings
│   └── inertia.php       # Inertia.js route bindings
├── src/                   # Source code
│   ├── Contracts/        # Interface definitions
│   ├── Events/           # Event classes
│   ├── Exceptions/       # Custom exceptions
│   ├── Facades/          # Facade classes
│   ├── Http/Controllers/ # Request handlers
│   ├── Listeners/        # Event listeners
│   ├── Providers/        # Service providers
│   ├── Services/         # Business logic
│   └── Supports/          # Helper classes
├── helpers/               # Global helper functions
└── public/                # Published assets
```

## UI Template Structure

Each UI template (theme) should follow this structure:

```
ui/{template-name}/
├── views/
│   ├── layouts/          # Layout files (default.blade.php)
│   ├── partials/         # Partial components
│   └── {view}.blade.php  # View templates for routes
├── mobile-ui.json        # Template metadata
├── config.php            # Optional template-specific config
└── public/               # Assets to publish (optional)
```

### Example: `mobile-ui.json`

```json
{
    "name": "Sample App",
    "version": "1.0.0",
    "inherit": "default",
    "namespace": "App\\\\UI\\\\SampleApp"
}
```

## Usage

### Basic Rendering

Use the `MobileUi` facade or helper function:

```php
// Using Facade
return MobileUi::uses('sample-app')
    ->layout('app')
    ->scope('index')
    ->render();

// Using helper
return mobile_ui('sample-app', 'app')->scope('index')->render();
```

### Setting Regions

Define content for specific regions in your views:

```php
MobileUi::set('header', '<h1>Welcome</h1>');
MobileUi::append('sidebar', '<div>Sidebar content</div>');
MobileUi::prepend('footer', '<div>Footer prepend</div>');
```

### Accessing Regions

```php
// Check if region exists
if (MobileUi::has('header')) { ... }

// Get region content
$header = MobileUi::get('header');
$header = MobileUi::place('header', 'Default content');

// Content is reserved for the main template output
$content = MobileUi::content();
```

### Asset Management

The package provides an asset container system:

```php
// Add CSS file
MobileUi::asset()->container('styles')->style('main', 'css/main.css');

// Add JavaScript with dependencies
MobileUi::asset()->script('jquery', 'js/jquery.js')
    ->script('app', 'js/app.js', ['jquery']);

// Render assets
echo MobileUi::asset()->styles();
echo MobileUi::asset()->scripts();
```

Named containers for organized asset grouping:

```php
MobileUi::asset()
    ->container('before_header')->style('mobile', 'css/mobile.css')
    ->container('header')->script('analytics', 'js/analytics.js');
```

### Layouts and Partials

Use layouts to define page structure:

```php
MobileUi::layout('app')->of('views.dashboard')->render();
```

Load partials within templates:

```php
// Load from current UI template
$partial = MobileUi::partial('navigation');

// With data
$data = ['user' => $user];
$partial = MobileUi::partial('sidebar', $data);

// Watch partial (fallback to app views)
$partial = MobileUi::watchPartial('components.footer');
```

### Inheritance

UI templates can inherit from parent templates:

```json
{
    "inherit": "default"
}
```

Child templates inherit assets, configurations, and view paths from parents.

### Event System

Hook into the UI lifecycle:

```php
// In config or service provider
'events' => [
    'before' => function ($ui) {
        $ui->share('appName', 'My App');
    },
    'after' => function ($ui) {
        // Cleanup or logging
    },
    'asset' => function ($asset) {
        // Add default assets
        $asset->style('reset', 'css/reset.css');
    },
],
```

Available events: `before`, `appendBefore`, `after`, `asset`, `beforeRenderMobileUi`, `beforeRenderLayout.{name}`.

### Page Templates

Register available page templates for selection:

```php
register_page_template([
    'landing' => 'Landing Page',
    'dashboard' => 'Dashboard',
]);
```

Access registered templates:

```php
$templates = \Kernery\MobileUi\Supports\Template::getPageTemplates();
```

## Debug Mode

Enable debug badge in development to show current UI, layout, and view information:

```php
MobileUi::scope('index')->withDebugBadge()->render();
```

The debug badge displays when `app()->runningInConsole()` is false.

## Service Providers

Register providers in `config/app.php`:

```php
'providers' => [
    Kernery\MobileUi\Providers\MobileUiServiceProvider::class,
    Kernery\MobileUi\Providers\RouteServiceProvider::class,
],
```

Aliases are automatically registered:

- `MobileUi` — Main UI facade
- `MobileUiManager` — Manager facade

## Customization Hooks

Filter points for extending functionality:

```php
// Modify body attributes
add_filter(MOBILE_UI_BODY_ATTRIBUTES, function ($attributes) {
    $attributes['class'] .= ' custom-class';
    return $attributes;
}, 10);

// Add to header meta
add_filter(MOBILE_UI_FRONT_META, function ($html) {
    return $html . '<meta name="custom" content="value">';
});

// Customize error view paths
add_filter(GET_HTTP_EXCEPTION_VIEW, function ($view, $exception) {
    if (view()->exists('mobile-ui::views.' . $exception->getStatusCode())) {
        return 'mobile-ui::views.' . $exception->getStatusCode();
    }
    return $view;
}, 10);

// List available icons
add_filter(MOBILE_UI_LIST_ICONS, function ($icons) {
    return array_merge($icons, ['custom-icon']);
});
```

## API Reference

### MobileUi Facade Methods

| Method | Description |
|--------|-------------|
| `uses($name)` / `mobileUi($name)` | Set UI template name |
| `layout($name)` | Set layout name |
| `scope($view, $data)` | Render view in content region |
| `of($view, $data)` | Load and render view |
| `load($view, $data)` | Load from custom path |
| `set($region, $content)` | Set region content |
| `append($region, $content)` | Append to region |
| `prepend($region, $content)` | Prepend to region |
| `get($region, $default)` | Get region content |
| `has($region)` | Check region exists |
| `bind($var, $callback)` | Bind variable to views |
| `partial($name, $data)` | Load partial view |
| `composer($view, $callback)` | Hook into view rendering |
| `asset()` | Get asset instance |
| `render($status = 200)` | Render and return response |
| `withDebugBadge()` | Add debug badge for development |

### Asset Methods

| Method | Description |
|--------|-------------|
| `add($name, $source, $deps, $attrs)` | Add CSS/JS file |
| `style($name, $source, $deps, $attrs)` | Add stylesheet |
| `script($name, $source, $deps, $attrs)` | Add JavaScript |
| `styles()` / `scripts()` | Render all assets of type |
| `container($name)` | Get named asset container |

## Exception Handling

Custom exceptions are thrown for common errors:

- `UnknownMobileUiException` — UI template not found
- `UnknownLayoutFileException` — Layout file missing
- `UnknownPartialFileException` — Partial view not found
- `UnknownViewFileException` — View file not found

## CLI Helpers

```php
// Get path to UI templates directory
ui_path('sample-app/views/layouts');

// Register page template
register_page_template(['custom' => 'Custom Layout']);
```

## License

MIT License. See [LICENSE](LICENSE) for details.
