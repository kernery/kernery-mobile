<?php

namespace Kernery\Main\Supports;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Kernery\Main\Events\AppUpdateAvailable;
use Kernery\Main\Events\AppUpdateChecked;
use Kernery\Main\Events\AppUpdateChecking;
use Kernery\Main\Events\AppUpdateUnavailable;
use Kernery\Main\Exceptions\MissingCURLExtensionException;
use Kernery\Main\Supports\ValueObjects\AppProject;

/**
 * @readonly
 */
final class AppCore
{
    private string $apiBaseUrl = 'https://updater.kernery.com';

    private string $coreDataFilePath;

    private string $appId;

    private string $appVersion = '1.0.0';

    private string $minimumPhpVersion = '8.4.0';

    public function __construct(
        private readonly Filesystem $files,
    ) {
        $this->coreDataFilePath = core_path('main/updater.json');
        $this->parseDataFromCoreDataFile();
    }

    public static function make(): self
    {
        return app(self::class);
    }

    public function version(): string
    {
        return $this->appVersion;
    }

    public function minimumPhpVersion(): string
    {
        return $this->minimumPhpVersion;
    }

    private function parseAppUpdateResponse(Response $response): AppProject | false
    {
        $data = $response->json();

        if ($response->ok() && Arr::get($data, 'status')) {
            return new AppProject(
                Arr::get($data, 'update_id'),
                Arr::get($data, 'version'),
                Carbon::createFromFormat('Y-m-d', Arr::get($data, 'release_date')),
            );
        }

        return false;
    }

    public function checkUpdate(): AppProject | false
    {
        AppUpdateChecking::dispatch();

        $response = $this->createRequest('check_update', [
            'app_id' => $this->appId,
            'current_version' => $this->appVersion,
        ]);

        AppUpdateChecked::dispatch();

        $app = $this->parseAppUpdateResponse($response);

        return tap($app, function (AppProject | false $appProject) {
            if (! $appProject || ! $appProject->hasUpdate()) {
                AppUpdateUnavailable::dispatch();

                return;
            }

            AppUpdateAvailable::dispatch($appProject);
        });
    }

    public function getLatestVersion(): AppProject | false
    {
        $response = $this->createRequest('check_update', [
            'product_id' => $this->appId,
            'current_version' => '0.0.0',
        ]);

        return $this->parseAppUpdateResponse($response);
    }

    public function getCoreFileData(): array
    {
        try {
            return json_decode($this->files->get($this->coreDataFilePath), true) ?: [];
        } catch (FileNotFoundException) {
            return [];
        }
    }

    private function createRequest(string $path, array $data = [], string $method = 'POST'): Response
    {
        if (! extension_loaded('curl')) {
            throw new MissingCURLExtensionException;
        }

        $request = Http::baseUrl(ltrim($this->apiBaseUrl, '/') . '/api')
            ->withHeaders([
                'X-APP-ID' => $this->appId,
                'X-APP-IP' => $this->getClientIpAddress(),
                'X-APP-VERSION' => $this->appVersion,
            ])
            ->asJson()
            ->acceptJson()
            ->withoutVerifying()
            ->connectTimeout(100)
            ->timeout(300);

        return match (Str::upper($method)) {
            'GET' => $request->get($path, $data),
            'HEAD' => $request->head($path),
            default => $request->post($path, $data)
        };
    }

    private function parseDataFromCoreDataFile(): void
    {
        if (! $this->files->exists($this->coreDataFilePath)) {
            return;
        }

        $data = $this->getCoreFileData();
        $this->appId = Arr::get($data, 'appId', '');
        $this->apiBaseUrl = Arr::get($data, 'apiBaseUrl', '');
        $this->appVersion = Arr::get($data, 'appVersion', $this->appVersion);
        $this->minimumPhpVersion = Arr::get($data, 'minimumPhpVersion', $this->minimumPhpVersion);
    }

    private function getClientIpAddress(): string
    {
        return AppHelper::getIpFromThirdParty();
    }
}
