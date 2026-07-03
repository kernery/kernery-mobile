<?php

namespace Kernery\MobileUi;

use Closure;

class Asset
{
    public static string $path;

    public static array $containers = [];

    public static bool $isInheritMobileUi = false;

    protected array $stacks = [
        'cooks' => [],
        'serves' => [],
    ];

    public function addPath(string $path): void
    {
        static::$path = rtrim($path, '/') . '/';
    }

    public function isInheritMobileUi(bool $isInheritMobileUi = true): void
    {
        static::$isInheritMobileUi = $isInheritMobileUi;
    }

    public function cook(string $name, Closure $callbacks): void
    {
        $this->stacks['cooks'][$name] = $callbacks;
    }

    /**
     * Serve asset preparing from cook.
     */
    public function serve(string $name): self
    {
        $this->stacks['serves'][$name] = true;

        return $this;
    }

    public function flush(): void
    {
        foreach (array_keys($this->stacks['serves']) as $key) {
            if (array_key_exists($key, $this->stacks['cooks'])) {
                $callback = $this->stacks['cooks'][$key];

                if ($callback instanceof Closure) {
                    $callback($this);
                }
            }
        }
    }

    /**
     * Magic Method for calling methods on the default container.
     *
     * This will call the "styles" method on the default container
     * echo Asset::styles();
     *
     * This will call the "add" method on the default container
     * Asset::add('jquery', 'js/jquery.js');
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([static::container(), $method], $parameters);
    }

    /**
     * Get an asset container instance.
     *
     * Get the default asset container
     * $container = Asset::container();
     *
     * Get a named asset container
     * $container = Asset::container('footer');
     */
    public static function container(string $container = 'default'): AssetContainer
    {
        if (! isset(static::$containers[$container])) {
            static::$containers[$container] = new AssetContainer($container);
        }

        return static::$containers[$container];
    }
}
