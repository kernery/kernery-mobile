<?php

namespace Kernery\MobileUi\Providers;

use Kernery\Main\Facades\Html;
use Kernery\Main\Supports\ServiceProvider;
use Kernery\MobileUi\Facades\MobileUi;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        add_filter(GET_HTTP_EXCEPTION_VIEW, function (string $defaultView, HttpExceptionInterface $exception) {
            if (view()->exists($view = MobileUi::getMobileUiNamespace('views.errors.' . $exception->getStatusCode()))) {
                return $view;
            }

            return $defaultView;
        }, 10, 2);

        add_filter(MOBILE_UI_FRONT_HEADER, function (?string $html): ?string {

            $file = MobileUi::getStyleIntegrationPath();

            if (app('files')->exists($file)) {
                $html .= PHP_EOL . Html::style(MobileUi::asset()->url('css/style.integration.css?v=' . filectime($file)));
            }

            return $html;
        }, 15);
    }
}
