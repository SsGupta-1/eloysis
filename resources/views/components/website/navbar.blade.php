<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <div class="container">

        <a class="navbar-brand" href="{{ route('home') }}">

            Home

        </a>

        <button
            class="navbar-toggler"
            data-bs-toggle="collapse"
            data-bs-target="#navbar">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div
            class="collapse navbar-collapse"
            id="navbar">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('news') ? 'active' : '' }}" href="{{ route('news') }}">
                        News
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('events') ? 'active' : '' }}" href="{{ route('events') }}">
                        Events
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                        Contact
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::is('admission') ? 'active' : '' }}" href="{{ route('admission') }}">
                        Admission
                    </a>
                </li>

                <li class="nav-item ms-lg-2">
                    @if(auth('student')->check())
                        <a class="btn btn-warning btn-sm text-dark fw-bold px-3 py-1 mt-1 mt-lg-0 rounded-pill shadow-sm" href="{{ route('student.dashboard') }}">
                            <i class="bi bi-mortarboard-fill me-1"></i> My Portal
                        </a>
                    @else
                        <a class="btn btn-warning btn-sm text-dark fw-bold px-3 py-1 mt-1 mt-lg-0 rounded-pill shadow-sm" href="{{ route('student.login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Student Portal
                        </a>
                    @endif
                </li>

            </ul>

        </div>

    </div>

</nav>