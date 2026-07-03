<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>{{ PageTitle::getTitle() }}</title>
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    {!! apply_filters(MAIN_FILTER_HEAD_LAYOUT_TEMPLATE, null) !!}
</head>

<body>

    {!! apply_filters(MAIN_FILTER_HEADER_LAYOUT_TEMPLATE, null) !!}

    <div id="app">
        {{ $slot }}
    </div>

    {!! Assets::renderFooter() !!}

    {!! apply_filters(MAIN_FILTER_FOOTER_LAYOUT_TEMPLATE, null) !!}
</body>

</html>
