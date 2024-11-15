@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    SỬA COUPON
                </header>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <?php
                $message = Session::get('message');
                if ($message) {
                    // Kiểm tra thông báo
                    if (strpos($message, 'không thành công') !== false) {
                        echo '<span style="color: red;">' . $message . '</span>';
                    } else {
                        echo '<span style="color: green;">' . $message . '</span>';
                    }
                    Session::put('message', null);
                }
                ?>
                <div class="panel-body">
                    @foreach ($edit_coupon as $key => $edit_value)
                        <div class="position-center">
                            <form role="form" action="{{ URL::to('/update-coupon/' . $edit_value->coupon_id) }}"
                                method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Mã coupon</label>
                                    <div class="input-group">
                                        <input type="text" value="{{ $edit_value->coupon_code }}" name="coupon_code" class="form-control" id="coupon_code"
                                            placeholder="Điền mã coupon" autocomplete="off">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"
                                                onclick="generateCouponCode()">Tạo
                                                mã</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Phần trăm giảm giá</label>
                                    <input type="text" value="{{ $edit_value->discount }}" name="discount" class="form-control" id="exampleInputEmail1"
                                        placeholder="Nhập phần trăm giảm giá" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Ngày hết hạn</label>
                                    <input type="date" value="{{ $edit_value->expiration_date }}" name="expiration_date" class="form-control" id="exampleInputEmail1"
                                        autocomplete="off">
                                </div>
                                <button type="submit" name="update_coupon" class="btn btn-info">Cập nhật coupon
                                    </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>
    <script>
        function generateCouponCode() {
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var couponCode = '';
            var length = 15; // Độ dài mã coupon
            for (var i = 0; i < length; i++) {
                var randomIndex = Math.floor(Math.random() * characters.length);
                couponCode += characters.charAt(randomIndex);
            }
            document.getElementById('coupon_code').value = couponCode;
        }
    </script>

@endsection
