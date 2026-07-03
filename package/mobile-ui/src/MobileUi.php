<?php

namespace Kernery\MobileUi;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Factory;
use Kernery\Main\Facades\Html;
use Kernery\Main\Facades\MainHelper;
use Kernery\MobileUi\Contracts\MobileUi as MobileUiContract;
use Kernery\MobileUi\Exceptions\UnknownMobileUiException;
use Kernery\MobileUi\Exceptions\UnknownPartialFileException;
use Symfony\Component\HttpFoundation\Cookie;

class MobileUi implements MobileUiContract
{
    public static string $namespace = 'ui';

    protected array $mobileUiConfig = [];

    protected ?string $mobileUi = null;

    protected ?string $inheritMobileUi = null;

    protected string $layout;

    protected string $content;

    protected array $regions = [];

    protected array $arguments = [];

    protected array $bindings = [];

    protected ?Cookie $cookie = null;

    protected array $widgets = [];

    protected array $bodyAttributes = [];

    public function __construct(
        protected Repository $config,
        protected Dispatcher $events,
        protected Factory $view,
        protected Asset $asset,
        protected Filesystem $files,
    ) {
        $this->uses($this->getMobileUiName())->layout('default');
    }

    public function layout(string $layout): self
    {
        // If layout name is not set, so use default from config.
        if ($layout) {
            $this->layout = $layout;
        }

        return $this;
    }

    /**
     * Alias of ui method.
     */
    public function uses(?string $mobileUi = null): self
    {
        return $this->mobileUi($mobileUi);
    }

    public function resolvePathException(string $path = '/')
    {
        if (request()->path() !== $path) {

            throw new HttpResponseException(redirect($path));
        }
    }

    /**
     * Set up a ui name.
     */
    public function mobileUi(?string $mobileUi = null): self
    {
        // If ui name is not set, so use default from config.
        if ($mobileUi) {
            $this->mobileUi = $mobileUi;
        }

        // Is mobile ui ready? check and handle error gracefully.
        if (! $this->exists($mobileUi) && ! app()->runningInConsole()) {

            $this->resolvePathException();
        }

        $this->inheritMobileUi = $this->getConfig('inherit');

        // If inherit mobileUi is set and not exists, so throw exception.
        if ($this->hasInheritMobileUi() && ! $this->exists($this->getInheritMobileUi())) {
            throw new UnknownMobileUiException('Parent mobile ui [' . $this->getInheritMobileUi() . '] not found.');
        }

        // Add location to look up view.
        $this->addPathLocation($this->path());

        // Fire event before set up a mobileUi.
        $this->fire('before', $this);

        // Before from a public mobileUi config.
        $this->fire('appendBefore', $this);

        // Add asset path to asset container.
        $this->registerAssetsPath();

        return $this;
    }

    protected function registerAssetsPath(): void
    {
        $assetsPath = $this->getMobileAssetsPath();

        $this->asset->addPath($assetsPath . '/' . $this->getConfig('containerDir.asset'));
    }

    public function hasInheritMobileUi(): bool
    {
        return $this->inheritMobileUi !== null;
    }

    public function getInheritMobileUi(): ?string
    {
        return $this->inheritMobileUi;
    }

    protected function getMobileUiAssetsPath(): string
    {
        $publicMobileUiName = $this->getPublicMobileUiName();

        $currentMobileUi = $this->getMobileUiName();

        $assetPath = $this->path();

        if ($publicMobileUiName != $currentMobileUi) {
            $assetPath = substr($assetPath, 0, -strlen($currentMobileUi)) . $publicMobileUiName;
        }

        return $assetPath;
    }

    /**
     * Check ui exists.
     */
    public function exists(?string $mobileUi): bool
    {
        $path = mobile_path($this->path($mobileUi)) . '/';

        return File::isDirectory($path);
    }

    public function path(?string $forceMobileUiName = null): string
    {
        $mobileUiDir = $this->getConfig('mobileUiDir');

        $mobileUi = $forceMobileUiName ?: $this->mobileUi;

        return $mobileUiDir . '/' . $mobileUi;
    }

