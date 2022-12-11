<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsVerify;
use App\Http\Middleware\VerifiedEmail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
  
// /* New Added Routes */
      //---------- Login Route -------------
Route::post('/login', [UserController::class, 'postLogin'])->middleware(VerifiedEmail::class)->name('login.post'); 
      //----------- SignUp Route---------
Route::post('/registration', [UserController::class, 'signUp'])->name('register.post'); 
     // ----------- Dashboard Route --------
 Route::get('/dashboard/{token}', [UserController::class, 'dashboard'])->middleware(IsVerify::class)->name('dashboard'); 
     // ----------- Forgot Password Raoute ---------
 Route::post('/forgot', [UserController::class, 'forgotPassword'])->name('forgotPassword.post'); 
     // ----------- Reset Password Route ---------
 Route::post('/reset/(token}', [UserController::class, 'passwordReset'])->name('reset.password');
     // ------------- Email Verified Route --------- 
 Route::get('account/verify/{token}', [UserController::class, 'emailVerify'])->name('user.verify'); 
     // ------------- Verfication View Route ---------
 Route::get('/verify', function(){
    return "Your e-mail is verified. You can now login.";
 })->name('user.verify'); 
     // ------------- Not Verfiy View Route ---------
 Route::get('/notVerify', function(){
    return "Your token is invalid or expired. You may go to register urself first.";
 })->name('user.notverify'); 
     // ------------ Already Veriffied View Route
 Route::get('/verify', function(){
    return view('/emailVerified');
 })->name('user.verified'); 
