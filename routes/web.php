<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Root route
Route::get('/', function () {
    return redirect()->route('home');
});

// Public routes
Route::get('/home', function () {
    return view('home');
})->name('home');
Route::get('/services', function () {
    return view('services');
})->name('services');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');
Route::get('/book', function () {
    return view('book');
})->name('book');
Route::get('/chatbot', \App\Livewire\Chatbot::class)->name('chatbot');

// Dashboard route (from Jetstream)
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return redirect()->route('admin.booking.calendar');
})->name('dashboard');
