{{-- resources/views/layouts/partials/admin_sidebar.blade.php --}}
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <img src="{{ asset('admin_assets/assets/img/kaiadmin/logo_light.svg') }}"
                     alt="navbar brand"
                     class="navbar-brand"
                     height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Dashboard -->
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Management Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Management</h4>
                </li>

                <!-- Tasks -->
                @php
                    $tasksActive = request()->routeIs('admin.tasks.*');
                @endphp
                <li class="nav-item {{ $tasksActive ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#tasks" aria-expanded="{{ $tasksActive ? 'true' : 'false' }}">
                        <i class="fas fa-tasks"></i>
                        <p>Tasks</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $tasksActive ? 'show' : '' }}" id="tasks">
                        <ul class="nav nav-collapse">
                            <li class="{{ request()->routeIs('admin.tasks.create') ? 'active' : '' }}">
                                <a href="{{ route('admin.tasks.create') }}">
                                    <span class="sub-item">Add Task</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('admin.tasks.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.tasks.index') }}">
                                    <span class="sub-item">Task List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Users (Admin Only) -->
                @if(auth()->check() && auth()->user()->role == 0)
                    @php
                        $usersActive = request()->routeIs('admin.users.*');
                    @endphp
                    <li class="nav-item {{ $usersActive ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#users" aria-expanded="{{ $usersActive ? 'true' : 'false' }}">
                            <i class="fas fa-users"></i>
                            <p>Users</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $usersActive ? 'show' : '' }}" id="users">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users.create') }}">
                                        <span class="sub-item">Add User</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users.index') }}">
                                        <span class="sub-item">Users List</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- System Section (Admin Only) -->
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">System</h4>
                    </li>

                    <!-- Activity Logs (Admin Only) -->
                    <li class="nav-item {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.activity-logs.index') }}">
                            <i class="fas fa-history"></i>
                            <p>Activity Logs</p>
                        </a>
                    </li>
                @endif

                <!-- Account Section -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Account</h4>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off"></i>
                        <p>Logout</p>
                    </a>
                </li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </ul>
        </div>
    </div>
</div>
