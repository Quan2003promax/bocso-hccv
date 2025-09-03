<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    @stack('styles')
</head>

<body class="font-sans antialiased">
    @if (session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="min-h-screen bg-gray-100 flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            @yield('content')
        </main>


        <footer class="bg-gradient-to-r from-gray-100 to-gray-200 border-t border-gray-300 mt-10">
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center justify-between text-sm text-gray-600">

                <!-- Bên trái -->
                <p class="mb-2 md:mb-0">
                    © 2025 <span class="font-semibold text-gray-700">VIETTECHKEY</span>.
                    <span class="italic text-gray-500">Thiết kế & phát triển bởi Phạm Huy Hoàng</span>
                </p>

                <!-- Bên phải -->
                <div class="flex items-center space-x-4">
                    <a href="#" class="hover:text-blue-600 transition">Chính sách</a>
                    <span class="text-gray-400">|</span>
                    <a href="#" class="hover:text-blue-600 transition">Liên hệ</a>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAllDepartments');
    const items = document.querySelectorAll('.department-item');

    checkAll.addEventListener('change', function() {
      items.forEach(cb => cb.checked = checkAll.checked);
    });

    items.forEach(cb => {
      cb.addEventListener('change', function() {
        if (![...items].every(i => i.checked)) {
          checkAll.checked = false;
        } else {
          checkAll.checked = true;
        }
      });
    });
  });
</script>
</html>

</html>