<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Manajemen Pegawai') }}</title>
    
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
    
    <style>
        .glass-dark {
            background: rgba(23, 23, 23, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #262626;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 3px;
        }
        
        /* Additional styles to ensure Bootstrap components still look good */
        .btn-primary {
            background: #f97316 !important;
            border-color: #f97316 !important;
        }
        .btn-success {
            background: #10b981 !important;
            border-color: #10b981 !important;
        }
        .btn-danger {
            background: #ef4444 !important;
            border-color: #ef4444 !important;
        }
        .btn-warning {
            background: #f59e0b !important;
            border-color: #f59e0b !important;
            color: white !important;
        }
        .card {
            background: rgba(31, 41, 55, 0.7) !important;
            border: 1px solid #374151 !important;
            border-radius: 1rem !important;
        }
        .card-header {
            background: rgba(31, 41, 55, 0.9) !important;
            border-bottom: 1px solid #374151 !important;
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }
        .table {
            color: #e5e7eb !important;
        }
        .form-control, .form-select {
            background-color: rgba(31, 41, 55, 0.8) !important;
            border: 1px solid #4b5563 !important;
            color: #e5e7eb !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.25) !important;
        }
        .form-label {
            color: #e5e7eb !important;
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.2) !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
            color: #10b981 !important;
        }
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.2) !important;
            border-color: rgba(239, 68, 68, 0.5) !important;
            color: #ef4444 !important;
        }
        .modal-content {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
            color: #e5e7eb !important;
        }
        .dropdown-menu {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
        }
        .dropdown-item {
            color: #e5e7eb !important;
        }
        .dropdown-item:hover {
            background-color: #374151 !important;
            color: #f97316 !important;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 custom-scroll">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @auth
        <div class="w-72 glass-dark border-r border-gray-800 text-gray-300 h-screen fixed">
            <!-- Profile Section -->
            <div class="p-8 text-center">
                <div class="w-24 h-24 bg-gradient-to-tr from-orange-500 to-orange-600 rounded-[24px] mx-auto mb-4 flex items-center justify-center shadow-lg shadow-orange-500/20">
                    <i class="fas fa-building text-white text-4xl"></i>
                </div>
                <h3 class="font-semibold text-2xl text-white">Bhatari Corporate</h3>
                <p class="text-sm text-gray-400 mt-1">Employee Management System</p>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-xs px-4 py-1.5 rounded-full mt-3 inline-block text-white shadow-lg shadow-orange-500/20">
                    EST. 2023
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-6 space-y-1">
                @if(Auth::user()->role === 'pegawai')
                    <!-- Employee Navigation -->
                    <a href="{{ route('pegawai.dashboard') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('pegawai.dashboard') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('absensi.pegawai') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('absensi.pegawai') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-clipboard-check w-5"></i>
                        <span>Absensi</span>
                    </a>
                    <a href="{{ route('cuti.create') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('cuti.create') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-calendar-alt w-5"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                    <a href="{{ route('gaji.pegawai') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('gaji.pegawai') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-money-bill-wave w-5"></i>
                        <span>Informasi Gaji</span>
                    </a>
                @else
                    <!-- Admin Navigation -->
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('pegawai') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('pegawai') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-users w-5"></i>
                        <span>Pegawai</span>
                    </a>
                    <a href="{{ route('jabatan') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('jabatan') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-briefcase w-5"></i>
                        <span>Jabatan</span>
                    </a>
                    <a href="{{ route('unit-kerja') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('unit-kerja') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-building w-5"></i>
                        <span>Unit Kerja</span>
                    </a>
                    <a href="{{ route('cuti') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('cuti') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-calendar-alt w-5"></i>
                        <span>Pengajuan Cuti</span>
                    </a>
                    <a href="{{ route('absensi') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('absensi') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-clipboard-check w-5"></i>
                        <span>Absensi</span>
                    </a>
                    <a href="{{ route('laporan-absensi') }}" class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 {{ request()->routeIs('laporan-absensi') ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400' }}">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Laporan Absensi</span>
                    </a>
                @endif
                
                <div class="mt-6 border-t border-gray-800 pt-4">
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="flex items-center space-x-4 px-4 py-3.5 rounded-2xl hover:bg-gray-800/50 transition-all duration-200 text-gray-400">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </nav>
        </div>
        @endauth

        <!-- Main Content -->
        <div class="flex-1 {{ Auth::check() ? 'ml-72' : '' }}">
            <!-- Header -->
            <div class="flex justify-between items-center p-6 bg-gray-800/50 border-b border-gray-700">
                <div class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600">
                    Sistem Manajemen Pegawai
                </div>
                
                @auth
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search" 
                               class="bg-gray-800/50 border border-gray-700 rounded-2xl px-5 py-2 pl-10
                                      focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200
                                      text-gray-300 placeholder-gray-500 w-60">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                    </div>
                    <button class="p-2.5 hover:bg-gray-800/70 rounded-2xl transition-colors">
                        <i class="fas fa-bell text-gray-400 hover:text-orange-500"></i>
                    </button>
                </div>
                @else
                <div>
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white px-3 py-2">Login</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-full ml-2 hover:shadow-lg hover:shadow-orange-500/20 transition-all">Register</a>
                </div>
                @endauth
            </div>

            <!-- Page Content -->
            <div class="p-6">
                @auth
                    {{ $slot ?? '' }}
                    @yield('content')
                @else
                <div class="flex items-center justify-center min-h-[calc(100vh-100px)]">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
                @endauth
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('closeModal', () => {
                let modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    let modalInstance = bootstrap.Modal.getInstance(modal);
                    if(modalInstance) {
                        modalInstance.hide();
                    }
                });
            });
        });
    </script>
</body>
</html>