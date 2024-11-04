@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container" id="login-section">
            {{-- TRANG SHOW_CART LẤY QUA --}}
            <div class="review-payment">
                <h2 style="font-weight: bold">CẢM ƠN BẠN ĐÃ MUA HÀNG CHÚNG TÔI SẼ LIÊN LẠC VỚI BẠN TRONG THỜI GIAN SỚM NHẤT</h2>
            </div>
        </div>
    </section> <!--/#cart_items-->
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
