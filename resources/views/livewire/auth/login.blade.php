<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Redirect based on role
        $user = Auth::user();   
        if ($user->role === 'admin') {
            // Assuming admin dashboard route is named 'admin.dashboard'
            // Make sure to define this route in routes/web.php
            $this->redirect(route('admin.dashboard'), navigate: true);
            return;
        } elseif ($user->role === 'talent') {
            // Assuming talent dashboard route is named 'talent.dashboard'
            // Make sure to define this route in routes/web.php
            $this->redirect(route('talent.dashboard'), navigate: true);
            return;
        }

        // Default redirect for 'user' role
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="grid grid-cols-5 rounded-xl overflow-hidden shadow-lg bg-white dark:bg-neutral-900">
    <!-- Left image -->
    <div class="hidden md:flex col-span-3 relative items-center justify-center bg-cover bg-center"
        style="background-image: url('{{ asset('images/3260649.jpg') }}'); min-height: 600px;">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 text-white p-8">
            <img src="{{ asset('images/telkom-logo.png') }}" alt="Logo" class="mb-4 w-24">
            <h2 class="text-2xl font-bold">Selamat Datang di</h2>
            <h2 class="text-2xl font-bold">Sistem Pengelolaan Beasiswa</h2>
            <p class="mt-2">PUI PT IS-IoT</p>
        </div>
    </div>

    <!-- Form -->
    <div class="col-span-5 md:col-span-2 flex items-center justify-center p-8">
        <form wire:submit="login" class="w-full max-w-md space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Login</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">Enter your email and password below to log in</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-white">Email</label>
                <input wire:model.defer="email" type="email" id="email" required
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-neutral-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-white">Password</label>
                <input wire:model.defer="password" type="password" id="password" required
                    class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-neutral-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input wire:model="remember" id="remember" type="checkbox"
                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-white">Remember me</label>
            </div>

            <div>
                <button type="submit"
                    class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:outline-none">
                    Login
                </button>
            </div>

            @if (Route::has('password.request'))
                <div class="text-right">
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">Forgot
                        your password?</a>
                </div>
            @endif

            @if (Route::has('register'))
                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Don\'t have an account?') }}
                    <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
                </div>
            @endif
        </form>

    </div>
</div>
