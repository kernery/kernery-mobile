<?php

namespace Kernery\MobileUi\Database\Seeders;

use Illuminate\Support\Str;
use Kernery\Main\Supports\MainSeeder;
use Kernery\MobileUi\Services\MobileUiService;
use ReflectionClass;

class MobileUiSeeder extends MainSeeder
{
    public function uploadFiles(string $folder, ?string $mainPath = null): array
    {
        $this->setCurrentDirectoryPath((string) $mainPath);

        return parent::uploadFiles($folder);
    }

    protected function setCurrentDirectoryPath(string $mobileUi): static
    {
        $mainPath = $this->currentDirectoryPath('', $mobileUi);

        $this->setMainPath($mainPath);

        return $this;
    }

    protected function currentDirectoryPath(string $path, ?string $mobileUi = null): string
    {
        $mobileUi = $mobileUi ?: $this->getMobileUiName();

        return db_path(sprintf("seeders/files/$mobileUi%s", $path ? sprintf('/%s', $path) : ''));
    }

    protected function getMobileUiName(): string
    {
        $reflection = new ReflectionClass($this);

        return Str::slug(Str::afterLast($reflection->getNamespaceName(), '\\'));
    }

    protected function filePath(string $path, ?string $mainPath = null): string
    {
        if (! $mainPath && ! isset($this->mainPath)) {
            $this->setCurrentDirectoryPath('');
        }

        return parent::filePath($path, $mainPath);
    }

    protected function activateMobileUi(string $mobileUi): void
    {
        app(MobileUiService::class)->activate($mobileUi);
    }
}
