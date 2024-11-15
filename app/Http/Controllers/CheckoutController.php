<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Mail;
use PayOS\PayOS;
use Illuminate\Support\Facades\Hash; // Thư viện mã hóa mật khẩu
use Carbon\Carbon; // thư viện hỗ trợ định dạng thời gian
use Illuminate\Support\Facades\URL;

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
            'shipping_phone' => 'required|digits:10',
            'shipping_email' => 'required|email',
            'shipping_address' => 'required|string|max:255',
            'shipping_notes' => 'nullable|string|max:1000',
        ]);

        // Chuẩn bị dữ liệu
        $data = array();
        $data['shipping_name'] = $request->shipping_name;
        $data['shipping_phone'] = $request->shipping_phone;
        $data['shipping_email'] = $request->shipping_email;
        $data['shipping_notes'] = $request->shipping_notes;
        $data['shipping_address'] = $request->shipping_address;

        // Lưu vào cơ sở dữ liệu và session
        $shipping_id = DB::table('shipping')->insertGetId($data);
        Session::put('shipping_id', $shipping_id);
        Session::put('shipping_data', $data); // lưu thông tin ở phần thông tin giao hàng

        return redirect('/payment')->with('success', 'Thông tin giao hàng đã được lưu.');
    }
    public function payment()
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
            ->limit(4)
            ->get();

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
    // ĐĂNG NHẬP
    public function user_login(Request $request)
    {
        $request->validate([
            'email_account' => 'required|email',
            'password_account' => 'required|min:5',
        ], [
            'email_account.required' => 'Vui lòng nhập email.',
            'email_account.email' => 'Email không hợp lệ.',
            'password_account.required' => 'Vui lòng nhập mật khẩu.',
            'password_account.min' => 'Mật khẩu phải có ít nhất 5 ký tự.',
        ]);

        $user = DB::table('user')
            ->where('email', $request->email_account)
            ->first();

        // Kiểm tra tài khoản
        if (!$user) {
            return redirect()->to('/login-checkout')->withErrors(['email_account' => 'Tài khoản không tồn tại.']);
        }

        // Kiểm tra trạng thái tài khoản
        if ($user->role != 'admin' && $user->status == 'inactive') {
            return redirect()->to('/login-checkout')->withErrors(['account_inactive' => 'Tài khoản của bạn đã bị tạm khóa. Hãy liên hệ với admin để mở khóa.']);
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
        Session::forget('coupon');
        Session::flush(); // xóa phiên
        return Redirect::to('/trang-chu')->with('success', 'Đăng xuất thành công.');
    }
    public function edit_shipping()
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

        return view('pages.checkout.edit_shipping')
            ->with('category', $category_book)
            ->with('tacgia_book', $tacgia_book)
            ->with('all_book', $all_book)
            ->with('publisher_list', $publisher_list)
            ->with('limitWordsFunc', $limitWordsFunc);
    }

    public function update_shipping(Request $request)
    {


        $data = $request->validate([
            'shipping_email' => 'required|email',
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_phone' => 'required|digits_between:10,11',
            'shipping_notes' => 'nullable|string|max:1000'
        ]);

        // Lấy shipping_id từ session
        $shipping_id = Session::get('shipping_id');

        // Cập nhật thông tin giao hàng trong cơ sở dữ liệu
        DB::table('shipping')
            ->where('shipping_id', $shipping_id)
            ->update($data);

        // Cập nhật thông tin giao hàng trong session
        Session::put('shipping_data', $data);

        return redirect('/payment')->with('success', 'Thông tin giao hàng đã được cập nhật.');
    }
    //TẠO CODE ORDER NGẪU NHIÊN
    public function generateCodeOrder($length = 20)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // ĐẶT HÀNG VÀ THANH TOÁN
    public function order_place(Request $request)
    {
        $user_id = Session::get('user_id');
        $shipping_id = Session::get('shipping_id');
        if (!$user_id || !$shipping_id) {
            return Redirect::back()->with('error', 'User ID hoặc Shipping ID không hợp lệ.');
        }

        $total_price = str_replace(',', '', Cart::subtotal());
        $order_code = $this->generateCodeOrder();
        $discount_percent = 0;
        $coupon_id = null;

        $coupon = Session::get('coupon');
        if ($coupon) {
            $discount_percent = $coupon['discount'];
            $discount_amount = ($total_price * $discount_percent) / 100;
            $total_price = $total_price - $discount_amount;
            $coupon_id = $coupon['coupon_id'];
        }

        // INSERT PAYMENT_METHOD
        $data = array();
        $data['payment_method'] = $request->payment_option;
        $data['payment_date'] = Carbon::now()->format('Y-m-d H:i:s');
        $data['payment_status'] = 'Pending';
        $data['code_order'] = $order_code;
        $data['user_id'] = $user_id;
        $data['total_price'] = $total_price;
        $payment_id = DB::table('payment')->insertGetId($data);

        // INSERT ORDER
        $order_data = array();
        $order_data['user_id'] = $user_id;
        $order_data['payment_id'] = $payment_id;
        $order_data['shipping_id'] = $shipping_id;
        $order_data['coupon_id'] = $coupon_id; // Lưu coupon_id vào orders
        $order_data['code_order'] = $data['code_order'];
        $order_data['order_date'] = Carbon::now()->format('Y-m-d H:i:s');
        $order_data['order_status'] = 'Processing';
        $order_data['order_total'] = $data['total_price'];
        $order_id = DB::table('orders')->insertGetId($order_data);

        // INSERT ORDER_DETAILS
        $content = Cart::content();
        foreach ($content as $value) {
            $order_details_data = array();
            $order_details_data['order_id'] = $order_id;
            $order_details_data['book_id'] = $value->id;
            $order_details_data['book_name'] = $value->name;
            $order_details_data['order_details_quantity'] = $value->qty;
            $order_details_data['book_price'] = $value->price;
            DB::table('order_details')->insert($order_details_data);
        }

        // Lấy thông tin vận chuyển
        $shipping_info = DB::table('shipping')->where('shipping_id', $shipping_id)->first();
        $email_data = [
            'name' => $shipping_info->shipping_name,
            'address' => $shipping_info->shipping_address,
            'phone' => $shipping_info->shipping_phone,
            'email' => $shipping_info->shipping_email,
            'notes' => $shipping_info->shipping_notes,
            'order_code' => $order_data['code_order'],
            'order_total' => $order_data['order_total'],
            'order_details' => $content,
            'payment_method' => $data['payment_method'],
            'shipping_info' => $shipping_info,
            'discount' => $discount_percent
        ];

        $current_time = Carbon::now()->format('d/m/Y H:i:s');
        $subject = "Đơn đặt mua hàng đã được xác nhận vào lúc $current_time";

        Mail::send('pages.emails.order_notification', $email_data, function ($message) use ($email_data, $subject) {
            $message->to($email_data['email'], $email_data['name'])->subject($subject);
        });

        // Hủy session coupon sau khi thanh toán thành công
        Session::forget('coupon');

        if ($data['payment_method'] === 'VNPAY') {
            return $this->vnpay_payment($order_code, $total_price);
        } else {
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
                ->limit(4)
                ->get();
            $publisher_list = $all_publishers->unique('publisher')->values();
            $limitWordsFunc = function ($string, $word_limit) {
                $words = explode(' ', $string);
                if (count($words) > $word_limit) {
                    return implode(' ', array_splice($words, 0, $word_limit)) . '...';
                }
                return $string;
            };

            Cart::destroy();
            return view('pages.checkout.cash_on_delivery')
                ->with('category', $category_book)
                ->with('tacgia_book', $tacgia_book)
                ->with('all_book', $all_book)
                ->with('publisher_list', $publisher_list)
                ->with('limitWordsFunc', $limitWordsFunc);
        }
    }
    // MOI TRUONG TEST VN PAY: 
    // số thẻ: 9704198526191432198
    // tên: NGUYEN VAN A
    // ngày: 07/15
    // otp: 123456
    public function vnpay_payment($order_code, $total_price)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = url::to('pages/checkout/VNPAY');
        $vnp_TmnCode = "UJOWEHRT"; // Mã website tại VNPAY 
        $vnp_HashSecret = "DII9OEI5AT96BYM2ICQL2YAT3Z18JQA9"; // Chuỗi bí mật

        $vnp_TxnRef = $order_code; // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toán đơn hàng';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $total_price * 100; // VNPAY yêu cầu đơn vị là đồng (VND) nhân với 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB'; // Có thể thay thế bằng mã ngân hàng cụ thể
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }
    public function vnpayReturn(Request $request)
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

        Cart::destroy();

        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $order_code = $request->input('vnp_TxnRef');

        // Kiểm tra trạng thái thanh toán 
        if ($vnp_ResponseCode == '00') { // '00' là mã phản hồi thành công từ VNPay 
            // Cập nhật trạng thái thanh toán thành công 
            DB::table('payment')
                ->where('code_order', $order_code)
                ->update(['payment_status' => 'Completed']);

            return view('pages.checkout.VNPAY')
                ->with('message', 'Thanh toán thành công')
                ->with('category', $category_book)
                ->with('tacgia_book', $tacgia_book)
                ->with('all_book', $all_book)
                ->with('publisher_list', $publisher_list)
                ->with('limitWordsFunc', $limitWordsFunc);
        } else {
            return view('pages.checkout.VNPAY')
                ->with('error', 'Thanh toán không thành công')
                ->with('category', $category_book)
                ->with('tacgia_book', $tacgia_book)
                ->with('all_book', $all_book)
                ->with('publisher_list', $publisher_list)
                ->with('limitWordsFunc', $limitWordsFunc);
        }
    }
}
