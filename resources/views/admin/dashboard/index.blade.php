@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">
            Selamat datang di panel admin. Berikut adalah ringkasan data sistem.
        </p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-indigo-100 rounded-lg p-3">
                        <i class="fas fa-users text-indigo-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Total Pengguna</h3>
                        <div class="flex items-end">
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($userCount) }}</span>
                            <span class="text-sm text-green-500 ml-2">
                                <i class="fas fa-arrow-up"></i> 
                                +{{ rand(5, 15) }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        Lihat detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Pending Top Up Requests -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <i class="fas fa-wallet text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Permintaan Top Up</h3>
                        <div class="flex items-end">
                            <?php
                            $pendingTopUpCount = App\Models\TopUpRequest::where('status', 'pending')->count();
                            ?>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($pendingTopUpCount) }}</span>
                            @if($pendingTopUpCount > 0)
                            <span class="text-sm text-yellow-500 ml-2 animate-pulse">
                                <i class="fas fa-exclamation-circle"></i> 
                                Menunggu
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.topup.index') }}" class="text-sm text-yellow-600 hover:text-yellow-800">
                        Proses sekarang <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Bookings Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-lg p-3">
                        <i class="fas fa-ticket-alt text-purple-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Total Booking</h3>
                        <div class="flex items-end">
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($bookingCount) }}</span>
                            <span class="text-sm text-green-500 ml-2">
                                <i class="fas fa-arrow-up"></i> 
                                +{{ rand(5, 20) }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.bookings.index') }}" class="text-sm text-purple-600 hover:text-purple-800">
                        Lihat detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Revenue Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-lg p-3">
                        <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm font-medium">Pendapatan</h3>
                        <div class="flex items-end">
                            <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
                            <span class="text-sm text-green-500 ml-2">
                                <i class="fas fa-arrow-up"></i> 
                                +{{ rand(10, 25) }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.reports.revenue') }}" class="text-sm text-green-600 hover:text-green-800">
                        Lihat detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts & Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking & Top Up Chart (Takes 2 columns) -->
        <div class="lg:col-span-2 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Statistik Tahun {{ date('Y') }}</h2>
                    <div class="flex space-x-2">
                        <button onclick="toggleChartData('booking')" id="btn-booking" class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm focus:outline-none hover:bg-indigo-200 active:bg-indigo-700 active:text-white">
                            Booking
                        </button>
                        <button onclick="toggleChartData('topup')" id="btn-topup" class="px-3 py-1 rounded-full text-sm focus:outline-none bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700">
                            Top Up
                        </button>
                        <button onclick="toggleChartData('revenue')" id="btn-revenue" class="px-3 py-1 rounded-full text-sm focus:outline-none bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-700">
                            Pendapatan
                        </button>
                    </div>
                </div>
                <div>
                    <canvas id="statsChart" class="w-full h-80"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Top Up Requests -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Permintaan Top Up Terbaru</h2>
                <div class="space-y-4">
                    <?php
                    $recentTopUps = App\Models\TopUpRequest::with('user')
                        ->latest()
                        ->limit(5)
                        ->get();
                    ?>
                    
                    @forelse($recentTopUps as $index => $topUp)
                        <div class="flex items-center p-3 rounded-lg {{ $index % 2 == 0 ? 'bg-gray-50' : '' }} hover:bg-blue-50 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full flex items-center justify-center 
                                    @if($topUp->status == 'pending') bg-yellow-100 text-yellow-600
                                    @elseif($topUp->status == 'approved') bg-green-100 text-green-600
                                    @else bg-red-100 text-red-600
                                    @endif">
                                    @if($topUp->status == 'pending')
                                        <i class="fas fa-clock"></i>
                                    @elseif($topUp->status == 'approved')
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">{{ $topUp->user->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $topUp->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-indigo-600">Rp {{ number_format($topUp->amount, 0, ',', '.') }}</span>
                                <p class="text-xs text-gray-500">
                                    @if($topUp->status == 'pending')
                                        <span class="text-yellow-600">Menunggu</span>
                                    @elseif($topUp->status == 'approved')
                                        <span class="text-green-600">Disetujui</span>
                                    @else
                                        <span class="text-red-600">Ditolak</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-wallet text-gray-300 text-3xl mb-2"></i>
                            <p>Belum ada permintaan top up</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.topup.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        Lihat semua permintaan <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Bookings -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Booking Terbaru</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentBookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $booking->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->destination->name ?? 'Paket Tour' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->visit_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
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
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart data
        const months = @json($months);
        const bookingData = @json($bookingData);
        const completedData = @json($completedData);
        const cancelledData = @json($cancelledData);
        
        // Top Up data (generate some sample data if not available)
        const topUpData = @json($topUpData ?? array_map(function() { return rand(5, 40); }, array_fill(0, count($months), 0)));
        const topUpApprovedData = @json($topUpApprovedData ?? array_map(function() { return rand(5, 35); }, array_fill(0, count($months), 0)));
        const topUpRejectedData = @json($topUpRejectedData ?? array_map(function() { return rand(0, 5); }, array_fill(0, count($months), 0)));
        
        // Revenue data
        const revenueData = @json($revenueData ?? array_map(function() { return rand(2000000, 10000000); }, array_fill(0, count($months), 0)));
        
        // Initialize chart
        const ctx = document.getElementById('statsChart').getContext('2d');
        const statsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Total Booking',
                    data: bookingData,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1F2937',
                        bodyColor: '#1F2937',
                        borderColor: 'rgba(220, 220, 220, 1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: false,
                            color: 'rgba(220, 220, 220, 0.3)'
                        },
                        ticks: {
                            stepSize: 5,
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        font: {
                            size: 12
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Toggle chart data function
        window.toggleChartData = function(type) {
            // Reset all buttons
            document.getElementById('btn-booking').classList.remove('bg-indigo-700', 'text-white');
            document.getElementById('btn-topup').classList.remove('bg-blue-700', 'text-white');
            document.getElementById('btn-revenue').classList.remove('bg-green-700', 'text-white');
            
            document.getElementById('btn-booking').classList.add('bg-gray-100', 'text-gray-700');
            document.getElementById('btn-topup').classList.add('bg-gray-100', 'text-gray-700');
            document.getElementById('btn-revenue').classList.add('bg-gray-100', 'text-gray-700');
            
            if (type === 'booking') {
                document.getElementById('btn-booking').classList.remove('bg-gray-100', 'text-gray-700');
                document.getElementById('btn-booking').classList.add('bg-indigo-700', 'text-white');
                
                statsChart.data.datasets = [{
                    label: 'Total Booking',
                    data: bookingData,
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                    pointRadius: 4
                }];
                
                statsChart.options.scales.y.title = {
                    display: true,
                    text: 'Jumlah Booking'
                };
            } else if (type === 'topup') {
                document.getElementById('btn-topup').classList.remove('bg-gray-100', 'text-gray-700');
                document.getElementById('btn-topup').classList.add('bg-blue-700', 'text-white');
                
                statsChart.data.datasets = [
                    {
                        label: 'Permintaan Top Up',
                        data: topUpData,
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 4
                    },
                    {
                        label: 'Disetujui',
                        data: topUpApprovedData,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false,
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                        pointRadius: 3
                    },
                    {
                        label: 'Ditolak',
                        data: topUpRejectedData,
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false,
                        pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                        pointRadius: 3
                    }
                ];
                
                statsChart.options.scales.y.title = {
                    display: true,
                    text: 'Jumlah Top Up'
                };
            } else if (type === 'revenue') {
                document.getElementById('btn-revenue').classList.remove('bg-gray-100', 'text-gray-700');
                document.getElementById('btn-revenue').classList.add('bg-green-700', 'text-white');
                
                statsChart.data.datasets = [{
                    label: 'Pendapatan (Rp)',
                    data: revenueData,
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    pointRadius: 4
                }];
                
                statsChart.options.scales.y.title = {
                    display: true,
                    text: 'Pendapatan (Rp)'
                };
            }
            
            statsChart.update();
        };
        
        // Add animation on load
        const cards = document.querySelectorAll('.bg-white.shadow-md');
        cards.forEach((card, index) => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = 1;
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    });
</script>
@endpush
@endsection