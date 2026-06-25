<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Kernery\DevTool\Commands\Abstracts\MainMakeCommand;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\DevTool\Helper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\select;

#[AsCommand('kernery:vite:create', 'Generate config file for vite')]
class ViteConfigPublishCommand extends MainMakeCommand implements PromptsForMissingInput
{
    protected ?string $buildTool = null;

    use MobileUiTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $path = $this->getBasePath();

        $viteConfig = $this->getMobileUi();

        $this->buildTool = select(
            label: 'Use vite build tool and Tailwind CSS?',
            options: ['yes' => 'Yes', 'no' => 'No'],
            default: 'yes',
        );

        if ($this->buildTool === 'no') {
            $this->components->info('Skipping Vite config setup.');

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);

        $this->searchAndReplaceInFiles($viteConfig, $path);

        $this->components->info(sprintf('Vite Config "%s" has been created.', $viteConfig));

        return self::SUCCESS;

    }

    public function getStub(): string
    {
        $stub = match ($this->buildTool) {
            default => 'vite-config/vite.config.js',
        };

        return Helper::joinPaths([dirname(__DIR__, 2), 'stubs', $stub]);
    }

    protected function getBasePath(?string $rootPath = null): string
    {
        $rootPath = base_path();

        if ($this->option('path')) {
            $rootPath = $this->option('path');
        }

        return rtrim($rootPath, '/') . '/vite.config.js';
    }

    public function baseReplacements(string $replaceText): array
    {
        return ['.js.stub' => '.js'] + parent::baseReplacements($replaceText);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{viteConfig}' => strtolower($replaceText),
            '{ViteConfig}' => Str::studly($replaceText),
        ];
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'The UI config name to publish');
        $this->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to base directory');
    }
}
