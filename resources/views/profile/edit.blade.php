<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

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
        
        .fade-in {
            opacity: 0;
            animation: fadeIn 0.6s ease-in-out forwards;
        }
        
        .slide-in {
            opacity: 0;
            animation: slideInUp 0.6s ease-in-out forwards;
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

    <div id="loading-overlay" class="fixed inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90">
        <div class="text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p class="text-gray-600">Mempersiapkan profil...</p>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 opacity-0" id="main-content">
            <!-- Informasi Profil -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg hover-scale opacity-0" data-animate="fade-in">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 slide-in">
                                {{ __('Profile Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 slide-in delay-100">
                                {{ __("Update your account's profile information and email address.") }}
                            </p>
                        </header>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div class="opacity-0" data-animate="fade-in" data-delay="200">
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div class="opacity-0" data-animate="fade-in" data-delay="300">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div>
                                        <p class="text-sm mt-2 text-gray-800">
                                            {{ __('Your email address is unverified.') }}

                                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Click here to re-send the verification email.') }}
                                            </button>
                                        </p>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-green-600">
                                                {{ __('A new verification link has been sent to your email address.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="opacity-0" data-animate="fade-in" data-delay="400">
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" :value="old('phone_number', $user->phone_number)" autocomplete="tel" />
                                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                            </div>

                            <div class="opacity-0" data-animate="fade-in" data-delay="500">
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="street-address" />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="opacity-0" data-animate="fade-in" data-delay="600">
                                <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md relative" 
                                     x-data="{ 
                                        fileName: '', 
                                        previewUrl: '{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : null }}',
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
                                                    <label for="profile_picture" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>Upload gambar</span>
                                                        <input id="profile_picture" name="profile_picture" type="file" class="sr-only" accept="image/*" @change="updatePreview($event)">
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
                                                <img :src="previewUrl" class="max-h-48 rounded-full mx-auto" />
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
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>

                            <div class="flex items-center gap-4 opacity-0" data-animate="fade-in" data-delay="700">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transform transition duration-200 hover:scale-105 flex items-center">
                                    <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Save') }}
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Wallet Information -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg hover-scale opacity-0" data-animate="fade-in" data-delay="200">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 slide-in">
                                {{ __('Wallet Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 slide-in delay-100">
                                {{ __('View your current balance and transaction history.') }}
                            </p>
                        </header>

                        <div class="mt-6">
                            <div class="bg-gray-100 p-4 rounded-lg opacity-0" data-animate="fade-in" data-delay="300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600">{{ __('Current Balance') }}</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $user->getFormattedBalanceAttribute() }}</p>
                                    </div>
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Top Up') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 opacity-0" data-animate="fade-in" data-delay="400">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Recent Transactions') }}</h3>
                            
                            @if($user->transactions && $user->transactions->count() > 0)
                                <div class="mt-4 space-y-3">
                                    @foreach($user->transactions->take(5) as $transaction)
                                        <div class="flex items-center justify-between p-3 bg-white border rounded-lg">
                                            <div>
                                                <p class="font-medium">{{ $transaction->type }}</p>
                                                <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                            <div class="{{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                {{ $transaction->amount > 0 ? '+' : '' }} Rp {{ number_format($transaction->amount / 100, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4">
                                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        {{ __('View all transactions') }} →
                                    </a>
                                </div>
                            @else
                                <p class="mt-4 text-sm text-gray-600">{{ __('No transactions found.') }}</p>
                            @endif
                        </div>
                    </section>
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg hover-scale opacity-0" data-animate="fade-in" data-delay="300">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 slide-in">
                                {{ __('Update Password') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 slide-in delay-100">
                                {{ __('Ensure your account is using a long, random password to stay secure.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('put')

                            <div class="opacity-0" data-animate="fade-in" data-delay="300">
                                <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                            </div>

                            <div class="opacity-0" data-animate="fade-in" data-delay="400">
                                <x-input-label for="update_password_password" :value="__('New Password')" />
                                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                            </div>

                            <div class="opacity-0" data-animate="fade-in" data-delay="500">
                                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4 opacity-0" data-animate="fade-in" data-delay="600">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transform transition duration-200 hover:scale-105 flex items-center">
                                    <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Save') }}
                                </button>

                                @if (session('status') === 'password-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg hover-scale opacity-0" data-animate="fade-in" data-delay="400">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 slide-in">
                                {{ __('Delete Account') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 slide-in delay-100">
                                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                            </p>
                        </header>

                        <div class="opacity-0" data-animate="fade-in" data-delay="300">
                            <x-danger-button
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                class="transform transition duration-200 hover:scale-105"
                            >
                                <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                {{ __('Delete Account') }}
                            </x-danger-button>
                        </div>

                        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Are you sure you want to delete your account?') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                                </p>

                                <div class="mt-6">
                                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                                    <x-text-input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="mt-1 block w-3/4"
                                        placeholder="{{ __('Password') }}"
                                    />

                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>

                                    <x-danger-button class="ms-3">
                                        {{ __('Delete Account') }}
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
            fadeElements.forEach((element) => {
                setTimeout(() => {
                    element.style.animation = 'fadeIn 0.5s ease-in-out forwards';
                }, element.getAttribute('data-delay') || 0);
            });
        }, 800);

        // Form validation dengan animasi
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
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
                
                if (!hasError && form.getAttribute('action').includes('profile.update')) {
                    // Animasi submit untuk profile update form
                    const submitBtn = form.querySelector('button[type="submit"]');
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
    });
    </script>
    @endpush
</x-app-layout>