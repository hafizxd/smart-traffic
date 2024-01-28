<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SensorController;

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

    Route::get('/admin/iot', [SensorController::class, 'sensorView'])->name('sensor.index');
    Route::post('/admin/iot', [SensorController::class, 'store'])->name('sensor.store');
    Route::put('/admin/iot/{id}', [SensorController::class, 'update'])->name('sensor.update');
    Route::delete('/admin/iot/{id}', [SensorController::class, 'destroy'])->name('sensor.destroy');

    Route::get('/admin/carpool', function () {
        return view('admin/pages/carpool');
    });
    Route::get('/admin/comment', [AdminController::class, 'comments'])->name('comment');

});
