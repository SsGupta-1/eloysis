<div class="bg-dark text-white py-2">

    <div class="container d-flex justify-content-between">

        <div>

            Email :
            info@example.com

            |

            Phone :
            +91-9876543210

        </div>

        <div class="d-flex align-items-center gap-3">
            @if(auth('student')->check())
                <span class="small text-light">
                    <i class="bi bi-person-check-fill text-success me-1"></i>
                    {{ auth('student')->user()->name }}
                </span>
                <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-light py-0 px-2 text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i> Portal
                </a>
                <form action="{{ route('student.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-white-50 p-0 text-decoration-none" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('student.login') }}" class="text-white text-decoration-none fw-semibold">
                    <i class="bi bi-person-circle me-1"></i> Student Login
                </a>
            @endif
        </div>

    </div>

</div>