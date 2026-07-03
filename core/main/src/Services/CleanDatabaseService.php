<?php

namespace Kernery\Main\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kernery\Setting\Facades\Setting;
use Throwable;

class CleanDatabaseService
{
    public function getIgnoreTables(): array
    {
        return [
            'sessions',
            'jobs',
            'job_batches',
            'failed_jobs',
            'settings',
        ];
    }

    public function execute(array $except = []): bool
    {
        $except = array_merge($except, $this->getIgnoreTables());

        try {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $tables = array_diff($tables, $except);
        } catch (Throwable) {
            $tables = [];
        }

        if (empty($tables)) {
            return false;
        }

        Schema::disableForeignKeyConstraints();

        foreach (Arr::except($tables, 'settings') as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        if (in_array('settings', $tables)) {
            Setting::forceDelete(except: [
                'mobile-ui',
            ]);
        }

        return true;
    }
}
