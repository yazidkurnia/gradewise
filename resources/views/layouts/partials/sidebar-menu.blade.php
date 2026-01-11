{{-- Dashboard Menu --}}
<li class="menu-header">Dashboard</li>
<li class="{{ $title == 'Dashboard' ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}" class="nav-link">
        <i class="fas fa-fire"></i>
        <span>Dashboard</span>
    </a>
</li>

{{-- Add your menu items here --}}
<li class="menu-header">Setting</li>
<li class="{{ $title == 'Manage Data Mahasiswa' ? 'active' : '' }}">
    <a href="{{ route('student') }}" class="nav-link">
        <i class="fas fa-graduation-cap"></i>
        <span>Student</span>
    </a>
</li>

<li class="{{ $title == 'Manage Data Dosen' ? 'active' : '' }}">

    <a href="{{ route('lecture') }}" class="nav-link ">
        <i class="fas fa-school"></i>
        <span>Manage Data Dosen</span>
    </a>
</li>
<li class="menu-header">Skripsi</li>
<li class="{{ $title == 'Skripsi Mahasiswa' ? 'active' : '' }}">

    <a href="{{ route('thesis') }}" class="nav-link ">
        <i class="fas fa-person-chalkboard"></i>
        <span>Data Skripsi</span>
    </a>
</li>

@stack('sidebar-items')
