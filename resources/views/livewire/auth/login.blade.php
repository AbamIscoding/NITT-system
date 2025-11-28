<x-layouts.auth>
    <div class="flex flex-col gap-2 max-w-md w-full">

        {{-- Brand + Title --}}
        <div class="space-y-2 text-center">
            {{-- already added in the simple.blade.php
            div class="flex justify-center">
                <img src="{{ asset('images/logo.png') }}"
                    alt="Northern Island Logo"
                    class="h-40 w-auto rounded-lg shadow-sm">
            </div> --}}

            <div>
                <h1 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">
                    Northern Island Staff Login
                </h1>
                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-200">
                    Internal booking &amp; invoicing portal for Northern Island Travel &amp; Tours.
                </p>
            </div>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="you@northern-island.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        {{-- Instead of public Sign up, show admin note --}}
        <div class="mt-1 text-[12px] text-center text-zinc-600 dark:text-zinc-100">
            Accounts are created by the system administrator. If you need access,
            please coordinate with Northern Island management.
        </div>
    </div>
</x-layouts.auth>
