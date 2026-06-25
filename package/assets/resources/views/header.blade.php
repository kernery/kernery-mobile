{{-- Push all assets styles and scripts appended to app header and push optional scripts fallback url  --}}
@foreach ($styles as $style)
    {!! Assets::getHtmlBuilder()->style($style['src'] . Assets::getBuildVersion(), $style['attributes']) !!}
@endforeach

@foreach ($headScripts as $script)
    {!! Assets::getHtmlBuilder()->script($script['src'] . Assets::getBuildVersion(), $script['attributes']) !!}
    @if (!empty($script['fallback']))
        <script>window.{!! $script['fallback'] !!} || document.write('<script src="{{ $script['fallbackURL'] }}"><\/script>')</script>
    @endif
@endforeach
