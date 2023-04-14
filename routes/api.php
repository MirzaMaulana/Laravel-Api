<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\PostSaveController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Route untuk register
    Route::post('/register', [AuthController::class, 'register']);
    // Route untuk login
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    //Route untuk logout
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    //reset password
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->middleware('throttle:reset');
    //change password
    Route::post('/password/change', [PasswordResetController::class, 'changePassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/comment/{id}', [CommentController::class, 'update'])->middleware('pemilik-comment', 'throttle:editcomment');
        Route::post('/comment/reply', [CommentController::class, 'reply']);
        Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->middleware('pemilik-comment');
    });

    Route::middleware('auth:sanctum')->group(function () {
        // Route untuk mengecek siapa yang login
        Route::get('/profile', [MyProfileController::class, 'index']);
        // Route update profile user
        Route::put('/profile/update', [MyProfileController::class, 'update']);
    });

    // Route untuk users 
    Route::prefix('user')->middleware('auth:sanctum')->group(function () {
        Route::controller(UserController::class)->group(function () {
            // membuat user baru
            Route::post('/create', 'store');
            // mengambil data semua user
            Route::get('/list', 'show');
            //menghapus user
            Route::delete('/{user}', 'destroy');
            // update user
            Route::put('/{user}', 'update');
        });
    })->name('user');

    // Route untuk post
    Route::prefix('post')->group(function () {
        Route::controller(PostController::class)->group(function () {
            //menampilkan semua post
            Route::get('/list', 'index');
            //menampilkan satu post sesuai idnya
            Route::get('/show/{id}', 'show');

            Route::post('/{postId}/comment', [CommentController::class, 'create'])->middleware('throttle:comment', 'auth:sanctum');
            // membuat post baru
            Route::post('/create', 'store')->middleware('auth:sanctum');
            //Update post
            Route::put('/update/{post}', 'update')->middleware('auth:sanctum');
            // Hapus Post
            Route::delete('/delete/{post}', 'destroy')->middleware('auth:sanctum');
        });
        Route::controller(PostLikeController::class)->group(function () {
            Route::post('/like/{post}', 'store')->middleware('auth:sanctum');
            Route::delete('/unlike/{like}', 'destroy')->middleware('auth:sanctum');
        });
        Route::controller(PostSaveController::class)->group(function () {
            Route::post('/save/{post}', 'store')->middleware('auth:sanctum');
            Route::get('/postsave', 'index')->middleware('auth:sanctum');
            Route::delete('/unsave/{save}', 'destroy')->middleware('auth:sanctum');
        });
    });

    Route::prefix('tag')->group(function () {
        Route::controller(TagsController::class)->group(function () {
            Route::get('/all', 'index');
            Route::post('/create', 'store')->middleware('auth:sanctum');
            Route::delete('/delete/{tag}', 'destroy')->middleware('auth:sanctum');
            Route::put('/update/{tag}', 'update')->middleware('auth:sanctum');
        });
    });
});
