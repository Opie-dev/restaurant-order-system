<!-- Navigation -->
<div>
<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-indigo-600">Gourmet Express</h1>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('stores.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Stores</a>
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
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
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

            <!-- Cart System -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Sistem Troli</h3>
                <p class="text-gray-600">Pelanggan boleh menambah item ke troli, mengubah kuantiti, dan mengira jumlah secara automatik.</p>
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

            <!-- Payment Integration -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Integrasi Pembayaran</h3>
                <p class="text-gray-600">Terima pembayaran dari pelanggan menggunakan Stripe dengan webhook untuk kemaskini status automatik.</p>
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
        </div>
    </div>
</div>

<!-- Tech Stack Section -->
<div class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Teknologi Moden</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Dibangunkan dengan teknologi terkini untuk prestasi dan pengalaman pengguna yang terbaik
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Laravel 11</h3>
                    <p class="text-gray-600 text-sm">PHP Framework</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Livewire 3</h3>
                    <p class="text-gray-600 text-sm">Full-stack Framework</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Alpine.js 3</h3>
                    <p class="text-gray-600 text-sm">Lightweight JS</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tailwind CSS</h3>
                    <p class="text-gray-600 text-sm">Utility-first CSS</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-indigo-600 text-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Bersedia untuk Memulakan?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            Daftar akaun secara PERCUMA sekarang dan mulakan menguruskan restoran anda dengan lebih cekap.
        </p>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h3 class="text-2xl font-bold mb-4">Gourmet Express</h3>
            <p class="text-gray-400 mb-4">Sistem Pesanan Restoran Paling Mudah</p>
            <p class="text-gray-500 text-sm">
                Dibangunkan dengan Laravel 11, Livewire 3, Alpine.js 3, dan Tailwind CSS
            </p>
        </div>
    </div>
</footer>
</div>
