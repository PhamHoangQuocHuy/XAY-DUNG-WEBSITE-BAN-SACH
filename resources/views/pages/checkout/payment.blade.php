@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs" id="login-section">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">TRANG CHỦ</a></li>
                    <li class="active">BƯỚC 2: ĐẶT HÀNG VÀ THANH TOÁN</li>
                </ol>
            </div><!--/breadcrums-->
            
            {{-- TRANG SHOW_CART LẤY QUA --}}
            <div class="review-payment">
                <h2 style="font-weight: bold">XEM LẠI GIỎ HÀNG</h2>
            </div>
            <div class="table-responsive cart_info">
                <?php
                $content = Cart::content();
                ?>
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">SẢN PHẨM</td>
                            <td class="description">MÔ TẢ</td>
                            <td class="price">GIÁ </td>
                            <td class="quantity">SỐ LƯỢNG</td>
                            <td class="total">TỔNG TIỀN</td>
                            <td>THAO TÁC</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($content as $value_content)
                            <tr>
                                <td class="cart_product">
                                    <a href=""> <img
                                            src="{{ URL::to('public/uploads/product/' . $value_content->options->image) }}"
                                            width="50" alt="" />
                                    </a>
                                </td>
                                <td class="cart_description">
                                    <h4><a href="">{{ $limitWordsFunc($value_content->name, 4) }}</a></h4>
                                    <p>ISBN:{{ $value_content->options->isbn }}</p>
                                </td>
                                <td class="cart_price">
                                    <p>{{ $value_content->formatted_price = number_format($value_content->price, 0, ',', '.') }}
                                        VNĐ</p>
                                </td>
                                <td class="cart_quantity">
                                    <div class="cart_quantity_button">
                                        <form action="{{ URL::to('/update-cart-quantity') }}" method="POST">
                                            {{ csrf_field() }}
                                            {{-- SỐ LƯỢNG --}}
                                            <input class="cart_quantity_input" type="text" name="cart_quantity"
                                                value="{{ $value_content->qty }}" min="1">
                                            <input type="hidden" value="{{ $value_content->rowId }}" name="rowId_cart"
                                                class="form-control">

                                            <input type="submit" value="Cập nhật" name="update_qty"
                                                class="btn btn-default btn-sm">
                                        </form>
                                    </div>
                                </td>
                                <td class="cart_total">
                                    <p class="cart_total_price">
                                        <?php
                                        $subtotal = $value_content->price * $value_content->qty;
                                        echo number_format($subtotal) . '' . 'VNĐ';
                                        ?>
                                    </p>
                                </td>
                                <td class="cart_delete">
                                    <a class="cart_quantity_delete"
                                        href="{{ URL::to('/delete-to-cart/' . $value_content->rowId) }}"><i
                                            class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAYMENT_METHOD --}}
            <h4 style="margin: 40px 0;font-size: 20px;">CHỌN PHƯƠNG THỨC THANH TOÁN</h4>
            <form action="{{ URL::to('/orer-place') }}" method="POST">
                {{ csrf_field() }}
                <div class="payment-options">
                    <span>
                        <label><input name="payment_option" value="Baking" type="radio"> Banking</label>
                    </span>
                    <span>
                        <label><input name="payment_option" value="Cash-on-delivery" type="radio"> Nhận hàng rồi thanh
                            toán</label>
                        <input type="submit" value="ĐẶT HÀNG" name="send_order_place" class="btn btn-primary btn-sm">
                    </span>
                </div>
            </form>
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
