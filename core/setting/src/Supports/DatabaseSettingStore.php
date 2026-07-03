<?php

namespace Kernery\Setting\Supports;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Kernery\Main\Models\AppModel;
use Kernery\Main\Supports\AppHelper;
use Kernery\Setting\Models\Setting;
use UnexpectedValueException;

class DatabaseSettingStore extends SecureSetting
{
    protected bool $connectedDatabase = false;

    protected array $encrypted = [
        // add any sensitive keys here
    ];

    protected function shouldEncrypt(string $key): bool
    {
        return in_array($key, $this->encrypted, true);
    }

    protected function encryptValue(mixed $value): string
    {
        return Crypt::encryptString((string) $value);
    }

    protected function decryptValue(string $value): mixed
    {
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {

            return $value;
        }
    }

    public function forget(string $key, bool $force = false): SecureSetting
    {
        parent::forget($key);

        $segments = explode('.', $key);
        array_pop($segments);

        while ($segments) {
            $segment = implode('.', $segments);

            if ($this->get($segment)) {
                break;
            }

            $this->forget($segment);
            array_pop($segments);
        }

        return $this;
    }

    public function newQuery(): Builder
    {
        return Setting::query();
    }

    protected function write(array $data): void
    {
        $keys = $this->newQuery()->pluck('key')->all();

        $data = Arr::dot($data);

        foreach ($data as $key => $value) {
            if ($this->shouldEncrypt($key)) {
                $data[$key] = $this->encryptValue($value);
            }
        }

        $updateData = Arr::only($data, $keys);
        $insertData = Arr::except($data, $keys);

        foreach ($updateData as $key => $value) {
            $this->newQuery()
                ->where('key', $key)
                ->update(['value' => $value]);
        }

        if ($insertData) {
            $this->newQuery()->insert($this->prepareInsertData($insertData));
        }
    }

    protected function prepareInsertData(array $data): array
    {
        $dbData = [];

        foreach ($data as $key => $value) {
            $data = compact('key', 'value');

            if (AppModel::getTypeOfId() !== 'BIGINT') {
                $data['id'] = (new AppModel)->newUniqueId();
            }

            $dbData[] = $data;
        }

        return apply_filters(APP_SETTINGS_PREPARE_TO_INSERT_DATA, $dbData);
    }

    protected function read(): array
    {
        if (! $this->connectedDatabase) {
            $this->connectedDatabase = AppHelper::isConnectedDatabase();
        }

        if (! $this->connectedDatabase) {
            return [];
        }

        return $this->parseReadData($this->newQuery()->get());
    }

    public function parseReadData(Collection | array $data): array
    {
        $results = [];

        foreach ($data as $row) {
            if (is_array($row)) {
                $key = $row['key'];
                $value = $row['value'];
            } elseif (is_object($row)) {
                $key = $row->key;
                $value = $row->value;
            } else {
                $msg = 'Expected array or object, got ' . gettype($row);

                throw new UnexpectedValueException($msg);
            }

            if ($this->shouldEncrypt($key) && $value !== null) {
                $value = $this->decryptValue($value);
            }

            Arr::set($results, $key, $value);
        }

        return $results;
    }

    public function delete(array | string $keys = [], array $except = [], bool $force = false)
    {
        if (! $keys && ! $except) {
            return false;
        }

        if (! is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $k => $v) {
            if (! $force && in_array($k, $this->guard)) {
                unset($keys[$k]);
            }
        }

        $query = $this->newQuery();

        if ($keys) {
            $query = $query->whereIn('key', $keys);
        }

        if ($except) {
            $query = $query->whereNotIn('key', $keys);
        }

        return $query->delete();
    }

    public function forceDelete(array | string $keys = [], array $except = [])
    {
        return $this->delete($keys, $except, true);
    }
}
