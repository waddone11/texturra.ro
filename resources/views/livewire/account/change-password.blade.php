<div class="max-w-7xl mx-auto py-12 px-3 md:px-0">
    <div class="flex flex-wrap md:flex-nowrap">
        <!-- Sidebar (1/5) -->
        <aside class="w-full md:w-1/5 p-3 md:p-0">
            @can('client')
                <livewire:sidebar-account />
            @endcan

            @can('admin')
                <livewire:sidebar-stats />
            @endcan
            @can('manager')
                <livewire:sidebar-stats />
            @endcan
            @can('employee')
                <livewire:sidebar-stats />
            @endcan
        </aside>

        <!-- Main Content (4/5) -->
        <main class="w-full md:w-4/5 pl-3">
            <div class="container">
                <h1 class="text-2xl font-semibold mb-6">{{ __('Change Password') }}</h1>

                @if (session('success'))
                    <div class="mb-4 text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword">
                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="block font-semibold text-gray-700">{{ __('Current Password') }}</label>
                        <input
                            type="password"
                            id="current_password"
                            wire:model="current_password"
                            class="block w-full px-4 py-2 mt-2 border rounded focus:outline-none focus:ring focus:ring-blue-500"
                        />
                        @error('current_password')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="new_password" class="block font-semibold text-gray-700">{{ __('New Password') }}</label>
                        <input
                            type="password"
                            id="new_password"
                            wire:model="new_password"
                            class="block w-full px-4 py-2 mt-2 border rounded focus:outline-none focus:ring focus:ring-blue-500"
                        />
                        @error('new_password')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="block font-semibold text-gray-700">{{ __('Confirm New Password') }}</label>
                        <input
                            type="password"
                            id="new_password_confirmation"
                            wire:model="new_password_confirmation"
                            class="block w-full px-4 py-2 mt-2 border rounded focus:outline-none focus:ring focus:ring-blue-500"
                        />
                        @error('new_password_confirmation')
                        <span class="text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Update Button -->
                    <x-secondary-button type="submit" class="mt-4">
                        {{ __('Update Password') }}
                    </x-secondary-button>
                </form>
            </div>
        </main>
    </div>
</div>
