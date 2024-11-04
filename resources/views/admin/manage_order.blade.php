@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ ĐƠN HÀNG
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
                            <th style="text-align: center;color: black">TÊN KHÁCH HÀNG</th>
                            <th style="text-align: center;color: black">TỔNG GIÁ TIỀN</th>
                            <th style="text-align: center;color: black">MÃ ĐƠN HÀNG</th>
                            <th style="text-align: center;color: black">NGÀY ĐẶT HÀNG</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI</th>
                            <th style="text-align: center; width: 100px;color: black">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($all_order as $key => $order)
                            <tr>
                                <td style="text-align: center;align-content: center"><label class="i-checks m-b-none"><input
                                            type="checkbox" name="post[]"><i></i></label></td>
                                <td style="text-align: center;align-content: center; color: black">{{ $order->username }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ number_format($order->order_total, 0, '', '.') }} VNĐ</td>
                                <td style="text-align: center;align-content: center; color: black">{{ $order->code_order }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">{{ $order->order_date }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $order->order_status }}</td>
                                <td style="text-align: center; align-content: center;">
                                    <a href="{{ URL::to('view-order/' . $order->order_id) }}" class="active"
                                        ui-toggle-class="">
                                        <i
                                            class="fa fa-eye text-info text-active"style="color: #007bff; font-size: 30px; margin: 0 5px;"></i>
                                    </a>
                                    <a onclick="return confirm ('Bạn có chắc muốn xóa đơn hàng này không ?')"
                                        href="{{ URL::to('delete-order/' . $order->order_id) }}">
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
