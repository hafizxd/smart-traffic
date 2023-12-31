<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

Route::get('/login', function (){
    return view('login');
});


Route::middleware(['auth', 'role'])->group(function () {
    
});

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

Route::get('/admin/user', function (){
    return view('admin/pages/user');
});
Route::get('/admin/iot', function (){
    return view('admin/pages/iot');
});
Route::get('/admin/carpool', function (){
    return view('admin/pages/carpool');
});

Route::get('/admin/comment', [AdminController::class, 'showComments'])->name('comment');
