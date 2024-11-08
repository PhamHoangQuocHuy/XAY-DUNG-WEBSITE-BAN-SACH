<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
            ->paginate(5);

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

        $all_user = DB::table('user')->paginate(5);

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

    // QUÊN MẬT KHẨU
    public function showForgotPasswordForm() // Hiển thị form để điền email
    {
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

        return view('pages.checkout.show_forgot_password_form')
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('limitWordsFunc', $limitWordsFunc);
    }
    public function sendResetLinkEmail(Request $request) // gửi qua mail đã điền link cập nhật 
    {
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get();

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4)
            ->get();

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

        $request->validate(['email' => 'required|email']);

        $user = DB::table('user')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.'])
                ->with('category', $category_book)
                ->with('publisher_list', $publisher_list)
                ->with('tacgia_book', $tacgia_book)
                ->with('all_book', $all_book)
                ->with('limitWordsFunc', $limitWordsFunc);
        }

        $token = Str::random(20);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('pages.checkout.link_reset_password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Liên kết đặt lại mật khẩu của bạn');
        });

        return back()->with('message', 'Liên kết đặt lại mật khẩu đã được gửi tới email của bạn.');
    }
    public function showResetForm($token) // Hiển thị form điền lại mật khẩu mới 
    {
        $reset = DB::table('password_resets')->where('token', $token)->first();
        if (!$reset) {
            return redirect('/quen-mat-khau')->withErrors(['token' => 'Token không hợp lệ.']);
        }

        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get();

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4)
            ->get();

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

        return view('pages.checkout.reset_password', [
            'token' => $token,
            'email' => $reset->email,
            'category' => $category_book,
            'publisher_list' => $publisher_list,
            'tacgia_book' => $tacgia_book,
            'all_book' => $all_book,
            'limitWordsFunc' => $limitWordsFunc,
        ]);
    }
    public function resetPassword(Request $request) // Xử lý cập nhật mật khẩu mới
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:5|confirmed',
            'token' => 'required'
        ]);

        // Kiểm tra token và email
        $reset = DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->token
        ])->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ.']);
        }

        // Đặt lại mật khẩu
        DB::table('user')->where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Xóa token sau khi đặt lại mật khẩu thành công
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/login-checkout')->with('message', 'Mật khẩu của bạn đã được đặt lại thành công.');
    }

    // THAY ĐỔI THÔNG TIN TÀI KHOẢN
    public function showUserInfo($user_id)
    {
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

        // Lấy thông tin người dùng từ cơ sở dữ liệu 
        $user = DB::table('user')->where('user_id', $user_id)->first();
        if (!$user) {
            return redirect('/')->withErrors(['user' => 'Người dùng không tồn tại.']);
        }

        return view('pages.user.user_info', [
            'user' => $user,
            'category' => $category_book,
            'publisher_list' => $publisher_list,
            'tacgia_book' => $tacgia_book,
            'all_book' => $all_book,
            'limitWordsFunc' => $limitWordsFunc,
        ]);
    }
    // Cập nhật thông tin tài khoản người dùng 
    public function updateUserInfo(Request $request, $user_id)
    {
        $request->validate([
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'fullname' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:5|confirmed'
        ]);

        // Lấy thông tin người dùng hiện tại từ cơ sở dữ liệu
        $user = DB::table('user')->where('user_id', $user_id)->first();

        // Chuẩn bị dữ liệu để cập nhật
        $data = [];

        if ($request->username && $request->username != $user->username) {
            $data['username'] = $request->username;
        }
        if ($request->email && $request->email != $user->email) {
            $data['email'] = $request->email;
        }
        if ($request->fullname && $request->fullname != $user->fullname) {
            $data['fullname'] = $request->fullname;
        }
        if ($request->address && $request->address != $user->address) {
            $data['address'] = $request->address;
        }
        if ($request->phone && $request->phone != $user->phone) {
            $data['phone'] = $request->phone;
        }
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Cập nhật thông tin người dùng
        if (!empty($data)) {
            DB::table('user')->where('user_id', $user_id)->update($data);
        }

        return redirect('/user-info/' . $user_id)->with('message', 'Thông tin tài khoản đã được cập nhật.');
    }
    // TÌM KIẾM ĐƠN HÀNG
    public function search_order(Request $request)
    {
        $keywords = $request->input('query');

        // Nếu không có từ khóa, trả về thông báo không tìm thấy
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        // Tìm kiếm theo các tiêu chí tên khách hàng (username) và mã đơn hàng (code_order)
        $search_order = DB::table('orders')
            ->join('user', 'user.user_id', '=', 'orders.user_id')
            ->select('orders.*', 'user.username') // Chọn các cột từ bảng orders và user
            ->where('user.username', 'like', '%' . $keywords . '%')
            ->orWhere('orders.code_order', 'like', '%' . $keywords . '%')
            ->get();

        // Kiểm tra nếu không có kết quả nào tìm thấy
        if ($search_order->isEmpty()) {
            return view('admin.manage_order')
                ->with('all_order', collect()) // gửi danh sách rỗng nếu không tìm thấy kết quả nào
                ->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        return view('admin.manage_order')
            ->with('all_order', $search_order);
    }
    // TÌM KIẾM TÀI KHOẢN
    public function search_user(Request $request)
    {
        $keywords = $request->input('query');

        // Nếu không có từ khóa, trả về thông báo không tìm thấy
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        // Tìm kiếm theo các tiêu chí trong bảng user
        $search_user = DB::table('user')
            ->select('user_id', 'username', 'fullname', 'address', 'email', 'phone', 'role', 'status', 'register_date') // Chọn các cột từ bảng user
            ->where('username', 'like', '%' . $keywords . '%')
            ->orWhere('fullname', 'like', '%' . $keywords . '%')
            ->orWhere('address', 'like', '%' . $keywords . '%')
            ->orWhere('email', 'like', '%' . $keywords . '%')
            ->orWhere('phone', 'like', '%' . $keywords . '%')
            ->get();

        // Kiểm tra nếu không có kết quả nào tìm thấy
        if ($search_user->isEmpty()) {
            return view('admin.manage_user')
                ->with('all_user', collect()) // gửi danh sách rỗng nếu không tìm thấy kết quả nào
                ->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        return view('admin.manage_user')
            ->with('all_user', $search_user);
    }
}
