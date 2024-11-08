{{-- TRANG ĐĂNG NHẬP VÀ ĐĂNG KÝ USER --}}
@extends('layout')
@section('content')
    <section id="form"><!--form-->
        <div class="container">
            <div class="row" id="login-section">
                <div class="col-sm-4 col-sm-offset-1">
                    <div class="login-form" id="login-section"><!--login form-->
                        <h2>ĐĂNG NHẬP VÀO TÀI KHOẢN</h2>

                        <form action="{{ URL::to('/user-login') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="email" name="email_account" value="{{ old('email_account') }}"
                                placeholder="Nhập email" required />
                            <input type="password" name="password_account" placeholder="Nhập password" required />
                            <span>
                                <a href="{{ URL::to('/quen-mat-khau') }}">
                                    Quên mật khẩu
                                </a>
                            </span>
                            <button type="submit" name="login" class="btn btn-default">ĐĂNG NHẬP</button>
                        </form>
                    </div><!--/login form-->
                </div>
                <div class="col-sm-1">
                    <h2 class="or">Hoặc</h2>
                </div>
                <div class="col-sm-4">
                    <div class="signup-form"><!--sign up form-->
                        <h2>ĐĂNG KÝ</h2>
                        <form action="{{ URL::to('/add-customer') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="text" name="customer_username" placeholder="Tên tài khoản"
                                value="{{ old('customer_username') }}" required />
                            <input type="email" name="customer_email" placeholder="Email"
                                value="{{ old('customer_email') }}" required />
                            <input type="password" name="password" placeholder="Password" required />
                            <input type="text" name="customer_fullname" placeholder="Họ tên đầy đủ"
                                value="{{ old('customer_fullname') }}" required />
                            <input type="text" name="customer_address" placeholder="Địa chỉ"
                                value="{{ old('customer_address') }}" required />
                            <input type="text" name="customer_phone" placeholder="Số điện thoại"
                                value="{{ old('customer_phone') }}" required />
                            <button type="submit" name="register" class="btn btn-default">ĐĂNG KÝ</button>
                        </form>
                    </div><!--/sign up form-->
                </div>
                {{-- Hiển thị thông báo lỗi đăng nhập --}}
                <div class="col-sm-12 text-center">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section><!--/form-->
@endsection
<script>
    // Tự động cuộn xuống phần form đăng nhập khi trang được tải
    document.addEventListener("DOMContentLoaded", function() {
        var loginSection = document.getElementById("login-section");
        if (loginSection) {
            loginSection.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
</script>
