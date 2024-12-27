<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class AuthorController extends Controller
{
    // Kiểm tra đăng nhập không cho truy cập thẳng
    public function AuthLogin()
    {
        $user_id = Session::get('user_id');
        if ($user_id) {
            return Redirect::to('dashboard');
        } else {
            return Redirect::to('admin')->send();
        }
    }

    public function add_author()
    {
        $this->AuthLogin();
        return view('/admin.add_author');
    }
    // HIỂN THỊ TOÀN BỘ tác giả
    public function all_author()
    {
        $this->AuthLogin();
        $all_author = DB::table('author')->paginate(4);
        $manager_author = view('admin.all_author')->with('all_author', $all_author);
        return view('admin_layout')->with('admin.all_author', $manager_author);
    }
    // THÊM TÁC GIẢ
    public function save_author(Request $request)
    {
        $this->AuthLogin();
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
        $this->AuthLogin();
        $edit_author = DB::table('author')->where('author_id', $author_id)->get();
        $manager_author = view('admin.edit_author')->with('edit_author', $edit_author);
        return view('admin_layout')->with('admin.edit_author', $manager_author);
    }

    public function update_author(Request $request, $author_id)
    {
        $this->AuthLogin();
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
        $this->AuthLogin();
        $book = DB::table('book')->where('author_id', $author_id)->exists();
        if ($book) {
            Session::put('message', 'Không xóa được do có sách đang bày bán với tên tác giả này rồi');
            return Redirect::to('/all-author');
        } else {
            DB::table('author')->where('author_id', $author_id)->delete();
            Session::put('message', 'Xóa tác giả thành công');
            return Redirect::to('/all-author');
        }
    }

    //END FUNCTION ADMIN PAGE
    public function show_author_home($author_id)
    {
        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get();

        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(5) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();


        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $tacgia_name = DB::table('author')
            ->where('author.author_id', $author_id)
            ->limit(1)
            ->get();

        $author_by_id = DB::table('book')
            ->join('author', 'book.author_id', '=', 'author.author_id')
            ->where('book.author_id', $author_id)
            ->where('book.status', 'active')
            ->paginate(6);



        return view('pages.author.show_author')
            ->with('tacgia_book', $tacgia_book)
            ->with('author_by_id', $author_by_id)
            ->with('tacgia_name', $tacgia_name) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('publisher_list', $publisher_list) //nxb
            ->with('category', $category_book); // thể loại
    }
    // TÌM KIẾM TÁC GIẢ
    public function search_author(Request $request)
    {
        $keywords = $request->input('query');

        // Nếu không có từ khóa, trả về thông báo không tìm thấy
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        // Tìm kiếm theo author_name trong bảng author
        $search_author = DB::table('author')
            ->select('author_id', 'author_name') // Chọn các cột từ bảng author
            ->where('author_name', 'like', '%' . $keywords . '%')
            ->paginate(5);

        // Kiểm tra nếu không có kết quả nào tìm thấy
        if ($search_author->isEmpty()) {
            return view('admin.all_author')
                ->with('all_author', collect()) // gửi danh sách rỗng nếu không tìm thấy kết quả nào
                ->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        return view('admin.all_author')
            ->with('all_author', $search_author);
    }
}
