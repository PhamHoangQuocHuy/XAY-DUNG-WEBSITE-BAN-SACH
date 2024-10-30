@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    CẬP NHẬT THÔNG TIN SÁCH
                </header>
                <!-- Hiển thị thông báo lỗi -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <?php
                $message = Session::get('message');
                if ($message) {
                    // Kiểm tra thông báo
                    if (strpos($message, 'không thành công') !== false) {
                        echo '<span style="color: red;">' . $message . '</span>';
                    } else {
                        echo '<span style="color: green;">' . $message . '</span>';
                    }
                    Session::put('message', null);
                }
                ?>
                <div class="panel-body">
                    <div class="position-center">
                        @foreach ($edit_book as $key => $book)
                            <form role="form" action="{{ URL::to('/update-book/'.$book->book_id) }}" method="POST"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên sách</label>
                                    <input type="text" name="book_name" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->book_name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Số ISBN</label>
                                    <input type="text" name="book_isbn" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->isbn }}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Tác giả</label>
                                    <select name="book_author" class="form-control input-sm m-bot15" required>
                                        @foreach ($author_book as $key => $author)
                                            @if ($author->author_id == $book->author_id)
                                                <option selected value="{{ $author->author_id }}">{{ $author->author_name }}
                                                </option>
                                            @else
                                                <option value="{{ $author->author_id }}">{{ $author->author_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Danh mục</label>
                                    <select name="book_category" class="form-control input-sm m-bot15" required>
                                        @foreach ($category_book as $key => $cate)
                                            @if ($cate->category_id == $book->category_id)
                                                <option selected value="{{ $cate->category_id }}">
                                                    {{ $cate->category_name }}</option>
                                            @else
                                                <option value="{{ $cate->category_id }}">{{ $cate->category_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Nhà cung cấp</label>
                                    <select name="book_supplier" class="form-control input-sm m-bot15" required>
                                        @foreach ($supplier_book as $key => $sup)
                                            @if ($sup->supplier_id == $book->supplier_id)
                                                <option selected value="{{ $sup->supplier_id }}">{{ $sup->supplier_name }}
                                                </option>
                                            @else
                                                <option value="{{ $sup->supplier_id }}">{{ $sup->supplier_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Nhà xuất bản</label>
                                    <input type="text" name="book_publisher" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->publisher }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Ngày xuất bản</label>
                                    <input type="date" name="book_publication_date" class="form-control"
                                        id="exampleInputEmail1" value="{{ $book->publication_date }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Số lượng sách</label>
                                    <input type="text" name="book_quantity" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->quantity }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Giá bán</label>
                                    <input type="text" name="book_price" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->price }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Mô tả của sách</label>
                                    <textarea type="text" style="resize: none" rows="8" name="book_description" class="form-control"
                                        id="exampleInputEmail1" required>{{ $book->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Hình ảnh sách</label>
                                    <input type="file" name="book_image" class="form-control" id="exampleInputEmail1">
                                    <img src="{{ URL::to('public/uploads/product/' . $book->image) }}" alt=""
                                        height="100" width="100">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Ngôn ngữ</label>
                                    <input type="text" name="book_language" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->language }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Từ khóa</label>
                                    <input type="text" name="book_tags" class="form-control" id="exampleInputEmail1"
                                        value="{{ $book->tags }}" required>
                                </div>
                                <button type="submit" name="update_book" class="btn btn-info">
                                    Cập nhật thông tin sách
                                </button>
                            </form>
                        @endforeach
                    </div>

                </div>
            </section>

        </div>
    </div>
@endsection
