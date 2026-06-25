@php
    PageTitle::setTitle(__('404 - Not found'));
    MobileUi::fireEventGlobalAssets();
@endphp

{!! MobileUi::partial('header') !!}

<div class="container">
    <div class="row">
        <div class="col-12 text-center py-5">
            <h1 class="display-1">404</h1>
            <h2>{{ __('Page Not Found') }}</h2>
            <p class="lead">{{ __('Sorry, the page you are looking for could not be found.') }}</p>
            <a href="{{ MainHelper::getHomepageUrl() }}" class="btn btn-primary">
                <i class="bi bi-house"></i> {{ __('Back to Home') }}
            </a>
        </div>
    </div>
</div>

{!! MobileUi::partial('footer') !!}
