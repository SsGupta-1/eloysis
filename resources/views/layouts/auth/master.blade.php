<!DOCTYPE html>
<html lang="en">

@include('components.auth.head')

<body class="auth-page">

    {{-- Dynamic Glowing Ambient Shapes --}}
    <div class="auth-bg-shapes">
        <div class="auth-shape auth-shape-1"></div>
        <div class="auth-shape auth-shape-2"></div>
        <div class="auth-shape auth-shape-3"></div>
    </div>

    {{-- Top Action Bar (Theme Toggle) --}}
    <div class="auth-top-bar">
        <x-ui.theme-toggle />
    </div>

    {{-- Main Container --}}
    <div class="auth-container">
        @yield('content')
    </div>

    @include('components.auth.scripts')

    @stack('scripts')

</body>

</html>
