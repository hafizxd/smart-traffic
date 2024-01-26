<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;

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

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login-process', [LoginController::class, 'loginAction'])->name('login-process');
Route::get('/logout-process', [LoginController::class, 'logoutAction'])->name('logout-process');

Route::middleware(['auth:web'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/user', [AdminController::class, 'verifView'])->name('admin.pages.user');
    Route::post('/admin/verify-document/{documentId}', [AdminController::class, 'verifyDocument'])->name('verify.document');

    Route::get('/admin/iot', function () {
        return view('admin/pages/iot');
    });
    Route::get('/admin/carpool', function () {
        return view('admin/pages/carpool');
    });
    Route::get('/admin/comment', [AdminController::class, 'comments'])->name('comment');

});
