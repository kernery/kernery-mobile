<?php

namespace Kernery\Main\Supports;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Conditionable;
use Kernery\Main\Events\FinishedSeederEvent;
use Kernery\Main\Events\SeederPrepared;
use Kernery\Main\Facades\MainHelper;
use Kernery\Setting\Facades\SecureSetting;

class MainSeeder extends Seeder
{
    use Conditionable;

    protected Carbon $now;

    protected string $mainPath;

    public function uploadFiles(string $folder, ?string $mainPath = null): array
    {
        $folderPath = $mainPath ?: $this->getMainPath() . '/' . $folder;

        $folder = ltrim(str_replace(db_path('seeders/files'), '', $folderPath), '/');

        if (! File::isDirectory($folderPath)) {
            throw new FileNotFoundException('Folder not found: ' . $folderPath);
        }

        $storage = Storage::disk('public');

        if ($storage->exists($folder)) {
            $storage->deleteDirectory($folder);
        }

        $files = [];

        foreach (File::allFiles($folderPath) as $file) {
            $files[] = $file;
        }

        return $files;
    }

    protected function filePath(string $path, ?string $mainPath = null): string
    {
        $filePath = ($mainPath ? sprintf('%s/%s', $mainPath, $path) : $this->getMainPath() . '/' . $path);
        $path = str_replace(db_path('seeders/files/'), '', $filePath);

        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        throw new FileNotFoundException('File not found: ' . $filePath);
    }

    public function prepareRun(): void
    {

        SecureSetting::newQuery()->truncate();

        SecureSetting::forgetAll();

        SecureSetting::save();

        SeederPrepared::dispatch();
    }

    protected function random(int $from, int $to, array $exceptions = []): int
    {
        sort($exceptions); // lets us use break; in the foreach reliably
        $number = rand($from, $to - count($exceptions)); // or mt_rand()

        foreach ($exceptions as $exception) {
            if ($number >= $exception) {
                $number++; // make up for the gap
            } else { /* if ($number < $exception) */
                break;
            }
        }

        return $number;
    }

    protected function finished(): void
    {
        FinishedSeederEvent::dispatch();
    }

    protected function now(): Carbon
    {
        if (isset($this->now)) {
            return $this->now;
        }

        $this->now = Carbon::now();

        return $this->now;
    }

    protected function removeMainUrlFromString(string $value): ?string
    {
        return str_replace(url(''), '', $value);
    }

    protected function getFilesFromPath(string $path): Collection
    {
        $directoryPath = $this->getMainPath() . '/' . $path;

        $files = [];

        if (File::isDirectory($directoryPath)) {
            $files = array_map(fn ($item) => $path . '/' . $item, MainHelper::scanFolder($directoryPath));
        }

        return collect($files);
    }

    protected function saveSettings(array $settings): void
    {
        SecureSetting::delete(array_keys($settings));

        SecureSetting::forceSet($settings)->save();
    }

    protected function getMainPath(): ?string
    {
        return $this->mainPath ?? db_path('seeders/files');
    }

    protected function setMainPath(string $path): static
    {
        $this->mainPath = $path;

        return $this;
    }
}
