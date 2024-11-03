@extends('layout')
@section('content')
    @foreach ($product_details_nxb as $key => $value_nxb)
        <div class="product-details"><!--product-details-->
            <div class="col-sm-5">
                <div class="view-product">
                    <img src="{{ URL::to('public/uploads/product/' . $value_nxb->image) }}" alt="" />
                    <h3>ZOOM</h3>
                </div>
                <div id="similar-product" class="carousel slide" data-ride="carousel">

                    <!-- Wrapper for slides -->

                    <div class="carousel-inner">
                        <div class="item active">
                            @foreach ($relate_to_nxb as $key => $lienquan_nxb)
                                <a href="{{ URL::to('/chi-tiet-san-pham-theo-nxb/' . $lienquan_nxb->book_id) }}">
                                    <div class="col-sm-4">
                                        <div class="product-image-wrapper">
                                            <div class="single-products">
                                                <div class="productinfo text-center">
                                                    <img src="{{ URL::to('public/uploads/product/' . $lienquan_nxb->image) }}"
                                                        alt="" width="100%" height="70%" />
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
                    <h2 style="font-size:25px "><strong>{{ $value_nxb->book_name }}</strong></h2>
                    <p>Mã ISBN: {{ $value_nxb->isbn }}</p>
                    <form action="{{ URL::to('/save-cart') }}" method="POST">
                        {{ csrf_field() }}
                        <img src="images/product-details/rating.png" alt="" />
                        <span>
                            <span>{{ $value_nxb->formatted_price = number_format($value_nxb->price, 0, ',', '.') }}
                                VNĐ</span>

                            <label style="padding-top: 15px">Số lượng:</label>
                            <input name="qty" style="padding-top: 0px;" type="number" min="1" value="1" />
                            <input name="product_id_hidden" style="padding-top: 0px;" type="hidden"
                                value="{{ $value_nxb->book_id }}" />
                            <div><button type="submit" class="btn btn-fefault cart">
                                    <i class="fa fa-shopping-cart"></i>
                                    Thêm vào giỏ hàng
                                </button></div>
                        </span>
                    </form>
                    <p><b>Tình trạng:</b>
                        @if ($value_nxb->status === 'active')
                            Còn hàng
                        @else
                            Hết hàng
                        @endif
                    </p>
                    <p><b>Tình trạng sách:</b> Mới 100%</p>
                    <p><b>Thể loại:</b> {{ $value_nxb->category_name }}</p>
                    <p><b>Tác giả:</b> {{ $value_nxb->author_name }}</p>
                    <p><b>Nhà xuất bản:</b> {{ $value_nxb->publisher }}</p>
                    <p><b>Ngày xuất bản:</b> {{ date('d/m/Y', strtotime($value_nxb->publication_date)) }}</p>
                    <p><b>Ngôn ngữ:</b> {{ $value_nxb->language }}</p>
                    <p><b>Từ khóa liên quan:</b> {{ $value_nxb->tags }}</p>

                    <a href=""><img src="images/product-details/share.png" class="share img-responsive"
                            alt="" /></a>
                </div><!--/product-information-->
            </div>
        </div><!--/product-details-->

        <div class="category-tab shop-details-tab"><!--category-tab-->
            <div class="col-sm-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab">THÔNG TIN CHI TIẾT</a></li>
                    <li><a href="#reviews" data-toggle="tab">ĐÁNH GIÁ (5)</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade  active in" id="details">
                    <h1 style="color: #FE980F;text-align: center">MÔ TẢ {{ $value_nxb->book_name }}</h1>
                    <p><strong>{!! $value_nxb->description !!}</strong></p>
                </div>
                <div class="tab-pane fade" id="reviews">
                    <div class="col-sm-12">
                        <ul>
                            <li><a href=""><i class="fa fa-user"></i>EUGEN</a></li>
                            <li><a href=""><i class="fa fa-clock-o"></i>12:41 PM</a></li>
                            <li><a href=""><i class="fa fa-calendar-o"></i>31 DEC 2014</a></li>
                        </ul>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore
                            et dolore magna aliqua.Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                            ut
                            aliquip ex ea commodo consequat.Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu fugiat nulla pariatur.</p>
                        <p><b>Write Your Review</b></p>

                        <form action="#">
                            <span>
                                <input type="text" placeholder="Your Name" />
                                <input type="email" placeholder="Email Address" />
                            </span>
                            <textarea name=""></textarea>
                            <b>Rating: </b> <img src="images/product-details/rating.png" alt="" />
                            <button type="button" class="btn btn-default pull-right">
                                Submit
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div><!--/category-tab-->
    @endforeach

    <div class="recommended_items"><!--recommended_items-->
        <h2 class="title text-center" style="padding-top: 10px">SẢN PHẨM LIÊN QUAN {{$value_nxb->publisher}}</h2>
        @if ($relate_to_nxb->isNotEmpty())
            <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="item active">
                        @foreach ($relate_to_nxb as $key => $lienquan_nxb)
                            <div class="col-sm-4">
                                <div class="product-image-wrapper">
                                    <div class="single-products">
                                        <div class="productinfo text-center">
                                            <img src="{{ URL::to('public/uploads/product/' . $lienquan_nxb->image) }}"
                                                alt="" />
                                            <h2>{{ number_format($lienquan_nxb->price, 0, ',', '.') }} VNĐ</h2>
                                            <p>{{ $lienquan_nxb->book_name }}</p>
                                            <a href="#" class="btn btn-default add-to-cart"><i
                                                    class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
                    <i class="fa fa-angle-left"></i>
                </a>
                <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        @else
            <h3 style="text-align: center">Hiện tại không có sản phẩm liên quan nào.</h3>
            <p style="text-align: center">Hãy khám phá các danh mục khác để tìm kiếm sản phẩm phù hợp!</p>
        @endif
    </div><!--/recommended_items-->
@endsection
