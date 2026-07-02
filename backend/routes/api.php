<?php

use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\HeadOfFamilyController;
use App\Http\Controllers\SocialAssistanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('user/get-all-paginate', [UserController::class, 'indexPaginate']);
Route::apiResource('user', UserController::class);

Route::get('head-of-family/get-all-paginate', [HeadOfFamilyController::class, 'indexPaginate']);
Route::apiResource('head-of-family', HeadOfFamilyController::class);

Route::get('family-member/get-all-paginate', [FamilyMemberController::class, 'indexPaginate']);
Route::apiResource('family-member', FamilyMemberController::class);

Route::get('social-assistance/get-all-paginate', [SocialAssistanceController::class, 'indexPaginate']);
Route::apiResource('social-assistance', SocialAssistanceController::class);