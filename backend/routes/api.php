<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('user/get-all-paginate', [UserController::class, 'indexPaginate']);
Route::apiResource('user', UserController::class);
