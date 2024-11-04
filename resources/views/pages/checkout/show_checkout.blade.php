@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs" id="login-section">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">TRANG CHỦ</a></li>
                    <li class="active">BƯỚC 1: ĐIỀN THÔNG TIN GIAO HÀNG</li>
                </ol>
            </div><!--/breadcrums-->

            <div class="register-req">
                <p style="font-size: 15px;font-weight: bold;color: red;">Lưu ý: Xem lại các thông tin trước khi nhấn đặt hàng
                    !</p>
            </div><!--/register-req-->

            <div class="shopper-informations">
                <div class="row">
                    <div class="col-sm-12 clearfix">
                        <div class="bill-to">
                            <p>ĐIỀN THÔNG TIN</p>
                            {{-- Thông báo hiện lỗi validate --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-one">
                                <form action="{{ URL::to('/save-checkout-customer') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="email" placeholder="Email" name="shipping_email" required>
                                    <input type="text" placeholder="Họ và tên" name="shipping_name" required>
                                    <input type="text" placeholder="Địa chỉ giao hàng" name="shipping_address" required>
                                    <input type="text" placeholder="Số điện thoại" name="shipping_phone" required>
                                    <textarea name="shipping_notes" placeholder="Ghi chú đơn hàng" rows="16"></textarea>
                                    <input type="submit" value="Gửi" name="send_order" class="btn btn-primary btn-sm">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
