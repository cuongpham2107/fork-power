<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/login', 'pages::auth.login')->name('login');

Route::middleware('auth')->group(function () {
    // Route::livewire('/', 'pages::home');
    Route::livewire('/', 'pages::battery.index');
    Route::livewire('/history', 'pages::battery-usage.history');
    Route::livewire('/user', 'pages::user.view');
});
