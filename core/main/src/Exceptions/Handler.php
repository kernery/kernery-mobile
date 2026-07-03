<?php

namespace Kernery\Main\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Kernery\Main\Http\Responses\MainHttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected MainHttpResponse $mainHttpResponse;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->mainHttpResponse = new MainHttpResponse;
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException || $e instanceof MethodNotAllowedHttpException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        switch (true) {
            case $e instanceof MethodNotAllowedHttpException:
            case $e instanceof TokenMismatchException:
                return $this->mainHttpResponse
                    ->setError()
                    ->setCode($e->getCode())
                    ->setMessage($e->getMessage());
            case $e instanceof NotFoundHttpException:
                if (config('core.main.general.redirect_404_to_homepage', false) == true) {
                    return redirect(url('/'));
                }

                break;

            case $e instanceof HttpExceptionInterface:
                $code = $e->getStatusCode();

                if ($request->expectsJson()) {
                    return match ($code) {
                        401 => $this->mainHttpResponse
                            ->setError()
                            ->setMessage(trans('core/main::permissions.access_denied_message'))
                            ->setCode($code)
                            ->toResponse($request),
                        403 => $this->mainHttpResponse
                            ->setError()
                            ->setMessage(trans('core/main::permissions.action_unauthorized'))
                            ->setCode($code)
                            ->toResponse($request),
                        404 => $this->mainHttpResponse
                            ->setError()
                            ->setMessage(trans('core/main::errors.not_found'))
                            ->setCode(404)
                            ->toResponse($request),
                        default => $this->mainHttpResponse
                            ->setError()
                            ->setMessage($e->getMessage())
                            ->setCode($code)
                            ->toResponse($request),
                    };
                }
        }

        return parent::render($request, $e);
    }

    protected function getErrorFilePath(Throwable $e): string
    {
        return ltrim(str_replace(base_path(), '', $e->getFile()), '/') . ':' . $e->getLine();
    }

    protected function getHttpExceptionView(HttpExceptionInterface $e)
    {
        $defaultView = parent::getHttpExceptionView($e);

        if (app()->runningInConsole() || request()->wantsJson() || request()->expectsJson()) {
            return $defaultView;
        }

        if (view()->exists($view = 'core/main::errors.' . $e->getStatusCode())) {
            return $view;
        }

        return apply_filters(GET_HTTP_EXCEPTION_VIEW, $defaultView, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->wantsJson() || $request->expectsJson()) {
            return $this
                ->mainHttpResponse
                ->setError()
                ->setMessage($exception->getMessage())
                ->setCode(401)
                ->toResponse($request);
        }

        if (array_filter($exception->guards())) {
            $defaultException = redirect()
                ->guest($exception->redirectTo($request) ?? (Route::has('login') ? route('login') : url('login')));

            return apply_filters(GET_APP_UNAUTHENTICATED_RESPONSE, $defaultException, $request, $exception);
        }

        return redirect()->guest(route('public.index'));
    }
}
