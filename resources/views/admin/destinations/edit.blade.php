@extends('layouts.admin')

@section('title', 'Edit Destinasi')

@section('header', 'Edit Destinasi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* Animasi Custom */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(40px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes wiggle {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    .slide-up {
        opacity: 0;
        animation: slideInUp 0.5s ease-in-out forwards;
    }
    
    .animate-wiggle {
        animation: wiggle 0.3s ease-in-out 2;
    }
    
    /* Hover Effect */
    .hover-scale {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Parallax Effect */
    .parallax {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    
    /* Loading Animation */
    .loading-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90">
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat data destinasi...</p>
    </div>
</div>

<div class="max-w-4xl mx-auto opacity-0" id="main-content">
    <div class="bg-white shadow-md rounded-lg overflow-hidden hover-scale">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 slide-up">Edit Destinasi: {{ $destination->name }}</h2>
            
            <form action="{{ route('admin.destinations.update', $destination) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="edit-form">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Destinasi -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Destinasi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $destination->name) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama destinasi">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror"
                            placeholder="Deskripsikan destinasi ini">{{ old('description', $destination->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Lokasi -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location', $destination->location) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('location') border-red-500 @enderror"
                            placeholder="Alamat lengkap destinasi">
                        @error('location')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Koordinat -->
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $destination->latitude) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('latitude') border-red-500 @enderror"
                            placeholder="-6.2088">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $destination->longitude) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('longitude') border-red-500 @enderror"
                            placeholder="106.8456">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Map Preview -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in">
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
                                Klik pada peta untuk mengubah koordinat atau masukkan nilai latitude dan longitude secara manual
                            </p>
                        </div>
                    </div>
                    
                    <!-- Harga Tiket -->
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga Tiket <span class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="price" id="price" value="{{ old('price', $destination->price) }}" required
                                class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('price') border-red-500 @enderror"
                                placeholder="50000">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Kapasitas -->
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Pengunjung</label>
                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $destination->capacity) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('capacity') border-red-500 @enderror"
                            placeholder="100">
                        <p class="mt-1 text-xs text-gray-500">Biarkan kosong jika tidak ada batasan kapasitas</p>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Jam Operasional -->
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="opening_hour" class="block text-sm font-medium text-gray-700 mb-1">Jam Buka</label>
                        <input type="time" name="opening_hour" id="opening_hour" value="{{ old('opening_hour', $destination->opening_hour) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('opening_hour') border-red-500 @enderror">
                        @error('opening_hour')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="closing_hour" class="block text-sm font-medium text-gray-700 mb-1">Jam Tutup</label>
                        <input type="time" name="closing_hour" id="closing_hour" value="{{ old('closing_hour', $destination->closing_hour) }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('closing_hour') border-red-500 @enderror">
                        @error('closing_hour')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Status -->
                    <div class="opacity-0" data-animate="fade-in">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('status') border-red-500 @enderror">
                            <option value="open" {{ (old('status', $destination->status) == 'open') ? 'selected' : '' }}>Buka</option>
                            <option value="closed" {{ (old('status', $destination->status) == 'closed') ? 'selected' : '' }}>Tutup</option>
                            <option value="maintenance" {{ (old('status', $destination->status) == 'maintenance') ? 'selected' : '' }}>Pemeliharaan</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Gambar Destinasi -->
                    <div class="col-span-1 md:col-span-2 opacity-0" data-animate="fade-in">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Destinasi</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md relative" 
                             x-data="{ 
                                fileName: '', 
                                previewUrl: '{{ $destination->image ? Storage::url($destination->image) : null }}',
                                currentImage: '{{ $destination->image ? true : false }}',
                                updatePreview(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        this.fileName = file.name;
                                        this.previewUrl = URL.createObjectURL(file);
                                        this.currentImage = false;
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
                                            PNG, JPG, GIF hingga 5MB
                                        </p>
                                    </div>
                                </template>
                                
                                <template x-if="previewUrl">
                                    <div class="relative">
                                        <img :src="previewUrl" class="max-h-48 rounded mx-auto" />
                                        <p class="mt-2 text-sm text-gray-500" x-text="currentImage ? 'Gambar saat ini' : fileName"></p>
                                        <button type="button" @click="previewUrl = null; fileName = ''; currentImage = false;" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 transform translate-x-1/2 -translate-y-1/2 hover:bg-red-600 focus:outline-none">
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
                
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 opacity-0" data-animate="fade-in">
                    <a href="{{ route('admin.destinations.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" id="submit-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transform transition duration-200 hover:scale-105 flex items-center">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Parallax Section -->
    <div class="mt-6 rounded-lg overflow-hidden shadow-lg opacity-0" data-animate="fade-in">
        <div class="parallax p-8 flex items-center justify-center" style="background-image: url('{{ asset('images/parallax-bg.jpg') }}'); height: 200px;">
            <div class="text-center bg-white bg-opacity-80 p-4 rounded-lg">
                <h3 class="text-xl font-bold text-gray-800">Kelola Destinasi Wisata</h3>
                <p class="text-gray-600">Edit informasi destinasi untuk memberikan pengalaman terbaik kepada wisatawan</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simulasikan loading
    setTimeout(function() {
        document.getElementById('loading-overlay').style.display = 'none';
        document.getElementById('main-content').style.opacity = '1';
        
        // Animate elements dengan fade-in
        const fadeElements = document.querySelectorAll('[data-animate="fade-in"]');
        fadeElements.forEach((element, index) => {
            setTimeout(() => {
                element.style.animation = 'fadeIn 0.5s ease-in-out forwards';
            }, index * 100);
        });
    }, 800);
    
    // Inisialisasi map
    const lat = parseFloat(document.getElementById('latitude').value) || -2.5489;
    const lng = parseFloat(document.getElementById('longitude').value) || 118.0149;
    
    const map = L.map('map').setView([lat, lng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    let marker = L.marker([lat, lng]).addTo(map);
    
    // Add marker on map click
    map.on('click', function(e) {
        map.removeLayer(marker);
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
            map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 13);
        }
    }
    
    latInput.addEventListener('change', updateMap);
    lngInput.addEventListener('change', updateMap);
    
    // Form validation dengan animasi
    document.getElementById('edit-form').addEventListener('submit', function(e) {
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
        const parallaxElements = document.querySelectorAll('.parallax');
        parallaxElements.forEach(element => {
            const scrollPosition = window.scrollY;
            element.style.backgroundPositionY = -scrollPosition * 0.15 + 'px';
        });
    });
});
</script>
@endpush
@endsection
