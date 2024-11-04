@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                QUẢN LÝ NGƯỜI DÙNG
            </div>
            <div class="row w3-res-tb">
                <div class="col-sm-5 m-b-xs">
                    <select class="input-sm form-control w-sm inline v-middle">
                        <option value="0">Bulk action</option>
                        <option value="1">Delete selected</option>
                        <option value="2">Bulk edit</option>
                        <option value="3">Export</option>
                    </select>
                    <button class="btn btn-sm btn-default">Apply</button>
                </div>
                <div class="col-sm-4">
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input type="text" class="input-sm form-control" placeholder="Search">
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-default" type="button">Go!</button>
                        </span>
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
                            <th style="text-align: center;color: black">ID</th>
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
                        @foreach ($all_user as $key => $user)
                            <tr>
                                <td style="text-align: center;align-content: center">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox" name="post[]"><i></i>
                                    </label>
                                </td>
                                <td style="text-align: center;align-content: center; color: black">{{ $key + 1 }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->username }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->email }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->fullname }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->address }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $user->phone }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ ucfirst($user->role) }}</td>
                                <td style="text-align: center;align-content: center; color: black">{{ date('d/m/Y H:i:s', strtotime($user->register_date)) }}</td>
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
                    <div class="col-sm-5 text-center">
                        <small class="text-muted inline m-t-sm m-b-sm">showing 20-30 of 50 items</small>
                    </div>
                    <div class="col-sm-7 text-right text-center-xs">
                        <ul class="pagination pagination-sm m-t-none m-b-none">
                            <li><a href=""><i class="fa fa-chevron-left"></i></a></li>
                            <li><a href="">1</a></li>
                            <li><a href="">2</a></li>
                            <li><a href="">3</a></li>
                            <li><a href="">4</a></li>
                            <li><a href=""><i class="fa fa-chevron-right"></i></a></li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
