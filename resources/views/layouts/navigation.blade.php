<nav x-data="{ open: false, userDropdown: false, notificationDropdown: false }" class="bg-white shadow">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="block h-9 w-auto hover:scale-105 transition-transform duration-300">
                        <span class="ml-2 text-xl font-bold text-indigo-600">WisataKu</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all duration-200 h-16">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    
                    <a href="{{ route('destinations.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('destinations.*') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all duration-200 h-16">
                        <i class="fas fa-map-marker-alt mr-1"></i> Destinasi
                    </a>
                    
                    <a href="{{ route('hotels.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('hotels.*') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all duration-200 h-16">
                        <i class="fas fa-hotel mr-1"></i> Hotel
                    </a>
                    
                    <a href="{{ route('restaurants.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('restaurants.*') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all duration-200 h-16">
                        <i class="fas fa-utensils mr-1"></i> Restoran
                    </a>
                    
                    <a href="{{ route('packages.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('packages.*') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all duration-200 h-16">
                        <i class="fas fa-suitcase mr-1"></i> Paket Wisata
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <!-- Notification Dropdown -->
                <div class="relative mr-3" x-data="{ unreadCount: 0 }" x-init="
                    fetch('{{ route('notifications.count') }}')
                        .then(response => response.json())
                        .then(data => { unreadCount = data.count })
                ">
                    <button @click="notificationDropdown = !notificationDropdown; userDropdown = false" class="relative p-1 rounded-full text-gray-600 hover:text-indigo-600 hover:bg-gray-100 transition-all duration-200">
                        <span class="sr-only">Notifikasi</span>
                        <i class="fas fa-bell text-xl"></i>
                        <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"></span>
                    </button>

                    <div x-show="notificationDropdown" @click.away="notificationDropdown = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 origin-top" style="display: none;">
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                            </div>
                            
                            <!-- Notification items will be loaded here -->
                            <div id="notification-items" class="divide-y divide-gray-100 animate-pulse">
                                <div class="px-4 py-3">
                                    <div class="h-2 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                                </div>
                                <div class="px-4 py-3">
                                    <div class="h-2 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-100 py-2 px-4">
                            <a href="{{ route('notifications.index') }}" class="block text-sm text-center text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                <!-- Wallet Balance -->
                <div class="mr-3">
                    <a href="{{ route('wallet.index') }}" class="inline-flex items-center px-3 py-1 bg-green-50 border border-green-200 rounded-full text-green-600 text-sm font-medium hover:bg-green-100 transition duration-200 group">
                        <i class="fas fa-wallet mr-1 group-hover:animate-bounce"></i>
                        <span id="wallet-balance">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</span>
                    </a>
                </div>

                <!-- Booking Status -->
                <div class="mr-3">
                    <a href="{{ route('booking.index') }}" class="inline-flex items-center px-3 py-1 bg-blue-50 border border-blue-200 rounded-full text-blue-600 text-sm font-medium hover:bg-blue-100 transition duration-200">
                        <i class="fas fa-ticket-alt mr-1"></i> Booking
                    </a>
                </div>

                <!-- Chat Button -->
                <div class="mr-3">
                    <a href="{{ route('chat.index') }}" class="p-1 rounded-full text-gray-600 hover:text-indigo-600 hover:bg-gray-100 transition-all duration-200 relative">
                        <span class="sr-only">Chat</span>
                        <i class="fas fa-comments text-xl"></i>
                        <span id="unread-chat-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full hidden">0</span>
                    </a>
                </div>

                <!-- Profile Dropdown -->
                <div class="ml-3 relative">
                    <div>
                        <button @click="userDropdown = !userDropdown; notificationDropdown = false" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-indigo-300 transition duration-150 ease-in-out overflow-hidden bg-gray-100">
                            @if(auth()->user()->profile_picture)
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full flex items-center justify-center bg-indigo-600 text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </button>
                    </div>

                    <div x-show="userDropdown" @click.away="userDropdown = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 origin-top-right" style="display: none;">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                        
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <i class="fas fa-user-circle mr-1"></i> Profil
                        </a>
                        
                        <a href="{{ route('booking.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <i class="fas fa-history mr-1"></i> Riwayat Perjalanan
                        </a>
                        
                        <a href="{{ route('destinations.favorites') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <i class="fas fa-heart mr-1"></i> Favorit
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard.index') }}" class="block px-4 py-2 text-sm text-indigo-600 font-medium hover:bg-indigo-50 transition-colors duration-200">
                                <i class="fas fa-cogs mr-1"></i> Admin Dashboard
                            </a>
                        @endif
                        
                        <div class="border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="flex space-x-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 hover:text-indigo-800 transition-all duration-200">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all duration-200">
                        Register
                    </a>
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden slide-in-top">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition">
                <i class="fas fa-home mr-1"></i> Dashboard
            </a>
            
            <a href="{{ route('destinations.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('destinations.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition">
                <i class="fas fa-map-marker-alt mr-1"></i> Destinasi
            </a>
            
            <a href="{{ route('hotels.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('hotels.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition">
                <i class="fas fa-hotel mr-1"></i> Hotel
            </a>
            
            <a href="{{ route('restaurants.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('restaurants.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition">
                <i class="fas fa-utensils mr-1"></i> Restoran
            </a>
            
            <a href="{{ route('packages.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('packages.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition">
                <i class="fas fa-suitcase mr-1"></i> Paket Wisata
            </a>
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    @if(auth()->user()->profile_picture)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url(auth()->user()->profile_picture) }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-indigo-600 text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="ml-3">
                    <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-user-circle mr-1"></i> Profil
                </a>
                
                <a href="{{ route('wallet.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-wallet mr-1"></i> Wallet
                </a>
                
                <a href="{{ route('booking.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-ticket-alt mr-1"></i> Booking
                </a>
                
                <a href="{{ route('notifications.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-bell mr-1"></i> Notifikasi
                </a>
                
                <a href="{{ route('chat.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-comments mr-1"></i> Chat
                </a>
                
                <a href="{{ route('booking.history') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-history mr-1"></i> Riwayat Perjalanan
                </a>
                
                <a href="{{ route('destinations.favorites') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition">
                    <i class="fas fa-heart mr-1"></i> Favorit
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition">
                        <i class="fas fa-cogs mr-1"></i> Admin Dashboard
                    </a>
                @endif
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-red-600 hover:text-red-800 hover:bg-red-50 hover:border-red-300 focus:outline-none focus:text-red-800 focus:bg-red-50 focus:border-red-300 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="py-3 border-t border-gray-200 flex flex-col space-y-2 px-4">
            <a href="{{ route('login') }}" class="w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 hover:text-indigo-800 transition-all duration-200">
                Log in
            </a>
            <a href="{{ route('register') }}" class="w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all duration-200">
                Register
            </a>
        </div>
        @endauth
    </div>
