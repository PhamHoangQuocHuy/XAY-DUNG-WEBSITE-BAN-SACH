@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    SỬA NHÀ CUNG CẤP
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
                    @foreach ($edit_supplier as $key => $edit_value)
                        <div class="position-center">
                            <form role="form" action="{{ URL::to('/update-supplier/' . $edit_value->supplier_id) }}"
                                method="POST">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tên nhà cung cấp</label>
                                    <input type="text" value="{{ $edit_value->supplier_name }}" name="supplier_name" class="form-control" id="exampleInputEmail1"
                                        placeholder="Điền tên nhà cung cấp" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Số điện thoại nhà cung cấp</label>
                                    <input type="text" value="{{ $edit_value->supplier_phone }}" name="supplier_phone" class="form-control" id="exampleInputEmail1"
                                        placeholder="Điền số điện nhà cung cấp" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email nhà cung cấp</label>
                                    <input type="email" value="{{ $edit_value->supplier_email }}" name="supplier_email" class="form-control" id="exampleInputEmail1"
                                        placeholder="Điền email nhà cung cấp" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Địa chỉ nhà cung cấp</label>
                                    <input type="text" value="{{ $edit_value->supplier_address }}" name="supplier_address" class="form-control" id="exampleInputEmail1"
                                        placeholder="Điền địa chỉ nhà cung cấp" autocomplete="off">
                                </div>    
                                <button type="submit" name="update_supplier" class="btn btn-info">Cập nhật thông tin nhà cung cấp</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>
@endsection
