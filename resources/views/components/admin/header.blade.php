<header class="admin-header">

    <div class="header-left">

        <button class="sidebar-toggle me-2" id="sidebarToggle" type="button" aria-label="Toggle Sidebar">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="header-brand">

            <a href="{{ route('admin.dashboard') }}" class="brand-link">

                <img src="{{ asset('assets/common/images/logo.png') }}"
                     alt="Logo"
                     class="brand-logo">

            </a>

        </div>

    </div>


    <div class="header-right">

        {{-- Theme Toggle --}}
        <x-ui.theme-toggle />

        {{-- Notifications --}}
        <div class="dropdown">

            <button class="header-icon"
                data-bs-toggle="dropdown"
                aria-expanded="false">

                <i class="bi bi-bell"></i>

                <span class="badge bg-danger rounded-pill">0</span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                <li>

                    <h6 class="dropdown-header">

                        Notifications

                    </h6>

                </li>

                <li>

                    <span class="dropdown-item-text text-muted fs-7">

                        No notifications found

                    </span>

                </li>

            </ul>

        </div>

        {{-- Profile --}}
        <div class="dropdown">

            <button
                class="profile-btn"
                data-bs-toggle="dropdown"
                aria-expanded="false">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode(auth('admin')->user()->name) }}&background=E0F2FE&color=0284C7"
                    alt="Profile">

                <div class="d-none d-sm-block text-start">

                    <strong class="d-block leading-tight fs-7">

                        {{ auth('admin')->user()->name }}

                    </strong>

                    <small class="text-muted fs-8">

                        {{ ucfirst(auth('admin')->user()->role->role_name) }}

                    </small>

                </div>

                <i class="bi bi-chevron-down ms-1 fs-8 text-muted"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                <li>

                    <a
                        href="#"
                        class="dropdown-item">

                        <i class="bi bi-person me-2"></i>

                        My Profile

                    </a>

                </li>

                <li>

                    <a
                        href="#"
                        class="dropdown-item">

                        <i class="bi bi-gear me-2"></i>

                        Settings

                    </a>

                </li>

                <li>

                    <hr class="dropdown-divider">

                </li>

                <li>

                    <form
                        action="{{ route('admin.logout') }}"
                        method="POST">

                        @csrf

                        <button
                            class="dropdown-item text-danger">

                            <i class="bi bi-box-arrow-right me-2"></i>

                            Logout

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</header>