</nav>

<script>
    // Dynamic notification loading
    document.addEventListener('DOMContentLoaded', function() {
        @auth
        // Load notification count every minute
        function loadNotificationCount() {
    fetch('{{ route('notifications.count') }}')
        .then(response => response.json())
        .then(data => {
            const unreadCountElement = document.querySelector('[x-data="{ unreadCount: 0 }"]');
            if (unreadCountElement) {
                unreadCountElement.__x.$data.unreadCount = data.count;
            }
            
            // Update unread count display
            const unreadCountDisplay = document.getElementById('unread-notification-count');
            if (unreadCountDisplay) {
                if (data.count > 0) {
                    unreadCountDisplay.textContent = data.count;
                    unreadCountDisplay.classList.remove('hidden');
                } else {
                    unreadCountDisplay.classList.add('hidden');
                }
            }
        });
}
        
        loadNotificationCount();
        setInterval(loadNotificationCount, 60000); // Check every minute
        
        // Load notifications when dropdown opens
        function loadNotifications() {
            const notificationItems = document.getElementById('notification-items');
            
            if (notificationItems) {
                notificationItems.innerHTML = '<div class="px-4 py-3"><div class="h-2 bg-gray-200 rounded w-3/4 mb-2"></div><div class="h-2 bg-gray-200 rounded w-1/2"></div></div>';
                
                fetch('{{ route('notifications.index') }}?format=json')
                    .then(response => response.json())
                    .then(data => {
                        if (data.notifications && data.notifications.length > 0) {
                            notificationItems.innerHTML = '';
                            
                            data.notifications.slice(0, 5).forEach(notification => {
                                const notificationElement = document.createElement('a');
                                notificationElement.href = '{{ route('notifications.index') }}';
                                notificationElement.className = 'block px-4 py-2 hover:bg-gray-50 transition-colors duration-150' + (notification.is_read ? '' : ' bg-blue-50');
                                
                                const icon = getNotificationIcon(notification.type);
                                
                                notificationElement.innerHTML = `
                                    <div class="flex">
                                        <div class="flex-shrink-0 mr-3">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full ${icon.bgColor}">
                                                <i class="${icon.icon} text-white text-sm"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                            <p class="text-xs text-gray-500 truncate">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${timeAgo(new Date(notification.created_at))}</p>
                                        </div>
                                    </div>
                                `;
                                
                                notificationItems.appendChild(notificationElement);
                            });
                        } else {
                            notificationItems.innerHTML = '<div class="px-4 py-3 text-center text-gray-500">Tidak ada notifikasi</div>';
                        }
                    })
                    .catch(() => {
                        notificationItems.innerHTML = '<div class="px-4 py-3 text-center text-gray-500">Gagal memuat notifikasi</div>';
                    });
            }
        }
        
        // Listen for notification dropdown toggle
        document.addEventListener('click', function(event) {
            const target = event.target;
            
            if (target.closest('[x-data="{ unreadCount: 0 }"] button')) {
                setTimeout(loadNotifications, 100);
            }
        });
        
        // Helper function to get notification icon
        function getNotificationIcon(type) {
            switch(type) {
                case 'booking':
                    return { icon: 'fas fa-ticket-alt', bgColor: 'bg-purple-500' };
                case 'wallet':
                    return { icon: 'fas fa-wallet', bgColor: 'bg-green-500' };
                case 'promo':
                    return { icon: 'fas fa-tag', bgColor: 'bg-yellow-500' };
                case 'review':
                    return { icon: 'fas fa-star', bgColor: 'bg-blue-500' };
                default:
                    return { icon: 'fas fa-bell', bgColor: 'bg-gray-500' };
            }
        }
        
        // Helper function for time ago
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            
            let interval = Math.floor(seconds / 31536000);
            if (interval > 1) return interval + ' tahun yang lalu';
            
            interval = Math.floor(seconds / 2592000);
            if (interval > 1) return interval + ' bulan yang lalu';
            
            interval = Math.floor(seconds / 86400);
            if (interval > 1) return interval + ' hari yang lalu';
            
            interval = Math.floor(seconds / 3600);
            if (interval > 1) return interval + ' jam yang lalu';
            
            interval = Math.floor(seconds / 60);
            if (interval > 1) return interval + ' menit yang lalu';
            
            return 'baru saja';
        }
        
        // Load unread chat count
        function loadUnreadChatCount() {
            fetch('{{ route('chat.unreadCount') }}')
                .then(response => response.json())
                .then(data => {
                    const unreadChatCount = document.getElementById('unread-chat-count');
                    if (unreadChatCount) {
                        if (data.count > 0) {
                            unreadChatCount.textContent = data.count;
                            unreadChatCount.classList.remove('hidden');
                        } else {
                            unreadChatCount.classList.add('hidden');
                        }
                    }
                });
        }
        
        loadUnreadChatCount();
        setInterval(loadUnreadChatCount, 30000); // Check every 30 seconds
        @endauth
    });
</script>