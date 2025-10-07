<!-- Navigation -->
<div>
<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-indigo-600">{{ config('app.name') }}</h1>
                </div>
            </div>
            <div x-data="{ open: false }" class="flex items-center">
                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('stores.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Stores</a>
                    <a href="{{ route('merchant.login') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-purple-700">Merchant Login</a>
                </div>
                <!-- Mobile Hamburger -->
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" :aria-expanded="open">
                        <svg class="h-6 w-6" x-show="!open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Mobile Dropdown -->
                <div x-show="open" @click.away="open = false" class="absolute top-16 right-4 w-48 bg-white rounded-lg shadow-lg p-2 z-50 md:hidden" x-transition>
                    <a href="{{ route('stores.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 hover:text-indigo-600 rounded-md text-sm font-medium transition-colors duration-150">Stores</a>
                    <a href="{{ route('merchant.login') }}" class="block px-4 py-2 text-white bg-purple-600 hover:bg-purple-700 hover:scale-105 rounded-md text-sm font-medium mt-1 transition-all duration-150">Merchant Login</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Sistem Pesanan Restoran Paling Mudah
            </h1>
            <p class="text-xl md:text-2xl max-w-3xl mx-auto">
                Memperkenalkan sistem pesanan restoran atas talian yang dibangunkan khas untuk kegunaan restoran dan pelanggan. Sistem ini direka secara ringkas untuk memudahkan penggunaan anda untuk mengurus menu, pesanan, dan juga menerima pembayaran dari pelanggan.
            </p>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fungsi & Ciri-ciri</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Sistem lengkap untuk menguruskan restoran anda dengan mudah dan cekap
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Menu Management -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Urus Menu</h3>
                <p class="text-gray-600">Cipta dan uruskan menu restoran anda secara mudah dengan kategori, harga, dan gambar yang menarik.</p>
            </div>

            <!-- Order Management -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Urus Pesanan</h3>
                <p class="text-gray-600">Terima dan uruskan pesanan pelanggan dengan status real-time dan notifikasi automatik.</p>
            </div>

            <!-- Address Management -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Urus Alamat</h3>
                <p class="text-gray-600">Pelanggan boleh menyimpan dan menguruskan alamat penghantaran dengan mudah.</p>
            </div>

            <!-- Real-time Updates -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Kemaskini Masa Nyata</h3>
                <p class="text-gray-600">Status pesanan dikemas kini secara real-time dengan notifikasi automatik untuk admin dan pelanggan.</p>
            </div>

            <!-- Analytics Dashboard -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Dashboard & Analitik</h3>
                <p class="text-gray-600">Lihat prestasi restoran, pesanan yang belum selesai, serta jumlah pendapatan harian, mingguan, dan bulanan.</p>
            </div>

            <!-- Order History -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Sejarah Pesanan</h3>
                <p class="text-gray-600">Pelanggan boleh melihat sejarah pesanan mereka dengan carian dan penapisan yang mudah.</p>
            </div>

            <!-- Admin Panel -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Panel Admin</h3>
                <p class="text-gray-600">Panel admin yang lengkap untuk menguruskan menu, kategori, pesanan, dan pelanggan dengan mudah.</p>
            </div>

              <!-- Payment Integration -->
            <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition hover:shadow-md">
                <!-- Icon -->
                <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>

                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-1">
                    Integrasi Pembayaran
                </h3>

                <!-- Description -->
                <p class="text-gray-600 text-sm leading-relaxed">
                    Sokongan pembayaran akan dilancarkan tidak lama lagi. Nantikan kemas kini terkini!
                </p>

                <!-- Coming Soon Overlay -->
                <div class="absolute inset-0 bg-yellow-50/20 backdrop-blur-[1px] w-full h-full pointer-events-none">
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1.5 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full shadow-sm">
                            🚧 Akan Datang
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg relative overflow-hidden border border-gray-200 shadow-sm ">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Pesanan Meja QR</h3>
                <p class="text-gray-600">Pelanggan imbas kod QR di meja untuk akses menu, buat pesanan, dan bayar terus dari telefon. Setiap pesanan dipautkan kepada nombor meja dan dihantar masa nyata ke dapur.</p>
                <!-- Akan Datang overlay -->
                <div class="absolute inset-0 bg-yellow-50/20 backdrop-blur-[1px] w-full h-full pointer-events-none">
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1.5 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full shadow-sm">
                            🚧 Akan Datang
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="py-16 bg-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Soalan Lazim (FAQ)</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Jawapan kepada soalan-soalan yang sering ditanya mengenai sistem pesanan restoran kami.
            </p>
        </div>
        <div class="space-y-4" x-data="{ open: null }">
            <!-- FAQ 1 -->
            <div class="bg-white rounded-lg shadow-sm">
                <button 
                    @click="open === 1 ? open = null : open = 1"
                    class="w-full flex justify-between items-center p-6 focus:outline-none"
                    :aria-expanded="open === 1"
                    aria-controls="faq-1"
                >
                    <span class="text-lg font-semibold text-gray-900 text-left">Bagaimana cara membuat pesanan?</span>
                    <svg :class="{'rotate-180': open === 1}" class="w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div 
                    x-show="open === 1"
                    x-collapse
                    id="faq-1"
                    class="px-6 pb-6 text-gray-600 text-sm"
                >
                    Anda boleh melayari menu restoran, menambah item ke troli, dan membuat pembayaran secara dalam talian. Pesanan anda akan diproses serta-merta.
                </div>
            </div>
            <!-- FAQ 2 -->
            <div class="bg-white rounded-lg shadow-sm">
                <button 
                    @click="open === 2 ? open = null : open = 2"
                    class="w-full flex justify-between items-center p-6 focus:outline-none"
                    :aria-expanded="open === 2"
                    aria-controls="faq-2"
                >
                    <span class="text-lg font-semibold text-gray-900 text-left">Adakah saya perlu mendaftar akaun?</span>
                    <svg :class="{'rotate-180': open === 2}" class="w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div 
                    x-show="open === 2"
                    x-collapse
                    id="faq-2"
                    class="px-6 pb-6 text-gray-600 text-sm"
                >
                    Anda boleh membuat pesanan sebagai tetamu, tetapi mendaftar akaun membolehkan anda menjejak sejarah pesanan dan mendapat pengalaman yang lebih baik.
                </div>
            </div>
            <!-- FAQ 3 -->
            <div class="bg-white rounded-lg shadow-sm">
                <button 
                    @click="open === 3 ? open = null : open = 3"
                    class="w-full flex justify-between items-center p-6 focus:outline-none"
                    :aria-expanded="open === 3"
                    aria-controls="faq-3"
                >
                    <span class="text-lg font-semibold text-gray-900 text-left">Bagaimana saya boleh membayar?</span>
                    <svg :class="{'rotate-180': open === 3}" class="w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div 
                    x-show="open === 3"
                    x-collapse
                    id="faq-3"
                    class="px-6 pb-6 text-gray-600 text-sm"
                >
                    Kami menyokong pembayaran dalam talian yang selamat melalui Stripe. Pilihan pembayaran lain akan ditambah pada masa akan datang.
                </div>
            </div>
            <!-- FAQ 4 -->
            <div class="bg-white rounded-lg shadow-sm">
                <button 
                    @click="open === 4 ? open = null : open = 4"
                    class="w-full flex justify-between items-center p-6 focus:outline-none"
                    :aria-expanded="open === 4"
                    aria-controls="faq-4"
                >
                    <span class="text-lg font-semibold text-gray-900 text-left">Bagaimana untuk menghubungi sokongan pelanggan?</span>
                    <svg :class="{'rotate-180': open === 4}" class="w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div 
                    x-show="open === 4"
                    x-collapse
                    id="faq-4"
                    class="px-6 pb-6 text-gray-600 text-sm"
                >
                    Anda boleh menghubungi kami melalui halaman <span class="font-medium text-indigo-600">Hubungi Kami</span> atau melalui <a href="mailto:assyaafi96@gmail.com" class="font-medium text-indigo-600">support@gourmetexpress.com</a>.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h3 class="text-2xl font-bold mb-4">{{ config('app.name') }}</h3>
            <p class="text-gray-400 mb-4">Sistem Pesanan Restoran Paling Mudah</p
        </div>
    </div>
</footer>
</div>
