<?php

namespace Kernery\Main\Supports;

use Illuminate\Config\Repository;
use Kernery\Assets\Assets as MainAssets;
use Kernery\Assets\HtmlBuilder;

class Assets extends MainAssets
{
    protected bool $hasVueJs = false;

    public function __construct(Repository $config, HtmlBuilder $htmlBuilder)
    {
        parent::__construct($config, $htmlBuilder);

        $this->config = $config->get('core.main.assets');

        $this->scripts = $this->config['scripts'];

        $this->styles = $this->config['styles'];
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function renderHeader($lastStyles = []): string
    {
        do_action(APP_ACTION_ENQUEUE_SCRIPTS);

        return parent::renderHeader($lastStyles);
    }

    public function renderFooter(): string
    {
        $bodyScripts = $this->getScripts(self::ASSETS_SCRIPT_POSITION_FOOTER);

        return view('assets::footer', compact('bodyScripts'))->render();
    }

    public function usingVueJS(): self
    {
        $this->addScripts(['vue', 'vue-app']);

        $this->hasVueJs = true;

        return $this;
    }

    public function disableVueJS(): self
    {
        $this->removeScripts(['vue', 'vue-app']);

        $this->hasVueJs = false;

        return $this;
    }

    public function hasVueJs(): bool
    {
        return $this->hasVueJs;
    }
}
