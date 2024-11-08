@extends('layout')
@section('content')
<div class="container">
    <div class="row">
        <!-- Thông tin người dùng -->
        <div class="col-md-6" id="login-section">
            <div class="card">
                <div class="card-header">
                    <h2>Thông tin tài khoản</h2>
                </div>
                <div class="card-body">
                    <p><strong>Tên tài khoản:</strong> {{ $user->username }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Họ tên:</strong> {{ $user->fullname }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $user->address }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $user->phone }}</p>
                    <button class="btn btn-primary" id="edit-info-button">Sửa thông tin</button>
                </div>
            </div>
        </div>

        <!-- Form sửa thông tin người dùng -->
        <div class="col-md-6" id="edit-info-form" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h2>Cập nhật thông tin</h2>
                </div>
                <div class="card-body">
                    <form action="{{ url('/user-info/' . $user->user_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="username">Tên tài khoản:</label>
                            <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="form-group">
                            <label for="fullname">Họ tên:</label>
                            <input type="text" name="fullname" class="form-control" value="{{ $user->fullname }}" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Địa chỉ:</label>
                            <input type="text" name="address" class="form-control" value="{{ $user->address }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Số điện thoại:</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
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

<script>
    document.getElementById('edit-info-button').addEventListener('click', function() {
        var form = document.getElementById('edit-info-form');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    });
</script>
@endsection
<script>
    // Tự động cuộn xuống phần form đăng nhập khi trang được tải
    document.addEventListener("DOMContentLoaded", function() {
        var loginSection = document.getElementById("login-section");
        if (loginSection) {
            loginSection.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
</script>
