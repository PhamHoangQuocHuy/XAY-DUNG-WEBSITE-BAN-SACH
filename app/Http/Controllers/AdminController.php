<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\Return_;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Message;
use KitLoong\MigrationsGenerator\Migration\Blueprint\DBStatementBlueprint;

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
    // ĐĂNG NHẬP ADMIN
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
            ->join('user', 'user.user_id', '=', 'orders.user_id')
            ->select('orders.*', 'user.username')
            ->orderBy('orders.order_id', 'asc')
            ->paginate(10);

        $manager_order = view('admin.manage_order')
            ->with('all_order', $all_order);

        return view('admin_layout')
            ->with('admin.manage_order', $manager_order);
    }
    public function view_order($order_id)
    {
        // Lấy thông tin đơn hàng, người dùng và vận chuyển
        $order_info = DB::table('orders')
            ->join('user', 'user.user_id', '=', 'orders.user_id')
            ->join('shipping', 'shipping.shipping_id', '=', 'orders.shipping_id')
            ->join('payment', 'payment.payment_id', '=', 'orders.payment_id')
            ->select('orders.*', 'shipping.*', 'user.*', 'payment.*')
            ->where('orders.order_id', $order_id)
            ->first();

        // Lấy tất cả các chi tiết đơn hàng
        $order_details = DB::table('order_details')
            ->join('book', 'order_details.book_id', '=', 'book.book_id')
            ->select('order_details.*')
            ->where('order_details.order_id', $order_id)
            ->get();

        // Truyền dữ liệu vào view
        return view('admin.view_order')
            ->with('order_info', $order_info)
            ->with('order_details', $order_details);
    }
    public function delete_order($order_id)
    {

        // Xóa các chi tiết đơn hàng liên quan trước
        DB::table('order_details')->where('order_id', $order_id)->delete();

        // Xóa đơn hàng
        DB::table('orders')->where('order_id', $order_id)->delete();

        return Redirect::to('/manage-order')->with('success', 'Đơn hàng đã được xóa thành công');
    }
    // CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
    public function update_order_status(Request $request, $order_id)
    {
        // Tìm đơn hàng theo id
        $order = DB::table('orders')->where('order_id', $order_id)->first();
        // Kiểm tra trạng thái hiện tại của đơn hàng 
        if ($order->order_status != 'Processing') {
            return redirect()->back()->with('error', 'Chỉ có thể thay đổi trạng thái từ Đơn mới.');
        }
        // Cập nhật trạng thái đơn hàng
        DB::table('orders')->where('order_id', $order_id)->update(['order_status' => $request->order_status]);

        // Lấy thông tin người đặt hàng 
        $shipping_info = DB::table('shipping')
            ->where('shipping_id', $order->shipping_id)
            ->first();
        // Lấy thông tin coupon nếu có 
        $coupon_info = null;
        if ($order->coupon_id) {
            $coupon_info = DB::table('coupons')
                ->where('coupon_id', $order->coupon_id)
                ->first();
        }
        $email_data = [
            'name' => $shipping_info->shipping_name,
            'address' => $shipping_info->shipping_address,
            'phone' => $shipping_info->shipping_phone,
            'email' => $shipping_info->shipping_email,
            'notes' => $shipping_info->shipping_notes,
            'order_code' => $order->code_order,
            'order_total' => $order->order_total,
            'coupons' => $coupon_info,
            'order_details' => DB::table('order_details')->where('order_id', $order_id)->get(),
            'payment_method' => DB::table('payment')
                ->where('payment_id', $order->payment_id)
                ->value('payment_method'),
            'shipping_info' => $shipping_info
        ];
        $subject = "Thư thông báo về trạng thái đơn hàng với mã đơn hàng là: " . $order->code_order;
        // Chọn template email dựa trên trạng thái đơn hàng 
        if ($request->order_status == 'Delivered') {
            $template = 'pages.emails.order_delivered_notification';
        } elseif ($request->order_status == 'Cancelled') {
            $template = 'pages.emails.order_canceled_notification';
        } else {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }
        // Gửi email thông báo 
        Mail::send($template, $email_data, function ($message) use ($email_data, $subject) {
            $message->to($email_data['email'], $email_data['name'])->subject($subject);
        });
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái đơn hàng và gửi email thông báo.');
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

        if (is_null($user->password)) {
            return back()->withErrors(['email' => 'Tài khoản đã đăng nhập bằng Google không thể đặt lại mật khẩu.'])
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
        try {
            Mail::send('pages.checkout.link_reset_password', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Liên kết đặt lại mật khẩu của bạn');
            });
            return back()->with('message', 'Liên kết đặt lại mật khẩu đã được gửi tới email của bạn.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage()]);
        }
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

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
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
        // Kiểm tra xem số điện thoại đã tồn tại hay chưa 
        $existingPhone = DB::table('user')
            ->where('phone', $request->phone)
            ->where('user_id', '!=', $user_id)
            ->first();
        if ($existingPhone) {
            return redirect()->back()->with('message', 'Số điện thoại đã tồn tại.');
        }
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
            ->paginate(5);

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
            ->paginate(5);

        // Kiểm tra nếu không có kết quả nào tìm thấy
        if ($search_user->isEmpty()) {
            return view('admin.manage_user')
                ->with('all_user', collect()) // gửi danh sách rỗng nếu không tìm thấy kết quả nào
                ->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        return view('admin.manage_user')
            ->with('all_user', $search_user);
    }

    // XEM LỊCH SỬ ĐƠN HÀNG ĐÃ MUA
    public function user_orders_history($user_id)
    {
        if (!Session::has('user_id')) {
            return redirect('/login-checkout')->with('error', 'Bạn cần đăng nhập để xem lịch sử đơn hàng.');
        }
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
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

        // Lấy tất cả các đơn hàng của user_id 
        $orders = DB::table('orders')
            ->where('user_id', $user_id)
            ->get();
        // Truyền dữ liệu đơn hàng sang view 
        return view('pages.user.history', [
            'orders' => $orders,
            'category' => $category_book,
            'publisher_list' => $publisher_list,
            'tacgia_book' => $tacgia_book,
            'all_book' => $all_book,
            'limitWordsFunc' => $limitWordsFunc,
        ]);
    }
    public function history_order_details($order_id)
    {
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
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
        // Lấy thông tin đơn hàng 
        $order = DB::table('orders')
            ->where('order_id', $order_id)
            ->first();
        // Lấy thông tin chi tiết đơn hàng 
        $order_details = DB::table('order_details')
            ->where('order_id', $order_id)
            ->get();
        // Lấy thông tin vận chuyển 
        $shipping_info = DB::table('shipping')
            ->where('shipping_id', $order->shipping_id)
            ->first();
        // Lấy thông tin thanh toán
        $payment_info = DB::table('payment')
            ->where('payment_id', $order->payment_id)
            ->first();
        return view('pages.user.history_order_details', [
            'order' => $order,
            'order_details' => $order_details,
            'shipping_info' => $shipping_info,
            'payment_info' => $payment_info,
            'category' => $category_book,
            'publisher_list' => $publisher_list,
            'tacgia_book' => $tacgia_book,
            'all_book' => $all_book,
            'limitWordsFunc' => $limitWordsFunc,
        ]);
    }
    // HỦY ĐƠN HÀNG TỪ PHÍA KHÁCH HÀNG
    public function cancel_order(Request $request, $order_id)
    {
        // Tìm đơn hàng theo id 
        $order = DB::table('orders')
            ->where('order_id', $order_id)
            ->first();
        // Kiểm tra nếu đơn hàng không phải là 'Đơn mới' 
        if ($order->order_status != 'Processing') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng với trạng thái Đơn mới.');
        }
        // Cập nhật trạng thái đơn hàng thành 'Cancelled' 
        DB::table('orders')
            ->where('order_id', $order_id)
            ->update(['order_status' => 'Cancelled']);
        return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công.');
    }
    // COUPONS
    public function show_coupons()
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
        $coupons = DB::table('coupons')
            ->where('coupon_status', 'active')
            ->whereDate('expiration_date', '>=', \Carbon\Carbon::now())
            ->get();
        return view('pages.coupons.show_coupons')
            ->with('coupons', $coupons)
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('limitWordsFunc', $limitWordsFunc);
    }
    public function add_coupon()
    {
        $this->AuthLogin();
        return view('/admin.add_coupon');
    }
    public function save_coupon(Request $request)
    {
        $this->AuthLogin();
        $data = [
            'coupon_code' => $request->coupon_code,
            'discount' => $request->discount,
            'expiration_date' => $request->expiration_date,
            'coupon_status' => $request->coupon_status,
        ];

        DB::table('coupons')->insert($data);
        Session::put('message', 'Thêm coupon thành công');
        return Redirect::to('/add-coupon');
    }
    public function all_coupon()
    {
        $this->AuthLogin();
        $all_coupon = DB::table('coupons')->select('coupons.*')->paginate(6);
        $manager_coupon = view('admin.all_coupon')->with('all_coupon', $all_coupon);
        return view('admin_layout')->with('admin.all_coupon', $manager_coupon);
    }
    public function search_coupon(Request $request)
    {
        $keywords = $request->input('query');

        // Nếu không có từ khóa, trả về thông báo không tìm thấy
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        // Tìm kiếm theo các trường trong bảng coupons
        $search_coupon = DB::table('coupons')
            ->select('coupon_id', 'coupon_code', 'discount', 'expiration_date', 'coupon_status')
            ->where('coupon_code', 'like', '%' . $keywords . '%')
            ->orWhere('expiration_date', 'like', '%' . $keywords . '%')
            ->orWhere('coupon_status', 'like', '%' . $keywords . '%')
            ->paginate(5);

        // Kiểm tra nếu không có kết quả nào tìm thấy
        if ($search_coupon->isEmpty()) {
            return view('admin.all_coupon')
                ->with('all_coupon', collect()) // gửi danh sách rỗng nếu không tìm thấy kết quả nào
                ->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        return view('admin.all_coupon')
            ->with('all_coupon', $search_coupon);
    }
    // CHỈNH SỬA TRẠNG THÁI COUPON
    public function checkAndUpdateExpiredCoupons()
    {
        DB::table('coupons')
            ->where('coupon_status', 'active')
            ->whereDate('expiration_date', '<', Carbon::now())
            ->update(['coupon_status' => 'inactive']);
    }
    public function active_coupon($cp_id)
    {
        $this->AuthLogin();
        $this->checkAndUpdateExpiredCoupons(); // Kiểm tra và cập nhật trạng thái mã khuyến mãi đã hết hạn
        DB::table('coupons')->where('coupon_id', $cp_id)->update(['coupon_status' => 'inactive']);
        Session::put('message', 'Đã đổi trạng thái thành không kích hoạt');
        return Redirect::to('all-coupon');
    }
    public function inactive_coupon($cp_id)
    {
        $this->AuthLogin();
        DB::table('coupons')->where('coupon_id', $cp_id)->update(['coupon_status' => 'active']);
        Session::put('message', 'Đã đổi trạng thái thành kích hoạt');
        return Redirect::to('all-coupon');
    }
    // XÓA COUPON
    public function delete_coupon($cp_id)
    {
        $this->AuthLogin();
        DB::table('coupons')->where('coupon_id', $cp_id)->delete();
        Session::put('message', 'Xóa coupon thành công');
        return Redirect::to('/all-coupon');
    }
    // SỬA COUPON
    public function edit_coupon($cp_id)
    {
        $this->AuthLogin();
        $edit_coupon = DB::table('coupons')->where('coupon_id', $cp_id)->get();
        $manager_coupon = view('admin.edit_coupon')->with('edit_coupon', $edit_coupon);
        return view('admin_layout')->with('admin.edit_coupon', $manager_coupon);
    }

    public function update_coupon(Request $request, $cp_id)
    {
        $this->AuthLogin();
        // Thêm validation cho số điện thoại và email
        $data = array();
        $data = [
            'coupon_code' => $request->coupon_code,
            'discount' => $request->discount,
            'expiration_date' => $request->expiration_date,
        ];

        DB::table('coupons')->where('coupon_id', $cp_id)->update($data);
        Session::put('message', 'Cập nhật coupon thành công');
        return Redirect::to('/all-coupon');
    }
    // ÁP DỤNG COUPON
    public function apply_coupon(Request $request)
    {
        $user_id = Session::get('user_id');
        $coupon_code = $request->input('coupon_code');

        // Kiểm tra xem mã coupon đã được sử dụng trước đó bởi người dùng này chưa
        $used_coupon = DB::table('orders')
            ->where('user_id', $user_id)
            ->where('coupon_id', function ($query) use ($coupon_code) {
                $query->select('coupon_id')
                    ->from('coupons')
                    ->where('coupon_code', $coupon_code)
                    ->limit(1);
            })
            ->exists();

        if ($used_coupon) {
            return redirect()->back()->with('error', 'Bạn đã sử dụng mã này rồi.');
        }

        // Kiểm tra xem mã coupon đã được áp dụng cho đơn hàng hiện tại chưa
        if (Session::has('coupon')) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể áp dụng một mã coupon cho mỗi đơn hàng.');
        }

        // Tìm kiếm mã coupon trong cơ sở dữ liệu
        $coupon = DB::table('coupons')
            ->where('coupon_code', $coupon_code)
            ->first();

        if ($coupon) {
            // Kiểm tra nếu mã coupon vẫn còn hiệu lực
            if (Carbon::parse($coupon->expiration_date)->isFuture() && $coupon->coupon_status == 'active') {
                // Áp dụng mã coupon
                Session::put('coupon', [
                    'coupon_id' => $coupon->coupon_id,
                    'discount' => $coupon->discount
                ]);
                return redirect()->back()->with('success', 'Áp dụng mã thành công!');
            } else {
                return redirect()->back()->with('error', 'Mã coupon đã hết hạn hoặc không hợp lệ.');
            }
        } else {
            return redirect()->back()->with('error', 'Mã coupon không tồn tại.');
        }
    }
    // ĐĂNG NHẬP GMAIL
    public function login_google()
    {
        config(['service.google.redirect' => env('GOOGLE_CLIENT_URL')]);
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function callback_google()
    {
        config(['services.google.redirect' => env('GOOGLE_CLIENT_URL')]);
        $users = Socialite::driver('google')->stateless()->user();
        $authUser = $this->findOrCreateUser($users, 'google');

        if ($authUser) {
            $account_name = DB::table('user')->where('user_id', $authUser->user_id)->first();
            Session::put('user_id', $account_name->user_id);
            Session::put('username', $account_name->username);
            // Lưu trữ thông tin đăng nhập Google vào session 
            Session::put('google_token', $users->token);
            Session::put('google_user', $users);
            return redirect('/trang-chu')->with('message', 'Đăng nhập bằng tài khoản Google <span style="color:green">' . $account_name->email . '</span> thành công');
        }

        return redirect('/dang-nhap')->with('error', 'Có lỗi xảy ra khi đăng nhập bằng tài khoản Google.');
    }
    public function findOrCreateUser($users)
    {
        // Kiểm tra xem người dùng có email đã tồn tại hay chưa
        $authUser = DB::table('user')
            ->where('email', $users->email)
            ->orWhere('google_id', $users->id)
            ->first();

        if ($authUser) {
            // Nếu người dùng đã tồn tại và google_id chưa được gán, cập nhật google_id
            if (empty($authUser->google_id)) {
                DB::table('user')
                    ->where('user_id', $authUser->user_id)
                    ->update(['google_id' => $users->id]);
            }
            return $authUser;
        } else {
            // Nếu người dùng chưa tồn tại, tạo bản ghi mới
            $user_new_id = DB::table('user')
                ->insertGetId([
                    'username' => $users->name,
                    'email' => $users->email,
                    'google_id' => $users->id, // Lưu google_id
                    'password' => '',
                    'fullname' => $users->name,
                    'register_date' => Carbon::now(),
                    'status' => 'active'
                ]);

            return DB::table('user')->where('user_id', $user_new_id)->first();
        }
    }
    // CẬP NHẬT THÔNG TIN ADMIN
    public function edit_admin()
    {
        $admin_id = Session::get('user_id');
        $admin_info = DB::table('user')->where('user_id', $admin_id)->first();

        return view('admin.edit_admin')->with('admin_info', $admin_info);
    }
    // Cập nhật thông tin tài khoản admin 
    public function update_admin(Request $request, $admin_id)
    {
        $request->validate([
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'fullname' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:5|confirmed'
        ]);

        // Lấy thông tin admin hiện tại từ cơ sở dữ liệu
        $admin = DB::table('user')->where('user_id', $admin_id)->first();

        // Kiểm tra xem số điện thoại đã tồn tại hay chưa 
        $existingPhone = DB::table('user')
            ->where('phone', $request->phone)
            ->where('user_id', '!=', $admin_id)
            ->first();
        if ($existingPhone) {
            return redirect()->back()->with('message', 'Số điện thoại đã tồn tại.');
        }

        // Chuẩn bị dữ liệu để cập nhật
        $data = [];

        if ($request->username && $request->username != $admin->username) {
            $data['username'] = $request->username;
        }
        if ($request->email && $request->email != $admin->email) {
            $data['email'] = $request->email;
        }
        if ($request->fullname && $request->fullname != $admin->fullname) {
            $data['fullname'] = $request->fullname;
        }
        if ($request->address && $request->address != $admin->address) {
            $data['address'] = $request->address;
        }
        if ($request->phone && $request->phone != $admin->phone) {
            $data['phone'] = $request->phone;
        }
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Cập nhật thông tin admin
        if (!empty($data)) {
            DB::table('user')->where('user_id', $admin_id)->update($data);
        }

        return redirect('/edit-admin')->with('message', 'Thông tin tài khoản đã được cập nhật.');
    }
}
