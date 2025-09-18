@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-5xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Brand / Illustration -->
                <div class="hidden lg:flex relative items-center justify-center bg-gradient-to-br from-purple-600 to-indigo-600 p-10">
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-white/10 via-white/0 to-transparent"></div>
                    <div class="relative z-10 text-white text-center space-y-4 max-w-sm">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/20 backdrop-blur">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold tracking-tight">Welcome back to Gourmet Express</h2>
                        <p class="text-white/80">Sign in to continue your order and track your history seamlessly.</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="px-8 py-10">
                    <div class="max-w-md mx-auto">
                        <div class="mb-8 text-center lg:text-left">
                            <h1 class="text-2xl font-bold text-gray-900">Sign in</h1>
                            <p class="mt-1 text-sm text-gray-600">Enter your details below to access your account</p>
                        </div>

                        @if ($errors->any())
                            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ show: false }">
                            @csrf

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:text-purple-700">Forgot?</a>
                                    @endif
                                </div>
                                <div class="mt-1 relative">
                                    <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 pr-10" />
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.07-3.368m3.872-2.497A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.043 5.197M15 12a3 3 0 00-3-3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-white font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                                Sign in
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="font-medium text-purple-600 hover:text-purple-700">Create one</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


