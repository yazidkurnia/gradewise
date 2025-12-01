<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mb-5">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo/logo_gradewise.png') }}" class="w-52 h-52" style="width:50%"
                    alt="" srcset="">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo/logo_gradewise.png') }}" class="w-52 h-52" style="width:100%"
                    alt="" srcset=""></a>
        </div>

        <ul class="sidebar-menu">
            @include('layouts.partials.sidebar-menu')
        </ul>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="#" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Documentation
            </a>
        </div>
    </aside>
</div>
