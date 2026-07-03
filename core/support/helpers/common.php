<?php

use Carbon\Carbon;
use Kernery\Main\Facades\MainHelper;

if (! function_exists('format_time')) {

    function format_time(Carbon $timestamp, string $format = 'j M Y H:i'): string
    {
        return MainHelper::formatTime($timestamp, $format);
    }
}

if (! function_exists('date_from_database')) {

    function date_from_database(string $time, string $format = 'Y-m-d'): string
    {
        return MainHelper::formatDate($time, $format);
    }
}

if (! function_exists('human_file_size')) {

    function human_file_size(float $bytes, int $precision = 2): string
    {
        return MainHelper::humanFilesize($bytes, $precision);
    }
}

if (! function_exists('get_file_data')) {

    function get_file_data(string $file, bool $toArray = true): string | array | null
    {
        return MainHelper::getFileData($file, $toArray);
    }
}

if (! function_exists('json_encode_prettify')) {

    function json_encode_prettify(array $data): string
    {
        return MainHelper::jsonEncodePrettify($data);
    }
}

if (! function_exists('save_file_data')) {

    function save_file_data(string $path, array | string | null $data, bool $json = true): bool
    {
        return MainHelper::saveFileData($path, $data, $json);
    }
}

if (! function_exists('scan_folder')) {

    function scan_folder(string $path, array $ignoreFiles = []): array
    {
        return MainHelper::scanFolder($path, $ignoreFiles);
    }
}
