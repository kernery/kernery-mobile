<?php

namespace Kernery\Main\Services;

class IgnoreDatabaseMigration
{
    public function getIgnoreDefaultTables(): array
    {
        return [
            // database_path('migrations/0001_01_01_000000_create_users_table.php'),
            database_path('migrations/0001_01_01_000002_create_jobs_table.php'),
        ];
    }

    public function execute()
    {
        $defaultUsersMigrations = $this->getIgnoreDefaultTables();

        foreach ($defaultUsersMigrations as $migration) {
            if (file_exists($migration)) {
                rename($migration, $migration . '.disabled');
            }
        }
    }

    public function revert()
    {
        $defaultUsersMigrations = $this->getIgnoreDefaultTables();

        foreach ($defaultUsersMigrations as $migration) {
            $disabledFile = $migration . '.disabled';

            if (file_exists($disabledFile)) {
                rename($disabledFile, $migration);
            }
        }
    }
}
