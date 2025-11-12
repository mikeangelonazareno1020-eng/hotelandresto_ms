<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'My Laravel App')</title>

    {{-- ✅ Load Tailwind + JS via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ Favicon (optional) --}}
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        #map { height: 75vh; width: 100%; border-radius: 1rem; }
    </style>
    
</head>
<body class="bg-gray-50 min-h-screen flex flex-col text-gray-900">

    @if(request()->routeIs('landing') || request()->routeIs('login.form'))
        @include('includes.header')
        <main class="flex-1 pt-16 px-0">@yield('content')</main>
    @else
        @if(Auth::check() && in_array(Auth::user()->role, ['Administrator', 'Restaurant Manager', 'Hotel Manager', 'Super Admin']))
            <div class="flex flex-1">
                @include('includes.sidebar')
                <main class="flex-1 px-2 py-2">@yield('content')</main>
            </div>
        @else
            @include('includes.header')
            <main class="flex-1 pt-15">@yield('content')</main>
        @endif
    @endif

    {{-- ⚓ Footer --}}
    @include('includes.footer')

</body>

{{-- Flash toasts (wait for Vite module init) --}}
@if(session('success'))
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            if (window.toast) {
                window.toast({ icon: 'success', title: @json(session('success')) });
            } else if (window.Swal) {
                Swal.fire({ icon: 'success', title: @json(session('success')), timer: 1800, showConfirmButton: false });
            } else {
                setTimeout(function(){
                    if (window.toast) {
                        window.toast({ icon: 'success', title: @json(session('success')) });
                    }
                }, 0);
            }
        });
    </script>
@endif
@if(session('error'))
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            if (window.toast) {
                window.toast({ icon: 'error', title: @json(session('error')) });
            } else if (window.Swal) {
                Swal.fire({ icon: 'error', title: @json(session('error')) });
            } else {
                setTimeout(function(){
                    if (window.toast) {
                        window.toast({ icon: 'error', title: @json(session('error')) });
                    }
                }, 0);
            }
        });
    </script>
@endif

@auth
    <script>
        // If returning via back/forward cache on protected pages, show Page Expired
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.replace("{{ route('expired') }}");
            }
        });
    </script>
@endauth
</html>
