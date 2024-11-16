@extends('admin_layout')
@section('admin_content')
    {{-- THÔNG TIN TÀI KHOẢN --}}
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                THÔNG TIN TÀI KHOẢN
            </div>
            <div class="table-responsive">
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="text-align: center;color: black">TÊN TÀI KHOẢN</th>
                            <th style="text-align: center;color: black">EMAIL</th>
                            <th style="text-align: center;color: black">TÊN KHÁCH HÀNG</th>
                            <th style="text-align: center;color: black">ĐỊA CHỈ</th>
                            <th style="text-align: center;color: black">SỐ ĐIỆN THOẠI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_info->username }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_info->email }}</td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_info->fullname }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_info->address }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_info->phone }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    {{-- THÔNG TIN THANH TOÁN --}}
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                THÔNG TIN THANH TOÁN
            </div>
            <div class="table-responsive">
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="text-align: center;color: black">PHƯƠNG THỨC THANH TOÁN</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI THANH TOÁN</th>
                            <th style="text-align: center;color: black">MÃ ĐƠN HÀNG</th>
                            <th style="text-align: center;color: black">TỔNG TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_info->payment_method }}
                            </td>
                            <td
                                style="text-align: center; align-content: center; color: 
                                @if ($order_info->payment_status == 'Pending') blue 
                                @elseif($order_info->payment_status == 'Completed') green 
                                @elseif($order_info->payment_status == 'Failed') red @endif">
                                @if ($order_info->payment_status == 'Pending')
                                    Đang chờ
                                @elseif($order_info->payment_status == 'Completed')
                                    Đã thanh toán
                                @elseif($order_info->payment_status == 'Failed')
                                    Không thành công
                                @endif
                            </td>

                            <td style="text-align: center;align-content: center;color: black">{{ $order_info->code_order }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ number_format($order_info->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    {{-- THÔNG TIN VẬN CHUYỂN --}}
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                THÔNG TIN VẬN CHUYỂN
            </div>
            <div class="table-responsive">
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="text-align: center;color: black">TÊN NGƯỜI MUA</th>
                            <th style="text-align: center;color: black">ĐỊA CHỈ GIAO HÀNG</th>
                            <th style="text-align: center;color: black">SỐ ĐIỆN THOẠI NGƯỜI NHẬN</th>
                            <th style="text-align: center;color: black">EMAIL LIÊN LẠC</th>
                            <th style="text-align: center;color: black">GHI CHÚ ĐƠN HÀNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_info->shipping_name }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_info->shipping_address }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_info->shipping_phone }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_info->shipping_email }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_info->shipping_notes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    {{-- ORDER_DETAILS --}}
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ CHI TIẾT ĐƠN HÀNG
            </div>
            <div class="table-responsive">
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="text-align: center;color: black">TÊN SÁCH </th>
                            <th style="text-align: center;color: black">SỐ LƯỢNG</th>
                            <th style="text-align: center;color: black">GIÁ SÁCH</th>
                            <th style="text-align: center;color: black">TỔNG TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($order_details as $item)
                            <tr>
                                <td style="text-align: center;align-content: center;color: black">{{ $item->book_name }}
                                </td>
                                <td style="text-align: center;align-content: center;color: black">
                                    {{ $item->order_details_quantity }}</td>
                                <td style="text-align: center;align-content: center;color: black">
                                    {{ number_format($item->book_price, 0, ',', '.') }} VNĐ</td>
                                <td style="text-align: center;align-content: center;color: black">
                                    {{ number_format($item->book_price * $item->order_details_quantity, 0, ',', '.') }} VNĐ
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    {{-- CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG --}}
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="table-responsive">
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="text-align: center; color: black">TRẠNG THÁI ĐƠN HÀNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; align-content: center; color: black">
                                <form action="{{ URL::to('/update-order-status/' . $order_info->order_id) }}"
                                    method="POST" style="display: inline;">
                                    {{ csrf_field() }}
                                    <select name="order_status">
                                        <option value="Processing"
                                            {{ $order_info->order_status == 'Processing' ? 'selected' : '' }}>Đơn mới
                                        </option>
                                        <option value="Delivered"
                                            {{ $order_info->order_status == 'Delivered' ? 'selected' : '' }}>Đã xử lý
                                        </option>
                                        <option value="Cancelled"
                                            {{ $order_info->order_status == 'Cancelled' ? 'selected' : '' }}>Đã hủy
                                        </option>
                                    </select>
                                    <button type="submit" style="margin-left: 10px;">Cập nhật trạng thái</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
