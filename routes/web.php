<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;    //gọi controller của home
use App\Http\Controllers\CategoryProduct;   //gọi controller của cate

// Route::get('/', 'HomeController@index');
// Route::get('/trang-chu','HomeController@index' ); 
// Đây là cách cho laravel 8 trở xuống

//User
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);

//Admin
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/dashboard', [AdminController::class, 'show_dashboard']);
Route::post('/admin_dashboard', [AdminController::class, 'dashboard']); // đăng nhập thành công sẽ hiển thị
Route::get('/logout', [AdminController::class, 'logout']);

//Category
Route::get('/add-category', [CategoryProduct::class, 'add_category']);
Route::get('/all-category', [CategoryProduct::class, 'all_category']);


// laravel 8 trở lên phải sử dụng [Controller::class, 'method'] theo cú pháp array-based
// Gọi tên controller rồi đến hàm của controller đó