    /**
     * Get ui config.
     */
    public function getConfig(?string $key = null): mixed
    {
        if (! $this->mobileUiConfig) {
            $this->mobileUiConfig = $this->config->get('package.mobile-ui.general', []);
        }

        $this->loadConfigFromMobileUi($this->mobileUi);

        $this->mobileUiConfig = $this->evaluateConfig($this->mobileUiConfig);

        return empty($key) ? $this->mobileUiConfig : Arr::get($this->mobileUiConfig, $key);
    }

    public function getInheritConfig(?string $key = null): mixed
    {
        if (! $this->hasInheritMobileUi()) {
            return null;
        }

        $this->loadConfigFromMobileUi($mobileUi = $this->getInheritMobileUi());

        if (! isset($this->mobileUiConfig['ui'][$mobileUi])) {
            return null;
        }

        $config = $this->mobileUiConfig['ui'][$mobileUi];

        return empty($key) ? $config : Arr::get($config, $key);
    }

    protected function loadConfigFromMobileUi(string $mobileUi): void
    {
        // Config inside a public ui.
        // This config having buffer by array object.
        if ($mobileUi && ! isset($this->mobileUiConfig['ui'][$mobileUi])) {
            $this->mobileUiConfig['ui'][$mobileUi] = [];

            // Require public ui config.
            $minorConfigPath = ui_path($mobileUi . '/config.php');

            if ($this->files->exists($minorConfigPath)) {
                $this->mobileUiConfig['ui'][$mobileUi] = $this->files->getRequire($minorConfigPath);
            }
        }
    }

    /**
     * Evaluate config.
     *
     * Config minor is at public folder [ui]/config.php,
     * they can be overridden package config.
     */
    protected function evaluateConfig(array $config): array
    {
        if (! isset($config['ui'][$this->mobileUi])) {
            return $config;
        }

        // Config inside a public ui.
        $minorConfig = $config['ui'][$this->mobileUi];

        // Before event is special case, It's combination.
        if (isset($minorConfig['events']['before'])) {
            $minorConfig['events']['appendBefore'] = $minorConfig['events']['before'];
            unset($minorConfig['events']['before']);
        }

        // Merge two config into one.
        $config = array_replace_recursive($config, $minorConfig);

        // Reset ui config.
        $config['ui'][$this->mobileUi] = [];

        return $config;
    }

    /**
     * Add location path to look up.
     */
    protected function addPathLocation(string $location): void
    {
        // First path is in the selected ui.
        $hints[] = mobile_path($location);

        // This is nice feature to use inherit from another.
        if ($this->hasInheritMobileUi()) {
            $inheritPath = mobile_path($this->path($this->getInheritMobileUi()));

            if ($this->files->isDirectory($inheritPath)) {
                $hints[] = $inheritPath;
            }
        }

        // Add namespace with hinting paths.
        $this->view->addNamespace($this->getMobileUiNamespace(), $hints);
    }

    public function getMobileUiNamespace(string $path = ''): string
    {
        // Namespace relate with the ui name.
        $namespace = static::$namespace . '.' . $this->getMobileUiName();

        if ($path) {
            return $namespace . '::' . $path;
        }

        return $namespace;
    }

    public function getMobileUiName(): string
    {
        if ($this->mobileUi) {
            return $this->mobileUi;
        }

        $mobileUi = setting('mobile-ui');

        if ($mobileUi) {
            return $mobileUi;
        }

        return Arr::first(MainHelper::scanFolder(ui_path())) ?: 'default';
    }

    public function setMobileUiName(string $mobileUi): self
    {
        $this->mobileUi = $mobileUi;

        return $this;
    }

    public function getPublicMobileUiName(): string
    {
        $mobileUi = $this->getMobileUiName();

        $publicMobileUiName = $this->getConfig('public_mobile_ui_name');

        if ($publicMobileUiName && $publicMobileUiName != $mobileUi) {
            return $publicMobileUiName;
        }

        return $mobileUi;
    }

    /**
     * Fire event to config listener.
     */
    public function fire(string $event, string | array | callable | null | object $args): void
    {
        if ($this->hasInheritMobileUi()) {
            $this->asset->isInheritMobileUi();

            $onEvent = $this->getInheritConfig('events.' . $event);

            if ($onEvent instanceof Closure) {
                $onEvent($args);
            }

            $this->asset->isInheritMobileUi(false);
        }

        $onEvent = $this->getConfig('events.' . $event);

        if ($onEvent instanceof Closure) {
            $onEvent($args);
        }
    }

