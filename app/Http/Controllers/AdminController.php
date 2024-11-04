<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\Return_;

session_start();

class AdminController extends Controller
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

    public function index()
    {
        return view('admin_login');
    }
    public function show_dashboard()
    {
        $this->AuthLogin();
        return view('admin.dashboard');
    }
    public function dashboard(Request $request)
    {
        $admin_email = $request->admin_email;
        $admin_password = $request->admin_password;
        $result = DB::table('user')
            ->where('email', $admin_email)
            ->where('role', 'admin')
            ->first();

        // Kiểm tra nếu người dùng tồn tại và so sánh mật khẩu
        if ($result) {
            if (Hash::check($admin_password, $result->password)) {
                // Nếu mật khẩu đúng
                Session::put('username', $result->username);
                Session::put('user_id', $result->user_id);
                return Redirect::to('/dashboard');
            } else {
                // Nếu mật khẩu sai
                Session::put('message', 'Tài khoản hoặc mật khẩu không đúng');
                return Redirect::to('/admin');
            }
        } else {
            // Nếu không tìm thấy người dùng
            Session::put('message', 'Tài khoản hoặc mật khẩu không đúng');
            return Redirect::to('/admin');
        }
    }
    public function logout()
    {
        $this->AuthLogin();
        Session::put('username', null);
        Session::put('user_id', null);
        Session::flush();
        return Redirect::to('/admin');
    }

    // ORDER
    public function manage_order()
    {

        $all_order = DB::table('orders')
            ->join('user', 'user.user_id', '=', 'orders.order_id')
            ->select('orders.*', 'user.username')
            ->orderBy('orders.order_id', 'asc')
            ->get();

        $manager_order = view('admin.manage_order')
            ->with('all_order', $all_order);

        return view('admin_layout')
            ->with('admin.manage_order', $manager_order);
    }
    public function view_order($order_id)
    {

        $order_by_id = DB::table('orders')
            ->join('user', 'user.user_id', '=', 'orders.user_id')
            ->join('shipping', 'shipping.shipping_id', '=', 'orders.shipping_id')
            ->join('order_details', 'order_details.order_id', '=', 'orders.order_id')
            ->select('orders.*', 'shipping.*', 'order_details.*', 'user.*')
            ->where('orders.order_id', $order_id)
            ->first();

        $manager_order_by_id = view('admin.view_order')
            ->with('order_by_id', $order_by_id);
        return view('admin_layout')->with('admin.view_order', $manager_order_by_id);
    }
    public function delete_order($order_id)
    {

        // Xóa các chi tiết đơn hàng liên quan trước
        DB::table('order_details')->where('order_id', $order_id)->delete();

        // Xóa đơn hàng
        DB::table('orders')->where('order_id', $order_id)->delete();

        return Redirect::to('order-list')->with('success', 'Đơn hàng đã được xóa thành công');
    }

    // ACCOUNT (USER)
    public function manage_user()
    {

        $all_user = DB::table('user')->get();

        $manager_user = view('admin.manage_user')
            ->with('all_user', $all_user);

        return view('admin_layout')
            ->with('admin.manage_user', $manager_user);
    }
    // CHỈNH SỬA TRẠNG THÁI
    public function active_user($user_id)
    {

        $user = DB::table('user')->where('user_id', $user_id)->first();

        if ($user && $user->role != 'admin') {
            DB::table('user')->where('user_id', $user_id)->update(['status' => 'inactive']);
            Session::put('message', 'Đã đổi trạng thái thành không kích hoạt');
        } else {
            Session::put('message', 'Không thể thay đổi trạng thái của admin');
        }

        return Redirect::to('manage-user');
    }

    public function inactive_user($user_id)
    {

        $user = DB::table('user')->where('user_id', $user_id)->first();

        if ($user && $user->role != 'admin') {
            DB::table('user')->where('user_id', $user_id)->update(['status' => 'active']);
            Session::put('message', 'Đã đổi trạng thái thành kích hoạt');
        } else {
            Session::put('message', 'Không thể thay đổi trạng thái của admin');
        }

        return Redirect::to('manage-user');
    }

    public function delete_user($user_id)
    {

        $user = DB::table('user')->where('user_id', $user_id)->first();

        if ($user && $user->role != 'admin') {
            DB::table('user')->where('user_id', $user_id)->delete();
            Session::put('success', 'Người dùng đã được xóa thành công');
        } else {
            Session::put('message', 'Không thể xóa tài khoản admin');
        }

        return Redirect::to('manage-user');
    }
    // KHÓA NGƯỜI DÙNG
    public function lock_login(Request $request)
    {
        $email = $request->email_account;
        $password = $request->password_account;

        // Kiểm tra xem người dùng có tồn tại và lấy thông tin người dùng
        $user = DB::table('user')->where('email', $email)->first();

        if ($user) {
            // Kiểm tra xem tài khoản có phải là admin không
            if ($user->role != 'admin') {
                // Kiểm tra xem tài khoản có bị khóa không
                if ($user->status == 'inactive') {
                    return Redirect::back()->with('error', 'Tài khoản của bạn đã bị tạm khóa. Hãy liên hệ với admin để mở khóa.');
                }
            }

            // Kiểm tra mật khẩu
            if (Hash::check($password, $user->password)) {
                Session::put('user_id', $user->user_id);
                Session::put('username', $user->username);
                return Redirect::to('/home');
            } else {
                return Redirect::back()->with('error', 'Mật khẩu không đúng.');
            }
        } else {
            return Redirect::back()->with('error', 'Email không tồn tại.');
        }
    }
}
