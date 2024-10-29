@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    THÊM TÁC GIẢ
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
                        <form role="form" action="{{ URL::to('/save-author') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tên tác giả</label>
                                <input type="text" name="author_name" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền tên tác giả" autocomplete="off">
                            </div>

                            <button type="submit" name="add_author" class="btn btn-info">Thêm tác giả</button>
                        </form>
                    </div>

                </div>
            </section>

        </div>
    </div>
@endsection
