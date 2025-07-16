<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', fn () => redirect('/admin/login'))->name('login');

Route::get('/tes-auth', function () {
    return dd(Auth::guard('web')->user());
});
