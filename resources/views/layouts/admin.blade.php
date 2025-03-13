<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | {{ config('app.name', 'E-Ticketing Wisata') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Fade-in animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Slide-in animation */
        .slide-in-bottom {
            animation: slideInBottom 0.5s ease-in-out;
        }
        
        @keyframes slideInBottom {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        /* Loading animation */
        .loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #4F46E5;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Admin sidebar animation */
        .sidebar-enter {
            animation: sidebarEnter 0.3s ease-in-out;
        }
        
        @keyframes sidebarEnter {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(0); }
        }
        
        /* Admin sidebar hover effect */
        .admin-nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .admin-nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: currentColor;
            transition: width 0.3s ease;
        }
        
        .admin-nav-link:hover::after {
            width: 100%;
        }
        
        /* Active page indicator animation */
        .nav-indicator {
            transition: transform 0.3s ease-in-out;
        }
        
        .nav-link:hover .nav-indicator {
            transform: translateX(5px);
        }
        
        /* Top-up request badge animation */
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        
        .pulse-badge {
            animation: pulse 2s infinite;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen" x-data="{ sidebarOpen: false }">
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
        <div class="loading-spinner"></div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-black bg-opacity-50 sm:hidden" 
         @click="sidebarOpen = false">
    </div>

    <!-- Sidebar -->
    <aside x-show="sidebarOpen" x-cloak class="sidebar-enter fixed inset-y-0 left-0 z-40 w-64 bg-indigo-800 text-white overflow-y-auto sm:translate-x-0 transform transition-transform duration-300 ease-in-out sm:static sm:block" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
        <div class="px-4 py-6 flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-center mb-8">
                <a href="{{ route('admin.dashboard.index') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto">
                    <span class="ml-2 text-xl font-bold text-white">Admin Panel</span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="space-y-1 flex-grow">
                <a href="{{ route('admin.dashboard.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                    @if(request()->routeIs('admin.dashboard.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <!-- Top Up Menu -->
                <a href="{{ route('admin.topup.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.topup.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-wallet w-6"></i>
                    <span>Top Up Management</span>
                    <?php
                    $pendingTopUpCount = App\Models\TopUpRequest::where('status', 'pending')->count();
                    ?>
                    @if($pendingTopUpCount > 0)
                        <span class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-semibold text-white pulse-badge">
                            {{ $pendingTopUpCount }}
                        </span>
                    @elseif(request()->routeIs('admin.topup.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.destinations.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.destinations.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-map-marker-alt w-6"></i>
                    <span>Destinasi</span>
                    @if(request()->routeIs('admin.destinations.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.hotels.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.hotels.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-hotel w-6"></i>
                    <span>Hotel</span>
                    @if(request()->routeIs('admin.hotels.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.restaurants.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.restaurants.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-utensils w-6"></i>
                    <span>Restoran</span>
                    @if(request()->routeIs('admin.restaurants.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.packages.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.packages.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-suitcase w-6"></i>
                    <span>Paket Wisata</span>
                    @if(request()->routeIs('admin.packages.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.bookings.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.bookings.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-ticket-alt w-6"></i>
                    <span>Booking</span>
                    @if(request()->routeIs('admin.bookings.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-users w-6"></i>
                    <span>Pengguna</span>
                    @if(request()->routeIs('admin.users.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.reviews.index') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.reviews.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-star w-6"></i>
                    <span>Review</span>
                    @if(request()->routeIs('admin.reviews.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
                
                <a href="{{ route('admin.qrcode.verify') }}" class="admin-nav-link flex items-center px-4 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.qrcode.*') ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700' }} transition-colors duration-200">
                    <i class="fas fa-qrcode w-6"></i>
                    <span>QR Verification</span>
                    @if(request()->routeIs('admin.qrcode.*'))
                        <span class="ml-auto nav-indicator">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </a>
            </nav>
            
            <!-- User Info -->
            <div class="mt-auto pt-4 border-t border-indigo-700">
                <div class="flex items-center px-4 py-3">
                    @if(auth()->user()->profile_picture)
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="h-8 w-8 rounded-full flex items-center justify-center bg-indigo-600 text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-medium text-indigo-300">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <div class="mt-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="admin-nav-link flex items-center px-4 py-2 text-sm font-medium text-indigo-100 hover:bg-indigo-700 rounded-md transition-colors duration-200">
                        <i class="fas fa-home w-6"></i>
                        <span>Kembali ke Website</span>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm font-medium text-indigo-100 hover:bg-indigo-700 rounded-md transition-colors duration-200 text-left">
                            <i class="fas fa-sign-out-alt w-6"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300" :class="{'sm:ml-64': !sidebarOpen}">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- Page Title -->
                    <h1 class="text-xl font-semibold text-gray-700">@yield('header', 'Admin Dashboard')</h1>
                    
                    <!-- Right side actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Top Up Request Notifications -->
                        <div class="relative" x-data="{ topupNotificationsOpen: false }">
                            <button @click="topupNotificationsOpen = !topupNotificationsOpen" class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 relative">
                                <span class="sr-only">Top Up Requests</span>
                                <i class="fas fa-wallet"></i>
                                @if($pendingTopUpCount > 0)
                                <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 flex items-center justify-center text-xs text-white pulse-badge">
                                    {{ $pendingTopUpCount }}
                                </span>
                                @endif
                            </button>
                            
                            <!-- Top Up notifications dropdown -->
                            <div x-show="topupNotificationsOpen" @click.away="topupNotificationsOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-800">Top Up Requests</h3>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @if($pendingTopUpCount > 0)
                                        <?php
                                        $recentTopUps = App\Models\TopUpRequest::with('user')
                                            ->where('status', 'pending')
                                            ->latest()
                                            ->limit(5)
                                            ->get();
                                        ?>
                                        @foreach($recentTopUps as $topUp)
                                        <a href="{{ route('admin.topup.show', $topUp->id) }}" class="block px-4 py-2 hover:bg-gray-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500">
                                                    <i class="fas fa-wallet"></i>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ $topUp->user->name }}</p>
                                                    <p class="text-xs text-gray-500">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</p>
                                                </div>
                                                <span class="ml-2 text-xs text-gray-400">
                                                    {{ $topUp->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </a>
                                        @endforeach
                                        <div class="px-4 py-2 border-t border-gray-100 text-center">
                                            <a href="{{ route('admin.topup.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                                View all requests
                                            </a>
                                        </div>
                                    @else
                                        <div class="px-4 py-2 text-sm text-gray-500">No pending top up requests</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Regular notifications -->
                        <div class="relative" x-data="{ notificationsOpen: false }">
                            <button @click="notificationsOpen = !notificationsOpen" class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">View notifications</span>
                                <i class="fas fa-bell"></i>
                            </button>
                            
                            <!-- Admin notifications dropdown -->
                            <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <!-- Notifications will be dynamically loaded -->
                                    <div class="px-4 py-2 text-sm text-gray-500">No new notifications</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Admin quick actions dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span>Quick Actions</span>
                                <i class="ml-1 fas fa-chevron-down"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.topup.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-wallet mr-2 text-blue-500"></i>
                                        <span>Kelola Top Up</span>
                                    </a>
                                    <a href="{{ route('admin.destinations.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                                        <span>Tambah Destinasi</span>
                                    </a>
                                    <a href="{{ route('admin.hotels.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                                        <span>Tambah Hotel</span>
                                    </a>
                                    <a href="{{ route('admin.packages.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                                        <span>Tambah Paket Wisata</span>
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="{{ route('admin.qrcode.scanner') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-qrcode mr-2 text-indigo-500"></i>
                                        <span>Scan QR Code</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 fade-in">
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 slide-in-bottom bg-green-50 border-l-4 border-green-400 p-4 shadow-md rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        {{ session('success') }}
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button @click="show = false" class="inline-flex text-green-500 hover:text-green-600 focus:outline-none">
                                            <span class="sr-only">Dismiss</span>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 slide-in-bottom bg-red-50 border-l-4 border-red-400 p-4 shadow-md rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        {{ session('error') }}
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button @click="show = false" class="inline-flex text-red-500 hover:text-red-600 focus:outline-none">
                                            <span class="sr-only">Dismiss</span>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Notification Toast -->
    <div id="admin-notification-toast" class="fixed bottom-5 right-5 bg-white shadow-lg rounded-lg p-4 transform translate-y-full opacity-0 transition-all duration-300 z-50 max-w-xs">
        <div class="flex items-center">
            <div id="admin-notification-icon" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-bell text-white"></i>
            </div>
            <div class="flex-grow">
                <h3 id="admin-notification-title" class="font-medium text-gray-900 text-sm"></h3>
                <p id="admin-notification-message" class="text-gray-600 text-xs mt-1"></p>
            </div>
            <button onclick="closeAdminToast()" class="flex-shrink-0 ml-2">
                <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
            </button>
        </div>
    </div>

    <script>
        // Simulate loading screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-screen').classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(function() {
                    document.getElementById('loading-screen').style.display = 'none';
                }, 300);
            }, 500);
        });
        
        // Show admin toast notification
        function showAdminToast(title, message, type = 'info') {
            const toast = document.getElementById('admin-notification-toast');
            const toastTitle = document.getElementById('admin-notification-title');
            const toastMessage = document.getElementById('admin-notification-message');
            const toastIcon = document.getElementById('admin-notification-icon');
            
            // Set content
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            
            // Set icon and color based on type
            switch(type) {
                case 'success':
                    toastIcon.className = 'flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3';
                    toastIcon.innerHTML = '<i class="fas fa-check text-white"></i>';
                    break;
                case 'error':
                    toastIcon.className = 'flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-3';
                    toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-white"></i>';
                    break;
                case 'warning':
                    toastIcon.className = 'flex-shrink-0 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mr-3';
                    toastIcon.innerHTML = '<i class="fas fa-exclamation text-white"></i>';
                    break;
                case 'topup':
                    toastIcon.className = 'flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3';
                    toastIcon.innerHTML = '<i class="fas fa-wallet text-white"></i>';
                    break;
                default:
                    toastIcon.className = 'flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3';
                    toastIcon.innerHTML = '<i class="fas fa-info text-white"></i>';
            }
            
            // Show toast with animation
            toast.classList.remove('translate-y-full', 'opacity-0');
            
            // Auto-hide after 5 seconds
            setTimeout(closeAdminToast, 5000);
        }

        function closeAdminToast() {
            const toast = document.getElementById('admin-notification-toast');
            toast.classList.add('translate-y-full', 'opacity-0');
        }
        
        // Animation for elements as they enter viewport
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Pusher
            const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
                encrypted: true
            });

            // Subscribe to user-specific notification channel
            const channel = pusher.subscribe('user-notifications.{{ Auth::id() }}');
            
            // Listen for new notifications
            channel.bind('new-notification', function(data) {
                // Update notification count
                const notificationCountElement = document.getElementById('notification-count');
                if (notificationCountElement) {
                    const currentCount = parseInt(notificationCountElement.textContent) || 0;
                    notificationCountElement.textContent = currentCount + 1;
                }

                // Show toast notification
                showAdminToast(
                    data.title, 
                    data.message, 
                    getNotificationType(data.type)
                );

                // If it's a top-up notification, update the top-up count
                if (data.type === 'admin_topup') {
                    refreshTopUpCount();
                }
            });

            // Helper function to determine notification type
            function getNotificationType(type) {
                switch(type) {
                    case 'booking': return 'info';
                    case 'wallet': return 'success';
                    case 'admin_topup': return 'topup';
                    case 'promo': return 'warning';
                    default: return 'info';
                }
            }
            
            // Function to refresh top-up count (can be called after approving/rejecting)
            window.refreshTopUpCount = function() {
                fetch('/admin/topup/count')
                    .then(response => response.json())
                    .then(data => {
                        const topUpCountElements = document.querySelectorAll('.topup-count');
                        topUpCountElements.forEach(el => {
                            if (data.count > 0) {
                                el.textContent = data.count;
                                el.classList.remove('hidden');
                                el.classList.add('pulse-badge');
                            } else {
                                el.classList.add('hidden');
                            }
                        });
                    })
                    .catch(error => console.error('Error refreshing top-up count:', error));
            };
        });
    </script>

    @stack('scripts')
</body>
</html>