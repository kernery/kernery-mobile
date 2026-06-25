<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\MobileUi\Services\MobileUiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('kernery:ui:assets:publish', 'Publish assets for a mobile ui')]
class MobileUiAssetsPublishCommand extends Command
{
    use MobileUiTrait;

    public function handle(File $files, MobileUiService $mobileUiService): int
    {
        $name = $this->option('name');

        if ($name && ! preg_match('/^[a-z0-9\-]+$/i', $name)) {
            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        if ($name && ! $files->isDirectory($this->getPath())) {
            $this->components->error(sprintf('Mobile Ui "%s" is not exists.', $this->getMobileUi()));

            return self::FAILURE;
        }

        $result = $mobileUiService->publishAssets($name);

        if ($result['error']) {
            $this->components->error($result['message']);

            return self::FAILURE;
        }

        $this->components->info($result['message']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'The UI name assets to publish');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to UI directory');
    }
}
