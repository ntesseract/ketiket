@extends('layouts.admin')

@section('title', 'Tambah Restoran Baru')

@section('header', 'Tambah Restoran Baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Animasi */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    .slide-up {
        opacity: 0;
        animation: slideInUp 0.5s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    /* Hover Effect */
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Field Animation */
    @keyframes wiggle {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .animate-wiggle {
        animation: wiggle 0.3s ease-in-out 2;
    }
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-90 z-50 flex items-center justify-center">
    <div class="text-center">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-blue-200 rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600">Mempersiapkan formulir...</p>
    </div>
</div>

<div class="max-w-4xl mx-auto opacity-0" id="main-content">
    <div class="bg-white shadow-md rounded-lg overflow-hidden hover-scale">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 slide-up">Tambah Restoran Baru</h2>
            
            <form action="{{ route('admin.restaurants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="create-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Restoran -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Restoran <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama restoran">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in" data-delay="100">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="description" rows="5"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror"
                            placeholder="Deskripsikan restoran ini">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Tipe Masakan -->
                    <div class="opacity-0" data-animate="fade-in" data-delay="200">
                        <label for="cuisine_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Masakan</label>
                        <input type="text" name="cuisine_type" id="cuisine_type" value="{{ old('cuisine_type') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('cuisine_type') border-red-500 @enderror"
                            placeholder="Contoh: Indonesia, Jepang, Italia">
                        @error('cuisine_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Pilihan Vegetarian -->
                    <div class="opacity-0" data-animate="fade-in" data-delay="200">
                        <label for="has_vegetarian_options" class="block text-sm font-medium text-gray-700 mb-1">Pilihan Vegetarian</label>
                        <div class="mt-2">
                            <div class="flex items-center">
                                <input id="has_vegetarian_options" name="has_vegetarian_options" type="checkbox" value="1" {{ old('has_vegetarian_options') ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="has_vegetarian_options" class="ml-2 block text-sm text-gray-700">
                                    Restoran ini menyediakan pilihan menu vegetarian
                                </label>
                            </div>
                        </div>
                        @error('has_vegetarian_options')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Lokasi -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in" data-delay="300">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('location') border-red-500 @enderror"
                            placeholder="Alamat lengkap restoran">
                        @error('location')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Map Preview -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in" data-delay="400">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-md font-medium text-gray-700 mb-2 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                Lokasi di Peta
                            </h3>
                            <div id="map" class="h-64 rounded-md border border-gray-300 z-0"></div>
                            <p class="mt-2 text-xs text-gray-500">
                                <svg class="h-4 w-4 inline-block text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Klik pada peta untuk menentukan lokasi atau masukkan nilai latitude dan longitude secara manual
                            </p>
                        </div>
                    </div>
                    
                    <!-- Koordinat -->
                    <div class="opacity-0" data-animate="fade-in" data-delay="500">
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('latitude') border-red-500 @enderror"
                            placeholder="-6.2088">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="opacity-0" data-animate="fade-in" data-delay="500">
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('longitude') border-red-500 @enderror"
                            placeholder="106.8456">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Kontak -->
                    <div class="opacity-0" data-animate="fade-in" data-delay="600">
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kontak</label>
                        <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('contact_number') border-red-500 @enderror"
                            placeholder="0812-3456-7890">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Status -->
                    <div class="opacity-0" data-animate="fade-in" data-delay="600">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Jam Operasional -->
                    <div class="opacity-0" data-animate="fade-in" data-delay="700">
                        <label for="opening_hour" class="block text-sm font-medium text-gray-700 mb-1">Jam Buka</label>
                        <input type="time" name="opening_hour" id="opening_hour" value="{{ old('opening_hour') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('opening_hour') border-red-500 @enderror">
                        @error('opening_hour')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="opacity-0" data-animate="fade-in" data-delay="700">
                        <label for="closing_hour" class="block text-sm font-medium text-gray-700 mb-1">Jam Tutup</label>
                        <input type="time" name="closing_hour" id="closing_hour" value="{{ old('closing_hour') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('closing_hour') border-red-500 @enderror">
                        @error('closing_hour')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Gambar Restoran -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in" data-delay="800">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Restoran</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md relative" 
                             x-data="{ 
                                fileName: '', 
                                previewUrl: null,
                                updatePreview(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.fileName = file.name;
                                        this.previewUrl = URL.createObjectURL(file);
                                    }
                                }
                             }">
                            <div class="space-y-1 text-center">
                                <template x-if="!previewUrl">
                                    <div>
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload gambar</span>
                                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" @change="updatePreview($event)">
                                            </label>
                                            <p class="pl-1">atau drag dan drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF hingga 2MB
                                        </p>
                                    </div>
                                </template>
                                
                                <template x-if="previewUrl">
                                    <div class="relative">
                                        <img :src="previewUrl" class="max-h-48 rounded mx-auto" />
                                        <p class="mt-2 text-sm text-gray-500" x-text="fileName"></p>
                                        <button type="button" @click="previewUrl = null; fileName = '';" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 transform translate-x-1/2 -translate-y-1/2 hover:bg-red-600 focus:outline-none">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 opacity-0" data-animate="fade-in" data-delay="900">
                    <a href="{{ route('admin.restaurants.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" id="submit-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transform transition duration-200 hover:scale-105 flex items-center">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Restoran
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Parallax Section -->
    <div class="mt-6 rounded-lg overflow-hidden shadow-lg opacity-0" data-animate="fade-in" data-delay="1000">
        <div class="parallax p-8 flex items-center justify-center" style="background-image: url('{{ asset('images/parallax-bg.jpg') }}'); height: 200px; background-position: center; background-size: cover;">
            <div class="text-center bg-white bg-opacity-80 p-4 rounded-lg">
                <h3 class="text-xl font-bold text-gray-800">Kelola Restoran</h3>
                <p class="text-gray-600">Tambahkan restoran baru untuk memberikan pengalaman kuliner terbaik kepada wisatawan</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simulasikan loading
    setTimeout(function() {
        document.getElementById('loading-overlay').style.display = 'none';
        document.getElementById('main-content').style.opacity = '1';
        
        // Animate elements dengan fade-in
        const fadeElements = document.querySelectorAll('[data-animate="fade-in"]');
        fadeElements.forEach((element) => {
            setTimeout(() => {
                element.style.animation = 'fadeIn 0.5s ease-in-out forwards';
            }, element.getAttribute('data-delay') || 0);
        });
    }, 800);
    
    // Inisialisasi map
    const defaultLat = -2.5489; // Indonesia center
    const defaultLng = 118.0149;
    
    const map = L.map('map').setView([defaultLat, defaultLng], 5);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    let marker;
    
    // Add marker if coordinates exist
    const lat = document.getElementById('latitude').value;
    const lng = document.getElementById('longitude').value;
    
    if (lat && lng) {
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 13);
    }
    
    // Add marker on map click
    map.on('click', function(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker(e.latlng).addTo(map);
        
        document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
    });
    
    // Update map when coordinates change
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    function updateMap() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        
        if (lat && lng) {
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 13);
        }
    }
    
    latInput.addEventListener('change', updateMap);
    lngInput.addEventListener('change', updateMap);
    
    // Form validation dengan animasi
    document.getElementById('create-form').addEventListener('submit', function(e) {
        const requiredFields = document.querySelectorAll('[required]');
        let hasError = false;
        
        requiredFields.forEach(field => {
            if (!field.value) {
                e.preventDefault();
                hasError = true;
                
                // Add shake animation ke field yang invalid
                field.classList.add('border-red-500');
                field.classList.add('animate-wiggle');
                
                // Smooth scroll ke field pertama yang error
                if (field === requiredFields[0]) {
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                setTimeout(() => {
                    field.classList.remove('animate-wiggle');
                }, 600);
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!hasError) {
            // Animasi submit
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
            
            // Add confetti effect on successful form submission
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    });
    
    // Smooth scrolling untuk semua link internal
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Parallax effect on scroll
    window.addEventListener('scroll', function() {
        const scrollPosition = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax');
        parallaxElements.forEach(element => {
            element.style.backgroundPositionY = -scrollPosition * 0.15 + 'px';
        });
    });
});
</script>
@endpush
@endsection
