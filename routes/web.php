<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\LogoutController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
/* section  register[inicio] */
Route::get('/auth/register', [RegisterController::class, 'index'])->name('register');
Route::post('/auth/register', [RegisterController::class, 'store'])->name('register.store');
/* !section  fin - register[fin] */


/* section1 login[inicio] */
Route::get('/auth/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/login', [LoginController::class, 'store'])->name('login.store');
/* !section1 fin - login[fin] */

/* section2 logout[inicio] */
Route::post('/auth/logout', [LogoutController::class, 'store'])->name('logout.store');

/* !section2 fin - logout[fin] */


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')->with('success', 'Tu cuenta ha sido verificada.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {

    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'Se ha enviado el correo de verificacion.');
})->middleware(['auth', 'throttle:1,1'])->name('verification.send');


/* section6 dashboard[inicio] */
Route::get('/dashboard',[BudgetController::class,'index' ])->middleware(['auth', 'verified'])->name('dashboard');
/* !section6 fin - dashboard[fin] */
