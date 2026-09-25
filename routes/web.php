<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks')->name('home');

Route::livewire('/login', 'auth.login')->middleware('guest')->name('login');
Route::livewire('/register', 'auth.register')->middleware('guest')->name('register');

Route::post('/logout', function () {
    auth()->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::livewire('/tasks', 'tasks.index')->middleware('auth')->name('tasks.index');
