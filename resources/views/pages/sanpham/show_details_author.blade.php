@extends('layout')
@section('content')
    @foreach ($product_details_author as $key => $value_author)
        <div class="product-details" id="login-section"><!--product-details-->
            <div class="col-sm-5">
                <div class="view-product">
                    <img src="{{ URL::to('public/uploads/product/' . $value_author->image) }}" alt="" />
                    <h3>ZOOM</h3>
                </div>
                <div id="similar-product" class="carousel slide" data-ride="carousel">

                    <!-- Wrapper for slides -->

                    <div class="carousel-inner">
                        <div class="item active">
                            @foreach ($relate_to_author as $key => $lienquan_author)
                                <a href="{{ URL::to('/chi-tiet-san-pham-theo-author/' . $lienquan_author->book_id) }}">
                                    <div class="col-sm-4">
                                        <div class="product-image-wrapper">
                                            <div class="single-products">
                                                <div class="productinfo text-center">
                                                    <img src="{{ URL::to('public/uploads/product/' . $lienquan_author->image) }}"
                                                        alt="" width="100%" height="30%" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Controls -->
                    <a class="left item-control" href="#similar-product" data-slide="prev">
                        <i class="fa fa-angle-left"></i>
                    </a>
                    <a class="right item-control" href="#similar-product" data-slide="next">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>

            </div>
            <div class="col-sm-7">
                <div class="product-information"><!--/product-information-->
                    <img src="images/product-details/new.jpg" class="newarrival" alt="" />
                    <h2 style="font-size:25px "><strong>{{ $value_author->book_name }}</strong></h2>
                    <p>Mã ISBN: {{ $value_author->isbn }}</p>
                    <form action="{{ URL::to('/save-cart') }}" method="POST">
                        {{ csrf_field() }}
                        <img src="images/product-details/rating.png" alt="" />
                        <span>
                            <span>{{ $value_author->formatted_price = number_format($value_author->price, 0, ',', '.') }}
                                VNĐ</span>

                            <label style="padding-top: 15px">Số lượng:</label>
                            <input name="qty" style="padding-top: 0px;" type="number" min="1" value="1" />
                            <input name="product_id_hidden" style="padding-top: 0px;" type="hidden"
                                value="{{ $value_author->book_id }}" />
                            <div><button type="submit" class="btn btn-fefault cart">
                                    <i class="fa fa-shopping-cart"></i>
                                    Thêm vào giỏ hàng
                                </button></div>
                        </span>
                    </form>
                    <p><b>Tình trạng:</b>
                        @if ($value_author->status === 'active')
                            Còn hàng
                        @else
                            Hết hàng
                        @endif
                    </p>
                    <p><b>Tình trạng sách:</b> Mới 100%</p>
                    <p><b>Thể loại:</b> {{ $value_author->category_name }}</p>
                    <p><b>Tác giả:</b> {{ $value_author->author_name }}</p>
                    <p><b>Nhà xuất bản:</b> {{ $value_author->publisher }}</p>
                    <p><b>Ngày xuất bản:</b> {{ date('d/m/Y', strtotime($value_author->publication_date)) }}</p>
                    <p><b>Ngôn ngữ:</b> {{ $value_author->language }}</p>
                    <p><b>Từ khóa liên quan:</b> {{ $value_author->tags }}</p>

                    <a href=""><img src="images/product-details/share.png" class="share img-responsive"
                            alt="" /></a>
                </div><!--/product-information-->
            </div>
        </div><!--/product-details-->

        <div class="category-tab shop-details-tab"><!--category-tab-->
            <div class="col-sm-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab">THÔNG TIN CHI TIẾT</a></li>
                    <li><a href="#reviews" data-toggle="tab" data-book-id="{{ $value_author->book_id }}">XEM ĐÁNH GIÁ/ BÌNH
                            LUẬN</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade  active in" id="details">
                    <h1 style="color: #FE980F;text-align: center">MÔ TẢ {{ $value_author->book_name }}</h1>
                    <p><strong>{!! $value_author->description !!}</strong></p>
                </div>

                <div class="tab-pane fade" id="reviews">
                    <div class="col-sm-12">
                        {{-- EDIT SHOW REVIEW --}}
                        <style type="text/css">
                            .style_comment {
                                border: 1px solid #ddd;
                                border-radius: 10px;
                                background: #F0F0E9;
                            }

                            img.img.img-responsive.img-thumbnail.avatar-edit {
                                margin-top: 25px;
                                margin-bottom: 10px;
                                width: 80px;
                                border-radius: 100px;
                            }

                            .col-md-10.review-text-edit {
                                margin-left: -80px;
                                padding-top: 12px;
                            }
                        </style>
                        {{-- END --}}
                        {{-- HIỂN THỊ ĐÁNH GIÁ BÌNH LUẬN --}}
                        @if ($reviews->isNotEmpty())
                            @foreach ($reviews as $review)
                                <div class="row style_comment">
                                    <div class="col-md-2">
                                        <img width="100%" src="{{ asset('public/frontend/images/avatar_review.png') }}"
                                            class="img img-responsive img-thumbnail avatar-edit">
                                    </div>
                                    <div class="col-md-10 review-text-edit">
                                        <p style="color:blue;font-weight: bold">{{ $review->username }}</p>
                                        <p>{{ $review->comment }}</p>
                                        {{-- XÓA VÀ SỬA ĐÁNH GIÁ BÌNH LUẬN --}}
                                        @if (Session::get('user_id') == $review->user_id)
                                            <form action="{{ URL::to('/delete-review/' . $review->review_id) }}"
                                                method="POST" style="display: inline;">
                                                {{ csrf_field() }}
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-primary"
                                                    style="float: right; margin: -17px 25px; margin-right: 50px;">Xóa</button>
                                            </form>

                                            <form action="{{ URL::to('/edit-review/' . $review->review_id) }}"
                                                method="GET" style="display: inline;">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-primary"
                                                    style="float: right; margin: -17px">Sửa</button>
                                            </form>
                                        @endif

                                        <p>
                                        <div class="review-star-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <span class="fa fa-star-o checked" style="color:#f39c12; "></span>
                                                @else
                                                    <span class="fa fa-star-o"></span>
                                                @endif
                                            @endfor
                                        </div>
                                        </p>
                                        <p>Bình luận vào lúc: {{ date('d/m/Y H:i:s', strtotime($review->review_date)) }}
                                        </p>
                                    </div>
                                </div>
                                <br>
                            @endforeach
                        @else
                            <p style="font-size: 24px;margin-top: 10px;margin-bottom: 30px; font-weight: bold">Chưa có đánh
                                giá/ bình luận nào. Hãy là người đầu tiên cho chúng tôi biết cảm nghĩ của bạn
                                nhé!</p>
                        @endif


                        {{-- THÊM ĐÁNH GIÁ/ BÌNH LUẬN --}}
                        <?php
                        $user_id = Session::get('user_id');
                        if ($user_id != NULL) {
                        ?>
                        <form action="{{ URL::to('/save-review/' . $value_author->book_id) }}" method="POST">
                            {{ csrf_field() }}
                            <textarea name="comment" placeholder="Hãy cho chúng tôi biết cảm nghĩ của bạn !" required></textarea>
                            @if ($errors->has('comment'))
                                <span class="text-danger">{{ $errors->first('comment') }}</span>
                            @endif

                            <div class="star-rating">
                                <b>ĐÁNH GIÁ:</b>
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="fa fa-star" data-rating="{{ $i }}"></span>
                                @endfor
                                <input type="hidden" name="rating" class="rating-value" value="0">
                            </div>
                            @if ($errors->has('rating'))
                                <span class="text-danger">{{ $errors->first('rating') }}</span>
                            @endif
                            <button type="submit" class="btn btn-default pull-right">GỬI</button>
                        </form>


                        {{-- EDIT STAR --}}
                        <style>
                            .star-rating {
                                display: flex;
                            }

                            .fa-star {
                                font-size: 24px;
                                color: #ddd;
                                cursor: pointer;
                            }

                            .fa-star.hover,
                            .fa-star.checked {
                                color: #f39c12;
                            }
                        </style>
                        {{-- END EDIT STAR --}}
                        <?php
                        } else {
                        ?>
                        <a href="{{ url('/login-checkout') }}" class="btn btn-primary">Hãy đăng nhập để đánh giá/bình
                            luận</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div><!--/category-tab-->
    @endforeach

    <div class="recommended_items"><!--recommended_items-->
        <h2 class="title text-center" style="padding-top: 10px">SẢN PHẨM LIÊN QUAN {{ $value_author->author_name }}</h2>
        <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                @if ($relate_to_author->isNotEmpty())
                    <!-- Nếu có sản phẩm liên quan -->
                    <div class="item active">
                        @foreach ($relate_to_author as $key => $lienquan_author)
                            <div class="col-sm-4">
                                <div class="product-image-wrapper">
                                    <div class="single-products">
                                        <div class="productinfo text-center">
                                            <!-- Liên kết đến chi tiết sản phẩm khi nhấn vào hình ảnh -->
                                            <a
                                                href="{{ URL::to('/chi-tiet-san-pham-theo-author/' . $lienquan_author->book_id) }}">
                                                <img src="{{ URL::to('public/uploads/product/' . $lienquan_author->image) }}"
                                                    alt="" />
                                            </a>
                                            <!-- Liên kết đến chi tiết sản phẩm khi nhấn vào tên sách -->
                                            <h2>{{ number_format($lienquan_author->price, 0, ',', '.') }} VNĐ</h2>
                                            <p><a
                                                    href="{{ URL::to('/chi-tiet-san-pham-theo-author/' . $lienquan_author->book_id) }}">{{ $limitWordsFunc($lienquan_author->book_name, 8) }}</a>
                                            </p>
                                            <!-- Form thêm vào giỏ hàng -->
                                            <form action="{{ URL::to('/save-cart') }}" method="POST">
                                                {{ csrf_field() }}
                                                <input name="qty" type="hidden" value="1" />
                                                <input name="product_id_hidden" type="hidden"
                                                    value="{{ $lienquan_author->book_id }}" />
                                                <button type="submit" class="btn btn-default add-to-cart">
                                                    <i class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Nếu không có sản phẩm liên quan -->
                    <h3 style="text-align: center">Hiện tại không có sản phẩm liên quan nào.</h3>
                    <p style="text-align: center">Hãy khám phá các danh mục khác để tìm kiếm sản phẩm phù hợp!</p>
                @endif
            </div>
            <!-- Các nút điều khiển carousel, vẫn hiển thị dù không có sản phẩm liên quan -->
            <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div><!--/recommended_items-->
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
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const stars = document.querySelectorAll('.fa-star');
        let rating = document.querySelector('.rating-value');

        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                resetStars();
                this.classList.add('hover');
                let prevStar = this.previousElementSibling;

                while (prevStar) {
                    prevStar.classList.add('hover');
                    prevStar = prevStar.previousElementSibling;
                }
            });

            star.addEventListener('mouseout', function() {
                resetStars();
                setStars(rating.value);
            });

            star.addEventListener('click', function() {
                rating.value = this.dataset.rating;
                setStars(rating.value);
            });
        });

        function resetStars() {
            stars.forEach(star => {
                star.classList.remove('hover');
                star.classList.remove('checked');
            });
        }

        function setStars(value) {
            stars.forEach(star => {
                if (star.dataset.rating <= value) {
                    star.classList.add('checked');
                }
            });
        }

        setStars(rating.value);
    });
</script>