    /**
     * Append a place to existing region.
     */
    public function append(string $region, string $value): self
    {
        return $this->appendOrPrepend($region, $value);
    }

    /**
     * Append or prepend existing region.
     */
    protected function appendOrPrepend(string $region, string $value, string $type = 'append'): self
    {
        // If region not found, create a new region.
        if (isset($this->regions[$region])) {
            switch ($type) {
                case 'prepend':
                    $this->regions[$region] = $value . $this->regions[$region];

                    break;

                case 'append':
                    $this->regions[$region] .= $value;

                    break;
            }
        } else {
            $this->set($region, $value);
        }

        return $this;
    }

    /**
     * Set a place to regions.
     */
    public function set(string $region, mixed $value): self
    {
        // Content is reserve region for render sub-view.
        if ($region != 'content') {
            $this->regions[$region] = $value;
        }

        return $this;
    }

    /**
     * Prepend a place to existing region.
     */
    public function prepend(string $region, string $value): self
    {
        return $this->appendOrPrepend($region, $value, 'prepend');
    }

    /**
     * Binding data to view.
     */
    public function bind(string $variable, string | array | callable | null $callback = null)
    {
        $name = 'bind.' . $variable;

        // If callback pass, so put in a queue.
        if (! empty($callback)) {
            // Preparing callback in to queues.
            $this->events->listen($name, function () use ($callback) {
                return ($callback instanceof Closure) ? $callback() : $callback;
            });
        }

        // Passing variable to closure.
        $events = &$this->events;
        $bindings = &$this->bindings;

        // Buffer processes to save request.
        return Arr::get($this->bindings, $name, function () use (&$events, &$bindings, $name) {
            $response = current($events->dispatch($name));
            Arr::set($bindings, $name, $response);

            return $response;
        });
    }

    /**
     * Check having binded data.
     */
    public function binded(string $variable): bool
    {
        $name = 'bind.' . $variable;

        return $this->events->hasListeners($name);
    }

    /**
     * Assign data across all views.
     */
    public function share(string $key, $value)
    {
        return $this->view->share($key, $value);
    }

    /**
     * The same as "partial", but having prefix layout.
     */
    public function partialWithLayout(string $view, array $args = []): ?string
    {
        $view = $this->getLayoutName() . '.' . $view;

        return $this->partial($view, $args);
    }

    public function getLayoutName(): string
    {
        return $this->layout;
    }

    /**
     * Set up a partial.
     */
    public function partial(string $view, array $args = []): ?string
    {
        $partialDir = $this->getMobileUiNamespace($this->getConfig('containerDir.partial'));

        return $this->loadPartial($view, $partialDir, $args);
    }

    /**
     * Load a partial
     */
    public function loadPartial(string $view, string $partialDir, array $args): ?string
    {
        $path = $partialDir . '.' . $view;

        if (! $this->view->exists($path)) {
            throw new UnknownPartialFileException('Partial view [' . $view . '] not found.');
        }

        $partial = $this->view->make($path, $args)->render();
        $this->regions[$view] = $partial;

        return $this->regions[$view];
    }

    /**
     * Watch and set up a partial from anywhere.
     *
     * This method will first try to load the partial from current ui. If partial
     * is not found in ui then it loads it from app (i.e. app/views/partials)
     */
    public function watchPartial(string $view, array $args = []): ?string
    {
        try {
            return $this->partial($view, $args);
        } catch (UnknownPartialFileException) {
            $partialDir = $this->getConfig('containerDir.partial');

            return $this->loadPartial($view, $partialDir, $args);
        }
    }

    /**
     * Hook a partial before rendering.
     */
    public function partialComposer(string | array $view, Closure $callback, ?string $layout = null): void
    {
        $partialDir = $this->getConfig('containerDir.partial');

        $view = (array) $view;

        // Partial path with namespace.
        $path = $this->getMobileUiNamespace($partialDir);

        // This code support partialWithLayout.
        if (! empty($layout)) {
            $path = $path . '.' . $layout;
        }

        $view = array_map(function ($item) use ($path) {
            return $path . '.' . $item;
        }, $view);

        $this->view->composer($view, $callback);
    }

