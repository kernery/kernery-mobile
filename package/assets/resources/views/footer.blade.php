{{-- Push all assets scripts appended to app footer --}}
@foreach ($bodyScripts as $script)
    {!! Assets::getHtmlBuilder()->script($script['src'] . Assets::getBuildVersion(), $script['attributes']) !!}
@endforeach
