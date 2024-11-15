@extends('layout')
@section('content')
    <div class="container">
        <h1 style="text-align: center;margin-bottom: 25px">Danh sách mã khuyến mãi</h1>
        @if ($coupons->isEmpty())
            <h3 class="text-center">Hiện tại chưa có coupons nào.</h3>
        @else
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th class="text-center">Mã Coupon</th>
                        <th class="text-center">Giảm giá</th>
                        <th class="text-center">Ngày hết hạn</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coupons as $coupon)
                        <tr>
                            <td id="coupon-code-{{ $loop->index }}" class="align-middle">{{ $coupon->coupon_code }}</td>
                            <td class="align-middle">{{ $coupon->discount }}%</td>
                            <td class="align-middle">{{ \Carbon\Carbon::parse($coupon->expiration_date)->format('d/m/Y') }}
                            </td>
                            <td class="align-middle">
                                <button class="btn btn-primary" style="margin-top: 0px;"
                                    onclick="copyCouponCode('{{ $coupon->coupon_code }}', {{ $loop->index }})">LẤY
                                    MÃ</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div id="success-message" class="text-center" style="display:none; color:green;">Lấy coupon thành công!</div>
    </div>

    <script>
        function copyCouponCode(code, index) {
            // Tạo một phần tử input ẩn để sao chép mã
            var input = document.createElement('input');
            input.setAttribute('value', code);
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);

            // Hiển thị thông báo thành công
            var successMessage = document.getElementById('success-message');
            successMessage.style.display = 'block';
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 3000); // Ẩn thông báo sau 3 giây

            
        }
    </script>
@endsection
