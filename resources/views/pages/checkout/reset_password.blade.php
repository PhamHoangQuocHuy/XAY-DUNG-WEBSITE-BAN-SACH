@extends('layout')
@section('content')
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/reset-password') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="form-group">
            <label for="password">Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Xác nhận mật khẩu mới</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu mới"
                required>
        </div>
        <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
    </form>
@endsection
