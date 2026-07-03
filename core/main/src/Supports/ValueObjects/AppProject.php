<?php

namespace Kernery\Main\Supports\ValueObjects;

use Carbon\CarbonInterface;
use Kernery\Main\Supports\AppCore;

class AppProject
{
    public function __construct(
        public string $updateId,
        public string $version,
        public CarbonInterface $releasedDate,
        public ?string $summary = null,
        public ?string $changelog = null,
        public bool $hasSQL = false
    ) {}

    public function hasUpdate(): bool
    {
        return version_compare($this->version, AppCore::make()->version(), '>');
    }
}
