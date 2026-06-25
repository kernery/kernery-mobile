<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\MobileUi\Services\MobileUiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('kernery:ui:assets:remove', 'Remove assets for a mobile ui')]
class MobileUiAssetsRemoveCommand extends Command implements PromptsForMissingInput
{
    use MobileUiTrait;

    public function handle(MobileUiService $themeService): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $result = $themeService->removeAssets($this->getMobileUi());

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The UI name assets to remove');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to UI directory');
    }
}
