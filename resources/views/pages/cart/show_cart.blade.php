@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs" id="login-section">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">TRANG CHỦ</a></li>
                    <li class="active">GIỎ HÀNG</li>
                </ol>
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
        </div>
    </section> <!--/#cart_items-->
    <section id="do_action">
        <div class="container">
            <div class="row">
                {{-- BẢNG HIỆN TỔNG TIỀN --}}
                <div class="col-sm-6">
                    <div class="total_area">
                        <ul>
                            <li>Tổng tiền<span>{{ Cart::subtotal(0, ',', '.') . ' ' . 'VNĐ' }}</span></li>
                            <li>Thuế<span>0 VNĐ</span></li>
                            <li>Tiền vận chuyển<span>Free</span></li>
                            <li>Thành tiền <span>{{ Cart::subtotal(0, ',', '.') . ' ' . 'VNĐ' }}</span></li>
                        </ul>
                        <?php
                        $user_id = Session::get('user_id');
                        $productCount = Cart::count(); // Sử dụng Cart::count() để đếm số lượng sản phẩm trong giỏ hàng
                        
                        if ($user_id != NULL) {
                            if ($productCount == 0) {
                                // Nếu giỏ hàng không có sản phẩm
                                ?>
                        <p class="alert alert-warning" style="font-size: 20px;font-weight: bold;color: red; text-align: center">Không có sản phẩm nào để thanh toán</p>
                        <?php
                            } else {
                                // Nếu giỏ hàng có sản phẩm
                                ?>
                        <a class="btn btn-default check_out" href="{{ URL::to('/checkout') }}">THANH TOÁN</a>
                        <?php
                            }
                        } else {
                            ?>
                        <a class="btn btn-default check_out" href="{{ URL::to('/login-checkout') }}">HÃY ĐĂNG NHẬP ĐỂ THANH
                            TOÁN</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/#do_action-->
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
