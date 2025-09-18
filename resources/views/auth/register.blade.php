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
                        <h2 class="text-3xl font-bold tracking-tight">Create your account</h2>
                        <p class="text-white/80">Join to start ordering and enjoy a faster checkout experience.</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="px-8 py-10">
                    <div class="max-w-md mx-auto">
                        <div class="mb-8 text-center lg:text-left">
                            <h1 class="text-2xl font-bold text-gray-900">Sign up</h1>
                            <p class="mt-1 text-sm text-gray-600">Fill in the details below to create your account</p>
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

                        <form method="POST" action="{{ route('register') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                            </div>

                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-white font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create account
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-medium text-purple-600 hover:text-purple-700">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


