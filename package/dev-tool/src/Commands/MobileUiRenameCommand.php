<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem as File;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\MobileUi\Services\MobileUiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('kernery:ui:rename', 'Rename mobile ui to the new name')]
class MobileUiRenameCommand extends Command implements PromptsForMissingInput
{
    use MobileUiTrait;

    public function handle(File $files, MobileUiService $mobileUiService): int
    {
        $mobileUi = $this->getMobileUi();

        $newName = $this->argument('newName');

        if ($mobileUi == $newName) {
            $this->components->error('MobileUi name are the same!');

            return self::FAILURE;
        }

        if ($files->isDirectory(ui_path($newName))) {
            $this->components->error("MobileUi <info>$mobileUi</info> already existed.");

            return self::FAILURE;
        }

        $files->move(ui_path($mobileUi), ui_path($newName));

        $mobileUiService->activate($newName);

        $this->components->info("MobileUi <info>$mobileUi</info> has been renamed to <info>$newName</info>!");

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The UI name that you want to rename');
        $this->addArgument('newName', InputArgument::REQUIRED, 'The new name');
    }
}
