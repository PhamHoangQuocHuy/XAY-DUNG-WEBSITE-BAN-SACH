@extends('layout')
@section('content')
    <div class="features_items" id="login-section">
        <!--features_items-->
        @foreach ($tacgia_name as $key => $tg_name)
            <h2 class="title text-center" style="padding-top: 5px">TÁC GIẢ: {{ $tg_name->author_name }}</h2>
        @endforeach
        @foreach ($author_by_id as $key => $product)
            <div class="col-sm-4">
                <div class="product-image-wrapper">
                    <div class="single-products">
                        <div class="productinfo text-center">
                            <a href="{{ URL::to('/chi-tiet-san-pham-theo-author/' . $product->book_id) }}">
                                <img src="{{ URL::to('public/uploads/product/' . $product->image) }}" alt="" />
                                <h2>{{ $product->formatted_price = number_format($product->price, 0, ',', '.') }} VNĐ</h2>
                                <p>{{ $product->book_name }}</p>
                            </a>
                            <form action="{{ URL::to('/save-cart') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="product_id_hidden" value="{{ $product->book_id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn btn-default add-to-cart"><i
                                        class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng</button>
                            </form>
                        </div>
                    </div>
                    <div class="choose">
                        <ul class="nav nav-pills nav-justified">
                            <li><a href="#"><i class="fa fa-plus-square"></i>Thêm yêu thích</a></li>
                            <li><a href="#"><i class="fa fa-plus-square"></i>Thêm so sánh</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!--features_items-->
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
