<?php

namespace Kernery\Main\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;

class MainHelper
{
    public function formatTime(CarbonInterface $timestamp, ?string $format = 'j M Y H:i'): string
    {
        $first = Carbon::create(0000, 0, 0, 00, 00, 00);

        if ($timestamp->lte($first)) {
            return '';
        }

        return $timestamp->format($format);
    }

    public function formatDate(CarbonInterface | int | string | null $date, ?string $format = null): ?string
    {
        if (empty($format)) {
            $format = $this->getDateFormat();
        }

        if (empty($date)) {
            return $date;
        }

        if ($date instanceof CarbonInterface) {
            return $this->formatTime($date, $format);
        }

        return $this->formatTime(Carbon::parse($date), $format);
    }

    public function formatDateTime(CarbonInterface | int | string | null $date, ?string $format = null): ?string
    {
        if (empty($format)) {
            $format = $this->getDateTimeFormat();
        }

        return $this->formatDate($date, $format);
    }

    public function humanFilesize(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }

    public function getFileData(string $file, bool $convertToArray = true)
    {
        $file = File::get($file);

        if (! empty($file)) {
            if ($convertToArray) {
                return json_decode($file, true);
            }

            return $file;
        }

        if (! $convertToArray) {
            return null;
        }

        return [];
    }

    public function saveFileData(string $path, array | string | null $data, bool $json = true): bool
    {
        try {
            if ($json) {
                $data = $this->jsonEncodePrettify($data);
            }

            File::ensureDirectoryExists(File::dirname($path));

            File::put($path, $data);

            return true;
        } catch (Throwable $throwable) {
            $this->logError($throwable);

            return false;
        }
    }

    public function jsonEncodePrettify(array | string | null $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }

    public function scanFolder(string $path, array $ignoreFiles = []): array
    {
        if (! $path) {
            return [];
        }

        if (File::isDirectory($path)) {
            $data = array_diff(scandir($path), array_merge(['.', '..', '.DS_Store'], $ignoreFiles));
            natsort($data);

            return $data;
        }

        return [];
    }

    public function getAppDefaultPage(): string
    {
        $prefix = config('core.main.general.default_page');

        if (! $prefix && class_exists('MobileUI')) {
            return 'homepage';
        }

        return $prefix;
    }

    public function getAppDefaultLayout(): string
    {
        return apply_filters(APP_FILTER_FOR_DEFAULT_LAYOUT, 'core/main::layouts.main');
    }

    /**
     * @param  Builder|\Illuminate\Database\Eloquent\Builder  $query
     */
    public function isJoined($query, string $table): bool
    {
        $joins = $query->getQuery()->joins;

        if ($joins == null) {
            return false;
        }

        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }

    public function removeQueryStringVars(?string $url, array | string $key): ?string
    {
        if (! is_array($key)) {
            $key = [$key];
        }

        foreach ($key as $item) {
            $url = preg_replace('/(.*)(?|&)' . $item . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    public function getPhoneValidationRule(bool $asArray = false): string | array
    {
        $rule = config('core.main.general.phone_validation_rule');

        if ($asArray) {
            return explode('|', $rule);
        }

        return $rule;
    }

    public function getDateFormats(): array
    {
        $formats = [
            'Y-m-d',
            'Y-M-d',
            'y-m-d',
            'm-d-Y',
            'M-d-Y',
        ];

        foreach ($formats as $format) {
            $formats[] = str_replace('-', '/', $format);
        }

        $formats[] = 'M d, Y';

        return $formats;
    }

    public function clean(array | string | null $dirty, array | string | null $config = null): array | string | null
    {
        if (config('core.main.general.enable_less_secure_web', false)) {
            return $dirty;
        }

        if (! $dirty && $dirty !== null) {
            return $dirty;
        }

        if (! is_numeric($dirty)) {
            $dirty = (string) $dirty;
        }

        return $this->clean($dirty, $config);
    }

    public function html(array | string | null $dirty, array | string | null $config = null): HtmlString
    {
        return new HtmlString((string) $this->clean($dirty, $config));
    }

    public function hexToRgba(string $color, float $opacity = 1): string
    {
        $rgb = implode(',', $this->hexToRgb($color));

        if ($opacity == 1) {
            return 'rgb(' . $rgb . ')';
        }

        return 'rgba(' . $rgb . ', ' . $opacity . ')';
    }

    public function hexToRgb(string $color): array
    {
        $color = Str::lower($color);

        if (Str::startsWith($color, 'rgb')) {
            $color = str_replace('rgb(', '', $color);
            $color = str_replace(')', '', $color);
            $color = str_replace(' ', '', $color);

            [$red, $green, $blue] = explode(',', $color);

            return compact('red', 'green', 'blue');
        }

        [$red, $green, $blue] = sscanf($color, '#%02x%02x%02x');

        $blue = $blue === null ? 0 : $blue;

        return compact('red', 'green', 'blue');
    }

    public function iniSet(string $key, int | string | null $value): self
    {
        if (config('core.main.general.enable_ini_set', true)) {
            @ini_set($key, $value);
        }

        return $this;
    }

    public function maximumExecutionTimeAndMemoryLimit(): self
    {
        $this->iniSet('max_execution_time', -1);
        $this->iniSet('memory_limit', -1);

        return $this;
    }

    public function removeSpecialCharacters(?string $string): array | string | null
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public function getInputValueFromQueryString(string $name): string
    {
        $value = request()->input($name);

        if (! is_string($value)) {
            return '';
        }

        return $value;
    }

    public function stringify($content): ?string
    {
        if (empty($content)) {
            return null;
        }

        if (is_string($content) || is_numeric($content) || is_bool($content)) {
            return $content;
        }

        if (is_array($content)) {
            return json_encode($content);
        }

        return null;
    }

    public function logError(Throwable $throwable): void
    {
        logger()->error($throwable->getMessage() . ' - ' . $throwable->getFile() . ':' . $throwable->getLine());
    }

    public function getDateFormat(): string
    {
        return config('core.main.general.date_format.date');
    }

    public function getDateTimeFormat(): string
    {
        return config('core.main.general.date_format.date_time');
    }

    public function joinPaths(array $paths): string
    {
        return implode(DIRECTORY_SEPARATOR, $paths);
    }

    public function cleanToastMessage(string $message): string
    {
        if (Str::startsWith($message, ['http://', 'https://'])) {
            return $message;
        }

        return addslashes($message);
    }

    public function getHomepageUrl()
    {
        return apply_filters('app_homepage_url', rescue(fn () => route('public.index'), report: false));
    }
}
