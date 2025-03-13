@extends('layouts.app')

@section('title', 'Booking - ' . $destination->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Animation */
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
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    .slide-in-up {
        opacity: 0;
        animation: slideInUp 0.6s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    /* Hover Effect */
    .hover-scale {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endpush

@section('content')
<div class="container py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('destinations.index') }}" class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">Destinasi</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('destinations.show', $destination) }}" class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">{{ $destination->name }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Booking</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <!-- Page Title -->
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 fade-in">Booking Tiket - {{ $destination->name }}</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Booking Form -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale opacity-0" data-animate="fade-in">
                    <div class="p-6">
                        @if(session('error'))
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            {{ session('error') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Booking</h2>
                        
                        <form action="{{ route('booking.storeDestination', $destination) }}" method="POST" id="booking-form">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                                    <input type="text" name="visit_date" id="visit_date" required
                                        class="flatpickr shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('visit_date') border-red-500 @enderror"
                                        placeholder="Pilih tanggal">
                                    @error('visit_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Tanggal kunjungan minimal 1 hari dari sekarang</p>
                                </div>
                                
                                <div>
                                    <label for="number_of_tickets" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tiket <span class="text-red-500">*</span></label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="number" name="number_of_tickets" id="number_of_tickets" required min="1" value="1"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('number_of_tickets') border-red-500 @enderror">
                                    </div>
                                    @error('number_of_tickets')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">Harga per tiket:</span>
                                        <span class="font-medium">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total:</span>
                                        <span class="text-indigo-600" id="total-price">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                                        <input type="hidden" name="total_price" id="total-price-input" value="{{ $destination->price }}">
                                    </div>
                                </div>
                                
                                <div class="pt-4">
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Lanjutkan ke Pembayaran
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="100">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Syarat dan Ketentuan</h3>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p>Dengan melanjutkan pemesanan, Anda menyetujui syarat dan ketentuan berikut:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Tiket hanya berlaku pada tanggal kunjungan yang dipilih</li>
                            <li>Pembatalan atau perubahan jadwal dapat dilakukan maksimal 1 hari sebelum tanggal kunjungan</li>
                            <li>Anak di bawah 3 tahun dapat masuk gratis tanpa tiket</li>
                            <li>Biaya parkir tidak termasuk dalam harga tiket</li>
                            <li>Tiket yang sudah dibeli tidak dapat diuangkan kembali</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Destination Summary -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale opacity-0" data-animate="fade-in" data-delay="200">
                    <div class="relative h-40">
                        @if($destination->image)
                            <img src="{{ Storage::url($destination->image) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="h-16 w-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ $destination->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $destination->location }}</p>
                        
                        <div class="flex items-center mt-2">
                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="ml-1 text-sm text-gray-700">{{ number_format($destination->averageRating(), 1) }}</span>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium 
                                    {{ $destination->status === 'open' ? 'text-green-600' : '' }}
                                    {{ $destination->status === 'closed' ? 'text-red-600' : '' }}
                                    {{ $destination->status === 'maintenance' ? 'text-yellow-600' : '' }}">
                                    {{ ucfirst($destination->status) }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Jam Operasional:</span>
                                <span class="font-medium">
                                    @if($destination->opening_hour && $destination->closing_hour)
                                        {{ \Carbon\Carbon::parse($destination->opening_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($destination->closing_hour)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Harga Tiket:</span>
                                <span class="font-medium">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($destination->capacity)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Kapasitas:</span>
                                    <span class="font-medium">{{ number_format($destination->capacity, 0, ',', '.') }} orang</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('destinations.show', $destination) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Detail Destinasi
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- User Wallet -->
                <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="300">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Saldo E-Wallet</h3>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Saldo Tersedia:</p>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('wallet.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Top Up Saldo
                        </a>
                    </div>
                </div>
                
                <!-- Need Help -->
                <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="400">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Butuh Bantuan?</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('chat.index') }}" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Chat dengan AI Assistant
                        </a>
                        <a href="#" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Email Customer Support
                        </a>
                        <a href="#" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Hubungi Call Center
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate elements on load
        const animatedElements = document.querySelectorAll('[data-animate]');
        animatedElements.forEach((element, index) => {
            setTimeout(() => {
                element.style.animation = element.getAttribute('data-animate') + ' 0.6s ease-in-out forwards';
                element.style.opacity = '1';
            }, (element.getAttribute('data-delay') || 0));
        });
        
        // Initialize date picker
        flatpickr("#visit_date", {
            enableTime: false,
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: [
                function(date) {
                    // Disable dates based on destination availability (example)
                    // You could add logic here to disable dates when capacity is full
                    return false; // For now, no dates are disabled
                }
            ]
        });
        
        // Calculate total price
        const numberOfTicketsInput = document.getElementById('number_of_tickets');
        const totalPriceDisplay = document.getElementById('total-price');
        const totalPriceInput = document.getElementById('total-price-input');
        
        numberOfTicketsInput.addEventListener('input', function() {
            const quantity = parseInt(this.value) || 0;
            const ticketPrice = {{ $destination->price }};
            const total = quantity * ticketPrice;
            
            // Update display and hidden input
            totalPriceDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
            totalPriceInput.value = total;
        });
        
        // Form validation
        const bookingForm = document.getElementById('booking-form');
        bookingForm.addEventListener('submit', function(e) {
            const dateInput = document.getElementById('visit_date');
            const ticketsInput = document.getElementById('number_of_tickets');
            
            if (!dateInput.value) {
                e.preventDefault();
                dateInput.classList.add('border-red-500');
                dateInput.focus();
                return;
            }
            
            if (!ticketsInput.value || parseInt(ticketsInput.value) < 1) {
                e.preventDefault();
                ticketsInput.classList.add('border-red-500');
                ticketsInput.focus();
                return;
            }
        });
    });
</script>
@endpush