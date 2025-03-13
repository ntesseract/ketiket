<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-8 md:flex md:items-center md:justify-between">
            <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-12">
                <!-- Logo and slogan -->
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="block h-10 w-auto hover:scale-105 transition-transform duration-300">
                        <span class="ml-2 text-xl font-bold text-indigo-600">WisataKu</span>
                    </a>
                    <p class="text-sm text-gray-500">Jelajahi keindahan Indonesia bersama kami</p>
                </div>
                
                <!-- Quick links -->
                <div class="md:flex md:flex-row md:space-x-16">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 tracking-wider uppercase">Destinasi</h3>
                        <ul class="mt-4 space-y-2">
                            <li>
                                <a href="{{ route('destinations.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Semua Destinasi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('destinations.recommended') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Rekomendasi Destinasi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('destinations.favorites') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Favorit
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-6 md:mt-0">
                        <h3 class="text-sm font-semibold text-gray-700 tracking-wider uppercase">Akomodasi</h3>
                        <ul class="mt-4 space-y-2">
                            <li>
                                <a href="{{ route('hotels.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Hotel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('restaurants.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Restoran
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('packages.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Paket Wisata
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-6 md:mt-0">
                        <h3 class="text-sm font-semibold text-gray-700 tracking-wider uppercase">Tentang Kami</h3>
                        <ul class="mt-4 space-y-2">
                            <li>
                                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Tentang WisataKu
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Cara Kerja
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                                    Kontak
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Social media links and Newsletter -->
            <div class="mt-8 md:mt-0">
                <div class="flex space-x-5">
                    <a href="#" class="text-gray-400 hover:text-indigo-600 hover:scale-110 transform transition-all duration-200">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-600 hover:scale-110 transform transition-all duration-200">
                        <span class="sr-only">Instagram</span>
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-600 hover:scale-110 transform transition-all duration-200">
                        <span class="sr-only">Twitter</span>
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-600 hover:scale-110 transform transition-all duration-200">
                        <span class="sr-only">YouTube</span>
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
                
                <!-- Newsletter subscription -->
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-gray-700 tracking-wider uppercase">Dapatkan Informasi Terbaru</h3>
                    <p class="mt-2 text-sm text-gray-500">Langganan newsletter kami untuk mendapatkan update dan promo terbaru.</p>
                    <div class="mt-3 flex sm:flex-row flex-col">
                        <input type="email" id="email-newsletter" class="appearance-none bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 text-base text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs" placeholder="Email Anda">
                        <button type="button" onclick="subscribeNewsletter()" class="mt-3 sm:mt-0 sm:ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Langganan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="border-t border-gray-200 py-4 flex flex-col sm:flex-row justify-between items-center">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} WisataKu. All rights reserved.</p>
            <div class="mt-3 sm:mt-0 flex space-x-6">
                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                    Syarat & Ketentuan
                </a>
                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                    Kebijakan Privasi
                </a>
                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                    Pusat Bantuan
                </a>
            </div>
        </div>
    </div>
</footer>

<script>
    function subscribeNewsletter() {
        const email = document.getElementById('email-newsletter').value;
        if (!email || !email.includes('@')) {
            showToast('Error', 'Silakan masukkan email yang valid', 'error');
            return;
        }
        
        // Simulate API call
        setTimeout(() => {
            document.getElementById('email-newsletter').value = '';
            showToast('Berhasil', 'Terima kasih telah berlangganan newsletter kami!', 'success');
        }, 1000);
    }
</script>