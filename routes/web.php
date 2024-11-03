<?php

use App\Http\Controllers\AdminController; // gọi controller của admin
use App\Http\Controllers\AuthorController; // gọi controller của author
use App\Http\Controllers\CartController; // gọi controller của cart
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;    //gọi controller của home
use App\Http\Controllers\CategoryProduct;   //gọi controller của cate
use App\Http\Controllers\ProductController; // gọi controller của product
use App\Http\Controllers\SupplierController; // gọi controller của supplier
use App\Http\Controllers\CheckoutController; // gọi controller của checkout


// Route::get('/', 'HomeController@index');
// Route::get('/trang-chu','HomeController@index' ); 
// Đây là cách cho laravel 8 trở xuống

//User
Route::get('/', [HomeController::class, 'index']);
Route::get('/trang-chu', [HomeController::class, 'index']);
Route::post('/tim-kiem', [HomeController::class, 'search']);

// Danh mục sách trang chủ
Route::get('/danh-muc-sach/{category_id}', [CategoryProduct::class, 'show_category_home']);
Route::get('/danh-muc-tac-gia/{author_id}', [AuthorController::class, 'show_author_home']);
Route::get('/danh-muc-nxb/{book_id}', [ProductController::class, 'show_nxb_home']);
Route::get('/chi-tiet-san-pham-theo-cate/{book_id}', [ProductController::class, 'details_product_cate']);
Route::get('/chi-tiet-san-pham-theo-author/{book_id}', [ProductController::class, 'details_product_author']);
Route::get('/chi-tiet-san-pham-theo-nxb/{book_id}', [ProductController::class, 'details_product_nxb']);
Route::get('/chi-tiet-san-pham-theo-trang-chu/{book_id}', [ProductController::class, 'details_product_home']);

//Admin
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/dashboard', [AdminController::class, 'show_dashboard']);
Route::post('/admin_dashboard', [AdminController::class, 'dashboard']); // đăng nhập thành công sẽ hiển thị
Route::get('/logout', [AdminController::class, 'logout']);

//Category
Route::get('/add-category', [CategoryProduct::class, 'add_category']);
Route::get('/edit-category/{cate_id}', [CategoryProduct::class, 'edit_category']);
Route::get('/delete-category/{cate_id}', [CategoryProduct::class, 'delete_category']);
Route::get('/all-category', [CategoryProduct::class, 'all_category']);

Route::get('/active-category/{cate_id}', [CategoryProduct::class, 'active_category']);
Route::get('/inactive-category/{cate_id}', [CategoryProduct::class, 'inactive_category']);

Route::post('/save-category', [CategoryProduct::class, 'save_category']);
Route::post('/update-category/{cate_id}', [CategoryProduct::class, 'update_category']);

//Author
Route::get('/add-author', [AuthorController::class, 'add_author']);
Route::get('/edit-author/{author_id}', [AuthorController::class, 'edit_author']);
Route::get('/delete-author/{author_id}', [AuthorController::class, 'delete_author']);
Route::get('/all-author', [AuthorController::class, 'all_author']);

Route::post('/save-author', [AuthorController::class, 'save_author']);
Route::post('/update-author/{author_id}', [AuthorController::class, 'update_author']);

//Supplier
Route::get('/add-supplier', [SupplierController::class, 'add_supplier']);
Route::get('/edit-supplier/{supplier_id}', [SupplierController::class, 'edit_supplier']);
Route::get('/delete-supplier/{supplier_id}', [SupplierController::class, 'delete_supplier']);
Route::get('/all-supplier', [SupplierController::class, 'all_supplier']);

Route::get('/active-supplier/{supplier_id}', [SupplierController::class, 'active_supplier']);
Route::get('/inactive-supplier/{supplier_id}', [SupplierController::class, 'inactive_supplier']);

Route::post('/save-supplier', [SupplierController::class, 'save_supplier']);
Route::post('/update-supplier/{supplier_id}', [SupplierController::class, 'update_supplier']);


//Book
Route::get('/add-book', [ProductController::class, 'add_book']);
Route::get('/edit-book/{book_id}', [ProductController::class, 'edit_book']);
Route::get('/delete-book/{book_id}', [ProductController::class, 'delete_book']);
Route::get('/all-book', [ProductController::class, 'all_book']);

Route::get('/active-book/{book_id}', [ProductController::class, 'active_book']);
Route::get('/inactive-book/{book_id}', [ProductController::class, 'inactive_book']);

Route::post('/save-book', [ProductController::class, 'save_book']);
Route::post('/update-book/{book_id}', [ProductController::class, 'update_book']);

// Cart
Route::post('/save-cart', [CartController::class, 'save_cart']);
Route::get('/show-cart', [CartController::class, 'show_cart']);
Route::get('/delete-to-cart/{rowId}', [CartController::class, 'delete_to_cart']);
Route::post('/update-cart-quantity', [CartController::class, 'update_cart_quantity']);

//Checkout
Route::get('/login-checkout', [CheckoutController::class, 'login_checkout']);
Route::get('/checkout', [CheckoutController::class, 'checkout']);

Route::post('/add-customer', [CheckoutController::class, 'add_customer']);
Route::post('/save-checkout-customer', [CheckoutController::class, 'save_checkout_customer']);
Route::post('/user-login', [CheckoutController::class, 'user_login']);
Route::post('/order-place', [CheckoutController::class, 'order_place']);


Route::get('/payment', [CheckoutController::class, 'payment']);
Route::get('/logout-checkout', [CheckoutController::class, 'logout_checkout']);

// laravel 8 trở lên phải sử dụng [Controller::class, 'method'] theo cú pháp array-based
// Gọi tên controller rồi đến hàm của controller đó