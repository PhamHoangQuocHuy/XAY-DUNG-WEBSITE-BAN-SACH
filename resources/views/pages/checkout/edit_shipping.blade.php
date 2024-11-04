@extends('layout')
@section('content')
    <section id="edit_shipping">
        <div class="container">
            <div class="breadcrumbs">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">TRANG CHỦ</a></li>
                    <li class="active">CHỈNH SỬA THÔNG TIN GIAO HÀNG</li>
                </ol>
            </div>
            <div class="shopper-informations">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="bill-to">
                            <p style="font-size:24px;font-weight: bold;color: black ">Chỉnh sửa thông tin</p>
                            <form action="{{ URL::to('/update-shipping') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="email" placeholder="Email" name="shipping_email"
                                    value="{{ Session::get('shipping_data.shipping_email') }}" required>
                                <input type="text" placeholder="Họ và tên" name="shipping_name"
                                    value="{{ Session::get('shipping_data.shipping_name') }}" required>
                                <input type="text" placeholder="Địa chỉ giao hàng" name="shipping_address"
                                    value="{{ Session::get('shipping_data.shipping_address') }}" required>
                                <input type="text" placeholder="Số điện thoại" name="shipping_phone"
                                    value="{{ Session::get('shipping_data.shipping_phone') }}" required>
                                <textarea name="shipping_notes" placeholder="Ghi chú đơn hàng" rows="16">{{ Session::get('shipping_data.shipping_notes') }}</textarea>
                                <input type="submit" value="Cập nhật" class="btn btn-primary btn-sm">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
