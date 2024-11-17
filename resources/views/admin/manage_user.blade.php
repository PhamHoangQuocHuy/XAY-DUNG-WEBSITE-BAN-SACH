@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                QUẢN LÝ NGƯỜI DÙNG
            </div>
            <div class="row w3-res-tb">

                <div class="col-sm-5">
                </div>
                <div class="col-sm-7">
                    <div class="input-group" style="display: flex">
                        <form action="{{ URL::to('/search-user') }}" method="GET">
                            <input type="text" name="query" class="input-sm form-control" placeholder="Search">
                            <span class="input-group-btn">
                                <button class="btn btn-sm btn-primary" style="margin-right: -45px"
                                    type="submit">Tìm</button>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <?php
                $message = Session::get('message');
                if ($message) {
                    echo '<span style="color: green;">' . $message . '</span>';
                    Session::put('message', null);
                }
                ?>
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="width:20px;">
                                <label class="i-checks m-b-none">
                                    <input type="checkbox"><i></i>
                                </label>
                            </th>
                            <th style="text-align: center;color: black">TÊN TÀI KHOẢN</th>
                            <th style="text-align: center;color: black">EMAIL</th>
                            <th style="text-align: center;color: black">TÊN ĐẦY ĐỦ</th>
                            <th style="text-align: center;color: black">ĐỊA CHỈ</th>
                            <th style="text-align: center;color: black">SỐ ĐIỆN THOẠI</th>
                            <th style="text-align: center;color: black">VAI TRÒ</th>
                            <th style="text-align: center;color: black">NGÀY ĐĂNG KÝ</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI</th>
                            <th style="text-align: center; width: 100px;color: black">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($errors->any())
                            <div class="alert alert-danger"> {{ $errors->first() }} </div>
                        @endif
                        @foreach ($all_user as $key => $user)
                            <tr>
                                <td style="text-align: center;align-content: center">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox" name="post[]"><i></i>
                                    </label>
                                </td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->username }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->email }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->fullname }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->address }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->phone }}</td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ ucfirst($user->role) }}</td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ date('d/m/Y H:i:s', strtotime($user->register_date)) }}</td>
                                <td style="text-align: center;align-content: center"><span class="text-ellipsis">
                                        <?php
                                    if ($user->status != 'inactive') {
                                        ?>
                                        <a href="{{ URL::to('/active-user/' . $user->user_id) }}"><span
                                                class="fa-toggle-styling fa fa-toggle-on"></span></a>;
                                        <?php
                                    } else {
                                        ?>
                                        <a href="{{ URL::to('/inactive-user/' . $user->user_id) }}"><span
                                                class="fa-toggle-styling fa fa-toggle-off"></span></a>;
                                        <?php
                                    }
                                    ?>
                                    </span></td>
                                <td style="text-align: center; align-content: center;">
                                    <a onclick="return confirm('Bạn có chắc muốn xóa người dùng: {{ $user->username }}?')"
                                        href="{{ URL::to('delete-user/' . $user->user_id) }}">
                                        <i class="fa fa-trash text-danger" style="font-size: 30px; margin: 0 5px;"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-sm-7 text-right text-center-xs">
                        <div class="col-sm-7 text-right text-center-xs">
                            
                            @if ($all_user instanceof \Illuminate\Pagination\LengthAwarePaginator) {{ $all_user->links('pagination::bootstrap-4') }}
                            @endif
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
