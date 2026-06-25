<?php

namespace Kernery\DevTool\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kernery\DevTool\Commands\Abstracts\MainMakeCommand;
use Kernery\DevTool\Commands\Traits\MobileUiTrait;
use Kernery\DevTool\Helper;
use Kernery\MobileUi\Facades\MobileUi;
use Kernery\MobileUi\Services\MobileUiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\select;

#[AsCommand('kernery:ui:create', 'Generate mobile ui structure')]
class MobileUiCreateCommand extends MainMakeCommand implements PromptsForMissingInput
{
    use MobileUiTrait;

    protected ?string $parentMobileUi;

    protected ?string $starterKit = null;

    public function __construct(protected MobileUiService $mobileUiService)
    {
        parent::__construct();
    }

    public function handle(File $files): int
    {
        if (! preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {

            $this->components->error('Only alphabetic characters are allowed.');

            return self::FAILURE;
        }

        $mobileUi = $this->getMobileUi();

        $path = $this->getPath();

        $this->parentMobileUi = $this->option('parent');

        $this->starterKit = $this->argument('type') ?? select(
            label: 'Select a starter kit',
            options: ['blade' => 'Blade Templating'],
            default: 'blade',
        );

        if ($files->isDirectory($path)) {
            $this->components->error(sprintf('UI "%s" already existed.', $mobileUi));

            return self::FAILURE;
        }

        $this->publishStubs($this->getStub(), $path);

        $this->searchAndReplaceInFiles($mobileUi, $path);

        $this->renameFiles($mobileUi, $path);

        $this->mobileUiService->publishAssets($mobileUi);

        $this->components->info(sprintf('UI "%s" has been created.', $mobileUi));

        return self::SUCCESS;
    }

    public function getStub(): string
    {
        $stub = match ($this->starterKit) {
            // 'vue' => 'vue-mobile-ui',
            default => $this->parentMobileUi ? 'blade-mobile-ui' : 'mobile-ui',
        };

        return Helper::joinPaths([dirname(__DIR__, 2), 'stubs', $stub]);
    }

    public function baseReplacements(string $replaceText): array
    {
        return ['.js.stub' => '.js'] + parent::baseReplacements($replaceText);
    }

    public function getReplacements(string $replaceText): array
    {
        return [
            '{mobileUi}' => strtolower($replaceText),
            '{MobileUi}' => Str::studly($replaceText),
            '{parent}' => strtolower($this->parentMobileUi),
        ];
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The UI name of your project')
            ->addArgument('type', InputArgument::OPTIONAL, 'The UI template starter type for your project')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Path to template UI directory')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent UI name (if you want to create a child UI)');
    }

    protected function isValidParentMobileUi(): bool
    {
        if (! MobileUi::exists($this->parentMobileUi)) {
            $this->components->error(sprintf('Parent ui "%s" does not exist.', $this->parentMobileUi));

            return false;
        }

        $config = $this->mobileUiService->getMobileUiConfig($this->parentMobileUi);

        if (Arr::has($config, 'inherit') && Arr::get($config, 'inherit')) {
            $this->components->error(sprintf('Parent ui "%s" does not support child ui.', $this->parentMobileUi));

            return false;
        }

        return true;
    }
}
