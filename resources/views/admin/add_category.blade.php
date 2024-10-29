    @extends('admin_layout')
    @section('admin_content')
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        THÊM DANH MỤC SÁCH
                    </header>
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
                    <!-- Thông báo lỗi từ validation -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="panel-body">
                        <div class="position-center">
                            <form role="form" action="{{ URL::to('/save-category') }}" method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên danh mục sách</label>
                                    <input type="text" name="category_name" class="form-control" id="exampleInputEmail1"
                                        placeholder="Điền tên danh mục sách" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputPassword1">Trạng thái</label>
                                    <select name="category_status" class="form-control input-sm m-bot15">
                                        <option value="Kích hoạt">Kích hoạt</option>
                                        <option value="Không kích hoạt">Không kích hoạt</option>
                                    </select>
                                </div>
                                <button type="submit" name="add_category" class="btn btn-info">Thêm danh mục</button>
                            </form>
                        </div>

                    </div>
                </section>

            </div>
        </div>
    @endsection
