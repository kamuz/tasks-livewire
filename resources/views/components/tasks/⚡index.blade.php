<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $title = '';

    public string $description = '';

    public string $filter = 'all';

    public string $notice = '';

    public function addTask(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        auth()->user()->tasks()->create($validated);

        $this->reset('title', 'description');
        $this->notice = 'Задачу додано до списку.';
    }

    public function toggleTask(int $taskId): void
    {
        $task = auth()->user()->tasks()->findOrFail($taskId);
        $task->update(['is_completed' => ! $task->is_completed]);
        $this->notice = $task->is_completed ? 'Задачу виконано.' : 'Задачу повернуто до активних.';
    }

    public function deleteTask(int $taskId): void
    {
        auth()->user()->tasks()->findOrFail($taskId)->delete();
        $this->notice = 'Задачу видалено.';
    }
};
?>

@php
    $user = auth()->user();
    $totalTasks = $user->tasks()->count();
    $completedTasks = $user->tasks()->where('is_completed', true)->count();
    $activeTasks = $totalTasks - $completedTasks;
    $tasks = $user->tasks()
        ->when($filter === 'active', fn ($query) => $query->where('is_completed', false))
        ->when($filter === 'completed', fn ($query) => $query->where('is_completed', true))
        ->latest()
        ->get();
@endphp

<div class="min-h-screen">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 sm:px-8">
            <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 text-lg font-semibold tracking-tight">
                <span class="flex size-9 items-center justify-center rounded-xl bg-indigo-600 text-white">
                    <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="m5 12.5 4.2 4.2L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                Плануй
            </a>

            <div class="flex items-center gap-3 sm:gap-5">
                <span class="hidden text-sm text-slate-500 sm:inline">{{ $user->name }}</span>
                <span class="flex size-9 items-center justify-center rounded-full bg-indigo-50 text-sm font-semibold text-indigo-700">{{ $user->initials() }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Вийти</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-14">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold text-indigo-600">Твій особистий простір</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Список справ</h1>
                <p class="mt-2 text-slate-500">Маленькі кроки щодня ведуть до великих результатів.</p>
            </div>

            <div class="flex gap-3">
                <div class="min-w-28 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                    <p class="text-xs font-medium text-slate-500">Активні</p>
                    <p class="mt-1 text-2xl font-semibold tracking-tight">{{ $activeTasks }}</p>
                </div>
                <div class="min-w-28 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                    <p class="text-xs font-medium text-slate-500">Виконані</p>
                    <p class="mt-1 text-2xl font-semibold tracking-tight">{{ $completedTasks }}</p>
                </div>
            </div>
        </div>

        <div class="mt-9 grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <section class="order-2 min-w-0 lg:order-1">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">Мої задачі <span class="ml-1 text-sm font-medium text-slate-400">{{ $totalTasks }}</span></h2>
                    <div class="flex rounded-xl border border-slate-200 bg-white p-1 text-sm">
                        <button type="button" wire:click="$set('filter', 'all')" class="rounded-lg px-3 py-2 font-medium transition {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">Усі</button>
                        <button type="button" wire:click="$set('filter', 'active')" class="rounded-lg px-3 py-2 font-medium transition {{ $filter === 'active' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">Активні</button>
                        <button type="button" wire:click="$set('filter', 'completed')" class="rounded-lg px-3 py-2 font-medium transition {{ $filter === 'completed' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">Виконані</button>
                    </div>
                </div>

                @if ($notice)
                    <p role="status" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ $notice }}</p>
                @endif

                <div class="mt-4 space-y-3">
                    @forelse ($tasks as $task)
                        <article wire:key="task-{{ $task->id }}" class="group flex gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/40 transition hover:border-slate-300">
                            <button type="button" wire:click="toggleTask({{ $task->id }})" aria-label="{{ $task->is_completed ? 'Повернути задачу до активних' : 'Позначити задачу виконаною' }}" class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full border-2 transition {{ $task->is_completed ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 text-transparent hover:border-indigo-500' }}">
                                @if ($task->is_completed)
                                    <svg viewBox="0 0 20 20" fill="none" class="size-4" aria-hidden="true"><path d="m4 10 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endif
                            </button>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-medium {{ $task->is_completed ? 'text-slate-400 line-through' : 'text-slate-900' }}">{{ $task->title }}</h3>
                                @if ($task->description)
                                    <p class="mt-1.5 text-sm leading-6 text-slate-500">{{ $task->description }}</p>
                                @endif
                                <p class="mt-3 text-xs text-slate-400">Додано {{ $task->created_at->format('d.m.Y') }}</p>
                            </div>

                            <button type="button" wire:click="deleteTask({{ $task->id }})" wire:confirm="Видалити цю задачу?" aria-label="Видалити задачу" class="self-start rounded-lg p-2 text-slate-300 opacity-100 transition hover:bg-rose-50 hover:text-rose-600 sm:opacity-0 sm:group-hover:opacity-100 sm:focus:opacity-100">
                                <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5 7l1 13h12l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <svg viewBox="0 0 24 24" fill="none" class="size-6" aria-hidden="true"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <h3 class="mt-4 font-semibold">{{ $filter === 'all' ? 'Тут поки порожньо' : 'Задач не знайдено' }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $filter === 'all' ? 'Додай першу задачу у формі поруч.' : 'Спробуй обрати інший фільтр.' }}</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="order-1 h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/40 lg:order-2 lg:p-6">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <h2 class="font-semibold">Нова задача</h2>
                        <p class="text-xs text-slate-500">Запиши те, що потрібно зробити</p>
                    </div>
                </div>

                <form wire:submit="addTask" class="mt-5 space-y-4">
                    <div>
                        <label for="task-title" class="mb-2 block text-sm font-medium text-slate-700">Назва</label>
                        <input id="task-title" type="text" wire:model="title" maxlength="160" required placeholder="Наприклад, завершити звіт" class="w-full rounded-xl border border-slate-300 px-3.5 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                        @error('title') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="task-description" class="mb-2 block text-sm font-medium text-slate-700">Опис <span class="font-normal text-slate-400">необов’язково</span></label>
                        <textarea id="task-description" wire:model="description" rows="3" maxlength="1000" placeholder="Додай трохи деталей…" class="w-full resize-y rounded-xl border border-slate-300 px-3.5 py-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></textarea>
                        @error('description') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-60">
                        <svg wire:loading.remove wire:target="addTask" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <span wire:loading.remove wire:target="addTask">Додати задачу</span>
                        <span wire:loading wire:target="addTask">Додаємо…</span>
                    </button>
                </form>
            </aside>
        </div>
    </main>
</div>
