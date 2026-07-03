<?php

namespace Kernery\Main\Supports\Database;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\DB;
use Kernery\Main\Models\AppModel;
use Throwable;

class Blueprint extends IlluminateBlueprint
{
    public function __construct(Connection $connection, $table, ?Closure $callback = null, $prefix = '')
    {
        parent::__construct($connection, $table, $callback, $prefix);

        rescue(function () {
            if (DB::getDefaultConnection() === 'sqlite') {
                DB::statement('SET SESSION sql_require_primary_key=0');
            }
        }, report: false);
    }

    public function id($column = 'id'): ColumnDefinition
    {
        return match ($this->getModelTypeOfId()) {
            'UUID' => $this->uuid($column)->primary(),
            'ULID' => $this->ulid($column)->primary(),
            default => parent::id($column),
        };
    }

    public function foreignId($column): ColumnDefinition
    {
        return match ($this->getModelTypeOfId()) {
            'UUID' => $this->foreignUuid($column),
            'ULID' => $this->foreignUlid($column),
            default => parent::foreignId($column),
        };
    }

    public function morphs($name, $indexName = null, $after = null): void
    {
        match ($this->getModelTypeOfId()) {
            'UUID' => $this->uuidMorphs($name, $indexName, $after),
            'ULID' => $this->ulidMorphs($name, $indexName, $after),
            default => parent::morphs($name, $indexName, $after),
        };
    }

    public function nullableMorphs($name, $indexName = null, $after = null): void
    {
        match ($this->getModelTypeOfId()) {
            'UUID' => $this->nullableUuidMorphs($name, $indexName, $after),
            'ULID' => $this->nullableUlidMorphs($name, $indexName, $after),
            default => parent::nullableMorphs($name, $indexName, $after),
        };
    }

    protected function getModelTypeOfId(): string
    {
        try {
            return AppModel::getTypeOfId();
        } catch (Throwable) {
            return 'BIGINT';
        }
    }
}
