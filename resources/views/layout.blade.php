<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Home | Xây dựng website bán sách</title>
    <link href="{{ asset('public/frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/frontend/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/frontend/css/prettyPhoto.css') }}" rel="stylesheet">
    <link href="{{ asset('public/frontend/css/price-range.css') }}" rel="stylesheet">
    <link href="{{ asset('public/frontend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('public/frontend/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('public/frontend/css/responsive.css') }}" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="{{ asset('public/frontend/images/ico/favicon.ico') }}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57-precomposed.png">
</head>
<!--/head-->

<body>
    <header id="header">
        <!--header-->
        <div class="header_top">
            <!--header_top-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="contactinfo">
                            <ul class="nav nav-pills">
                                <li><a href="#" class="hover-effect" style="font-size: 15px;font-weight: bold"><i
                                            class="fa fa-phone"></i> +0764 514 276</a></li>
                                <li><a href="#" class="hover-effect" style="font-size: 15px;font-weight: bold"><i
                                            class="fa fa-envelope"></i> quochuy6422@gmail.com</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="social-icons pull-right">
                            <ul class="nav navbar-nav">
                                <li><a href="https://www.facebook.com/profile.php?id=100009328375081&mibextid=ZbWKwL"><i
                                            class="fa fa-facebook"></i></a></li>
                                {{-- <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                                <li><a href=""><i class="fa fa-google-plus"></i></a></li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/header_top-->

        <div class="header-middle">
            <!--header-middle-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="logo-edit pull-left">
                            <a href="{{ URL::to('/trang-chu') }}"><img
                                    src="{{ asset('public/frontend/images/logo.png') }}" alt="" /></a>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <?php
                        // Kiểm tra thông báo thành công
                        $message = Session::get('success');
                        if ($message) {
                            echo '<span style="color: green;">' . $message . '</span>';
                            Session::put('success', null); // Đặt lại thông báo sau khi hiển thị
                        }
                        
                        // Kiểm tra thông báo lỗi từ phương thức validate hoặc lỗi khác
                        $errors = Session::get('errors');
                        if ($errors) {
                            foreach ($errors->all() as $error) {
                                echo '<span style="color: red;">' . $error . '</span>';
                            }
                            Session::forget('errors'); // Đặt lại lỗi sau khi hiển thị
                        }
                        ?>
                        <div class="shop-menu pull-right">
                            <ul class="nav navbar-nav">


                                {{-- HIỆN TÊN USERNAME --}}
                                <?php
                                $user_id = Session::get('user_id');
                                if($user_id!=NULL){
                                ?>

                                <li class="dropdown ">
                                    <a href="#" style="font-size:20px;font-weight: bold;color: blue "><i
                                            class="fa fa-user"></i> {{ Session::get('username') }}<i
                                            class="fa fa-angle-down"></i></a>
                                    <ul role="menu" class="sub-menu">
                                        <li>
                                            <!-- Thông tin -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading"
                                                    style="background-color: rgba(0, 0, 0, 0.6);">
                                                    <h4 class="panel-title">
                                                        <a class="hover-effect"
                                                            style="font-size: 12px; font-weight: bold;background-color: rgb(108 99 99 / 60%);"
                                                            href="{{ url('/user-info/' . Session::get('user_id')) }}">
                                                            <i class="fa fa-info-circle" style="color: #FFD43B;"></i>
                                                            Thông tin tài khoản
                                                        </a>
                                                    </h4>
                                                </div>
                                            </div>

                                            <!-- Đơn hàng đã mua -->
                                            <div class="panel panel-default">
                                                <div class="panel-heading"
                                                    style="background-color: rgba(0, 0, 0, 0.6);">
                                                    <h4 class="panel-title">
                                                        <a class="hover-effect"
                                                            style="font-size: 12px; font-weight: bold;background-color: rgb(108 99 99 / 60%);"
                                                            href="{{ url('/user-orders-history/' . Session::get('user_id')) }}">
                                                            <i class="fa-solid fa-clock-rotate-left"
                                                                style="color: #FFD43B;"></i>
                                                            Đơn hàng đã mua
                                                        </a>
                                                    </h4>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>

                                <?php
                                }else {
                                ?>
                                {{-- KHÔNG HIỆN GÌ NẾU NGƯỜI DÙNG CHƯA ĐĂNG NHẬP --}}
                                <?php
                                    }
                                ?>


                                {{-- <li><a href="#"><i class="fa fa-star"></i> DANH SÁCH YÊU THÍCH</a></li> --}}

                                {{-- THANH TOÁN --}}
                                <?php
                                $user_id = Session::get('user_id');
                                if($user_id!=NULL && Cart::count()>0){
                                ?>
                                <li><a href="{{ URL::to('/checkout') }}"><i class="fa fa-crosshairs"></i> THANH
                                        TOÁN</a></li>
                                <?php
                                }else {
                                ?>
                                {{-- KHÔNG HIỆN GÌ NẾU CHƯA ĐĂNG NHẬP HOẶC CHƯA CÓ SẢN PHẨM TRONG GIỎ HÀNG --}}
                                <?php
                                    }
                                ?>


                                <li><a href="{{ URL::to('/show-cart') }}"><i class="fa fa-shopping-cart"></i> GIỎ
                                        HÀNG</a></li>

                                {{-- HIỆN ĐĂNG NHẬP/ ĐĂNG XUẤT --}}
                                <?php
                                $user_id = Session::get('user_id');
                                if($user_id!=NULL){
                                ?>
                                <li><a href="{{ URL::to('/logout-checkout') }}"><i class="fa fa-lock"></i> ĐĂNG
                                        XUẤT</a>
                                    <?php
                                }else {
                                ?>
                                <li><a href="{{ URL::to('/login-checkout') }}"><i class="fa fa-lock"></i> ĐĂNG
                                        NHẬP</a>
                                    <?php
                                    }
                                ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/header-middle-->

        <div class="header-bottom">
            <!--header-bottom-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="mainmenu pull-left">
                            <ul class="nav navbar-nav collapse navbar-collapse">
                                <li><a href="{{ URL::to('/trang-chu') }}" class="active"
                                        style="padding-left: 50px;">TRANG CHỦ</a></li>
                                <li class="dropdown"><a href="#">DANH MỤC SÁCH<i
                                            class="fa fa-angle-down"></i></a>
                                    <ul role="menu" class="sub-menu">
                                        <li>
                                            @foreach ($category as $key => $cate)
                                                <!--category-productsr-->
                                                <div class="panel panel-default">
                                                    <div class="panel-heading"
                                                        style="background-color: rgba(0, 0, 0, 0.6);">
                                                        <h4 class="panel-title">
                                                            <a class="hover-effect"
                                                                style="font-size: 15px;font-weight: bold"
                                                                href="{{ URL::to('/danh-muc-sach/' . $cate->category_id) }}"><i
                                                                    class="fa-brands fa-web-awesome"
                                                                    style="color: #FFD43B;"></i>{{ $cate->category_name }}</a>
                                                        </h4>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="{{ URL::to('/show-cart') }}">GIỎ HÀNG</a></li>
                                <li><a href="{{ URL::to('/show-coupons') }}">COUPONS</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <form action="{{ URL::to('/tim-kiem') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="search_box pull-right">
                                <input type="text" name="keywords_submit" style="border: solid"
                                    placeholder="Tìm kiếm sản phẩm" autocomplete="off" />
                                <input type="submit" class="btn btn-primary btn-sm"
                                    style="margin-top: 0px;color: black;margin-left: -4px;padding-right: 15px;margin-right: 30px;"
                                    value="Tìm kiếm" name="search_items">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/header-bottom-->
    </header>
    <!--/header-->

    <section id="slider">
        <!--slider-->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div id="slider-carousel" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#slider-carousel" data-slide-to="0" class="active"></li>
                            <li data-target="#slider-carousel" data-slide-to="1"></li>
                            <li data-target="#slider-carousel" data-slide-to="2"></li>
                        </ol>

                        <div class="carousel-inner">
                            <div class="item active">
                                <div class="col-sm-6">
                                    <h1><span>BOOK.VN</span></h1>
                                    <h2>SALE ALL KIND OF BOOK ON THE WORLD</h2>
                                </div>
                                <div class="col-sm-6">
                                    <img src="{{ asset('public/frontend/images/slide1.jpg') }}"
                                        class="girl img-responsive" alt="" />
                                </div>
                            </div>
                            <div class="item">
                                <div class="col-sm-6">
                                    <h1><span>BOOK.VN</span></h1>
                                    <h2>100% NEW BOOK</h2>
                                </div>
                                <div class="col-sm-6">
                                    <img src="{{ asset('public/frontend/images/slide2.jpg') }}"
                                        class="girl img-responsive" alt="" />
                                </div>
                            </div>

                            <div class="item">
                                <div class="col-sm-6">
                                    <h1><span>BOOK.VN</span></h1>
                                    <h2>Free Delivery</h2>
                                </div>
                                <div class="col-sm-6">
                                    <img src="{{ asset('public/frontend/images/slide3.jpg') }}"
                                        class="girl img-responsive" alt="" />
                                </div>
                            </div>

                        </div>

                        <a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!--/slider-->

    <section>
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <div class="left-sidebar">
                        <h2>DANH MỤC SÁCH</h2>
                        <div class="panel-group category-products" id="accordian">
                            @foreach ($category as $key => $cate)
                                <!--category-productsr-->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="hover-effect" style="font-size: 15px;font-weight: bold"
                                                href="{{ URL::to('/danh-muc-sach/' . $cate->category_id) }}"><i
                                                    class="fa-brands fa-web-awesome"
                                                    style="color: #FFD43B;"></i>{{ $cate->category_name }}</a>
                                        </h4>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!--/category-products-->

                        <div class="brands_products"><!--brands_products-->
                            <h2>TÁC GIẢ NỔI TIẾNG</h2>
                            <div class="brands-name">
                                <ul class="nav nav-pills nav-stacked">
                                    <li>
                                        @foreach ($tacgia_book as $key => $tacgia)
                                            <a class="hover-effect" style="font-size: 15px;font-weight: bold"
                                                href="{{ URL::to('/danh-muc-tac-gia/' . $tacgia->author_id) }}">{{ $tacgia->author_name }}</i></a>
                                        @endforeach
                                    </li>
                                </ul>
                            </div>
                        </div><!--/brands_products-->

                        <div class="brands_products"><!--brands_products-->
                            <h2>NHÀ XUẤT BẢN NỔI TIẾNG</h2>
                            <div class="brands-name">
                                <ul class="nav nav-pills nav-stacked">
                                    <li>
                                        @foreach ($publisher_list as $key => $publisher)
                                            <a class="hover-effect" style="font-size: 15px;font-weight: bold"
                                                href="{{ URL::to('/danh-muc-nxb/' . $publisher->book_id) }}">{{ $publisher->publisher }}</a>
                                        @endforeach
                                    </li>
                                </ul>
                            </div>
                        </div><!--/brands_products-->
                    </div>
                </div>
                <div class="col-sm-9 padding-right">
                    @yield('content')
                </div>
            </div>
        </div>
    </section>

    <footer id="footer">
        <!--Footer-->
        {{-- PAYMENT --}}
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="companyinfo">
                            <h2><span style="font-weight: bold">Payment Methods</span></h2>
                            <img class="payment_styling"
                                src="{{ asset('public/frontend/images/payment_methods.jpg') }}" alt="">
                        </div>
                    </div>
                    <div class="col-sm-3" style="float: right">
                        <div class="address">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.954410425893!2d106.67525717480439!3d10.73799718940847!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f62a90e5dbd%3A0x674d5126513db295!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBDw7RuZyBuZ2jhu4cgU8OgaSBHw7Ju!5e0!3m2!1svi!2s!4v1730443425775!5m2!1svi!2s"
                                width="600" height="150" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-widget">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2 class="hover-effect" style="font-size: 20px;font-weight: bold">Dịch Vụ</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="#">Giúp Đỡ Trực Tuyến</a></li>
                                <li><a href="#">Liên Hệ Chúng Tôi</a></li>
                                <li><a href="#">Trạng Thái Đơn Hàng</a></li>
                                <li><a href="#">Thay Đổi Địa Điểm</a></li>
                                <li><a href="#">Câu Hỏi Thường Gặp</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2 class="hover-effect" style="font-size: 20px;font-weight: bold">BOOK.VN</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="#">Sách thiếu nhi</a></li>
                                <li><a href="#">Sách nuôi dạy con</a></li>
                                <li><a href="#">Sách tiếng anh</a></li>
                                <li><a href="#">Sách kĩ năng sống</a></li>
                                <li><a href="#">Truyện thiếu nhi</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2 class="hover-effect" style="font-size: 20px;font-weight: bold">Các Chính Sách</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="#">Điều Khoản Sử Dụng</a></li>
                                <li><a href="#">Chính Sách Bảo Mật</a></li>
                                <li><a href="#">Chính Sách Hoàn Tiền</a></li>
                                <li><a href="#">Hệ Thống Thanh Toán</a></li>
                                <li><a href="#">Hệ Thống Vé</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="single-widget">
                            <h2 class="hover-effect" style="font-size: 20px;font-weight: bold">Về BOOK.VN</h2>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="#">Thông Tin Công Ty</a></li>
                                <li><a href="#">Cơ Hội Nghề Nghiệp</a></li>
                                <li><a href="#">Địa Điểm Cửa Hàng</a></li>
                                <li><a href="#">Chương Trình Liên Kết</a></li>
                                <li><a href="#">Bản Quyền</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-3 col-sm-offset-1">
                        <div class="single-widget">
                            <h2 class="hover-effect" style="font-size: 20px;font-weight: bold">Giới thiệu về BOOK.VN
                            </h2>
                            <form action="#" class="searchform">
                                <input type="text" placeholder="Địa chỉ email của bạn" />
                                <button type="submit" class="btn btn-default"><i
                                        class="fa fa-arrow-circle-o-right"></i></button>
                                <p>Nhận những cập nhật mới nhất từ <br />trang web của chúng tôi và cập nhật cho chính
                                    bạn...</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- TÍCH HỢP AI CHAT BOX --}}
        <div class="chat-icon" onclick="toggleChatBox()"></div>
        <div class="chat-box"> <!-- Chatbot widget -->
            <iframe allow="microphone;" width="350" height="430"
                src="https://console.dialogflow.com/api-client/demo/embedded/f27a1e4b-b1e5-4324-8e81-04981f4fa0c6">
            </iframe>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <p class="pull-left">Copyright © 2024 BOOK.VN Inc. All rights reserved.</p>
                    <p class="pull-right">Designed by <span><a target="_blank" href="#">PHẠM HOÀNG QUỐC
                                HUY</a></span></p>
                </div>
            </div>
        </div>

    </footer>
    <!--/Footer-->



    <script src="{{ asset('public/frontend/js/jquery.js') }}"></script>
    <script src="{{ asset('public/frontend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/frontend/js/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ asset('public/frontend/js/price-range.js') }}"></script>
    <script src="{{ asset('public/frontend/js/jquery.prettyPhoto.js') }}"></script>
    <script src="{{ asset('public/frontend/js/main.js') }}"></script>
