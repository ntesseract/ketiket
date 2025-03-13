@extends('layouts.admin')

@section('title', 'Detail Permintaan Top Up')

@section('header', 'Detail Permintaan Top Up #' . $topUpRequest->id)

@push('styles')
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
    
    @keyframes slideInRight {
        from {
            transform: translateX(30px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    .slide-in {
        opacity: 0;
        animation: slideInUp 0.6s ease-in-out forwards;
    }
    
    .slide-in-right {
        opacity: 0;
        animation: slideInRight 0.6s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    
    /* Hover Effect */
    .hover-scale {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Loading Spinner */
    .loading-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 36px;
        height: 36px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Parallax Effect */
    .parallax {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90">
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat detail permintaan...</p>
    </div>
</div>

<div class="max-w-6xl mx-auto opacity-0" id="main-content">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('admin.topup.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors flex items-center hover-scale">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
        
        @if($topUpRequest->status == 'pending')
        <div class="flex space-x-2">
            <button type="button" onclick="showApproveModal()" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded flex items-center hover-scale">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Setujui
            </button>
            
            <button type="button" onclick="showRejectModal()" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded flex items-center hover-scale">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tolak
            </button>
        </div>
        @endif
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale" data-animate="fade-in">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Detail Permintaan #{{ $topUpRequest->id }}</h2>
            <div>
                @if($topUpRequest->status == 'pending')
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu Persetujuan</span>
                @elseif($topUpRequest->status == 'approved')
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">Disetujui</span>
                @elseif($topUpRequest->status == 'rejected')
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Permintaan
                </h3>
                
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                            <p class="font-medium">{{ $topUpRequest->created_at->format('d M Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Jumlah</p>
                            <p class="font-bold text-lg text-gray-900">Rp {{ number_format($topUpRequest->amount, 0, ',', '.') }}</p>
                        </div>
                        
                        @if($topUpRequest->status != 'pending')
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Diproses</p>
                            <p class="font-medium">{{ $topUpRequest->processed_at ? $topUpRequest->processed_at->format('d M Y H:i') : '-' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Diproses Oleh</p>
                            <p class="font-medium">{{ $topUpRequest->admin ? $topUpRequest->admin->name : '-' }}</p>
                        </div>
                        @endif
                        
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Catatan dari User</p>
                            <p class="font-medium">{{ $topUpRequest->note ?: 'Tidak ada catatan' }}</p>
                        </div>
                        
                        @if($topUpRequest->status == 'rejected')
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Alasan Penolakan</p>
                            <p class="font-medium text-red-600">{{ $topUpRequest->note ?: 'Tidak ada alasan yang diberikan' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi User
                </h3>
                
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-medium">{{ $topUpRequest->user->name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">User ID</p>
                            <p class="font-medium">{{ $topUpRequest->user_id }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $topUpRequest->user->email }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Bergabung Sejak</p>
                            <p class="font-medium">{{ $topUpRequest->user->created_at->format('d M Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Saldo Saat Ini</p>
                            <p class="font-medium">
                                <?php
                                $currentBalance = DB::table('wallets')->where('user_id', $topUpRequest->user_id)->value('balance') ?? 0;
                                ?>
                                Rp {{ number_format($currentBalance, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('admin.users.show', $topUpRequest->user_id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Lihat Profil User
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            Riwayat Transaksi User
        </h3>
        
        <?php
        $userTransactions = App\Models\WalletTransaction::where('user_id', $topUpRequest->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        ?>
        
        @if(count($userTransactions) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($userTransactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->type == 'top_up')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Top Up</span>
                            @elseif($transaction->type == 'payment')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Pembayaran</span>
                            @elseif($transaction->type == 'refund')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Refund</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $transaction->type }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($transaction->type == 'payment')
                                <span class="text-red-600 font-medium">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-green-600 font-medium">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->status == 'success')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Berhasil</span>
                            @elseif($transaction->status == 'pending')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($transaction->status == 'failed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Gagal</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $transaction->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
            User belum memiliki riwayat transaksi.
        </div>
        @endif
    </div>
    
    <!-- Parallax Section -->
    <div class="mt-8 rounded-lg overflow-hidden shadow-lg opacity-0" data-animate="fade-in" data-delay="200">
        <div class="parallax p-8 flex items-center justify-center" style="background-image: url('{{ asset('images/admin-bg.jpg') }}'); height: 200px;">
            <div class="text-center bg-white bg-opacity-90 p-6 rounded-lg">
                <h3 class="text-xl font-bold text-gray-800">Manajemen Top Up E-Wallet</h3>
                <p class="text-gray-600 mt-2">Kelola permintaan top up user dengan cepat dan efisien</p>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.topup.approve', $topUpRequest->id) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Setujui Permintaan Top Up
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Anda yakin ingin menyetujui permintaan top up ini? Saldo sebesar <span class="font-semibold">Rp {{ number_format($topUpRequest->amount, 0, ',', '.') }}</span> akan langsung ditambahkan ke wallet user.
                                </p>
                                <div class="mt-4">
                                    <label for="note" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                    <textarea id="note" name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Setujui
                    </button>
                    <button type="button" onclick="closeApproveModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.topup.reject', $topUpRequest->id) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Tolak Permintaan Top Up
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Anda yakin ingin menolak permintaan top up ini? Permintaan yang ditolak tidak dapat diubah kembali.
                                </p>
                                <div class="mt-4">
                                    <label for="reject-note" class="block text-sm font-medium text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                                    <textarea id="reject-note" name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" placeholder="Berikan alasan penolakan" required></textarea>
                                    <p class="mt-1 text-sm text-gray-500">Alasan penolakan akan dikirimkan ke user.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tolak
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
    
    // Parallax effect on scroll
    window.addEventListener('scroll', function() {
        const scrollPosition = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax');
        parallaxElements.forEach(element => {
            element.style.backgroundPositionY = -scrollPosition * 0.15 + 'px';
        });
    });
});

// Modal functions for approve request
function showApproveModal() {
    document.getElementById('approve-modal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
}

// Modal functions for reject request
function showRejectModal() {
    document.getElementById('reject-modal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endpush
@endsection