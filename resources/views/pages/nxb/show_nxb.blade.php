@extends('layout')
@section('content')
    <div class="features_items" id="login-section">
        <!--features_items-->
        <h2 class="title text-center" style="padding-top: 5px">{{ $nxb_name }}</h2>

        @foreach ($nxb_by_id as $key => $nxb_book)
            <div class="col-sm-4">
                <div class="product-image-wrapper">
                    <div class="single-products">
                        <div class="productinfo text-center">
                            <a href="{{ URL::to('/chi-tiet-san-pham-theo-nxb/' . $nxb_book->book_id) }}">
                                <img src="{{ URL::to('public/uploads/product/' . $nxb_book->image) }}" alt="" />
                                <h2>{{ number_format($nxb_book->price, 0, ',', '.') }} VNĐ</h2>
                                <p>{{ $nxb_book->book_name }}</p>
                            </a>
                            <form action="{{ URL::to('/save-cart') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="product_id_hidden" value="{{ $nxb_book->book_id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn btn-default add-to-cart"><i
                                        class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng</button>
                            </form>
                        </div>
                    </div>
                    <div class="choose">
                        <ul class="nav nav-pills nav-justified">
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!--features_items-->
    {{-- PHÂN TRANG --}}
    <footer class="panel-footer" style="background-color: white; float: right;">
        <div class="row">
            <div class="col-sm-7 text-right text-center-xs" style="margin-right: 450px;margin-top: 25px;">
                <div class="col-sm-7 text-right text-center-xs">
                    {{ $nxb_by_id->links('pagination::bootstrap-4') }}
                    <!-- Sử dụng kiểu phân trang Bootstrap 4 -->
                </div>
            </div>
        </div>
    </footer>
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
