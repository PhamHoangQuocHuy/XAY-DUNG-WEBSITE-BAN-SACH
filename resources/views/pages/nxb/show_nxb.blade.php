@extends('layout')
@section('content')
    <div class="features_items">
        <!--features_items-->
        <h2 class="title text-center" style="padding-top: 5px">{{ $nxb_name }}</h2>

        @foreach ($nxb_by_id as $key => $nxb_book)
            <div class="col-sm-4">
                <div class="product-image-wrapper">
                    <div class="single-products">
                        <div class="productinfo text-center">
                            <img src="{{ URL::to('public/uploads/product/' . $nxb_book->image) }}" alt="" />
                            <h2>{{ number_format($nxb_book->price, 0, ',', '.') }} VNĐ</h2>
                            <p>{{ $nxb_book->book_name }}</p>
                            <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào
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
        @endforeach
    </div>
    <!--features_items-->
@endsection
