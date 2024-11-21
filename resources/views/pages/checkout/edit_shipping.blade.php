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
                            <p class="form-title">Chỉnh sửa thông tin</p>
                            <form action="{{ URL::to('/update-shipping') }}" method="POST" id="edit-shipping-form"
                                class="form-container">
                                {{ csrf_field() }}
                                <div class="form-group mb-3">
                                    <label for="shipping_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="shipping_email" name="shipping_email"
                                        value="{{ Session::get('shipping_data.shipping_email') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="shipping_name" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="shipping_name" name="shipping_name"
                                        value="{{ Session::get('shipping_data.shipping_name') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="shipping_phone" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="shipping_phone" name="shipping_phone"
                                        value="{{ Session::get('shipping_data.shipping_phone') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label">Tỉnh thành</label>
                                    <select id="city" class="form-select">
                                        <option value="" selected>Chọn tỉnh thành</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="district" class="form-label">Quận huyện</label>
                                    <select id="district" class="form-select">
                                        <option value="" selected>Chọn quận huyện</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="ward" class="form-label">Phường xã</label>
                                    <select id="ward" class="form-select">
                                        <option value="" selected>Chọn phường xã</option>
                                    </select>
                                </div>
                                <input type="hidden" id="shipping_address" name="shipping_address" required>
                                <div class="form-group mb-3">
                                    <label for="address_detail" class="form-label">Số nhà, tên đường</label>
                                    <input type="text" class="form-control" id="address_detail" name="address_detail"
                                        value="{{ Session::get('shipping_data.address_detail') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="shipping_notes" class="form-label">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" id="shipping_notes" name="shipping_notes" placeholder="Ghi chú đơn hàng" rows="4">{{ Session::get('shipping_data.shipping_notes') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
            let row = '<option disable value="">Chọn</option>';
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
        $("#address_detail").on('input', () => {
            combineAddress();
        });

        var combineAddress = () => {
            let city = $("#city option:selected").text();
            let district = $("#district option:selected").text();
            let ward = $("#ward option:selected").text();
            let address_detail = $("#address_detail").val();
            let full_address = `${address_detail}, ${ward}, ${district}, ${city}`;
            $("#shipping_address").val(full_address);
        }

        $("#edit-shipping-form").submit((event) => {
            combineAddress();
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
@endsection
