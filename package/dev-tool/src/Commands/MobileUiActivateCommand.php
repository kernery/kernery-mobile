<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\MobileUi\Facades\MobileUi;
use Kernery\MobileUi\Services\MobileUiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('kernery:ui:activate', 'Activate a mobile ui')]
class MobileUiActivateCommand extends Command implements PromptsForMissingInput
{
    use MobileUiTrait;

    public function handle(MobileUiService $mobileUiService): int
    {
        $mobileUi = $this->getMobileUi() ?: MobileUi::getMobileUiName();

        if (! preg_match('/^[a-z0-9\-]+$/i', $mobileUi)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $result = $mobileUiService->activate($mobileUi);

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'The UI name that you want to activate');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to UI directory');
    }
}
