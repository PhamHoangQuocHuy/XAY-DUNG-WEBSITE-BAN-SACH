<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Hash; // Thư viện mã hóa mật khẩu
use Carbon\Carbon; // thư viện hỗ trợ định dạng thời gian

class CheckoutController extends Controller
{
    public function login_checkout()
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

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        $publisher_list = $all_publishers->unique('publisher')->values();
        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        return view('pages.checkout.login_checkout')
            ->with('category', $category_book)
            ->with('tacgia_book', $tacgia_book)
            ->with('all_book', $all_book)
            ->with('publisher_list', $publisher_list)
            ->with('limitWordsFunc', $limitWordsFunc);
    }

    // ĐĂNG KÝ
    public function add_customer(Request $request)
    {
        $request->validate([
            'customer_username' => 'required|unique:user,username|regex:/^[a-zA-Z0-9\s]+$/',
            'customer_email' => 'required|email|unique:user,email',
            'password' => 'required|min:5',
            'customer_fullname' => 'required|regex:/^[\pL\s]+$/u',
            'customer_address' => 'required',
            'customer_phone' => 'required|unique:user,phone|regex:/^[0-9]{10}$/'
        ], [
            'customer_username.required' => 'Vui lòng nhập tên tài khoản.',
            'customer_username.unique' => 'Tên tài khoản đã tồn tại. Vui lòng nhập tên tài khoản khác',
            'customer_username.regex' => 'Tên tài khoản chỉ chứa chữ cái, số và khoảng trắng.',
            'customer_email.required' => 'Vui lòng nhập email.',
            'customer_email.email' => 'Email không hợp lệ.',
            'customer_email.unique' => 'Email đã tồn tại. Vui lòng nhập email khác',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 5 ký tự.',
            'customer_fullname.required' => 'Vui lòng nhập họ tên.',
            'customer_fullname.regex' => 'Họ tên chỉ chứa chữ cái, có thể bao gồm dấu và khoảng trắng.',
            'customer_address.required' => 'Vui lòng nhập địa chỉ.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer_phone.unique' => 'Số điện thoại đã tồn tại. Vui lòng nhập số điện thoại khác',
            'customer_phone.regex' => 'Số điện thoại phải có 10 chữ số.'
        ]);

        $data = array();
        $data['username'] = $request->customer_username;
        $data['email'] = $request->customer_email;
        $data['password'] = Hash::make($request->password);
        $data['fullname'] = $request->customer_fullname;
        $data['address'] = $request->customer_address;
        $data['phone'] = $request->customer_phone;
        $data['register_date'] = Carbon::now()->format('Y-m-d H:i:s');

        // Thêm tài khoản vào cơ sở dữ liệu
        DB::table('user')->insert($data);

        return redirect()->to('/login-checkout')->with('success', 'Đăng ký thành công! Hãy đăng nhập và bắt đầu mua hàng.');
    }
    // KHI NHẤN THANH TOÁN BÊN GIỎ HÀNG
    public function checkout()
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

        return view('pages.checkout.show_checkout')
            ->with('category', $category_book)
            ->with('tacgia_book', $tacgia_book)
            ->with('all_book', $all_book)
            ->with('publisher_list', $publisher_list)
            ->with('limitWordsFunc', $limitWordsFunc);
    }

    // NHẬN THÔNG TIN GIAO HÀNG
    public function save_checkout_customer(Request $request)
    {
        // Xác thực đầu vào
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|digits:10', // Số điện thoại phải đủ 10 số
            'shipping_email' => 'required|email', // Email phải đúng định dạng
            'shipping_address' => 'required|string|max:255',
            'shipping_notes' => 'nullable|string|max:1000', // shipping_notes không bắt buộc
        ]);
        $data = array();
        $data['shipping_name'] = $request->shipping_name;
        $data['shipping_phone'] = $request->shipping_phone;
        $data['shipping_email'] = $request->shipping_email;
        $data['shipping_notes'] = $request->shipping_notes;
        $data['shipping_address'] = $request->shipping_address;
        $shipping_id = DB::table('shipping')->insertGetId($data);
        Session::put('shipping_id', $shipping_id);
        return Redirect::to('/payment');
    }

    // THÔNG TIN THANH TOÁN
    public function payment()
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

        return view('pages.checkout.payment')
            ->with('category', $category_book)
            ->with('tacgia_book', $tacgia_book)
            ->with('all_book', $all_book)
            ->with('publisher_list', $publisher_list)
            ->with('limitWordsFunc', $limitWordsFunc);
    }

    // ĐẶT HÀNG VÀ THANH TOÁN
    public function order_place(Request $request)
    {
        // INSERT PAYMENT_METHOD
        $data = array();
        $data['payment_method'] = $request->payment_option;
        $data['payment_status'] = 'Pending';
        $payment_id = DB::table('payment')->insertGetId($data);

        // INSERT ORDER
        $order_data = array();
        $order_data['user_id'] = Session::get('user_id'); // lấy session user đang đăng nhập
        $order_data['payment_id'] = $payment_id; // sử dụng $payment_id vừa lấy được ở trên
        $order_data['shipping_id'] = Session::get('shipping_id');
        $order_data['code_order'] = $this->generateCodeOrder(20);
        $order_data['order_total'] = Cart::subtotal();
        $order_data['order_status'] = 'Processing';
        $order_id = DB::table('order')->insertGetId($order_data);

        // INSERT ORDER_DETAILS
        $content = Cart::content();
        foreach ($content as $value) {
            $order_details_data = array();
            $order_details_data['order_id'] = $order_id; // sử dụng $order_id vừa lấy được ở trên
            $order_details_data['book_id'] = $value->id; // lấy từ trường id package
            $order_details_data['book_name'] = $value->name; // lấy từ trường name package
            $order_details_data['book_price'] = $value->price; // lấy từ price package

            DB::table('order_details')->insert($order_details_data);
        }
        if ($data['payment_method'] === 'Baking') {
            echo 'Thanh toán trực tuyến';
        } else {
            echo 'Nhận hàng rồi thanh toán';
        }

        //return Redirect::to('/payment');
    }
    // ĐĂNG NHẬP
    public function user_login(Request $request)
    {
        $request->validate([
            'email_account' => 'required|email',
            'password_account' => 'required|min:6',
        ], [
            'email_account.required' => 'Vui lòng nhập email.',
            'email_account.email' => 'Email không hợp lệ.',
            'password_account.required' => 'Vui lòng nhập mật khẩu.',
            'password_account.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $user = DB::table('user')
            ->where('email', $request->email_account)
            ->where('role', 'customer')
            ->first();

        // Kiểm tra tài khoản
        if (!$user) {
            return redirect()->to('/login-checkout')->withErrors(['email_account' => 'Tài khoản không tồn tại.']);
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password_account, $user->password)) {
            return redirect()->to('/login-checkout')->withErrors(['password_account' => 'Mật khẩu sai! Vui lòng nhập lại.']);
        }

        // Đăng nhập thành công
        Session::put('user_id', $user->user_id);
        Session::put('username', $user->username);

        return redirect()->to('/trang-chu')->with('success', 'Đăng nhập thành công.');
    }

    // ĐĂNG XUẤT
    public function logout_checkout()
    {
        Session::forget('user_id');
        Session::forget('username'); // Xóa username khỏi session
        return Redirect::to('/trang-chu')->with('success', 'Đăng xuất thành công.');
    }
    //TẠO CODE ORDER NGẪU NHIÊN 20 GIÁ TRỊ GỒM CHỮ HOA, CHỮ THƯỜNG VÀ SỐ
    public function generateCodeOrder($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) { // Sửa điều kiện vòng lặp tại đây
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
