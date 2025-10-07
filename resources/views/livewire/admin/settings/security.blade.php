<div>
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
            <p class="mt-1 text-sm text-gray-500">Change your account password for security purposes.</p>
        </div>

        <form wire:submit="updatePassword" class="p-6 space-y-6">
            <!-- Current Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" wire:model.blur="current_password" 
                       class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" 
                       placeholder="Enter your current password" />
                @error('current_password')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" wire:model.blur="new_password" 
                       class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" 
                       placeholder="Enter your new password" />
                @error('new_password')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" wire:model.blur="new_password_confirmation" 
                       class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" 
                       placeholder="Confirm your new password" />
                @error('new_password_confirmation')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Security Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Security Notice</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>After updating your password, you will be automatically logged out for security purposes. You'll need to log in again with your new password.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- Store Deactivation Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Deactivate Store</h3>
            <p class="mt-1 text-sm text-gray-500">Temporarily disable your store from being visible to customers.</p>
        </div>

        <form wire:submit="deactivateStore" class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" wire:model.blur="store_deactivation_password" 
                       class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" 
                       placeholder="Enter your password to confirm" />
                @error('store_deactivation_password')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Warning Notice -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-orange-800">Important Notice</h3>
                        <div class="mt-2 text-sm text-orange-700">
                            <p>Deactivating your store will make it invisible to customers. You can reactivate it later from your store settings.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    Deactivate Store
                </button>
            </div>
        </form>
    </div>

    <!-- Account Deletion Section -->
    <div class="bg-white rounded-lg shadow-sm border border-red-200 mt-6">
        <div class="px-6 py-4 border-b border-red-200">
            <h3 class="text-lg font-medium text-red-900">Delete Account</h3>
            <p class="mt-1 text-sm text-red-600">Permanently delete your account and all associated data. This action cannot be undone.</p>
        </div>

        <form wire:submit="deleteAccount" class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" wire:model.blur="account_deletion_password" 
                       class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-red-500" 
                       placeholder="Enter your password to confirm" />
                @error('account_deletion_password')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type DELETE to confirm</label>
                <input type="text" wire:model.blur="account_deletion_confirmation" 
                       class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-red-500 focus:ring-red-500" 
                       placeholder="Type DELETE" />
                @error('account_deletion_confirmation')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Danger Notice -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Danger Zone</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>This will permanently delete:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Your account and profile</li>
                                <li>All your stores and menu items</li>
                                <li>All order history and customer data</li>
                                <li>All payment records</li>
                            </ul>
                            <p class="mt-2 font-medium">This action cannot be undone!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>