<div>
    <div class="min-h-screen flex items-center justify-center">
        <section class="w-full p-8 xl:px-4">
            <div class="max-w-md mx-auto">
                <div class="flex flex-col items-center  bg-white shadow-lg rounded-lg p-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Login to Your Account</h2>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form wire:submit.prevent="login" class="w-full">
                        @if (session('eligible_to_transfer_cart'))
{{--                            <div class="mb-4 p-2 bg-gray-100 text-gray-800 border border-gray-300 rounded">--}}
{{--                                Redirecting back to: <strong>{{ session()->getId() }}</strong>--}}
{{--                            </div>--}}
                        @endif
                        <!-- Email Address -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input
                                wire:model="form.email"
                                id="email"
                                class="block w-full px-3 py-3 mt-2 text-sm placeholder-gray-400 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-600 focus:ring-opacity-5"
                                type="email"
                                name="email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="example@example.ro"
                            />
                            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input
                                wire:model="form.password"
                                id="password"
                                class="block w-full px-3 py-3 mt-2 text-sm placeholder-gray-400 border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-600 focus:ring-opacity-5"
                              type="password"
                              name="password"
                              required autocomplete="current-password"
                            />
                            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <label for="remember" class="inline-flex items-center">
                                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <!-- Forgot Password and Login Button -->
                        <div class="flex items-center justify-between mb-4">
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif

                            <x-primary-button-border class="border border-black">
                                {{ __('Log in') }}
                            </x-primary-button-border>
                        </div>
                    </form>

                    <div class="w-full flex items-left justify-between pt-4 mb-4 border-t">
                        <p class="text-sm text-gray-600 hover:text-gray-900">
                            Nu ai cont? <a href="{{ route('register') }}" class="underline">Înregistrează-te aici</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

