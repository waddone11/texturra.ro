<div>
    <div class="min-h-screen flex items-center justify-center">
        <section class="w-full p-8  xl:px-4">
            <div class="max-w-md mx-auto">
                <div class="flex flex-col items-center bg-white shadow-lg rounded-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Register Your Account</h2>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form wire:submit.prevent="register" class="w-full">
                        @if (session('eligible_to_transfer_cart'))
{{--                            <div class="mb-4 p-2 bg-gray-100 text-gray-800 border border-gray-300 rounded">--}}
{{--                                Redirecting back to: <strong>{{ session()->getId() }}</strong>--}}
{{--                            </div>--}}
                        @endif
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input
                                wire:model.defer="form.name"
                                id="name"
                                class="block w-full px-3 py-3 mt-2 text-sm placeholder-gray-400 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-600 focus:ring-opacity-5"
                                type="text"
                                name="name"
                                required autofocus autocomplete="name"
                            />
                            <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input
                                wire:model.defer="form.email"
                                id="email"
                                class="block w-full px-3 py-3 mt-2 text-sm placeholder-gray-400 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-600 focus:ring-opacity-5"
                                type="email"
                                name="email"
                                required
                                autocomplete="username"
                            />
                            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input
                                wire:model.defer="form.password"
                                id="password"
                                class="block w-full px-3 py-3 mt-2 text-sm placeholder-gray-400 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-600 focus:ring-opacity-5"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input
                                wire:model.defer="form.password_confirmation"
                                id="password_confirmation"
                                class="block w-full px-3 py-3 mt-2 text-sm placeholder-gray-400 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-600 focus:ring-opacity-5"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('form.password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                                {{ __('Already registered?') }}
                            </a>

                            <x-primary-button-border class="ms-4">
                                {{ __('Register') }}
                            </x-primary-button-border>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
