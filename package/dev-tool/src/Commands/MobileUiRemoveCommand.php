<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\MobileUi\Services\MobileUiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('kernery:ui:remove', 'Remove an existing mobile ui')]
class MobileUiRemoveCommand extends Command implements PromptsForMissingInput
{
    use ConfirmableTrait;
    use MobileUiTrait;

    public function handle(MobileUiService $themeService): int
    {
        if (! $this->confirmToProceed('Are you sure you want to permanently delete?', true)) {
            return self::FAILURE;
        }

        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $result = $themeService->remove($this->getMobileUi());

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The UI name that you want to remove');
        $this->addOption('force', 'f', null, 'Force to remove UI without confirmation');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to UI directory');
    }
}
