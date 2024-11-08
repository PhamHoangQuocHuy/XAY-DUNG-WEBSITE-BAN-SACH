@extends('layout')
@section('content')
    <section id="form"><!--form-->
        <div class="container">
            <div class="row" id="login-section">
                <div class="col-sm-4 col-sm-offset-1">
                    <div class="login-form" id="login-section"><!--login form-->
                        <h2>NHẬP EMAIL ĐÃ ĐĂNH KÝ ĐỂ RESET MẬT KHẨU</h2>
                        <form action="{{ url('/quen-mat-khau') }}" method="POST">
                            @csrf
                            <input type="email" name="email" placeholder="Nhập email của bạn" required>
                            <button type="submit" class="btn btn-primary">Gửi liên kết đặt lại mật khẩu</button>
                        </form>
                    </div><!--/login form-->
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
