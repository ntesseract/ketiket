@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-8 bg-gradient-to-b from-indigo-100 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Banner -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-8 fade-in" data-animate="fade-in">
            <div class="md:flex">
                <div class="p-8 md:flex-1">
                    <h1 class="text-3xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h1>
                    <p class="mt-3 text-gray-600 max-w-3xl">
                        Jelajahi destinasi wisata terbaik di Indonesia, cari akomodasi, dan temukan pengalaman wisata terbaik. Semua dalam satu aplikasi.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('destinations.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-105">
                            <i class="fas fa-search mr-2"></i> Jelajahi Destinasi
                        </a>
                        <a href="{{ route('packages.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out transform hover:scale-105">
                            <i class="fas fa-suitcase mr-2"></i> Lihat Paket Wisata
                        </a>
                    </div>
                </div>
                <div class="relative md:w-1/3 overflow-hidden">
                    <img src="{{ asset('images/dashboard-banner.jpg') }}" alt="Banner" class="h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-white md:from-transparent to-transparent md:to-black md:opacity-60"></div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Balance Card -->
            <div class="bg-white rounded-xl shadow-md p-6 slide-in-bottom" data-animate="slide-in-bottom" style="animation-delay: 100ms;">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-800">Saldo Dompet</h2>
                        <p class="text-sm text-gray-500">Saldo dapat digunakan untuk booking</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-gray-800">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                    <div class="mt-2 flex">
                        <a href="{{ route('wallet.topup') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                            <i class="fas fa-plus-circle mr-1"></i> Top Up Saldo
                        </a>
                        <span class="mx-2 text-gray-300">|</span>
                        <a href="{{ route('wallet.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                            <i class="fas fa-history mr-1"></i> Riwayat Transaksi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Booking Stats -->
            <div class="bg-white rounded-xl shadow-md p-6 slide-in-bottom" data-animate="slide-in-bottom" style="animation-delay: 200ms;">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-800">Booking Saya</h2>
                        <p class="text-sm text-gray-500">Total {{ $bookingStats['total'] }} booking</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 hover:bg-blue-100 transition-colors duration-200">
                        <div class="text-blue-800 font-semibold">{{ $bookingStats['upcoming'] }}</div>
                        <div class="text-xs text-blue-600">Akan Datang</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 hover:bg-green-100 transition-colors duration-200">
                        <div class="text-green-800 font-semibold">{{ $bookingStats['completed'] }}</div>
                        <div class="text-xs text-green-600">Selesai</div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('booking.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        <i class="fas fa-calendar-alt mr-1"></i> Lihat Semua Booking
                    </a>
                </div>
            </div>

            <!-- Favorites Stats -->
            <div class="bg-white rounded-xl shadow-md p-6 slide-in-bottom" data-animate="slide-in-bottom" style="animation-delay: 300ms;">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-heart text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-800">Favorit Saya</h2>
                        <p class="text-sm text-gray-500">Destinasi yang Anda simpan</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-gray-800">{{ $favorites->count() }}</div>
                    <div class="mt-2">
                        <a href="{{ route('destinations.favorites') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                            <i class="fas fa-heart mr-1"></i> Lihat Favorit Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Rows -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="col-span-1 md:col-span-2 space-y-8">
                <!-- Upcoming Bookings -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-left" data-animate="slide-in-left">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Booking Akan Datang</h2>
                        <a href="{{ route('booking.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="px-6 py-4">
                        @if($upcomingBookings->count() > 0)
                            <div class="space-y-4">
                                @foreach($upcomingBookings as $index => $booking)
                                    <div class="bg-white border border-gray-200 hover:border-indigo-200 rounded-lg p-4 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-md" data-animate="fade-in" style="animation-delay: {{ 100 * $index }}ms;">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-16 h-16 rounded-md overflow-hidden bg-gray-100 mr-4">
                                                @if($booking->destination && $booking->destination->image)
                                                    <img src="{{ Storage::url($booking->destination->image) }}" alt="{{ $booking->destination->name }}" class="w-full h-full object-cover">
                                                @elseif($booking->hotel && $booking->hotel->image)
                                                    <img src="{{ Storage::url($booking->hotel->image) }}" alt="{{ $booking->hotel->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <i class="fas fa-map-marker-alt text-gray-400 text-2xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-md font-semibold text-gray-800 truncate">
                                                    @if($booking->destination)
                                                        {{ $booking->destination->name }}
                                                    @elseif($booking->is_package)
                                                        Paket Wisata
                                                    @else
                                                        Booking #{{ $booking->id }}
                                                    @endif
                                                </h3>
                                                <div class="mt-1 flex items-center">
                                                    <div class="flex items-center text-sm text-gray-500 mr-3">
                                                        <i class="far fa-calendar-alt mr-1 text-indigo-500"></i>
                                                        {{ $booking->visit_date->format('d M Y') }}
                                                    </div>
                                                    <div class="flex items-center text-sm text-gray-500">
                                                        <i class="fas fa-ticket-alt mr-1 text-indigo-500"></i>
                                                        {{ $booking->number_of_tickets }} tiket
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex items-center text-sm">
                                                    <span class="px-2 py-1 text-xs rounded-full {{ 
                                                        $booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                        ($booking->status == 'confirmed' ? 'bg-green-100 text-green-800' : 
                                                        ($booking->status == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'))
                                                    }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                    <span class="ml-3 text-gray-700 font-medium">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('booking.show', $booking->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="text-gray-400 mb-3">
                                    <i class="far fa-calendar-alt text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Booking Akan Datang</h3>
                                <p class="text-gray-500 mb-4">Ayo jelajahi destinasi wisata dan buat booking baru!</p>
                                <a href="{{ route('destinations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-search mr-1"></i> Jelajahi Destinasi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recommended Destinations -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: 100ms;">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Rekomendasi Untuk Anda</h2>
                        <a href="{{ route('destinations.recommended') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($recommendedDestinations->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($recommendedDestinations as $index => $destination)
                                    <div class="bg-white rounded-lg overflow-hidden border border-gray-200 hover:border-indigo-300 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1" data-animate="fade-in" style="animation-delay: {{ 150 * $index }}ms;">
                                        <div class="relative h-40">
                                            @if($destination->image)
                                                <img src="{{ Storage::url($destination->image) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                                    <i class="fas fa-mountain text-gray-400 text-4xl"></i>
                                                </div>
                                            @endif
                                            <div class="absolute top-0 right-0 p-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-tag mr-1"></i> Rp {{ number_format($destination->price, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <h3 class="text-md font-semibold text-gray-800 truncate">{{ $destination->name }}</h3>
                                            <div class="mt-1 flex items-center">
                                                <div class="flex items-center text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $destination->averageRating())
                                                            <i class="fas fa-star text-xs"></i>
                                                        @else
                                                            <i class="far fa-star text-xs"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="ml-1 text-xs text-gray-500">({{ $destination->reviews->count() }} reviews)</span>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-map-marker-alt mr-1 text-indigo-500"></i>
                                                {{ Str::limit($destination->location, 30) }}
                                            </div>
                                            <div class="mt-3 flex justify-between items-center">
                                                <a href="{{ route('destinations.show', $destination->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                                    Lihat Detail
                                                </a>
                                                <form action="{{ route('destinations.toggleFavorite', $destination->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                                                        <i class="far fa-heart"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="text-gray-400 mb-3">
                                    <i class="fas fa-compass text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Rekomendasi</h3>
                                <p class="text-gray-500 mb-4">Telusuri dan simpan destinasi favorit untuk mendapatkan rekomendasi yang lebih baik</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Featured Packages -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: 200ms;">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Paket Wisata Unggulan</h2>
                        <a href="{{ route('packages.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($featuredPackages->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($featuredPackages as $index => $package)
                                    <div class="bg-white rounded-lg overflow-hidden border border-gray-200 hover:border-green-300 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1" data-animate="fade-in" style="animation-delay: {{ 150 * $index }}ms;">
                                        <div class="flex">
                                            <div class="flex-shrink-0 w-32 h-32 md:w-40 md:h-40">
                                                @if($package->image)
                                                    <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                                        <i class="fas fa-suitcase text-gray-400 text-4xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-4 flex-1">
                                                <div class="flex justify-between items-start">
                                                    <h3 class="text-md font-semibold text-gray-800">{{ $package->name }}</h3>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $package->duration_days }} hari
                                                    </span>
                                                </div>
                                                <div class="mt-1 text-sm text-gray-500 line-clamp-2">
                                                    {{ Str::limit($package->description, 100) }}
                                                </div>
                                                <div class="mt-2 flex items-center text-sm">
                                                    <div class="mr-3 flex items-center">
                                                        <i class="fas fa-map-marker-alt mr-1 text-indigo-500"></i>
                                                        <span>{{ $package->destinations_count }} Destinasi</span>
                                                    </div>
                                                    <div class="mr-3 flex items-center">
                                                        <i class="fas fa-hotel mr-1 text-indigo-500"></i>
                                                        <span>{{ $package->hotels_count }} Hotel</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-utensils mr-1 text-indigo-500"></i>
                                                        <span>{{ $package->restaurants_count }} Restoran</span>
                                                    </div>
                                                </div>
                                                <div class="mt-3 flex justify-between items-center">
                                                    <div class="font-semibold text-indigo-600">
                                                        Rp {{ number_format($package->price, 0, ',', '.') }}
                                                    </div>
                                                    <a href="{{ route('packages.show', $package->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        Lihat Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="text-gray-400 mb-3">
                                    <i class="fas fa-suitcase text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Paket Wisata</h3>
                                <p class="text-gray-500">Paket wisata akan segera tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Notifications -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Notifikasi Terbaru</h2>
                        <a href="{{ route('notifications.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @if($unreadNotifications->count() > 0)
                            @foreach($unreadNotifications as $index => $notification)
                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200" data-animate="fade-in" style="animation-delay: {{ 100 * $index }}ms;">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full 
                                                {{ $notification->type == 'booking' ? 'bg-blue-100 text-blue-600' : 
                                                   ($notification->type == 'wallet' ? 'bg-green-100 text-green-600' : 
                                                   ($notification->type == 'promo' ? 'bg-yellow-100 text-yellow-600' : 'bg-indigo-100 text-indigo-600')) }}">
                                                @if($notification->type == 'booking')
                                                    <i class="fas fa-ticket-alt"></i>
                                                @elseif($notification->type == 'wallet')
                                                    <i class="fas fa-wallet"></i>
                                                @elseif($notification->type == 'promo')
                                                    <i class="fas fa-tag"></i>
                                                @else
                                                    <i class="fas fa-bell"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($notification->message, 80) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-6 py-8 text-center">
                                <div class="text-gray-400 mb-3">
                                    <i class="far fa-bell-slash text-4xl"></i>
                                </div>
                                <p class="text-gray-500">Tidak ada notifikasi baru</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Favorite Destinations -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 100ms;">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Destinasi Favorit</h2>
                        <a href="{{ route('destinations.favorites') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @if($favorites->count() > 0)
                            @foreach($favorites->take(4) as $index => $favorite)
                                <div class="p-4 hover:bg-gray-50 transition-colors duration-200" data-animate="fade-in" style="animation-delay: {{ 100 * $index }}ms;">
                                    <div class="flex">
                                        <div class="flex-shrink-0 w-16 h-16 rounded-md overflow-hidden bg-gray-100">
                                            @if($favorite->image)
                                                <img src="{{ Storage::url($favorite->image) }}" alt="{{ $favorite->name }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-mountain text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $favorite->name }}</h3>
                                            <div class="flex items-center mt-1">
                                                <div class="flex items-center text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $favorite->averageRating())
                                                            <i class="fas fa-star text-xs"></i>
                                                        @else
                                                            <i class="far fa-star text-xs"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="ml-1 text-xs text-gray-500">({{ $favorite->reviews->count() }})</span>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500 truncate">
                                                <i class="fas fa-map-marker-alt mr-1 text-indigo-500"></i>
                                                {{ $favorite->location }}
                                            </p>
                                        </div>
                                        <div class="ml-2">
                                            <a href="{{ route('destinations.show', $favorite->id) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium text-indigo-700 hover:text-indigo-800 focus:outline-none">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-6 py-8 text-center">
                                <div class="text-gray-400 mb-3">
                                    <i class="far fa-heart text-4xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4">Belum ada destinasi favorit</p>
                                <a href="{{ route('destinations.index') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Jelajahi Destinasi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Wallet Transaction -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 200ms;">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Transaksi Terakhir</h2>
                        <a href="{{ route('wallet.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div>
                        @php
                            $transactions = App\Models\WalletTransaction::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->take(3)
                                ->get();
                        @endphp

                        @if($transactions->count() > 0)
                            <div class="divide-y divide-gray-200">
                                @foreach($transactions as $index => $transaction)
                                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200" data-animate="fade-in" style="animation-delay: {{ 100 * $index }}ms;">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full 
                                                        {{ $transaction->type == 'top_up' ? 'bg-green-100 text-green-600' : 
                                                        ($transaction->type == 'refund' ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600') }}">
                                                        @if($transaction->type == 'top_up')
                                                            <i class="fas fa-plus"></i>
                                                        @elseif($transaction->type == 'refund')
                                                            <i class="fas fa-undo"></i>
                                                        @else
                                                            <i class="fas fa-minus"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $transaction->type == 'top_up' ? 'Top Up' : ($transaction->type == 'refund' ? 'Refund' : 'Pembayaran') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold {{ $transaction->type == 'payment' ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $transaction->type == 'payment' ? '-' : '+' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </p>
                                                <p class="text-xs text-gray-500">{{ Str::limit($transaction->description, 20) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-8 text-center">
                                <div class="text-gray-400 mb-3">
                                    <i class="fas fa-wallet text-4xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4">Belum ada transaksi wallet</p>
                                <a href="{{ route('wallet.topup') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-plus mr-1"></i> Top Up Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Support Chat -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 300ms;">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Bantuan & Dukungan</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">Ada pertanyaan atau membutuhkan bantuan? Tim kami siap membantu Anda kapan saja.</p>
                        <a href="{{ route('chat.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <i class="fas fa-comments mr-2"></i> Mulai Chat dengan CS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize animation observers
        const animatedElements = document.querySelectorAll('[data-animate]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animation = element.getAttribute('data-animate');
                    
                    // Add the animation class
                    element.classList.add(animation);
                    element.style.opacity = 1;
                    
                    // Stop observing after animation is applied
                    observer.unobserve(element);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(element => {
            element.style.opacity = 0;
            observer.observe(element);
        });
        
        // Random welcome message
        const welcomeMessages = [
            "Mau ke mana hari ini?",
            "Saatnya jelajahi Indonesia!",
            "Temukan destinasi impianmu.",
            "Yuk, liburan sekarang!",
            "Ada promo menarik hari ini!"
        ];
        
        // Get current hour for time-based greeting
        const currentHour = new Date().getHours();
        let greeting = "Halo";
        
        if (currentHour < 12) {
            greeting = "Selamat pagi";
        } else if (currentHour < 17) {
            greeting = "Selamat siang";
        } else {
            greeting = "Selamat malam";
        }
        
        // Update welcome message with random suggestion
        const randomIndex = Math.floor(Math.random() * welcomeMessages.length);
        const welcomeMessage = document.querySelector('h1');
        if (welcomeMessage) {
            welcomeMessage.innerHTML = `${greeting}, ${welcomeMessage.textContent.split(',')[1]}`;
        }
    });
</script>
@endpush
@endsection