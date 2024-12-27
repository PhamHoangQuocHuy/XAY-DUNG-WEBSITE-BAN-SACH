@extends('layout')
@section('content')
    <script src="https://cdn.payos.vn/payos-checkout/v1/stable/payos-initialize.js"></script>
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs" id="login-section">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">TRANG CHỦ</a></li>
                    <li class="active">BƯỚC 2: ĐẶT HÀNG VÀ THANH TOÁN</li>
                </ol>
            </div><!--/breadcrums-->

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- TRANG SHOW_CART LẤY QUA --}}
            <div class="review-payment">
                <h2 style="font-weight: bold">XEM LẠI GIỎ HÀNG</h2>
            </div>
            <div class="table-responsive cart_info">
                <?php $content = Cart::content(); ?>
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">SẢN PHẨM</td>
                            <td class="description">MÔ TẢ</td>
                            <td class="price">GIÁ</td>
                            <td class="quantity">SỐ LƯỢNG</td>
                            <td class="price">GIẢM GIÁ</td>
                            <td class="total">TỔNG TIỀN</td>
                            {{-- <td>THAO TÁC</td> --}}
                        </tr>
                    </thead>
                    <tbody id="cart-contents">
                        @foreach ($content as $value_content)
                            <tr>
                                <td class="cart_product">
                                    <a href=""><img
                                            src="{{ URL::to('public/uploads/product/' . $value_content->options->image) }}"
                                            width="50" alt="" /></a>
                                </td>
                                <td class="cart_description">
                                    <h4><a href="">{{ $limitWordsFunc($value_content->name, 4) }}</a></h4>
                                    <p>ISBN:{{ $value_content->options->isbn }}</p>
                                </td>
                                <td class="cart_price">
                                    <p>{{ number_format($value_content->price, 0, ',', '.') }} VNĐ</p>
                                </td>
                                <td class="cart_quantity">
                                    <div class="cart_quantity_button">
                                        <form class="update-cart-form" action="{{ URL::to('/update-cart-quantity') }}"
                                            method="POST">
                                            {{ csrf_field() }}
                                            <input class="cart_quantity_input" type="text" name="cart_quantity"
                                                value="{{ $value_content->qty }}" min="1" readonly>
                                            <input type="hidden" value="{{ $value_content->rowId }}" name="rowId_cart"
                                                class="form-control">
                                            {{-- <button type="submit" class="btn btn-default btn-sm">Cập nhật</button> --}}
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <?php $coupon = Session::get('coupon'); ?>
                                    {{ isset($coupon[0]['discount']) ? $coupon[0]['discount'] : '0' }}%
                                </td>

                                <td class="cart_total">
                                    <?php
                                    $discount = isset($coupon[0]['discount']) ? $coupon[0]['discount'] / 100 : 0;
                                    $item_total = $value_content->price * $value_content->qty;
                                    $discounted_total = $item_total - $item_total * $discount;
                                    ?>
                                    <p class="cart_total_price">
                                        {{ number_format($discounted_total, 0, ',', '.') }} VNĐ
                                    </p>
                                </td>

                                {{-- <td class="cart_delete">
                                    <a class="cart_quantity_delete"
                                        href="{{ URL::to('/delete-to-cart/' . $value_content->rowId) }}"><i
                                            class="fa fa-times"></i></a>
                                </td> --}}
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="12" class="text-end">
                                <div class="alert alert-info" role="alert"
                                    style="font-size: 24px; font-weight: bold; color: red; background-color: #f8d7da; border-color: #f5c6cb;">
                                    Tổng tiền:
                                    <?php
                                    $overall_total = $content->sum(function ($item) use ($discount) {
                                        $item_total = $item->price * $item->qty;
                                        return $item_total - $item_total * $discount;
                                    });
                                    if ($overall_total < 0) {
                                        echo 'Đơn hàng không hợp lệ';
                                        return redirect()->back()->with('error', 'Đơn hàng không hợp lệ.');
                                    }
                                    ?>
                                    {{ number_format($overall_total, 0, ',', '.') }} VNĐ
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- THÔNG TIN GIAO HÀNG --}}
            <div class="review-shipping">
                <h4 style="font-weight: bold; margin: 40px 0; font-size: 20px;">THÔNG TIN GIAO HÀNG</h4>
                <p>Email: {{ Session::get('shipping_data.shipping_email') }}</p>
                <p>Họ và tên: {{ Session::get('shipping_data.shipping_name') }}</p>
                <p>Địa chỉ giao hàng: {{ Session::get('shipping_data.shipping_address') }}</p>
                <p>Số điện thoại: {{ Session::get('shipping_data.shipping_phone') }}</p>
                <p>Ghi chú đơn hàng: {{ Session::get('shipping_data.shipping_notes') }}</p>
                <a href="{{ URL::to('/edit-shipping') }}">Chỉnh sửa thông tin giao hàng</a>
            </div>

            {{-- PAYMENT_METHOD --}}
            <h4 style="margin: 40px 0; font-size: 20px;">CHỌN PHƯƠNG THỨC THANH TOÁN</h4>
            <form id="order-form" action="{{ URL::to('/order-place') }}" method="POST">
                {{ csrf_field() }}
                <div class="payment-options">
                    <span>
                        <label><input name="payment_option" value="VNPAY" type="radio" required> VNPAY</label>
                    </span>
                    <span>
                        <label><input name="payment_option" value="Cash on Delivery" type="radio" required> Nhận hàng rồi
                            thanh toán</label>
                    </span>
                    <span>
                        <input type="submit" onclick="return confirm('Xác nhận đặt hàng ?')" value="ĐẶT HÀNG"
                            name="send_order_place" class="btn btn-primary btn-sm">
                    </span>
                </div>
            </form>
        </div>
    </section>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // AJAX for updating cart quantity
        document.querySelectorAll('.update-cart-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(form);
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update total amount in the summary
                            document.getElementById('total-amount').innerText = data
                                .totalAmount + " VNĐ";
                            document.getElementById('final-amount').innerText = data
                                .finalAmount + " VNĐ";

                            // Find and update item total for this row
                            const row = form.closest('tr');
                            if (row) {
                                row.querySelector('.cart_total_price').innerText = data
                                    .itemTotal + " VNĐ";
                            }
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // AJAX for deleting cart item
        document.querySelectorAll('.cart_quantity_delete').forEach(function(deleteBtn) {
            deleteBtn.addEventListener('click', function(event) {
                event.preventDefault();
                var url = this.href;

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove item row
                            const row = deleteBtn.closest('tr');
                            if (row) {
                                row.remove();
                            }

                            // Update total amount in the summary
                            document.getElementById('total-amount').innerText = data
                                .totalAmount + " VNĐ";
                            document.getElementById('final-amount').innerText = data
                                .finalAmount + " VNĐ";
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