    /**
     * Hook a partial before rendering.
     */
    public function composer(string | array $view, Closure $callback, ?string $layout = null): void
    {
        $partialDir = $this->getConfig('containerDir.view');

        if (! is_array($view)) {
            $view = [$view];
        }

        // Partial path with namespace.
        $path = $this->getMobileUiNamespace($partialDir);

        // This code support partialWithLayout.
        if (! empty($layout)) {
            $path = $path . '.' . $layout;
        }

        $view = array_map(function ($item) use ($path) {
            return $path . '.' . $item;
        }, $view);

        $this->view->composer($view, $callback);
    }

    /**
     * Render a region.
     */
    public function place(string $region, ?string $default = null): ?string
    {
        return $this->get($region, $default);
    }

    /**
     * Render a region.
     */
    public function get(string $region, ?string $default = null)
    {
        if ($this->has($region)) {
            return $this->regions[$region];
        }

        return $default ?: '';
    }

    /**
     * Check region exists.
     */
    public function has(string $region): bool
    {
        return isset($this->regions[$region]);
    }

    /**
     * Place content in sub-view.
     */
    public function content(): ?string
    {
        return $this->regions['content'];
    }

    /**
     * Return asset instance.
     */
    public function asset(): Asset | AssetContainer
    {
        return $this->asset;
    }

    /**
     * The same as "of", but having prefix layout.
     */
    public function ofWithLayout(string $view, array $args = []): self
    {
        $view = $this->getLayoutName() . '.' . $view;

        return $this->of($view, $args);
    }

    /**
     * Set up a content to template.
     */
    public function of(string $view, array $args = []): self
    {
        $this->fireEventGlobalAssets();

        // Keeping arguments.
        $this->arguments = $args;

        $content = $this->view->make($view, $args)->render();

        // View path of content.
        $this->content = $view;

        // Set up a content regional.
        $this->regions['content'] = $content;

        return $this;
    }

    /**
     * Container view.
     *
     * Using a container module view inside a ui, this is
     * useful when you separate a view inside a ui.
     */
    public function scope(string $view, array $args = [], $default = null)
    {
        $viewDir = $this->getConfig('containerDir.view');

        // Add namespace to find in a ui path.
        $path = $this->getMobileUiNamespace($viewDir . '.' . $view);

        if ($this->view->exists($path)) {
            return $this->setUpContent($path, $args);
        }

        if (! empty($default)) {
            return $this->of($default, $args);
        }

        $this->handleViewNotFound($path);
    }

    /**
     * Set up a content to template.
     */
    public function setUpContent(string $view, array $args = []): self
    {
        $this->fireEventGlobalAssets();

        // Keeping arguments.
        $this->arguments = $args;

        $content = $this->view->make($view, $args)->render();

        // View path of content.
        $this->content = $view;

        // Set up a content regional.
        $this->regions['content'] = $content;

        return $this;
    }

    protected function handleViewNotFound(string $path): void
    {
        if (app()->isLocal() && app()->hasDebugModeEnabled()) {
            $path = str_replace($this->getMobileUiNamespace(), $this->getMobileUiName(), $path);
            $file = str_replace('::', '/', str_replace('.', '/', $path));
            dd(
                'This mobile ui has not supported this view, please create file "' . ui_path(
                    $file
                ) . '.blade.php" to render this page!'
            );
        }

        abort(404);
    }

    /**
     * Load subview from direct path.
     */
    public function load(string $view, array $args = []): self
    {
        $view = ltrim($view, '/');

        $segments = explode('/', str_replace('.', '/', $view));

        // Pop file from segments.
        $view = array_pop($segments);

        // Custom directory path.
        $pathOfView = app('path.base') . '/' . implode('/', $segments);

        // Add temporary path with a hint type.
        $this->view->addNamespace('custom', $pathOfView);

        return $this->setUpContent('custom::' . $view, $args);
    }

    /**
     * Get all arguments assigned to content.
     */
    public function getContentArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get a argument assigned to content.
     */
    public function getContentArgument(string $key, $default = null)
    {
        return Arr::get($this->arguments, $key, $default);
    }

    /**
     * Checking content argument existing.
     */
    public function hasContentArgument(string $key): bool
    {
        return isset($this->arguments[$key]);
    }

    /**
     * Find view location.
     */
    public function location(bool $realPath = false): ?string
    {
        if ($this->view->exists($this->content)) {
            return $realPath ? $this->view->getFinder()->find($this->content) : $this->content;
        }

        return null;
    }

