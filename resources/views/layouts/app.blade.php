<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            {{-- Navbar --}}
            @include('layouts.partials.navbar')

            {{-- Sidebar --}}
            @include('layouts.partials.sidebar')

            {{-- Main Content --}}
            <div class="main-content">
                <section class="section">
                    @yield('content')
                </section>
            </div>

            {{-- Footer --}}
            @include('layouts.partials.footer')
        </div>
    </div>

    {{-- Scripts --}}
    @include('layouts.partials.scripts')
</body>

</html>
