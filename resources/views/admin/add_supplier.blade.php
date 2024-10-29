@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    THÊM NHÀ CUNG CẤP
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
                        <form role="form" action="{{ URL::to('/save-supplier') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tên nhà cung cấp</label>
                                <input type="text" name="supplier_name" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền tên nhà cung cấp" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Số điện thoại nhà cung cấp</label>
                                <input type="text" name="supplier_phone" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền số điện nhà cung cấp" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email nhà cung cấp</label>
                                <input type="email" name="supplier_email" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền email nhà cung cấp" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Địa chỉ nhà cung cấp</label>
                                <input type="text" name="supplier_address" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền tên nhà cung cấp" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Trạng thái</label>
                                <select name="supplier_status" class="form-control input-sm m-bot15">
                                    <option value="Kích hoạt">Kích hoạt</option>
                                    <option value="Không kích hoạt">Không kích hoạt</option>
                                </select>
                            </div>

                            <button type="submit" name="add_supplier" class="btn btn-info">Thêm nhà cung cấp</button>
                        </form>
                    </div>

                </div>
            </section>

        </div>
    </div>
@endsection
