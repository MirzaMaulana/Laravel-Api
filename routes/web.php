<?php

use App\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/password/reset/{token}', [PasswordResetController::class, 'index'])->name('password.reset');

Route::get('/notif', function () {
    $user = User::first();
    $user->notify(new \App\Notifications\PasswordNotification);
});
