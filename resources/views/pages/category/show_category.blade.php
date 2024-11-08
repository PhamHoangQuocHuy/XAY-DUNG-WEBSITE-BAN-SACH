{{-- SẢN PHẨM THEO DANH MỤC THỂ LOẠI --}}
@extends('layout')
@section('content')
    <div class="features_items" id="login-section">
        <!--features_items-->
        @foreach ($cate_name as $key => $name)
            <h2 class="title text-center" style="padding-top: 5px">{{ $name->category_name }}</h2>
        @endforeach
        @foreach ($category_by_id as $key => $book)
            <div class="col-sm-4">
                <div class="product-image-wrapper">
                    <div class="single-products">
                        <div class="productinfo text-center">
                            <a href="{{ URL::to('/chi-tiet-san-pham-theo-cate/' . $book->book_id) }}">
                                <img src="{{ URL::to('public/uploads/product/' . $book->image) }}" alt="" />
                                <h2>{{ $book->formatted_price = number_format($book->price, 0, ',', '.') }} VNĐ</h2>
                                <p>{{ $limitWordsFunc($book->book_name, 8) }}</p>
                            </a>
                            <form action="{{ URL::to('/save-cart') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="product_id_hidden" value="{{ $book->book_id }}">
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
                    {{ $category_by_id->links('pagination::bootstrap-4') }}
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
