<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class AuthorController extends Controller
{
    public function add_author()
    {
        return view('/admin.add_author');
    }
    // HIỂN THỊ TOÀN BỘ tác giả
    public function all_author()
    {
        $all_author = DB::table('author')->get();
        $manager_author = view('admin.all_author')->with('all_author', $all_author);
        return view('admin_layout')->with('admin.all_author', $manager_author);
    }
    // THÊM TÁC GIẢ
    public function save_author(Request $request)
    {
        // Thêm validation
        $request->validate([
            'author_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
        ], [
            'author_name.regex' => 'Tên tác giả chỉ được phép chứa chữ cái và khoảng trắng.',
        ]);

        $data = array();
        $data['author_name'] = $request->author_name;

        // Kiểm tra tồn tại của tên tác giả
        $existingauthor = DB::table('author')
            ->whereRaw('LOWER(author_name) = ?', [strtolower($data['author_name'])])
            ->first();

        if ($existingauthor) {
            // Nếu tác giả đã tồn tại
            Session::put('message', 'Thêm tác giả không thành công - Tên tác giả đã tồn tại.');
            return Redirect::to('/add-author');
        } else {
            // Thêm tác giả mới thành công
            DB::table('author')->insert($data);
            Session::put('message', 'Thêm tác giả thành công');
            return Redirect::to('/add-author');
        }
    }
    //SỬA TÁC GIẢ
    public function edit_author($author_id)
    {
        $edit_author = DB::table('author')->where('author_id', $author_id)->get();
        $manager_author = view('admin.edit_author')->with('edit_author', $edit_author);
        return view('admin_layout')->with('admin.edit_author', $manager_author);
    }

    public function update_author(Request $request, $author_id)
    {
        // Thêm validation
        $request->validate([
            'author_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
        ], [
            'author_name.regex' => 'Tên tác giả chỉ được phép chứa chữ cái và khoảng trắng.',
        ]);
    
        $data = array();
        $data['author_name'] = $request->author_name;
    
        // Kiểm tra tồn tại của tên tác giả
        $existingauthor = DB::table('author')
            ->whereRaw('LOWER(author_name) = ?', [strtolower($data['author_name'])])
            ->first();
    
        if ($existingauthor) {
            // Nếu tác giả đã tồn tại
            Session::put('message', 'Cập nhật tác giả không thành công - Tên tác giả đã tồn tại.');
            return Redirect::to('/all-author');
        } else {
            // Cập nhật tác giả thành công
            DB::table('author')->where('author_id', $author_id)->update($data);
            Session::put('message', 'Cập nhật tác giả thành công');
            return Redirect::to('/all-author');
        }
    }
        // XÓA tác giả
    public function delete_author($author_id)
    {
        DB::table('author')->where('author_id', $author_id)->delete();
        Session::put('message', 'Xóa tác giả thành công');
        return Redirect::to('/all-author');
    }
}
