<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function add_book()
    {
        $category_book=DB::table('category')->orderBy('category_id','desc')->get();
        $author_book=DB::table('author')->orderBy('author_id','desc')->get();
        $supplier_book=DB::table('supplier')->orderBy('supplier_id','desc')->get();
        return view('/admin.add_book')->with('category_book',$category_book)->with('author_book',$author_book)->with('supplier_book',$supplier_book);
    }
    // HIỂN THỊ TOÀN BỘ DANH MỤC
    public function all_book()
    {
        $all_book = DB::table('book')->get();
        $manager_book = view('admin.all_book')->with('all_book', $all_book);
        return view('admin_layout')->with('admin.all_book', $manager_book);
    }

    // THÊM DANH MỤC
    public function save_book(Request $request)
    {
        // Thêm validation
        $request->validate([
            'book_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
        ], [
            'book_name.regex' => 'Tên danh mục chỉ được phép chứa chữ cái và khoảng trắng.',
        ]);

        $data = array();
        $data['book_name'] = $request->book_name;
        $data['status'] = $request->book_status === 'Kích hoạt' ? 'active' : 'inactive';
        
        // Kiểm tra tồn tại của tên danh mục kể cả viết hoa hay thường là cũng giống nhau về tên danh mục
        $existingbook = DB::table('book')
            ->whereRaw('LOWER(book_name) = ?', [strtolower($data['book_name'])])
            ->first();

        if ($existingbook) {
            // Nếu danh mục đã tồn tại
            Session::put('message', 'Thêm danh mục không thành công - Tên danh mục đã tồn tại.');
            return Redirect::to('/add-book');
        } else {
            // Thêm danh mục mới thành công
            DB::table('book')->insert($data);
            Session::put('message', 'Thêm danh mục thành công');
            return Redirect::to('/add-book');
        }
    }

    // CHỈNH SỬA TRẠNG THÁI
    public function active_book($book_id)
    {
        DB::table('book')->where('book_id', $book_id)->update(['status' => 'inactive']);
        Session::put('message', 'Đã đổi trạng thái thành không kích hoạt');
        return Redirect::to('all-book');
    }

    public function inactive_book($book_id)
    {
        DB::table('book')->where('book_id', $book_id)->update(['status' => 'active']);
        Session::put('message', 'Đã đổi trạng thái thành kích hoạt');
        return Redirect::to('all-book');
    }

    // SỬA DANH MỤC
    public function edit_book($book_id)
    {
        $edit_book = DB::table('book')->where('book_id', $book_id)->get();
        $manager_book = view('admin.edit_book')->with('edit_book', $edit_book);
        return view('admin_layout')->with('admin.edit_book', $manager_book);
    }

    public function update_book(Request $request, $book_id)
    {
        // Thêm validation
        $request->validate([
            'book_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
        ], [
            'book_name.regex' => 'Tên danh mục chỉ được phép chứa chữ cái và khoảng trắng.',
        ]);
    
        $data = array();
        $data['book_name'] = $request->book_name;
        
        // Kiểm tra tồn tại của tên danh mục
        $existingbook = DB::table('book')
            ->whereRaw('LOWER(book_name) = ?', [strtolower($data['book_name'])])
            ->first();

        if ($existingbook) {
            // Nếu danh mục đã tồn tại
            Session::put('message', 'Cập nhật danh mục không thành công - Tên danh mục đã tồn tại.');
            return Redirect::to('/all-book');
        } else {
            // Cập nhật danh mục thành công
            DB::table('book')->where('book_id', $book_id)->update($data);
            Session::put('message', 'Cập nhật danh mục thành công');
            return Redirect::to('/all-book');
        }
    }

    // XÓA DANH MỤC
    public function delete_book($book_id)
    {
        DB::table('book')->where('book_id', $book_id)->delete();
        Session::put('message', 'Xóa danh mục thành công');
        return Redirect::to('/all-book');
    }
}