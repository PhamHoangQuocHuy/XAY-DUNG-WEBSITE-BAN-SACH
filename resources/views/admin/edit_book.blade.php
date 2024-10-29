@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    SỬA THÔNG TIN SÁCH
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
                    @foreach ($edit_book as $key => $edit_value)
                        <div class="position-center">
                            <form role="form" action="{{ URL::to('/update-book/' . $edit_value->book_id) }}"
                                method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên danh mục sách</label>
                                    <input type="text" value="{{ $edit_value->book_name }}" name="book_name"
                                        class="form-control" id="exampleInputEmail1" placeholder="Điền tên danh mục sách">
                                </div>

                                <button type="submit" name="update_book" class="btn btn-info">Cập nhật thông tin sách</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>
@endsection
