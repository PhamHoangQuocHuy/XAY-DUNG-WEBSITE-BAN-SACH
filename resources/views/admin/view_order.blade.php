@extends('admin_layout')
@section('admin_content')
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
                            <td style="text-align: center;align-content: center;color: black">{{ $order_by_id->username }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_by_id->email }}</td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_by_id->fullname }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_by_id->address }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_by_id->phone }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
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
                                {{ $order_by_id->shipping_name }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_by_id->shipping_address }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_by_id->shipping_phone }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_by_id->shipping_email }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_by_id->shipping_notes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
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
                        <tr>
                            <td style="text-align: center;align-content: center;color: black">{{ $order_by_id->book_name }}
                            </td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ $order_by_id->order_details_quantity }}</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ number_format($order_by_id->book_price, 0, ',', '.') }} VNĐ</td>
                            <td style="text-align: center;align-content: center;color: black">
                                {{ number_format($order_by_id->order_total, 0, ',', '.') }} VNĐ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
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
                                <form action="{{ URL::to('/update-order-status/' . $order_by_id->order_id) }}"
                                    method="POST" style="display: inline;">
                                    {{ csrf_field() }}
                                    <select name="order_status">
                                        <option value="Processing"
                                            {{ $order_by_id->order_status == 'Processing' ? 'selected' : '' }}>Đơn mới
                                        </option>
                                        <option value="Delivered"
                                            {{ $order_by_id->order_status == 'Delivered' ? 'selected' : '' }}>Đã xử lý
                                        </option>
                                        <option value="Cancelled"
                                            {{ $order_by_id->order_status == 'Cancelled' ? 'selected' : '' }}>Đã hủy
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
