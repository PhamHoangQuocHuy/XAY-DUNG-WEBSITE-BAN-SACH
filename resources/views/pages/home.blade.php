@extends('layout')
@section('content')
    <div class="features_items">
        <!--features_items-->
        <h2 class="title text-center">SẢN PHẨM MỚI NHẤT</h2>
        @foreach ($all_book as $key => $book)
            <div class="col-sm-4">
                <div class="product-image-wrapper">
                    <div class="single-products">
                        <div class="productinfo text-center">
                            <a href="{{ URL::to('/chi-tiet-san-pham-theo-trang-chu/' . $book->book_id) }}">
                                <img src="{{ URL::to('public/uploads/product/' . $book->image) }}" alt="" />
                                <h2>{{ $book->formatted_price = number_format($book->price, 0, ',', '.') }} VNĐ</h2>
                                <p>{{ $limitWordsFunc($book->book_name, 8) }}</p>
                            </a>
                            <form action="{{ URL::to('/save-cart') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="product_id_hidden" value="{{ $book->book_id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng</button>
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
