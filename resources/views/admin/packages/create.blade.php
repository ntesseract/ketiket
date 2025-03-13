@extends('layouts.admin')

@section('title', 'Tambah Paket Wisata Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Paket Wisata Baru</h2>
            
            <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Paket -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Paket <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama paket wisata">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror"
                            placeholder="Deskripsikan paket wisata ini">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Minimal 50 karakter</p>
                    </div>
                    
                    <!-- Durasi dan Harga -->
                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Hari) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days') }}" required min="1" max="30"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('duration_days') border-red-500 @enderror"
                            placeholder="3">
                        @error('duration_days')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga <span class="text-red-500">*</span></label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" required
                                class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('price') border-red-500 @enderror"
                                placeholder="1500000">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Pilih Destinasi -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="destinations" class="block text-sm font-medium text-gray-700 mb-1">Pilih Destinasi <span class="text-red-500">*</span></label>
                        <div class="border border-gray-300 rounded-md p-2 max-h-60 overflow-y-auto">
                            @foreach($destinations as $destination)
                                <div class="p-2 hover:bg-gray-50 rounded-md flex items-start">
                                    <input type="checkbox" name="destinations[]" value="{{ $destination->id }}" id="destination-{{ $destination->id }}"
                                        class="rounded border-gray-300 text-indigo-600 mt-1 focus:ring-indigo-500 h-4 w-4"
                                        {{ in_array($destination->id, old('destinations', [])) ? 'checked' : '' }}>
                                    <label for="destination-{{ $destination->id }}" class="ml-2 cursor-pointer">
                                        <div class="font-medium text-gray-800">{{ $destination->name }}</div>
                                        <p class="text-sm text-gray-500">{{ Str::limit($destination->location, 80) }}</p>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('destinations')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Pilih minimal 1 destinasi</p>
                    </div>
                    
                    <!-- Pilih Hotel -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="hotels" class="block text-sm font-medium text-gray-700 mb-1">Pilih Hotel <span class="text-red-500">*</span></label>
                        <div class="border border-gray-300 rounded-md p-2 max-h-60 overflow-y-auto">
                            @foreach($hotels as $hotel)
                                <div class="p-2 hover:bg-gray-50 rounded-md flex items-start">
                                    <input type="checkbox" name="hotels[]" value="{{ $hotel->id }}" id="hotel-{{ $hotel->id }}"
                                        class="rounded border-gray-300 text-indigo-600 mt-1 focus:ring-indigo-500 h-4 w-4"
                                        {{ in_array($hotel->id, old('hotels', [])) ? 'checked' : '' }}>
                                    <label for="hotel-{{ $hotel->id }}" class="ml-2 cursor-pointer">
                                        <div class="font-medium text-gray-800">{{ $hotel->name }}</div>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="mr-2">{{ $hotel->location }}</span>
                                            <span class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $hotel->star_rating)
                                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                    @else
                                                        <i class="far fa-star text-yellow-400 text-xs"></i>
                                                    @endif
                                                @endfor
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('hotels')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Pilih minimal 1 hotel</p>
                    </div>
                    
                    <!-- Pilih Restoran -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="restaurants" class="block text-sm font-medium text-gray-700 mb-1">Pilih Restoran <span class="text-red-500">*</span></label>
                        <div class="border border-gray-300 rounded-md p-2 max-h-60 overflow-y-auto">
                            @foreach($restaurants as $restaurant)
                                <div class="p-2 hover:bg-gray-50 rounded-md flex items-start">
                                    <input type="checkbox" name="restaurants[]" value="{{ $restaurant->id }}" id="restaurant-{{ $restaurant->id }}"
                                        class="rounded border-gray-300 text-indigo-600 mt-1 focus:ring-indigo-500 h-4 w-4"
                                        {{ in_array($restaurant->id, old('restaurants', [])) ? 'checked' : '' }}>
                                    <label for="restaurant-{{ $restaurant->id }}" class="ml-2 cursor-pointer">
                                        <div class="font-medium text-gray-800">{{ $restaurant->name }}</div>
                                        <p class="text-sm text-gray-500">{{ $restaurant->location }}</p>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('restaurants')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Pilih minimal 1 restoran</p>
                    </div>
                    
                    <!-- Gambar Paket -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Paket <span class="text-red-500">*</span></label>
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
                    <a href="{{ route('admin.packages.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to form elements
    const formElements = document.querySelectorAll('form input, form textarea, form select, form .border');
    formElements.forEach((element, index) => {
        element.classList.add('opacity-0');
        element.setAttribute('data-animate', 'fade-in');
        element.style.animationDelay = `${index * 100}ms`;
        
        // Trigger animation after a slight delay
        setTimeout(() => {
            element.classList.add('fade-in');
            element.style.opacity = 1;
        }, 100);
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
    
    // Check minimum requirements for checkboxes
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const destinationCheckboxes = document.querySelectorAll('input[name="destinations[]"]:checked');
        const hotelCheckboxes = document.querySelectorAll('input[name="hotels[]"]:checked');
        const restaurantCheckboxes = document.querySelectorAll('input[name="restaurants[]"]:checked');
        
        if (destinationCheckboxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 destinasi');
            return false;
        }
        
        if (hotelCheckboxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 hotel');
            return false;
        }
        
        if (restaurantCheckboxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 restoran');
            return false;
        }
        
        // Add loading state to button
        this.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
        this.querySelector('button[type="submit"]').disabled = true;
    });
});
</script>

<style>
@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
</style>
@endpush
@endsection