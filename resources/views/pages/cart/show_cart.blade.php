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
            <div class="table-responsive cart_info">
                <?php $content = Cart::content(); ?>
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">SẢN PHẨM</td>
                            <td class="description">MÔ TẢ</td>
                            <td class="price">GIÁ </td>
                            <td class="quantity">SỐ LƯỢNG</td>
                            <td class="price">GIẢM GIÁ</td>
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
                                <td>
                                    @if (Session::has('coupon'))
                                        @php
                                            $coupons = Session::get('coupon');
                                        @endphp

                                        @if (is_array($coupons))
                                            @foreach ($coupons as $coupon)
                                                <span id="discount-amount">{{ $coupon['discount'] }}%</span>
                                            @endforeach
                                        @else
                                            <span id="discount-amount">0%</span>
                                        @endif
                                    @else
                                        <span id="discount-amount">0%</span>
                                    @endif

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
        </div>
    </section> <!--/#cart_items-->

    <section id="do_action">
        <div class="container">
            <div class="row">
                {{-- Textbox và nút áp dụng coupon --}}
                <div class="apply-coupon" style="margin: 20px 0; max-width: 300px;">
                    <h4 style="font-weight: bold; margin: 20px 0; font-size: 20px;">ÁP DỤNG COUPON</h4>
                    <form id="apply-coupon-form" action="{{ URL::to('/apply-coupon') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control" placeholder="Nhập mã coupon">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Áp dụng</button>
                            </span>
                        </div>
                    </form>
                </div>

                {{-- BẢNG HIỆN TỔNG TIỀN --}}
                <div class="col-sm-6">
                    <div class="total_area">
                        <ul>
                            <li>Tổng tiền <span id="total-amount">{{ Cart::subtotal(0, ',', '.') }} VNĐ</span></li>
                            <li>Thuế <span>0 VNĐ</span></li>
                            <li>Giảm giá <span id="discount-amount">
                                    @if (Session::has('coupon'))
                                        @foreach (Session::get('coupon') as $coupon)
                                            {{ $coupon['discount'] }}%
                                        @endforeach
                                    @else
                                        0%
                                    @endif
                                </span></li>
                            <li>Tiền vận chuyển <span>Free</span></li>
                            <li>Thành tiền <span id="final-amount">
                                    @if (Session::has('coupon'))
                                        @foreach (Session::get('coupon') as $coupon)
                                            {{ number_format(Cart::subtotal(0, '', '') * (1 - $coupon['discount'] / 100), 0, ',', '.') }}
                                            VNĐ
                                        @endforeach
                                    @else
                                        {{ Cart::subtotal(0, ',', '.') }} VNĐ
                                    @endif
                                </span></li>
                        </ul>
                        <?php
                        $user_id = Session::get('user_id');
                        $productCount = Cart::count();
                        ?>
                        @if ($user_id != null)
                            @if ($productCount == 0)
                                <p class="alert alert-warning"
                                    style="font-size: 20px;font-weight: bold;color: red; text-align: center">Không có sản
                                    phẩm nào để thanh toán</p>
                            @else
                                <a class="btn btn-default check_out" href="{{ URL::to('/checkout') }}">THANH TOÁN</a>
                            @endif
                        @else
                            <a class="btn btn-default check_out" href="{{ URL::to('/login-checkout') }}">HÃY ĐĂNG NHẬP ĐỂ
                                THANH TOÁN</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section> <!--/#do_action-->
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
