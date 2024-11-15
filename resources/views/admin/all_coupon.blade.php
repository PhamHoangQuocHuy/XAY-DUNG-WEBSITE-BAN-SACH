@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ NHÀ CUNG CẤP
            </div>
            <div class="row w3-res-tb">
                <div class="col-sm-5">
                </div>
                <div class="col-sm-7">
                    <div class="input-group" style="display: flex">
                        <form action="{{ URL::to('/search-coupon') }}" method="GET">
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
                                    <input type="checkbox" id="check-all"><i></i>
                                </label>
                            </th>
                            <th style="text-align: center;color: black">MÃ COUPON</th>
                            <th style="text-align: center;color: black">PHẦN TRĂM GIẢM GIÁ</th>
                            <th style="text-align: center;color: black">NGÀY HẾT HẠN</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI</th>
                            <th style="text-align: center; width: 100px;color: black">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($errors->any())
                            <div class="alert alert-danger"> {{ $errors->first() }} </div>
                        @endif
                        @foreach ($all_coupon as $key => $cp)
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox"><i></i>
                                    </label>
                                </th>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $cp->coupon_code }}</td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $cp->discount }}%</td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $cp->expiration_date }}</td>
                                <td style="text-align: center;align-content: center"><span class="text-ellipsis">
                                        @if ($cp->coupon_status != 'inactive')
                                            <a href="{{ URL::to('/active-coupon/' . $cp->coupon_id) }}"><span
                                                    class="fa-toggle-styling fa fa-toggle-on"></span></a>
                                        @else
                                            <a href="{{ URL::to('/inactive-coupon/' . $cp->coupon_id) }}"><span
                                                    class="fa-toggle-styling fa fa-toggle-off"></span></a>
                                        @endif
                                    </span></td>
                                <td style="text-align: center; align-content: center;">
                                    <a href="{{ URL::to('edit-coupon/' . $cp->coupon_id) }}" class="active"
                                        ui-toggle-class="">
                                        <i
                                            class="fa fa-wrench text-info text-active"style="color: #007bff; font-size: 30px; margin: 0 5px;"></i>
                                    </a>
                                    <a onclick="return confirm('Bạn có chắc muốn xóa mã coupon: {{ $cp->coupon_code }}?')"
                                        href="{{ URL::to('delete-coupon/' . $cp->coupon_id) }}">
                                        <i class="fa fa-trash text-danger" style="font-size: 30px;margin: 0 5px;"></i>
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
                            <!-- Chỉ hiển thị links nếu là Paginator -->
                            @if ($all_coupon instanceof \Illuminate\Pagination\LengthAwarePaginator) {{ $all_coupon->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
