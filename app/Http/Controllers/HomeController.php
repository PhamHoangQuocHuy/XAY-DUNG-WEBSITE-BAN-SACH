<?php
// tạo controller bằng câu lệnh composer tại htdocs/xaydungwebsitebansach với câu lệnh: 
// php artisan make:controller HomeController 
// HomeController: là tên controller muốn tạo
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function index()
    {
        $category_book = DB::table('category')
        ->where('status', 'active')
        ->orderBy('category_id', 'asc')
        ->get();
        
        $tacgia_book = DB::table('author')
        ->orderBy('author_id', 'asc')
        ->get();

        $all_book = DB::table('book')
        ->where('status', 'active')
        ->orderBy('book.book_id', 'asc')
        ->limit(6)
        ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        return view('pages.home')
            ->with('category', $category_book)
            ->with('tacgia_book', $tacgia_book)
            ->with('all_book', $all_book)
            ->with('limitWordsFunc', $limitWordsFunc);

        //đường dẫn laravel sử dụng dấu . thay vì dùng dấu /
    }

    // GIỚI HẠN NỘI DUNG
    function limit_words_with_ellipsis($string, $word_limit)
    {
        $words = explode(' ', $string);
        if (count($words) > $word_limit) {
            return implode(' ', array_splice($words, 0, $word_limit)) . '...';
        }
        return $string;
    }
}
