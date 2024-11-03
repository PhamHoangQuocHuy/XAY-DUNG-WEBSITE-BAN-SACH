{{-- HIỂN THỊ CÁC SẢN PHẨM TÌM ĐƯỢC --}}
@extends('layout')
@section('content')
    <div class="features_items" id="search-results">
        <!--features_items-->
        <h2 class="title text-center">KẾT QUẢ TÌM KIẾM</h2>

        @if ($errors->any())
            <div class="alert alert-danger text-center" role="alert">
                KẾT QUẢ TÌM KIẾM: Không tìm thấy sách nào trùng với từ khóa "{{ request()->keywords_submit }}"
            </div>
        @else
            @foreach ($search_product as $key => $book)
                <a href="{{ URL::to('/chi-tiet-san-pham-theo-trang-chu/' . $book->book_id) }}">
                    <div class="col-sm-4">
                        <div class="product-image-wrapper">
                            <div class="single-products">
                                <div class="productinfo text-center">
                                    <img src="{{ URL::to('public/uploads/product/' . $book->image) }}" alt="" />
                                    <h2>{{ number_format($book->price, 0, ',', '.') }} VNĐ</h2>
                                    <p>{{ $limitWordsFunc($book->book_name, 8) }}</p>
                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng</a>
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
        @endif
    </div>
    <!--features_items-->
@endsection

<script>
    // Tự động cuộn xuống phần kết quả tìm kiếm khi trang được tải
    document.addEventListener("DOMContentLoaded", function() {
        var searchResults = document.getElementById("search-results");
        if (searchResults) {
            searchResults.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
</script>
