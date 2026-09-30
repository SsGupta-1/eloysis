<!DOCTYPE html>
<html lang="en">

@include('components.admin.head')

<body>

    @include('components.common.loader')

    <div class="admin-wrapper">

        {{-- Header --}}
        @include('components.admin.header')

        <div class="admin-body">

            {{-- Sidebar --}}
            @include('components.admin.sidebar')
            <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

            {{-- Main Content --}}
            <div class="main-wrapper">


                <main class="content-wrapper">

                    <div class="container-fluid">

                        {{-- Breadcrumb --}}
                        @hasSection('breadcrumb')
                            @yield('breadcrumb')
                        @else
                            <x-ui.breadcrumb />
                        @endif

                        {{-- Flash Message --}}
                        @include('components.common.flash-message')

                        {{-- Page Content --}}
                        @yield('content')

                    </div>

                </main>

                {{-- Footer --}}
                @include('components.admin.footer')

            </div>

        </div>

    </div>

    @include('components.admin.scripts')

    @stack('scripts')

</body>

</html>