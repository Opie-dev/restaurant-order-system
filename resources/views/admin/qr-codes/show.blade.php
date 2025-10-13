@extends('layouts.admin')

@section('title', 'QR Code Details')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">QR Code Details</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.qr-codes.download', $qrCode) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PNG
            </a>
            <a href="{{ route('admin.qr-codes.download-pdf', $qrCode) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('admin.qr-codes.download-with-number', $qrCode) }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2"></path>
                </svg>
                With Table Number
            </a>
            <a href="{{ route('admin.qr-codes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to QR Codes
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Code Preview -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code Preview</h3>
                <div class="text-center">
                    <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                        <div class="w-64 h-64 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">QR Code Preview</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Information -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code Information</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Table</dt>
                        <dd class="text-sm text-gray-900">{{ $qrCode->table->display_name }} ({{ $qrCode->table->store->name }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">QR Code ID</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $qrCode->qr_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">QR Code URL</dt>
                        <dd class="text-sm text-gray-900 break-all">{{ $qrCode->qr_url }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Generated</dt>
                        <dd class="text-sm text-gray-900">{{ $qrCode->generated_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @if($qrCode->expires_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expires</dt>
                        <dd class="text-sm text-gray-900">{{ $qrCode->expires_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $qrCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('admin.qr-codes.regenerate', $qrCode) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full text-left px-3 py-2 text-sm text-orange-600 hover:text-orange-900 hover:bg-orange-50 rounded-md transition-colors"
                                    onclick="return confirm('This will deactivate the current QR code and generate a new one. Continue?')">
                                Regenerate QR Code
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.qr-codes.toggle-status', $qrCode) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full text-left px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors">
                                {{ $qrCode->is_active ? 'Deactivate' : 'Activate' }} QR Code
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.qr-codes.destroy', $qrCode) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full text-left px-3 py-2 text-sm text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors"
                                    onclick="return confirm('This will permanently delete the QR code and its files. Continue?')">
                                Delete QR Code
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
