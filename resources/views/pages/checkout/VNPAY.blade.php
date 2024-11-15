@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container" id="login-section">
            <div class="review-payment">
                @if (session('message'))
                    <h1>{{ session('message') }}</h1>
                    @endif @if (session('error'))
                        <h1>{{ session('error') }}</h1>
                    @endif
                    <h2 style="font-weight: bold">CẢM ƠN BẠN ĐÃ MUA HÀNG VÀ THANH TOÁN TRƯỚC VỚI VNPAY
                    </h2><h5>CHÚNG TÔI SẼ LIÊN LẠC VỚI BẠN TRONG THỜI GIAN SỚM
                        NHẤT</h5>
            </div>
        </div>
    </section>
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
