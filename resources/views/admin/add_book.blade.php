@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    THÊM SÁCH
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
                        <form role="form" action="{{ URL::to('/save-book') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tên sách</label>
                                <input type="text" name="book_name" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền tên sách" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Số ISBN</label>
                                <input type="text" name="book_isbn" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền mã số quốc tế của sách">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Tác giả</label>
                                <select name="book_author" class="form-control input-sm m-bot15" required>
                                    @foreach ($author_book as $key => $author)
                                        <option value="{{ $author->author_id }}">{{ $author->author_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Danh mục</label>
                                <select name="book_category" class="form-control input-sm m-bot15" required>
                                    @foreach ($category_book as $key => $cate)
                                        <option value="{{ $cate->category_id }}">{{ $cate->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Nhà cung cấp</label>
                                <select name="book_supplier" class="form-control input-sm m-bot15" required>
                                    @foreach ($supplier_book as $key => $sup)
                                        <option value="{{ $sup->supplier_id }}">{{ $sup->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Nhà xuất bản</label>
                                <input type="text" name="book_publisher" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền nhà xuất bản của sách" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Ngày xuất bản</label>
                                <input type="date" name="book_publication_date" class="form-control"
                                    id="exampleInputEmail1" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Số lượng sách</label>
                                <input type="text" name="book_quantity" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền số lượng sách" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Giá bán</label>
                                <input type="text" name="book_price" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền giá bán của sách" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Mô tả của sách</label>
                                <textarea type="text" style="resize: none" rows="8" name="book_description" class="form-control"
                                    id="exampleInputEmail1" placeholder="Nhập mô tả sách" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Hình ảnh sách</label>
                                <input type="file" name="book_image" class="form-control" id="exampleInputEmail1"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Ngôn ngữ</label>
                                <input type="text" name="book_language" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền ngôn ngữ của sách" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Từ khóa</label>
                                <input type="text" name="book_tags" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền từ khóa" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Trạng thái</label>
                                <select name="book_status" class="form-control input-sm m-bot15">
                                    <option value="Kích hoạt">Kích hoạt</option>
                                    <option value="Không kích hoạt">Không kích hoạt</option>
                                </select>
                            </div>
                            <button type="submit" name="add_book" class="btn btn-info">Thêm sách</button>
                        </form>
                    </div>

                </div>
            </section>

        </div>
    </div>
@endsection
