<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Console\Command;
use Kernery\Main\Services\AppMigrationService;
use Kernery\Main\Services\CleanDatabaseService;
use Kernery\Main\Services\IgnoreDatabaseMigration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

use function Laravel\Prompts\confirm;

#[AsCommand('kernery:mobile:install', 'Install, publish and migrate app.')]
class InstallCommand extends Command
{
    public function handle(IgnoreDatabaseMigration $ignoreDatabaseMigration, AppMigrationService $appMigrationService, CleanDatabaseService $cleanDatabaseService): int
    {
        if (! confirm('Do you want to proceed with this fresh installation?')) {
            return self::SUCCESS;
        }

        $name = $this->argument('name');

        if ($name && ! preg_match('/^[a-z0-9\-]+$/i', $name)) {

            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;

        }
        $this->components->info('Starting installation...');

        $this->components->task('Altering clashing migration', fn () => $ignoreDatabaseMigration->execute());

        $this->call('migrate:fresh');

        $this->components->task('Migrating app settings', fn () => $appMigrationService->runMigrations());

        $this->components->info('Migration done!');

        $this->components->task('Clear database junks', fn () => $cleanDatabaseService->execute());

        $this->components->task('Reverting altered migration', fn () => $ignoreDatabaseMigration->revert());

        $this->components->info('Scaffolding a new UI...');

        $result = $this->call('kernery:ui:create', ['name' => $name]);

        if ($result !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->call('kernery:ui:activate', ['name' => $name]);

        $this->components->info('Mobile Ui installation done!');

        $this->call('kernery:ui:assets:publish');

        $this->components->info('Publishing assets done!');

        $this->call('kernery:vite:create', ['name' => $name]);

        $this->call('vendor:publish', ['--tag' => 'app-public', '--force' => true]);

        $this->components->info('Publishing vendor assets done!');

        $this->components->info('Your mobile application is now ready to cook!');

        return self::SUCCESS;

    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Your UI template name');
    }
}
