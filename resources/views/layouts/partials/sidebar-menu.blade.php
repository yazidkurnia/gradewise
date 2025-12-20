{{-- Dashboard Menu --}}
<li class="menu-header">Dashboard</li>
<li class="{{ Request::is('dashboard*') ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}" class="nav-link">
        <i class="fas fa-fire"></i>
        <span>Dashboard</span>
    </a>
</li>

{{-- Student Menu --}}
<li class="menu-header">Mahasiswa</li>
<li class="{{ Request::is('student*') ? 'active' : '' }}">
    <a href="{{ route('student') }}" class="nav-link">
        <i class="fas fa-fire"></i>
        <span>Student</span>
    </a>
</li>

{{-- Add your menu items here --}}
<li class="menu-header">Menu</li>

@stack('sidebar-items')
