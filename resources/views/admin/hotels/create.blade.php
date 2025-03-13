@extends('layouts.admin')

@section('title', 'Tambah Hotel Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Hotel Baru</h2>
            
            <form action="{{ route('admin.hotels.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Hotel -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Hotel <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama hotel">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror"
                            placeholder="Deskripsikan hotel ini">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Minimal 50 karakter</p>
                    </div>
                    
                    <!-- Lokasi -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('location') border-red-500 @enderror"
                            placeholder="Alamat lengkap hotel">
                        @error('location')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Koordinat -->
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('latitude') border-red-500 @enderror"
                            placeholder="-6.2088">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude <span class="text-red-500">*</span></label>
                        <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('longitude') border-red-500 @enderror"
                            placeholder="106.8456">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Harga per Malam -->
                    <div>
                        <label for="price_per_night" class="block text-sm font-medium text-gray-700 mb-1">Harga per Malam <span class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="price_per_night" id="price_per_night" value="{{ old('price_per_night') }}" required
                                class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('price_per_night') border-red-500 @enderror"
                                placeholder="500000">
                        </div>
                        @error('price_per_night')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Rating Bintang -->
                    <div>
                        <label for="star_rating" class="block text-sm font-medium text-gray-700 mb-1">Rating Bintang <span class="text-red-500">*</span></label>
                        <select name="star_rating" id="star_rating" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('star_rating') border-red-500 @enderror">
                            <option value="">Pilih Rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('star_rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                            @endfor
                        </select>
                        @error('star_rating')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Gambar Hotel -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Hotel <span class="text-red-500">*</span></label>
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
                                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" @change="updatePreview($event)" required>
                                            </label>
                                            <p class="pl-1">atau drag dan drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF up to 2MB
                                        </p>
                                    </div>
                                </template>
                                
                                <template x-if="previewUrl">
                                    <div class="relative">
                                        <img :src="previewUrl" class="max-h-48 rounded mx-auto" />
                                        <p class="mt-2 text-sm text-gray-500" x-text="fileName"></p>
                                        <button type="button" @click="previewUrl = null; fileName = ''" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 transform translate-x-1/2 -translate-y-1/2 hover:bg-red-600 focus:outline-none">
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
                
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.hotels.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transform transition duration-200 hover:scale-105 flex items-center">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Map Preview Section (Hidden for now) -->
<div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="showMap = !showMap">
    <h3 class="text-lg font-medium text-gray-700">
        <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i> Pratinjau Lokasi di Peta
    </h3>
    <span x-show="!showMap"><i class="fas fa-chevron-down"></i></span>
    <span x-show="showMap"><i class="fas fa-chevron-up"></i></span>
</div>

<div x-show="showMap" class="p-4 transition-all duration-500 ease-in-out" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div id="map-preview" class="h-96 rounded-lg shadow-inner border border-gray-200"></div>
    <p class="mt-3 text-sm text-gray-500">
        <i class="fas fa-info-circle mr-1"></i> Tip: Isi latitude dan longitude pada form di atas untuk melihat lokasi di peta
    </p>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    let map, marker;
    
    // Function to initialize map
    function initMap() {
        // Default to Jakarta coordinates
        const defaultLat = -6.2088;
        const defaultLng = 106.8456;
        
        // Try to get coordinates from input fields
        const lat = parseFloat(latInput.value) || defaultLat;
        const lng = parseFloat(lngInput.value) || defaultLng;
        
        // Create map
        const mapOptions = {
            center: { lat, lng },
            zoom: 14
        };
        
        map = new google.maps.Map(document.getElementById('map-preview'), mapOptions);
        
        // Create marker
        marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: 'Hotel Location'
        });
        
        // Update form fields when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function() {
            const position = marker.getPosition();
            latInput.value = position.lat();
            lngInput.value = position.lng();
        });
        
        // Add click event to map to move marker
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            latInput.value = event.latLng.lat();
            lngInput.value = event.latLng.lng();
        });
    }
    
    // Update map when coordinates change
    function updateMap() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        
        if (lat && lng && map && marker) {
            const location = new google.maps.LatLng(lat, lng);
            marker.setPosition(location);
            map.setCenter(location);
        }
    }
    
    // Listen for input changes
    latInput.addEventListener('change', updateMap);
    lngInput.addEventListener('change', updateMap);
    
    // Initialize map when expanded
    document.querySelector('[x-data="{ showMap: false }"]').addEventListener('click', function() {
        setTimeout(function() {
            if (!map) {
                // Load Google Maps API dynamically
                if (!window.google || !window.google.maps) {
                    const script = document.createElement('script');
                    script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap';
                    script.defer = true;
                    script.async = true;
                    window.initMap = initMap;
                    document.head.appendChild(script);
                } else {
                    initMap();
                }
            } else {
                // Refresh map when showing again
                google.maps.event.trigger(map, 'resize');
            }
        }, 300);
    });
    
    // Add animation to form elements
    const formElements = document.querySelectorAll('form input, form textarea, form select');
    formElements.forEach((element, index) => {
        element.classList.add('opacity-0');
        element.setAttribute('data-animate', 'fade-in');
        element.style.animationDelay = `${index * 100}ms`;
    });
    
    // Custom file upload preview animation
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                // Add animation class when file is selected
                setTimeout(() => {
                    const previewImg = document.querySelector('[x-data] img');
                    if (previewImg) {
                        previewImg.classList.add('animate-pulse');
                        setTimeout(() => {
                            previewImg.classList.remove('animate-pulse');
                        }, 1000);
                    }
                }, 100);
            }
        });
    }
});

// Form validation animation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = document.querySelectorAll('[required]');
    let hasError = false;
    
    requiredFields.forEach(field => {
        if (!field.value) {
            e.preventDefault();
            hasError = true;
            
            // Add shake animation to invalid fields
            field.classList.add('border-red-500');
            field.classList.add('animate-wiggle');
            
            setTimeout(() => {
                field.classList.remove('animate-wiggle');
            }, 600);
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    if (!hasError) {
        // Add submit animation
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
        
        // Add confetti effect on successful form submission
        if (typeof confetti === 'function') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    }
});
</script>

<style>
@keyframes wiggle {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
.animate-wiggle {
    animation: wiggle 0.3s ease-in-out 2;
}
</style>

<!-- Load confetti.js for success animation -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
@endpush
@endsection