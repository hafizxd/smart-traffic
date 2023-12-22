<?php

use Illuminate\Support\Facades\Route;

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
    return view('index');
});
Route::get('/login', function (){
    return view('login');
});

Route::get('/admin', function (){
    return view('admin/index');
});
Route::get('/admin/user', function (){
    return view('admin/pages/user');
});
Route::get('/admin/iot', function (){
    return view('admin/pages/Iot');
});
Route::get('/admin/carpool', function (){
    return view('admin/pages/carpool');
});