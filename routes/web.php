<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Adminmenucontroller;

Route::get('/', [Adminmenucontroller::class, 'index']);
Route::get('/admin/create', [Adminmenucontroller::class, 'create']);

Route::get('/corn', [Menucontroller::class, 'corn']);
