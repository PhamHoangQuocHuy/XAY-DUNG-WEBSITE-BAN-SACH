<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class SupplierController extends Controller
{
    public function add_supplier()
    {
        return view('/admin.add_supplier');
    }

    // HIỂN THỊ TOÀN BỘ NHÀ CUNG CẤP
    public function all_supplier()
    {
        $all_supplier = DB::table('supplier')->get();
        $manager_supplier = view('admin.all_supplier')->with('all_supplier', $all_supplier);
        return view('admin_layout')->with('admin.all_supplier', $manager_supplier);
    }

    // THÊM NHÀ CUNG CẤP
    public function save_supplier(Request $request)
    {
        // Thêm validation cho số điện thoại và email
        $request->validate([
            'supplier_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
            'supplier_phone' => 'required|digits:10|numeric|unique:supplier,supplier_phone', // Chỉ cho phép số và đúng 10 chữ số
            'supplier_email' => 'required|email|unique:supplier,supplier_email', // Phải đúng định dạng email và không trùng lặp
        ], [
            'supplier_name.regex' => 'Tên nhà cung cấp chỉ được phép chứa chữ cái và khoảng trắng.',
            'supplier_phone.digits' => 'Số điện thoại phải gồm đúng 10 chữ số.',
            'supplier_phone.numeric' => 'Số điện thoại chỉ được chứa số.',
            'supplier_phone.unique' => 'Số điện thoại đã tồn tại.',
            'supplier_email.email' => 'Định dạng email không hợp lệ.',
            'supplier_email.unique' => 'Email đã tồn tại.',
        ]);

        $data = [
            'supplier_name' => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'supplier_email' => $request->supplier_email,
            'supplier_address' => $request->supplier_address,
        ];

        DB::table('supplier')->insert($data);
        Session::put('message', 'Thêm nhà cung cấp thành công');
        return Redirect::to('/add-supplier');
    }
    // CHỈNH SỬA TRẠNG THÁI
    public function active_supplier($sup_id)
    {
        DB::table('supplier')->where('supplier_id', $sup_id)->update(['status' => 'inactive']);
        Session::put('message', 'Đã đổi trạng thái thành không kích hoạt');
        return Redirect::to('all-supplier');
    }

    public function inactive_supplier($sup_id)
    {
        DB::table('supplier')->where('supplier_id', $sup_id)->update(['status' => 'active']);
        Session::put('message', 'Đã đổi trạng thái thành kích hoạt');
        return Redirect::to('all-supplier');
    }

    // SỬA NHÀ CUNG CẤP
    public function edit_supplier($sup_id)
    {
        $edit_supplier = DB::table('supplier')->where('supplier_id', $sup_id)->get();
        $manager_supplier = view('admin.edit_supplier')->with('edit_supplier', $edit_supplier);
        return view('admin_layout')->with('admin.edit_supplier', $manager_supplier);
    }

    public function update_supplier(Request $request, $sup_id)
    {
        // Thêm validation cho số điện thoại và email
        $request->validate([
            'supplier_name' => 'required|regex:/^[\p{L} ]+$/u',
            'supplier_phone' => 'required|digits:10|numeric|unique:supplier,supplier_phone,' . $sup_id . ',supplier_id',
            'supplier_email' => 'required|email|unique:supplier,supplier_email,' . $sup_id . ',supplier_id',
        ], [
            'supplier_name.regex' => 'Tên nhà cung cấp chỉ được phép chứa chữ cái và khoảng trắng.',
            'supplier_phone.digits' => 'Số điện thoại phải gồm đúng 10 chữ số.',
            'supplier_phone.numeric' => 'Số điện thoại chỉ được chứa số.',
            'supplier_phone.unique' => 'Số điện thoại đã tồn tại.',
            'supplier_email.email' => 'Định dạng email không hợp lệ.',
            'supplier_email.unique' => 'Email đã tồn tại.',
        ]);
        $data = array();
        $data = [
            'supplier_name' => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'supplier_email' => $request->supplier_email,
            'supplier_address' => $request->supplier_address,
        ];

        DB::table('supplier')->where('supplier_id', $sup_id)->update($data);
        Session::put('message', 'Cập nhật nhà cung cấp thành công');
        return Redirect::to('/all-supplier');
    }

    // XÓA NHÀ CUNG CẤP
    public function delete_supplier($sup_id)
    {
        DB::table('supplier')->where('supplier_id', $sup_id)->delete();
        Session::put('message', 'Xóa nhà cung cấp thành công');
        return Redirect::to('/all-supplier');
    }
}