    /**
     * Return a template with content.
     */
    public function render(int $statusCode = 200): Response
    {
        // Fire the event before render.
        $this->fire('after', $this);

        // Flush asset that need to serve.
        $this->asset->flush();

        // Layout directory.
        $layoutDir = $this->getConfig('containerDir.layout');

        $path = $this->getMobileUiNamespace($layoutDir . '.' . $this->layout);

        if (! $this->view->exists($path)) {
            $this->handleViewNotFound($path);
        }

        $content = $this->view->make($path)->render();

        // Append status code to view.
        $content = new Response($content, $statusCode);

        // Having cookie set.
        if ($this->cookie) {
            $content->withCookie($this->cookie);
        }

        $content->withHeaders([
            'app-version' => get_app_version(),
        ]);

        return $content;
    }

    /**
     * Append a debug badge to content.
     */
    public function withDebugBadge(): self
    {
        $this->asset()->container('header')->usePath()->add('debug', 'package/mobile-ui/css/debug.css');

        if (! App::hasDebugModeEnabled()) {
            return $this;
        }

        $badge = $this->view->make('mobile-ui::debug.badge', [
            'mobileUi' => $this->getMobileUiName(),
            'layout' => $this->getLayoutName(),
            'content' => $this->content,
            'arguments' => $this->arguments,
        ])->render();

        $this->regions['content'] = ($this->regions['content'] ?? '') . $badge;

        return $this;
    }

    public function header(): string
    {
        return $this->view->make('mobile-ui::partials.header')->render();
    }

    public function footer(): string
    {
        return $this->view->make('mobile-ui::partials.footer')->render();
    }

    /**
     * Magic method for set, prepend, append, has, get.
     */
    public function __call(string $method, array $parameters = [])
    {
        $callable = preg_split('|[A-Z]|', $method);

        if (in_array($callable[0], ['set', 'prepend', 'append', 'has', 'get'])) {
            $value = lcfirst(preg_replace('|^' . $callable[0] . '|', '', $method));
            array_unshift($parameters, $value);

            return call_user_func_array([$this, $callable[0]], $parameters);
        }

        return trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
    }

    public function routes(): void
    {
        require package_path('mobile-ui/routes/public.php');
    }

    /**
     * Register routes for mobile ui.
     *
     * This method will automatically apply "web" and "core" middleware to the routes
     * defined in the closure. You can also apply additional middleware by using the
     * "apply_filters" function with the "APP_FILTER_GROUP_PUBLIC_ROUTE" filter.
     *
     * @param  Closure|callable  $closure  The closure that defines the routes for the mobile UI.
     * @return Router The router instance with the registered routes.
     */
    public function registerRoutes(Closure | callable $closure): Router
    {
        return Route::group(['middleware' => ['web', 'core']], function () use ($closure) {
            Route::group(apply_filters(APP_FILTER_GROUP_PUBLIC_ROUTE, []), fn () => $closure());
        });
    }

    public function loadView(string $view): string
    {
        return $this->view->make($this->getMobileUiNamespace('views') . '.' . $view)->render();
    }

    public function getStyleIntegrationPath(): string
    {
        return public_path($this->getMobileAssetsPath() . '/css/style.integration.css');
    }

    public function fireEventGlobalAssets(): self
    {
        $this->fire('asset', $this->asset);

        // Fire event before render mobile ui.
        $this->fire('beforeRenderMobileUi', $this);

        // Fire event before render layout.
        $this->fire('beforeRenderLayout.' . $this->layout, $this);

        return $this;
    }

    public function addBodyAttributes(array $bodyAttributes): static
    {
        $this->bodyAttributes = [...$this->bodyAttributes, ...$bodyAttributes];

        return $this;
    }

    public function getBodyAttribute(string $attribute): ?string
    {
        return $this->bodyAttributes[$attribute] ?? null;
    }

    public function getBodyAttributes(): array
    {
        return $this->bodyAttributes;
    }

    public function bodyAttributes(): string
    {
        if ($this->get('bodyClass')) {
            $this->bodyAttributes['class'] = $this->get('bodyClass');
        }

        return apply_filters(MOBILE_UI_BODY_ATTRIBUTES, Html::attributes($this->bodyAttributes));
    }
}
