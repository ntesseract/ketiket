@extends('layouts.app')

@section('title', 'Pembayaran Booking')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Payment Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Header -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden fade-in" data-animate="fade-in">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h1 class="text-2xl font-bold text-gray-900">Pembayaran</h1>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Pembayaran
                                </span>
                            </div>
                        </div>
                        <div class="mt-1 text-sm text-gray-500">
                            Selesaikan pembayaran untuk melanjutkan booking Anda
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-gray-700 font-medium">Kode Booking</span>
                            <span class="text-indigo-600 font-semibold">#{{ $booking->id }}</span>
                        </div>
                        
                        <div class="border-t border-b border-gray-200 py-4 my-4">
                            <h2 class="text-lg font-medium text-gray-900 mb-3">Detail Booking</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700">Destinasi</h3>
                                    <p class="text-sm text-gray-900 mt-1">
                                        @if($booking->destination)
                                            {{ $booking->destination->name }}
                                        @elseif($booking->is_package)
                                            Paket Wisata
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700">Tanggal Kunjungan</h3>
                                    <p class="text-sm text-gray-900 mt-1">{{ $booking->visit_date->format('d M Y') }}</p>
                                </div>
                                
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700">Jumlah Tiket</h3>
                                    <p class="text-sm text-gray-900 mt-1">{{ $booking->number_of_tickets }} tiket</p>
                                </div>
                                
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700">Total Harga</h3>
                                    <p class="text-sm text-gray-900 mt-1">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h2 class="text-lg font-medium text-gray-900 mb-3">Metode Pembayaran</h2>
                            
                            <!-- Payment Method Selector -->
                            <div id="payment-methods">
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center">
                                        <input id="wallet" name="payment_method" type="radio" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="wallet" class="ml-3 flex flex-grow">
                                            <div>
                                                <span class="block text-sm font-medium text-gray-900">Dompet Digital</span>
                                                <span class="block text-sm text-gray-500">Gunakan saldo dompet digital Anda</span>
                                            </div>
                                            <div class="ml-auto">
                                                <i class="fas fa-wallet text-xl text-indigo-500"></i>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="mt-3 pl-7">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Saldo Tersedia</span>
                                            <span class="text-sm font-medium text-gray-900">Rp {{ number_format($balance, 0, ',', '.') }}</span>
                                        </div>
                                        
                                        @if(!$sufficientBalance)
                                            <div class="mt-2 text-sm text-red-600 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                <span>Saldo tidak mencukupi untuk melakukan pembayaran.</span>
                                            </div>
                                            <a href="{{ route('wallet.topup') }}" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <i class="fas fa-plus-circle mr-1"></i> Top Up Saldo
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Form -->
                            <form id="payment-form" action="{{ route('wallet.processPayment', $booking->id) }}" method="POST">
                                @csrf
                                
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-medium text-gray-900">Total Pembayaran</span>
                                        <span class="text-lg font-semibold text-indigo-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-end mt-6 space-x-3">
                                        <a href="{{ route('booking.cancel', $booking->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-times mr-2"></i> Batalkan Booking
                                        </a>
                                        
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out transform hover:scale-105" id="pay-button" {{ !$sufficientBalance ? 'disabled' : '' }}>
                                            <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Timeline -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-left" data-animate="slide-in-left">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Status Booking</h2>
                        
                        <div class="relative">
                            <!-- Line -->
                            <div class="absolute left-5 top-0 h-full w-1 bg-gray-200 z-0"></div>
                            
                            <!-- Timeline Items -->
                            <div class="space-y-6 relative z-10">
                                <!-- Item 1: Created -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center mr-4">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">Booking Dibuat</h3>
                                        <p class="mt-1 text-xs text-gray-500">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                
                                <!-- Item 2: Payment -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $booking->status != 'pending' ? 'bg-indigo-500' : 'bg-gray-300' }} flex items-center justify-center mr-4">
                                        <i class="fas {{ $booking->status != 'pending' ? 'fa-check' : 'fa-credit-card' }} text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">Pembayaran</h3>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $booking->status != 'pending' ? 'Pembayaran berhasil' : 'Menunggu pembayaran' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Item 3: Confirmation -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'bg-indigo-500' : 'bg-gray-300' }} flex items-center justify-center mr-4">
                                        <i class="fas {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'fa-check' : 'fa-ticket-alt' }} text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">Konfirmasi Tiket</h3>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'Tiket telah dikonfirmasi' : 'Menunggu konfirmasi tiket' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Item 4: Visit -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $booking->status == 'completed' ? 'bg-indigo-500' : 'bg-gray-300' }} flex items-center justify-center mr-4">
                                        <i class="fas {{ $booking->status == 'completed' ? 'fa-check' : 'fa-calendar-check' }} text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">Kunjungan</h3>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $booking->status == 'completed' ? 'Kunjungan telah selesai' : 'Jadwal kunjungan: ' . $booking->visit_date->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Additional Information -->
            <div class="space-y-6">
                <!-- Destination Info Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right">
                    <div class="relative h-48">
                        @if($booking->destination && $booking->destination->image)
                            <img src="{{ Storage::url($booking->destination->image) }}" alt="{{ $booking->destination->name }}" class="w-full h-full object-cover">
                        @elseif($booking->hotel && $booking->hotel->image)
                            <img src="{{ Storage::url($booking->hotel->image) }}" alt="{{ $booking->hotel->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                <i class="fas fa-map-marker-alt text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-transparent"></div>
                    </div>
                    
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-2">
                            @if($booking->destination)
                                {{ $booking->destination->name }}
                            @elseif($booking->is_package)
                                Paket Wisata
                            @else
                                Detail Booking
                            @endif
                        </h2>
                        
                        @if($booking->destination)
                            <div class="mb-4 text-sm text-gray-600">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-map-marker-alt text-indigo-500 mr-2 w-4"></i>
                                    <span>{{ $booking->destination->location }}</span>
                                </div>
                                @if($booking->destination->opening_hour && $booking->destination->closing_hour)
                                    <div class="flex items-center mb-1">
                                        <i class="far fa-clock text-indigo-500 mr-2 w-4"></i>
                                        <span>{{ \Carbon\Carbon::parse($booking->destination->opening_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->destination->closing_hour)->format('H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        @if($booking->hotel)
                            <div class="mb-2 mt-4">
                                <h3 class="text-sm font-medium text-gray-700">Hotel</h3>
                                <p class="text-sm text-gray-900 mt-1">{{ $booking->hotel->name }}</p>
                            </div>
                        @endif
                        
                        @if($booking->restaurant)
                            <div class="mb-2">
                                <h3 class="text-sm font-medium text-gray-700">Restaurant</h3>
                                <p class="text-sm text-gray-900 mt-1">{{ $booking->restaurant->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 100ms;">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembayaran</h2>
                        
                        <div class="space-y-3">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Pembayaran akan menggunakan saldo dompet digital Anda. Pembayaran bersifat non-refundable.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-700">Batas Waktu Pembayaran</h3>
                                <p class="text-sm text-red-600 mt-1">
                                    <i class="fas fa-clock mr-1"></i> 
                                    <span id="countdown">30:00</span> menit
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Need Help Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden slide-in-right" data-animate="slide-in-right" style="animation-delay: 200ms;">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Butuh Bantuan?</h2>
                        
                        <p class="text-sm text-gray-600 mb-4">
                            Jika Anda mengalami masalah dalam proses pembayaran, silakan hubungi customer service kami.
                        </p>
                        
                        <a href="{{ route('chat.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-comments mr-2"></i> Chat dengan CS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Success Modal -->
<div id="payment-success-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white p-6 sm:p-8">
                <div class="text-center">
                    <div class="mx-auto h-20 w-20 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-check-circle text-5xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mt-4">Pembayaran Berhasil!</h3>
                    <p class="text-gray-600 mt-2">Pembayaran Anda telah berhasil diproses. Tiket digital telah disiapkan.</p>
                    
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('booking.show', $booking->id) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-ticket-alt mr-2"></i> Lihat Tiket
                        </a>
                        
                        <a href="{{ route('booking.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-list mr-2"></i> Daftar Booking
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
        
        // Payment button animation
        const payButton = document.getElementById('pay-button');
        const paymentForm = document.getElementById('payment-form');
        
        if (payButton && !payButton.disabled) {
            payButton.addEventListener('mouseenter', function() {
                this.classList.add('animate-pulse');
            });
            
            payButton.addEventListener('mouseleave', function() {
                this.classList.remove('animate-pulse');
            });
            
            paymentForm.addEventListener('submit', function(e) {
                // Show loading state
                payButton.disabled = true;
                payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                
                // For demo purposes, we'll simulate a payment
                // In a real app, this would be handled by the server
                @if($sufficientBalance)
                e.preventDefault();
                
                setTimeout(function() {
                    // Show success modal
                    document.getElementById('payment-success-modal').classList.remove('hidden');
                    
                    // Add confetti effect
                    if (typeof confetti === 'function') {
                        confetti({
                            particleCount: 100,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }
                    
                    // Redirect after a delay
                    setTimeout(function() {
                        window.location.href = "{{ route('booking.show', $booking->id) }}";
                    }, 3000);
                }, 2000);
                @endif
            });
        }
        
        // Countdown timer
        function startCountdown() {
            const countdownEl = document.getElementById('countdown');
            let minutes = 30;
            let seconds = 0;
            
            const countdown = setInterval(function() {
                if (seconds == 0) {
                    if (minutes == 0) {
                        clearInterval(countdown);
                        countdownEl.innerHTML = "00:00";
                        countdownEl.classList.add('text-red-600', 'font-bold');
                        
                        // Redirect to booking list if time expires
                        showToast('Waktu Habis', 'Batas waktu pembayaran telah berakhir', 'error');
                        
                        setTimeout(function() {
                            window.location.href = "{{ route('booking.index') }}";
                        }, 3000);
                        
                        return;
                    }
                    minutes--;
                    seconds = 59;
                } else {
                    seconds--;
                }
                
                // Flash the timer when less than 5 minutes
                if (minutes < 5) {
                    countdownEl.classList.add('text-red-600');
                    
                    if (seconds % 2 == 0) {
                        countdownEl.classList.add('animate-pulse');
                    } else {
                        countdownEl.classList.remove('animate-pulse');
                    }
                }
                
                // Format time
                const formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
                const formattedSeconds = seconds < 10 ? "0" + seconds : seconds;
                
                countdownEl.innerHTML = formattedMinutes + ":" + formattedSeconds;
            }, 1000);
        }
        
        startCountdown();
    });
</script>

<!-- Load confetti.js for success animation -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
@endpush
@endsection