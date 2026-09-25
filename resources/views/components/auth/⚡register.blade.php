<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): mixed
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
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
            Плануй
        </a>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60 sm:p-10">
            <p class="text-sm font-semibold text-indigo-600">Почніть планувати</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Створити акаунт</h1>
            <p class="mt-2 text-sm text-slate-500">Реєстрація займе лише хвилину.</p>

            <form wire:submit="register" class="mt-8 space-y-5">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Ім’я</label>
                    <input id="name" type="text" wire:model="name" autocomplete="name" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Ваше ім’я">
                    @error('name') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Електронна пошта</label>
                    <input id="email" type="email" wire:model="email" autocomplete="email" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="name@example.com">
                    @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Пароль</label>
                    <input id="password" type="password" wire:model="password" autocomplete="new-password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Щонайменше 8 символів">
                    @error('password') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Підтвердіть пароль</label>
                    <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Повторіть пароль">
                </div>

                <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-60">
                    <span wire:loading.remove wire:target="register">Зареєструватися</span>
                    <span wire:loading wire:target="register">Створюємо акаунт…</span>
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">Вже маєте акаунт? <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Увійти</a></p>
    </section>
</main>
