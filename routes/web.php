<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/admin/faq/save', [AdminController::class, 'saveFaq']);
