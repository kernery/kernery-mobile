<?php

namespace Kernery\DevTool\Commands;

use Exception;
use Illuminate\Console\Command;
use Kernery\Main\Services\CleanDatabaseService;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;

#[AsCommand('kernery:db:cleanup', 'Cleanup all records from the database except the core system records that is needed to keep the app running.')]
class CleanupDatabaseCommand extends Command
{
    public function handle(CleanDatabaseService $cleanDatabaseService): int
    {
        try {
            if (! confirm('Are you sure you want to run this command?', false)) {
                return self::FAILURE;
            }

            $this->components->task('Running DB cleanup service', fn () => $cleanDatabaseService->execute());

            $this->components->info('✔ Application cleanup sucessful!');

        } catch (Exception $exception) {
            $this->components->error('Error during cleanup operation.');

            $this->components->error($exception->getMessage());
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('force', '--f', null, 'Refresh app without confirmation.');
    }
}
