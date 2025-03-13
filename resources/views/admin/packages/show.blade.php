@extends('layouts.admin')

@section('title', 'Detail Paket Wisata')

@section('content')
<div class="space-y-6">
    <!-- Package Header -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden relative fade-in" data-animate="fade-in">
        <div class="relative h-72 bg-indigo-700 overflow-hidden">
            @if ($package->image)
                <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500 ease-in-out">
            @else
                <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                    <i class="fas fa-suitcase text-indigo-300 text-6xl"></i>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-70"></div>
            <div class="absolute bottom-0 left-0 p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">{{ $package->name }}</h1>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>{{ $package->destinations->count() }} Destinasi</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>{{ $package->duration_days }} Hari</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions Toolbar -->
        <div class="px-6 py-4 bg-white border-b border-gray-200 flex flex-wrap justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <i class="fas fa-tag mr-1"></i> Rp {{ number_format($package->price, 0, ',', '.') }}
                </span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    <i class="fas fa-ticket-alt mr-1"></i> {{ $bookings->total() }} Booking
                </span>
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                    <i class="fas fa-star mr-1"></i> {{ $package->getAverageRating() }} Rating
                </span>
            </div>
            
            <div class="flex space-x-2 mt-2 sm:mt-0" x-data="{showConfirmDelete: false}">
                <a href="{{ route('admin.packages.edit', $package->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('admin.packages.itinerary', $package->id) }}" class="px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-route mr-1"></i> Itinerary
                </a>
                <button @click="showConfirmDelete = true" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                </button>
                
                <!-- Delete Confirmation Modal -->
                <div x-show="showConfirmDelete" class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            Hapus Paket Wisata
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Apakah Anda yakin ingin menghapus paket wisata ini? Tindakan ini tidak dapat dibatalkan dan semua data terkait paket ini akan dihapus.
                                            </p>
                                            @if($bookings->total() > 0)
                                            <p class="text-sm text-red-500 mt-2">
                                                <i class="fas fa-exclamation-circle"></i> Perhatian: Terdapat {{ $bookings->total() }} booking yang terkait dengan paket wisata ini.
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Hapus
                                    </button>
                                </form>
                                <button @click="showConfirmDelete = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Package Details and Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column: Package Details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Package Description -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-left" data-animate="slide-in-left">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Deskripsi</h2>
                    <div class="prose max-w-none">
                        <p>{{ $package->description }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Destinations -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: 100ms;">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Destinasi</h2>
                    @if($package->destinations->count() > 0)
                        <div class="space-y-4">
                            @foreach($package->destinations as $destination)
                                <div class="flex border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($destination->image)
                                            <img src="{{ Storage::url($destination->image) }}" alt="{{ $destination->name }}" class="h-16 w-16 object-cover rounded-lg">
                                        @else
                                            <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-map-marker-alt text-indigo-400 text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-800">{{ $destination->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($destination->description, 100) }}</p>
                                        <div class="flex items-center mt-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $destination->averageRating())
                                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    @else
                                                        <i class="far fa-star text-yellow-400 text-xs"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">{{ $destination->averageRating() }} ({{ $destination->reviews->count() }} ulasan)</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-map-marker-alt text-4xl"></i>
                            </div>
                            <p class="text-gray-500">Belum ada destinasi untuk paket wisata ini</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Hotels -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: 200ms;">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Hotel</h2>
                    @if($package->hotels->count() > 0)
                        <div class="space-y-4">
                            @foreach($package->hotels as $hotel)
                                <div class="flex border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($hotel->image)
                                            <img src="{{ Storage::url($hotel->image) }}" alt="{{ $hotel->name }}" class="h-16 w-16 object-cover rounded-lg">
                                        @else
                                            <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-hotel text-indigo-400 text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-800">{{ $hotel->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $hotel->location }}</p>
                                        <div class="flex items-center mt-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $hotel->star_rating)
                                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    @else
                                                        <i class="far fa-star text-yellow-400 text-xs"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">{{ $hotel->star_rating }} Bintang</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-hotel text-4xl"></i>
                            </div>
                            <p class="text-gray-500">Belum ada hotel untuk paket wisata ini</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Restaurants -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: 300ms;">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Restoran</h2>
                    @if($package->restaurants->count() > 0)
                        <div class="space-y-4">
                            @foreach($package->restaurants as $restaurant)
                                <div class="flex border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex-shrink-0 mr-4">
                                        @if($restaurant->image)
                                            <img src="{{ Storage::url($restaurant->image) }}" alt="{{ $restaurant->name }}" class="h-16 w-16 object-cover rounded-lg">
                                        @else
                                            <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-utensils text-indigo-400 text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-800">{{ $restaurant->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $restaurant->location }}</p>
                                        <div class="flex items-center mt-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $restaurant->averageRating())
                                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    @else
                                                        <i class="far fa-star text-yellow-400 text-xs"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">{{ $restaurant->averageRating() }} ({{ $restaurant->reviews->count() }} ulasan)</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-utensils text-4xl"></i>
                            </div>
                            <p class="text-gray-500">Belum ada restoran untuk paket wisata ini</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: 400ms;">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Booking Terbaru</h2>
                        <span class="text-sm text-gray-500">Total: {{ $bookings->total() }} booking</span>
                    </div>
                    
                    @if($bookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiket</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bookings as $booking)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $booking->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->visit_date->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->number_of_tickets }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                                                    @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $bookings->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="far fa-calendar-alt text-4xl"></i>
                            </div>
                            <p class="text-gray-500">Belum ada booking untuk paket wisata ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column: Stats and Charts -->
        <div class="space-y-6">
            <!-- Package Stats -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-right" data-animate="slide-in-right">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistik Paket</h2>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 font-medium">Total Booking</p>
                            <div class="flex items-end justify-between mt-2">
                                <p class="text-2xl font-bold text-indigo-600">{{ $bookings->total() }}</p>
                                <i class="fas fa-ticket-alt text-indigo-300 text-xl"></i>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 font-medium">Rating Rata-rata</p>
                            <div class="flex items-end justify-between mt-2">
                                <div class="flex items-center">
                                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($package->getAverageRating(), 1) }}</p>
                                    <div class="flex ml-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($package->getAverageRating()))
                                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="far fa-star text-yellow-400 text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <i class="fas fa-star text-yellow-400 text-xl"></i>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 font-medium">Harga</p>
                            <div class="flex items-end justify-between mt-2">
                                <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($package->price, 0, ',', '.') }}</p>
                                <i class="fas fa-tag text-indigo-300 text-xl"></i>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 font-medium">Durasi</p>
                            <div class="flex items-end justify-between mt-2">
                                <p class="text-2xl font-bold text-indigo-600">{{ $package->duration_days }} <span class="text-base">Hari</span></p>
                                <i class="fas fa-calendar-alt text-indigo-300 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Chart -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 100ms;">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Booking per Bulan</h2>
                    <div>
                        <canvas id="bookingChart" class="w-full h-64"></canvas>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        <p class="flex items-center">
                            <span class="inline-block w-3 h-3 bg-indigo-500 rounded-full mr-2"></span>
                            Jumlah Booking
                        </p>
                        <p class="flex items-center mt-1">
                            <span class="inline-block w-3 h-3 bg-green-400 rounded-full mr-2"></span>
                            Pendapatan
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Attraction Breakdown -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 200ms;">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Komposisi Paket</h2>
                    <div>
                        <canvas id="attractionChart" class="w-full h-48"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-center text-sm">
                        <div class="bg-blue-50 p-2 rounded-lg">
                            <div class="font-bold text-blue-600">{{ $package->destinations->count() }}</div>
                            <div class="text-gray-500">Destinasi</div>
                        </div>
                        <div class="bg-purple-50 p-2 rounded-lg">
                            <div class="font-bold text-purple-600">{{ $package->hotels->count() }}</div>
                            <div class="text-gray-500">Hotel</div>
                        </div>
                        <div class="bg-green-50 p-2 rounded-lg">
                            <div class="font-bold text-green-600">{{ $package->restaurants->count() }}</div>
                            <div class="text-gray-500">Restoran</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map Preview -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 300ms;">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Preview Rute</h2>
                    <div class="rounded-lg overflow-hidden h-64 bg-gray-100">
                        @if($mapUrl)
                            <img src="{{ $mapUrl }}" alt="Preview Rute" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <p class="text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-2"></i> Belum ada destinasi dengan koordinat yang valid
                                </p>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('admin.packages.itinerary', $package->id) }}" class="block text-center mt-3 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-route mr-1"></i> Lihat Rute Lengkap
                    </a>
                </div>
            </div>
            
            <!-- Export Options -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 400ms;">
                <div class="p-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Export Data</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="#" onclick="alert('Fungsi export data sedang dalam pengembangan')" class="flex items-center justify-center p-3 border border-gray-200 rounded-lg text-sm text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 transition-colors duration-200">
                            <i class="far fa-file-pdf text-red-500 mr-2 text-lg"></i>
                            PDF
                        </a>
                        <a href="#" onclick="alert('Fungsi export data sedang dalam pengembangan')" class="flex items-center justify-center p-3 border border-gray-200 rounded-lg text-sm text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 transition-colors duration-200">
                            <i class="far fa-file-excel text-green-600 mr-2 text-lg"></i>
                            Excel
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
        // Booking Chart
        const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        
        // Parse data from PHP
        const bookingData = @json($bookingData);
        
        // Extract labels and datasets
        const labels = bookingData.map(item => item.month);
        const bookingsData = bookingData.map(item => item.bookings);
        const revenueData = bookingData.map(item => item.revenue / 1000000); // Convert to millions
        
        // Create the chart with animation
        const bookingChart = new Chart(bookingCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Booking',
                        data: bookingsData,
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Pendapatan (jutaan)',
                        data: revenueData,
                        backgroundColor: 'rgba(52, 211, 153, 0.6)',
                        borderColor: 'rgba(52, 211, 153, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(200, 200, 200, 0.2)'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#4b5563',
                        bodyColor: '#4b5563',
                        borderColor: 'rgba(200, 200, 200, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.yAxisID === 'y1') {
                                    label += context.parsed.y + ' jt';
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Attraction Chart
        const attractionCtx = document.getElementById('attractionChart').getContext('2d');
        
        // Parse data
        const destinationsCount = {{ $package->destinations->count() }};
        const hotelsCount = {{ $package->hotels->count() }};
        const restaurantsCount = {{ $package->restaurants->count() }};
        
        // Create the doughnut chart
        const attractionChart = new Chart(attractionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Destinasi', 'Hotel', 'Restoran'],
                datasets: [{
                    data: [destinationsCount, hotelsCount, restaurantsCount],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',  // Blue
                        'rgba(139, 92, 246, 0.8)',  // Purple
                        'rgba(16, 185, 129, 0.8)'   // Green
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(16, 185, 129, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        // Add scroll animation for elements
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
    });
</script>
@endpush
@endsection