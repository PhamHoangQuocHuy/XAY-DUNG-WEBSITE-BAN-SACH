@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    SỬA DANH MỤC SÁCH
                </header>
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
                    @foreach ($edit_category as $key => $edit_value)
                        <div class="position-center">
                            <form role="form" action="{{ URL::to('/update-category/' . $edit_value->category_id) }}"
                                method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên danh mục sách</label>
                                    <input type="text" value="{{ $edit_value->category_name }}" name="category_name"
                                        class="form-control" id="exampleInputEmail1" placeholder="Điền tên danh mục sách">
                                </div>

                                <button type="submit" name="update_category" class="btn btn-info">Cập nhật danh
                                    mục</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>
@endsection
