@extends('layout')
@section('content')
    <div class="container">
        <h2 style="margin-bottom: 20px;font-weight: bold;">Chi tiết đơn hàng</h2>
        <div class="row">
            <div class="col-md-4">
                <h4>Thông tin đơn hàng</h4>
                <p>Mã đơn hàng: <strong>{{ $order->code_order }}</strong></p>
                <p>Ngày đặt hàng: <strong>{{ $order->order_date }}</strong></p>
                <p>Trạng thái: <strong>
                        @if ($order->order_status == 'Processing')
                            <span style="color: green;font-size:15px;font-weight: bold ">Đơn mới</span>
                        @elseif($order->order_status == 'Delivered')
                            <span style="color: blue;font-size:15px;font-weight: bold ">Đã xử lý</span>
                        @elseif($order->order_status == 'Cancelled')
                            <span style="color: red;font-size:15px;font-weight: bold ">Đã hủy</span>
                        @endif
                    </strong></p>
                <p>Tổng tiền: <strong>{{ number_format($order->order_total, 0, ',', '.') }} VNĐ</strong></p>
            </div>
            <div class="col-md-4">
                <h4>Thông tin người nhận</h4>
                <p>Họ tên: <strong>{{ $shipping_info->shipping_name }}</strong></p>
                <p>Email: <strong>{{ $shipping_info->shipping_email }}</strong></p>
                <p>Địa chỉ: <strong>{{ $shipping_info->shipping_address }}</strong></p>
                <p>Số điện thoại: <strong>{{ $shipping_info->shipping_phone }}</strong></p>
                <p>Ghi chú: <strong>{{ $shipping_info->shipping_notes }}</strong></p>
            </div>
            <div class="col-md-4">
                <h4>Thông tin thanh toán</h4>
                <p>Phương thức thanh toán: <strong>
                        @if ($payment_info->payment_method == 'VNPAY')
                            <span style="color: green;font-size:15px;font-weight: bold ">Thanh toán qua VNPAY</span>
                        @else
                            <span style="color: purple;font-size:15px;font-weight: bold ">Nhận hàng rồi thanh toán</span>
                        @endif
                    </strong></p>
                <p>Ngày tạo thanh toán: <strong>{{ $payment_info->payment_date }}</strong></p>
                <p>Trạng thái thanh toán: <strong>
                        @if ($payment_info->payment_status == 'Completed')
                            <span style="color: green;font-size:15px;font-weight: bold ">Đã thanh toán</span>
                        @elseif($payment_info->payment_status == 'Pending')
                            <span style="color: blue;font-size:15px;font-weight: bold ">Chưa thanh toán</span>
                        @elseif($payment_info->payment_status == 'Failed')
                            <span style="color: red;font-size:15px;font-weight: bold ">Không thành công</span>
                        @endif
                    </strong></p>
                <p>Mã đơn hàng thanh toán: <strong>{{ $payment_info->code_order }}</strong></p>
                <p>Tổng tiền thanh toán: <strong>{{ number_format($payment_info->total_price, 0, ',', '.') }} VNĐ</strong>
                </p>
            </div>

        </div>
        <h4>Sản phẩm trong đơn hàng</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá tiền</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order_details as $item)
                    <tr>
                        <td>{{ $item->book_name }}</td>
                        <td>{{ $item->order_details_quantity }}</td>
                        <td>{{ number_format($item->book_price, 0, ',', '.') }} VNĐ</td>
                        <td>{{ number_format($item->order_details_quantity * $item->book_price, 0, ',', '.') }} VNĐ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