</body>

</html>
<script>
    function toggleChatBox() {
        var chatBox = document.querySelector('.chat-box');
        if (chatBox.style.display === 'none' || chatBox.style.display === '') {
            chatBox.style.display = 'block';
        } else {
            chatBox.style.display = 'none';
        }
    }
</script>
<style>
    .chat-icon {
        position: fixed;
        bottom: 500px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: url('{{ asset('public/frontend/images/chat-icon.avif') }}') no-repeat center center;
        border-radius: 50px;
        border: solid 2px black;
        background-size: cover;
        cursor: pointer;
        z-index: 1000;
    }

    .chat-box {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 350px;
        height: 430px;
        z-index: 1000;
        border: 1px solid #ccc;
        background-color: #fff;
    }

    .chat-icon:hover {
        animation: gentle-shake-hover 0.5s infinite;
    }

    @keyframes gentle-shake-hover {
        0% {
            transform: translate(0, 0) rotate(0deg);
        }

        10% {
            transform: translate(-1px, 0) rotate(-1deg);
        }

        20% {
            transform: translate(1px, 1px) rotate(1deg);
        }

        30% {
            transform: translate(-1px, -1px) rotate(0deg);
        }

        40% {
            transform: translate(1px, 0) rotate(1deg);
        }

        50% {
            transform: translate(-1px, 1px) rotate(0deg);
        }

        60% {
            transform: translate(1px, -1px) rotate(-1deg);
        }

        70% {
            transform: translate(0, 1px) rotate(1deg);
        }

        80% {
            transform: translate(1px, -1px) rotate(0deg);
        }

        90% {
            transform: translate(0, 1px) rotate(-1deg);
        }

        100% {
            transform: translate(-1px, 0) rotate(0deg);
        }
    }

    .chat-icon::after,
    .chat-icon::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        background-color: transparent;
        display: none;
    }

    .chat-icon:hover::after,
    .chat-icon:hover::before {
        display: block;
        animation: signal 1.5s infinite;
    }

    .chat-icon::before {
        width: 60px;
        height: 60px;
        border: 2px solid red;
        opacity: 0.7;
    }

    .chat-icon::after {
        width: 80px;
        height: 80px;
        border: 2px solid red;
        animation-delay: 0.5s;
        opacity: 0.5;
    }

    .chat-icon:hover .vibration span {
        display: block;
        position: absolute;
        border-radius: 50%;
        background-color: red;
        animation: pulse 1.5s infinite;
    }

    .vibration {
        display: none;
    }

    .chat-icon:hover .vibration {
        display: block;
    }

    .vibration span:nth-child(1) {
        width: 40px;
        height: 40px;
        top: -10px;
        left: -10px;
    }

    .vibration span:nth-child(2) {
        width: 60px;
        height: 60px;
        top: -20px;
        left: -20px;
        animation-delay: 0.5s;
    }

    .vibration span:nth-child(3) {
        width: 80px;
        height: 80px;
        top: -30px;
        left: -30px;
        animation-delay: 1s;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.7;
        }

        50% {
            transform: scale(1.5);
            opacity: 0.3;
        }

        100% {
            transform: scale(2);
            opacity: 0;
        }
    }
</style>
<div class="vibaration">
    <span></span>
    <span></span>
    <span></span>
</div>
