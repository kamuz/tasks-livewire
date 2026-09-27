<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public function login(): mixed
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError('email', "Too many attempts. Please try again in {$seconds} seconds.");

            return null;
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'The email address or password is incorrect.');

            return null;
        }

        RateLimiter::clear($key);
        request()->session()->regenerate();

        return $this->redirectRoute('tasks.index');
    }
};
?>

<main class="flex min-h-screen items-center justify-center px-5 py-12">
    <section class="w-full max-w-md">
        <a href="{{ route('login') }}" class="mb-8 flex items-center justify-center gap-3 text-lg font-semibold tracking-tight">
            <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="m5 12.5 4.2 4.2L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            Planly
        </a>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60 sm:p-10">
            <p class="text-sm font-semibold text-indigo-600">Welcome back</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Log in to your account</h1>
            <p class="mt-2 text-sm text-slate-500">Log in to keep planning your tasks.</p>

            <form wire:submit="login" class="mt-8 space-y-5">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" type="email" wire:model="email" autocomplete="email" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="name@example.com">
                    @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    </div>
                    <input id="password" type="password" wire:model="password" autocomplete="current-password" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Your password">
                    @error('password') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-60">
                    <span wire:loading.remove wire:target="login">Log in</span>
                    <span wire:loading wire:target="login">Logging in…</span>
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">Don't have an account yet? <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Sign up</a></p>
    </section>
</main>
