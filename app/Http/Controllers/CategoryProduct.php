<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class CategoryProduct extends Controller
{
    public function add_category()
    {
        return view('/admin.add_category');
    }
    
    // HIỂN THỊ TOÀN BỘ DANH MỤC
    public function all_category()
    {
        $all_category = DB::table('category')->get();
        $manager_category = view('admin.all_category')->with('all_category', $all_category);
        return view('admin_layout')->with('admin.all_category', $manager_category);
    }

    // THÊM DANH MỤC
    public function save_category(Request $request)
    {
        // Thêm validation
        $request->validate([
            'category_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
        ], [
            'category_name.regex' => 'Tên danh mục chỉ được phép chứa chữ cái và khoảng trắng.',
        ]);

        $data = array();
        $data['category_name'] = $request->category_name;
        $data['status'] = $request->category_status === 'Kích hoạt' ? 'active' : 'inactive';
        
        // Kiểm tra tồn tại của tên danh mục kể cả viết hoa hay thường là cũng giống nhau về tên danh mục
        $existingCategory = DB::table('category')
            ->whereRaw('LOWER(category_name) = ?', [strtolower($data['category_name'])])
            ->first();

        if ($existingCategory) {
            // Nếu danh mục đã tồn tại
            Session::put('message', 'Thêm danh mục không thành công - Tên danh mục đã tồn tại.');
            return Redirect::to('/add-category');
        } else {
            // Thêm danh mục mới thành công
            DB::table('category')->insert($data);
            Session::put('message', 'Thêm danh mục thành công');
            return Redirect::to('/add-category');
        }
    }

    // CHỈNH SỬA TRẠNG THÁI
    public function active_category($cate_id)
    {
        DB::table('category')->where('category_id', $cate_id)->update(['status' => 'inactive']);
        Session::put('message', 'Đã đổi trạng thái thành không kích hoạt');
        return Redirect::to('all-category');
    }

    public function inactive_category($cate_id)
    {
        DB::table('category')->where('category_id', $cate_id)->update(['status' => 'active']);
        Session::put('message', 'Đã đổi trạng thái thành kích hoạt');
        return Redirect::to('all-category');
    }

    // SỬA DANH MỤC
    public function edit_category($cate_id)
    {
        $edit_category = DB::table('category')->where('category_id', $cate_id)->get();
        $manager_category = view('admin.edit_category')->with('edit_category', $edit_category);
        return view('admin_layout')->with('admin.edit_category', $manager_category);
    }

    public function update_category(Request $request, $cate_id)
    {
        // Thêm validation
        $request->validate([
            'category_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
        ], [
            'category_name.regex' => 'Tên danh mục chỉ được phép chứa chữ cái và khoảng trắng.',
        ]);
    
        $data = array();
        $data['category_name'] = $request->category_name;
        
        // Kiểm tra tồn tại của tên danh mục
        $existingCategory = DB::table('category')
            ->whereRaw('LOWER(category_name) = ?', [strtolower($data['category_name'])])
            ->first();

        if ($existingCategory) {
            // Nếu danh mục đã tồn tại
            Session::put('message', 'Cập nhật danh mục không thành công - Tên danh mục đã tồn tại.');
            return Redirect::to('/all-category');
        } else {
            // Cập nhật danh mục thành công
            DB::table('category')->where('category_id', $cate_id)->update($data);
            Session::put('message', 'Cập nhật danh mục thành công');
            return Redirect::to('/all-category');
        }
    }

    // XÓA DANH MỤC
    public function delete_category($cate_id)
    {
        DB::table('category')->where('category_id', $cate_id)->delete();
        Session::put('message', 'Xóa danh mục thành công');
        return Redirect::to('/all-category');
    }
}
