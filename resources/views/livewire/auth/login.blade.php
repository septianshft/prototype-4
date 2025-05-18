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
            $this->redirect(route('dashboard'), navigate: true);
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

<div
    class="grid md:grid-cols-5 rounded-xl overflow-hidden shadow-2xl bg-white dark:bg-neutral-900 max-w-6xl mx-auto mt-16">
    <!-- Left Image Section -->
    <div class="hidden md:flex col-span-3 relative items-center bg-cover bg-center"
        style="background-image: url('{{ asset('images/3260649.jpg') }}'); min-height: 600px;">
        <div class="w-full px-10 z-10 text-black text-left">
            <img src="{{ asset('images/telkom-logo.png') }}" alt="Logo" class="mb-6 w-24">
            <h2 class="text-4xl font-extrabold leading-tight">Selamat Datang di</h2>
            <h2 class="text-4xl font-extrabold leading-tight">Sistem Pengelolaan Beasiswa</h2>
            <p class="mt-4 text-lg font-light">PUI PT IS-IoT</p>
        </div>
    </div>

    <!-- Right Form Section -->
    <div class="col-span-5 md:col-span-2 flex items-center justify-center p-8">
        <form wire:submit="login" class="w-full max-w-md space-y-8">
            <!-- Title -->
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">Login</h2>
                <p class="text-base text-gray-600 dark:text-gray-300">Masukkan email dan password Anda</p>
            </div>

            <!-- Email -->
            <div>
                <label for="email"
                    class="block text-sm font-semibold text-gray-700 dark:text-white mb-1">Email</label>
                <input wire:model.defer="email" type="email" id="email" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password"
                    class="block text-sm font-semibold text-gray-700 dark:text-white mb-1">Password</label>
                <input wire:model.defer="password" type="password" id="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-neutral-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember -->
            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-white">
                    <input wire:model="remember" type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    <span>Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                        Forgot your password?
                    </a>
                @endif
            </div>

            <!-- Button -->
            <div>
                <button type="submit"
                    class="w-full inline-flex justify-center rounded-lg bg-indigo-600 px-6 py-3 text-white font-semibold hover:bg-indigo-700 focus:outline-none transition text-lg">
                    Login
                </button>
            </div>

            <!-- Register Link -->
            @if (Route::has('register'))
                <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __("Don't have an account?") }}
                    <flux:link :href="route('register')" wire:navigate class="text-indigo-600 hover:underline">
                        {{ __('Sign up') }}
                    </flux:link>
                </div>
            @endif
        </form>
    </div>
</div>
