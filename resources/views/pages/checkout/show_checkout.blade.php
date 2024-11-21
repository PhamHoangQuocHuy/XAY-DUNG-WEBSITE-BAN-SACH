@extends('layout')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs" id="login-section">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">TRANG CHỦ</a></li>
                    <li class="active">BƯỚC 1: ĐIỀN THÔNG TIN GIAO HÀNG</li>
                </ol>
            </div><!--/breadcrums-->

            <div class="register-req">
                <p style="font-size: 15px;font-weight: bold;color: red;">Lưu ý: Xem lại các thông tin trước khi nhấn đặt hàng
                    !</p>
            </div><!--/register-req-->

            <div class="shopper-informations">
                <div class="row">
                    <div class="col-sm-12 clearfix">
                        <div class="bill-to">
                            <p>ĐIỀN THÔNG TIN</p>
                            {{-- Thông báo hiện lỗi validate --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-one">
                                <form action="{{ URL::to('/save-checkout-customer') }}" method="POST" id="shipping-form">
                                    {{ csrf_field() }}
                                    <input type="email" placeholder="Email" name="shipping_email" required>
                                    <input type="text" placeholder="Họ và tên" name="shipping_name" required>
                                    <select id="city">
                                        <option value="" selected>Chọn tỉnh thành</option>
                                    </select>

                                    <select id="district">
                                        <option value="" selected>Chọn quận huyện</option>
                                    </select>

                                    <select id="ward">
                                        <option value="" selected>Chọn phường xã</option>
                                    </select>
                                    <input type="hidden" id="shipping_address" name="shipping_address" required>
                                    <input type="text" placeholder="Số nhà, tên đường" name="address_detail" required>
                                    <input type="text" placeholder="Số điện thoại" name="shipping_phone" required>
                                    <textarea name="shipping_notes" placeholder="Ghi chú đơn hàng" rows="16"></textarea>
                                    <input type="submit" value="Gửi" name="send_order" class="btn btn-primary btn-sm">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> <!--/#cart_items-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script>
        const host = "https://provinces.open-api.vn/api/";
        var callAPI = (api) => {
            return axios.get(api)
                .then((response) => {
                    renderData(response.data, "city");
                });
        }
        callAPI('https://provinces.open-api.vn/api/?depth=1');
        var callApiDistrict = (api) => {
            return axios.get(api)
                .then((response) => {
                    renderData(response.data.districts, "district");
                });
        }
        var callApiWard = (api) => {
            return axios.get(api)
                .then((response) => {
                    renderData(response.data.wards, "ward");
                });
        }

        var renderData = (array, select) => {
            let row = ' <option disable value="">Chọn</option>';
            array.forEach(element => {
                row += `<option data-id="${element.code}" value="${element.name}">${element.name}</option>`
            });
            document.querySelector("#" + select).innerHTML = row
        }

        $("#city").change(() => {
            callApiDistrict(host + "p/" + $("#city").find(':selected').data('id') + "?depth=2");
            combineAddress();
        });
        $("#district").change(() => {
            callApiWard(host + "d/" + $("#district").find(':selected').data('id') + "?depth=2");
            combineAddress();
        });
        $("#ward").change(() => {
            combineAddress();
        });
        $("input[name='address_detail']").on('input', () => {
            combineAddress();
        });

        var combineAddress = () => {
            let city = $("#city option:selected").text();
            let district = $("#district option:selected").text();
            let ward = $("#ward option:selected").text();
            let address_detail = $("input[name='address_detail']").val();
            let full_address = `${address_detail}, ${ward}, ${district}, ${city}`;
            $("#shipping_address").val(full_address);
        }

        // Kết hợp địa chỉ đầy đủ khi form được gửi
        $("#shipping-form").submit((event) => {
            combineAddress();
        });
    </script>
@endsection
<script>
    // Tự động cuộn xuống phần form đăng nhập khi trang được tải
    document.addEventListener("DOMContentLoaded", function() {
        var loginSection = document.getElementById("login-section");
        if (loginSection) {
            loginSection.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
</script>
