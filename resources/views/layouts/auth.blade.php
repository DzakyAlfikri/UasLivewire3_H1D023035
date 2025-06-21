<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Manajemen Pegawai') }}</title>
    
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    
    <!-- Custom Styles -->
    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(75, 85, 99, 0.1);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(107, 114, 128, 0.5);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.7);
        }
        
        body {
            background-image: url('https://images.unsplash.com/photo-1497366811353-6870744d04b2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .auth-overlay {
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 custom-scroll">
    <div class="auth-overlay min-h-screen flex items-center justify-center p-4">
        <div class="w-full">
            @yield('content')
        </div>
    </div>
</body>
</html>