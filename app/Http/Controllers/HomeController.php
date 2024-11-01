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


        // lấy sản phẩm ở trang chủ
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book.book_id', 'asc')
            ->limit(6)
            ->get();

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
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
            ->with('publisher_list', $publisher_list)
            ->with('limitWordsFunc', $limitWordsFunc);

        //đường dẫn laravel sử dụng dấu . thay vì dùng dấu /
    }

        public function details_product($book_id)
    {
        // code phải có ở các danh mục: thể loại, tác giả, nxb, sách. Chổ nào thiếu thì điền vô
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        // CHI TIẾT SẢN PHẨM
        $details_product = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('book.book_id', $book_id)
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });
        // SẢN PHẨM LIÊN QUAN   
        foreach ($details_product as $key => $value) {
            $category_id = $value->category_id;
        }
        $ralated_product = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('category.category_id', $category_id)
            ->whereNotIn('book.book_id',[$book_id])
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });


        return view('pages.sanpham.show_details')
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('product_details', $details_product) // chi tiết sản phẩm
            ->with('relate', $ralated_product) // sản phẩm liên quan
            ->with('limitWordsFunc', $limitWordsFunc);
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
