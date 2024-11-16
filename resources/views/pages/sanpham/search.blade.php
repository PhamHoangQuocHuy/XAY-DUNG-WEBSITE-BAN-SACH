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
                <div class="col-sm-4">
                    <div class="product-image-wrapper">
                        <div class="single-products">
                            <div class="productinfo text-center">
                                <a href="{{ URL::to('/chi-tiet-san-pham-theo-trang-chu/' . $book->book_id) }}">
                                    <img src="{{ URL::to('public/uploads/product/' . $book->image) }}" alt="" />
                                    <h2>{{ number_format($book->price, 0, ',', '.') }} VNĐ</h2>
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
                    </div>
                </div>
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
