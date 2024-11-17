@extends('layout')
@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success"> {{ session('success') }} </div>
            @endif @if (session('error'))
                <div class="alert alert-danger"> {{ session('error') }} </div>
            @endif
            <h2 style="margin-bottom: 20px;font-weight: bold;">Lịch sử đơn hàng</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="text-align: center">Mã đơn hàng</th>
                        <th style="text-align: center">Ngày đặt hàng</th>
                        <th style="text-align: center">Trạng thái</th>
                        <th style="text-align: center">Tổng tiền</th>
                        <th style="text-align: center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td style="text-align: center">{{ $order->code_order }}</td>
                            <td style="text-align: center">{{ $order->order_date }}</td>
                            <td style="text-align: center">
                                @if ($order->order_status == 'Processing')
                                    <span style="color: green;font-size:15px;font-weight: bold ">Đơn mới</span>
                                @elseif($order->order_status == 'Delivered')
                                    <span style="color: blue;font-size:15px;font-weight: bold ">Đã xử lý</span>
                                @elseif($order->order_status == 'Cancelled')
                                    <span style="color: red;font-size:15px;font-weight: bold ">Đã hủy</span>
                                @endif
                            </td>
                            <td style="text-align: center">{{ number_format($order->order_total, 0, ',', '.') }} VNĐ</td>
                            <td style="text-align: center">
                                <a href="{{ URL::to('/history-order-details/' . $order->order_id) }}" class="active"
                                    ui-toggle-class="">
                                    <i
                                        class="fa fa-eye text-info text-active"style="color: #007bff; font-size: 30px; margin: 0 5px;"></i>
                                </a>
                                {{-- HỦY ĐƠN --}}
                                @if ($order->order_status == 'Processing')
                                    <form action="{{ URL('/cancel-order/' . $order->order_id) }}" method="POST"
                                        style="display:inline;">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">Hủy đơn
                                        </button>
                                    </form>
                                @endif
                                {{-- TRẢ HÀNG --}}
                                {{-- @if ($order->order_status == 'Delivered')
                                    <form action="{{ URL('/return-order/' . $order->order_id) }}" method="POST"
                                        style="display:inline;"> {{ csrf_field() }} <button type="submit"
                                            class="btn btn-warning"
                                            onclick="return confirm('Bạn có chắc muốn trả hàng không?');"> Trả hàng
                                        </button> </form>
                                @endif --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </div>
@endsection
