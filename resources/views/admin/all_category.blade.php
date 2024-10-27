@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ DANH MỤC SÁCH
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
                <table class="table table-striped b-t b-light">
                    <thead>
                        <tr>
                            <th style="width:20px;">
                                <label class="i-checks m-b-none">
                                    <input type="checkbox"><i></i>
                                </label>
                            </th>
                            <th style="text-align: center;color: black">TÊN DANH MỤC</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI</th>
                            <th style="text-align: center;color: black">THỜI GIAN THÊM</th>
                            <th style="width:30px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;align-content: center"><label class="i-checks m-b-none"><input type="checkbox" name="post[]"><i></i></label></td>
                            <td style="text-align: center;align-content: center">Idrawfast prototype design prototype design prototype design prototype
                                design prototype design</td>
                            <td style="text-align: center;align-content: center"><span class="text-ellipsis">Kích hoạt/ Không kích hoạt</span></td>
                            <td style="text-align: center;align-content: center"><span class="text-ellipsis">01/01/1111</span></td>
                            <td>
                                <a href="" class="active" ui-toggle-class=""><i
                                        class="fa fa-wrench text-info text-active" style="color: #007bff;font-size:20px;margin-bottom: 10px "></i><i
                                        class="fa fa-trash text-danger text" style="font-size:20px "></i></a>
                            </td>
                        </tr>
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
