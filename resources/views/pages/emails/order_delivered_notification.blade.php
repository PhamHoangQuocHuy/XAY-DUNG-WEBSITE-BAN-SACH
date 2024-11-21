<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg320mUcww7on3RYdg4Va+pPmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>
    <div class="container" style="background: #222; border-radius:12px;padding: 15px;">
        <div class="col-md-12">
            <p style="text-align: center;color: #fff">Đây là email tự động. Quý khách vui lòng không phản hồi email
                này.Trân trọng cảm ơn quý khách</p>
            <div class="row" style="background: #67c77f;padding: 15px">

                <div class="col-md-6" style="text-align: center;color: #fff;font-weight: bold;font-size: 30px">
                    <h4 style="margin: 0">WEBSITE BÁN SÁCH <a href="http://localhost/xaydungwebsitebansach/">
                            BOOK.VN</a></h4>
                    <h6 style="margin: 0">EMAIL THÔNG BÁO ĐƠN HÀNG CỦA BẠN ĐÃ ĐƯỢC XỬ LÝ VÀ ĐANG VẬN CHUYỂN</h6>
                </div>

                <div class="col-md-6 logo" style="color: #fff">
                    <p>Chào bạn <strong style="color: #000; text-decoration: underline;">{{ $name }}</strong>
                        , chúng tôi đã vận chuyển đơn hàng của bạn
                    </p>
                </div>
                <div class="col-md-12">
                    <p style="color: #fff;font-size: 17px">Bạn hoặc một ai đó đã đặt hàng ở trang web chung tôi với
                        thông tin như sau:</p>
                    <h4 style="color: #000;">THÔNG TIN ĐƠN HÀNG</h4>
                    <p>Mã đơn hàng: <strong style="text-transform: uppercase;color: #fff">{{ $order_code }}</strong>
                    </p>
                    <p>Mã khuyến mãi áp dụng: <strong style="text-transform: uppercase;color: #fff">
                            @if (isset($coupons->discount) && $coupons->discount > 0)
                                {{ $coupons->discount }}%
                            @else
                                Không có
                            @endif
                        </strong></p>
                    <p>Phí vận chuyển: <strong style="text-transform: uppercase;color: #fff">0 VNĐ</strong></p>
                    <p>Dịch vụ: <strong style="text-transform: uppercase;color: #fff">Đặt hàng trực tuyến</strong></p>
                    <h4 style="color: #000">THÔNG TIN NGƯỜI NHẬN</h4>

                    <p>HỌ VÀ TÊN NGƯỜI ĐẶT HÀNG:
                        @if ($shipping_info->shipping_name == '')
                            <span style="color: #fff">Không có</span>
                        @else
                            <span style="color: #fff">{{ $shipping_info->shipping_name }}</span>
                        @endif
                    </p>

                    <p>EMAIL:
                        @if ($shipping_info->shipping_name == '')
                            <span style="color: #fff">Không có</span>
                        @else
                            <span style="color: #fff">{{ $shipping_info->shipping_email }}</span>
                        @endif
                    </p>
                    <p>ĐỊA CHỈ GIAO HÀNG:
                        @if ($shipping_info->shipping_address == '')
                            <span style="color: #fff">Không có</span>
                        @else
                            <span style="color: #fff">{{ $shipping_info->shipping_address }}</span>
                        @endif
                    </p>
                    <p>SỐ ĐIỆN THOẠI:
                        @if ($shipping_info->shipping_phone == '')
                            <span style="color: #fff">Không có</span>
                        @else
                            <span style="color: #fff">{{ $shipping_info->shipping_phone }}</span>
                        @endif
                    </p>
                    <p>GHI CHÚ ĐƠN HÀNG:
                        @if ($shipping_info->shipping_notes == '')
                            <span style="color: #fff">Không có</span>
                        @else
                            <span style="color: #fff">{{ $shipping_info->shipping_notes }}</span>
                        @endif
                    </p>
                    <p>HÌNH THỨC THANH TOÁN: <strong style="text-transform: uppercase;color: #fff">
                            @if ($payment_method == 'VNPAY')
                                Thanh toán qua VNPAY
                            @else
                                Nhận hàng rồi thanh toán
                            @endif
                        </strong></p>
                    <p style="color: #fff">Nếu thông tin người nhận không có chúng tôi sẽ liên hệ với người đặt hàng để
                        trao đổi thông tin về đơn đặt hàng</p>
                    <h4 style="color: #000;">SẢN PHẨM ĐƯỢC CHÚNG TÔI XÁC NHẬN:</h4>
                    <table class="table table-striped" style="border: 1px">
                        <thead>
                            <tr>
                                <th style="text-align: center">Sản phẩm</th>
                                <th style="text-align: center">Số lượng đặt</th>
                                <th style="text-align: center">Giá tiền</th>
                                <th style="text-align: center">Thành tiền</th>
                            </tr>
                        </thead>
                        @php
                            function limitWords($string, $word_limit)
                            {
                                $words = explode(' ', $string);
                                if (count($words) > $word_limit) {
                                    return implode(' ', array_splice($words, 0, $word_limit)) . '...';
                                }
                                return $string;
                            }
                        @endphp
                        <tbody>
                            @php
                                $total_before_discount = 0;
                                $discount_amount = 0;
                            @endphp

                            @foreach ($order_details as $item)
                                @php
                                    $sub_total = $item->order_details_quantity * $item->book_price;
                                    $total_before_discount += $sub_total;
                                @endphp
                                <tr style="text-align: center">
                                    <td style="text-align: center">{{ limitWords($item->book_name, 5) }}</td>
                                    <td style="text-align: center">{{ $item->order_details_quantity }}</td>
                                    <td style="text-align: center">{{ number_format($item->book_price, 0, ',', '.') }}
                                        VNĐ</td>
                                    <td style="text-align: center">{{ number_format($sub_total, 0, ',', '.') }} VNĐ
                                    </td>
                                </tr>
                            @endforeach

                            @php
                                if (isset($discount) && $discount > 0) {
                                    $discount_amount = $total_before_discount * ($discount / 100);
                                }
                                $total = $total_before_discount - $discount_amount;
                            @endphp

                            <tr>
                                <td colspan="4" align="left" style="font-weight:bold; font-size:20px; color: red">
                                    TỔNG TIỀN TRƯỚC GIẢM GIÁ:
                                    <span style="float: right">
                                        {{ number_format($total_before_discount, 0, ',', '.') }} VNĐ
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" align="left" style="font-weight:bold; font-size:20px; color: red">
                                    TIỀN GIẢM GIÁ:
                                    <span style="float: right">
                                        {{ number_format($discount_amount, 0, ',', '.') }} VNĐ
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" align="left" style="font-weight:bold; font-size:20px; color: red">
                                    TỔNG TIỀN THANH TOÁN:
                                    <span style="float: right">
                                        {{ number_format($total, 0, ',', '.') }} VNĐ
                                    </span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <p style="color: #fff;text-align: center;font-size: 15px">Xem lại lịch sử đơn hàng đã đặt <a
                        href="{{ URL::to('/user-orders-history/' . Session::get('user_id')) }}">tại đây</a></p>
                <p style="color: #fff;text-align: center;font-size: 15px">Mọi chi tiết xin liên hệ qua website: <a
                        href="http://localhost/xaydungwebsitebansach/">BOOK.VN</a> hoặc qua hotline: 19001234. Xin cám
                    ơn
                    quý khách đã đặt hàng tại website của chúng tôi</p>
            </div>
        </div>
    </div>
</body>

</html>
