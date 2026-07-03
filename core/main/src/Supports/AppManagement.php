<?php

namespace Kernery\Main\Supports;

use Illuminate\Support\Facades\File;

class AppManagement
{
    protected static function calculateAppSize(string $directory): int
    {
        $size = 0;

        foreach (File::glob(rtrim($directory, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += File::isFile($each) ? File::size($each) : self::calculateAppSize($each);
        }

        return $size;
    }

    public static function getMaximumExecutionTime(): int
    {
        return (int) (@ini_get('max_execution_time') ?: -1);
    }

    public static function getAppSize(): int
    {
        return self::calculateAppSize(app()->basePath());
    }
}
