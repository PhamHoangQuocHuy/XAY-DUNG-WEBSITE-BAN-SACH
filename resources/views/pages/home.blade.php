{{-- HIỂN THỊ CÁC SẢN PHẨM Ở TRANG CHỦ --}}
@extends('layout')
@section('content')
    {{-- đặt tên section và gọi bên layout bằng hàm @yield('cùng tên với section') và phải @endsection ở cuối --}}

    <div class="features_items">
        <!--features_items-->
        <h2 class="title text-center">SẢN PHẨM MỚI NHẤT</h2>
        @foreach ($all_book as $key => $book)
            <a href="{{ URL::to('/chi-tiet-san-pham-theo-trang-chu/' . $book->book_id) }}">
                <div class="col-sm-4">
                    <div class="product-image-wrapper">
                        <div class="single-products">
                            <div class="productinfo text-center">
                                <img src="{{ URL::to('public/uploads/product/' . $book->image) }}" alt="" />
                                <h2>{{ $book->formatted_price = number_format($book->price, 0, ',', '.') }} VNĐ</h2>
                                <p>{{ $limitWordsFunc($book->book_name, 8) }}</p>
                                <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm
                                    vào
                                    giỏ hàng</a>
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
            </a>
        @endforeach
    </div>
    <!--features_items-->
@endsection
