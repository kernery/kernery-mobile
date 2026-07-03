<x-app::layouts.main body-class="border-top-wide border-primary d-flex flex-column">
    <x-slot:title>
        @yield('title')
    </x-slot:title>

    <div class="page page-center">
        <div class="container py-4 container-tight">
            @yield('content')
        </div>
    </div>
</x-app::layouts.main>
