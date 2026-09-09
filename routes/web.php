<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Adminmenucontroller;

Route::get('/', [Adminmenucontroller::class, 'index']);

Route::get('/corn', [Menucontroller::class, 'corn']);
