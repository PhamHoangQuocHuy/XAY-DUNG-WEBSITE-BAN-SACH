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
                <p style="font-size: 15px;font-weight: bold;color: red;">Lưu ý: Nếu có cập nhật số lượng thì reload trang.
                    Đến bước này out ra thì điền thông tin lại từ đầu :^</p>
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
                            <td class="total">TỔNG TIỀN</td>
                            <td>THAO TÁC</td>
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
                                                value="{{ $value_content->qty }}" min="1">
                                            <input type="hidden" value="{{ $value_content->rowId }}" name="rowId_cart"
                                                class="form-control">
                                            <button type="submit" class="btn btn-default btn-sm">Cập nhật</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="cart_total">
                                    <p class="cart_total_price">
                                        {{ number_format($value_content->price * $value_content->qty, 0, ',', '.') }} VNĐ
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
            <form action="{{ URL::to('/order-place') }}" method="POST">
                {{ csrf_field() }}
                <div class="payment-options">
                    <span>
                        <label><input name="payment_option" value="Banking" type="radio" required> Banking</label>
                    </span>
                    <span>
                        <label><input name="payment_option" value="Cash on Delivery" type="radio" required> Nhận hàng rồi
                            thanh toán</label>
                    </span>
                    <span>
                        <input type="submit" value="ĐẶT HÀNG" name="send_order_place" class="btn btn-primary btn-sm">
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
