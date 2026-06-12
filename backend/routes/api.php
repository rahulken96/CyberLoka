<?php

use App\Http\Controllers\HeadOfFamilyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('user/get-all-paginate', [UserController::class, 'indexPaginate']);
Route::apiResource('user', UserController::class);

Route::get('head-of-family/get-all-paginate', [HeadOfFamilyController::class, 'indexPaginate']);
Route::apiResource('head-of-family', HeadOfFamilyController::class);