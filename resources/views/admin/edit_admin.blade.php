@extends('admin_layout')
@section('admin_content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Chỉnh sửa thông tin admin</div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                {{ Session::get('message') }}
                            </div>
                        @endif

                        <form action="{{ route('update.admin', $admin_info->user_id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="username">Tên tài khoản:</label>
                                <input type="text" name="username" class="form-control"
                                    value="{{ $admin_info->username }}" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control" value="{{ $admin_info->email }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="fullname">Họ tên:</label>
                                <input type="text" name="fullname" class="form-control"
                                    value="{{ $admin_info->fullname }}" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Địa chỉ:</label>
                                <input type="text" name="address" class="form-control"
                                    value="{{ $admin_info->address }}">
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại:</label>
                                <input type="text" name="phone" class="form-control" value="{{ $admin_info->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="password">Mật khẩu mới (nếu bạn muốn thay đổi):</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Xác nhận mật khẩu mới:</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
