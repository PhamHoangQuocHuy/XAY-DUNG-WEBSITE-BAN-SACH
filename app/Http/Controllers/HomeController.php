<?php
// tạo controller bằng câu lệnh composer tại htdocs/xaydungwebsitebansach với câu lệnh: 
// php artisan make:controller HomeController 
// HomeController: là tên controller muốn tạo
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return view('pages.home'); //đường dẫn laravel sử dụng dấu . thay vì dùng dấu /
    }
}
