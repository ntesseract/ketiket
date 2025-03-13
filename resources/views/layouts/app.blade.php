<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'E-Ticketing Wisata'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Fade-in animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Slide-in animation classes */
        .slide-in-bottom {
            animation: slideInBottom 0.5s ease-in-out;
        }
        
        @keyframes slideInBottom {
            0% { transform: translateY(50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        .slide-in-left {
            animation: slideInLeft 0.5s ease-in-out;
        }
        
        @keyframes slideInLeft {
            0% { transform: translateX(-50px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        
        .slide-in-right {
            animation: slideInRight 0.5s ease-in-out;
        }
        
        @keyframes slideInRight {
            0% { transform: translateX(50px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
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
        
        /* Parallax effect */
        .parallax {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
        <div class="loading-spinner"></div>
    </div>

    @include('layouts.navigation')

    <!-- Page Heading -->
    @hasSection('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
        <div class="fade-in">
            @yield('content')
        </div>
    </main>

    @include('layouts.footer')

    <!-- Notification Toast -->
    <div id="notification-toast" class="fixed bottom-5 right-5 bg-white shadow-lg rounded-lg p-4 transform translate-y-full opacity-0 transition-all duration-300 z-30 max-w-xs">
        <div class="flex items-center">
            <div id="notification-icon" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-bell text-white"></i>
            </div>
            <div class="flex-grow">
                <h3 id="notification-title" class="font-medium text-gray-900 text-sm"></h3>
                <p id="notification-message" class="text-gray-600 text-xs mt-1"></p>
            </div>
            <button onclick="closeToast()" class="flex-shrink-0 ml-2">
                <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
            </button>
        </div>
    </div>

    <!-- Initialize Notification Channel -->
    @auth
    <script>
        // Pusher and Echo initialization should be in your app.js
        // This is just to handle notifications

        // Simulate loading screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-screen').classList.add('opacity-0');
                setTimeout(function() {
                    document.getElementById('loading-screen').style.display = 'none';
                }, 300);
            }, 500);
        });

        // Show toast notification
        function showToast(title, message, type = 'info') {
            const toast = document.getElementById('notification-toast');
            const toastTitle = document.getElementById('notification-title');
            const toastMessage = document.getElementById('notification-message');
            const toastIcon = document.getElementById('notification-icon');
            
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
                default:
                    toastIcon.className = 'flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3';
                    toastIcon.innerHTML = '<i class="fas fa-info text-white"></i>';
            }
            
            // Show toast with animation
            toast.classList.remove('translate-y-full', 'opacity-0');
            
            // Auto-hide after 5 seconds
            setTimeout(closeToast, 5000);
        }

        function closeToast() {
            const toast = document.getElementById('notification-toast');
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
            if (typeof showToast === 'function') {
                showToast(
                    data.title, 
                    data.message, 
                    getNotificationType(data.type)
                );
            }

            // Optionally, update notification list
            loadNotifications();
        });

        // Helper function to determine notification type
        function getNotificationType(type) {
            switch(type) {
                case 'booking': return 'info';
                case 'wallet': return 'success';
                case 'promo': return 'warning';
                default: return 'info';
            }
        }
    });
    </script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    @endauth

    @stack('scripts')
</body>
</html>