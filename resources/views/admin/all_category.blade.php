@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ DANH MỤC SÁCH
            </div>

            <div class="row w3-res-tb">

                <div class="col-sm-5">
                </div>
                <div class="col-sm-7">
                    <div class="input-group" style="display: flex">
                        <form action="{{ URL::to('/search-category') }}" method="GET">
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
                            <th style="text-align: center;color: black">TÊN DANH MỤC</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI</th>

                            <th style="text-align: center; width: 100px;color: black">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($errors->any())
                            <div class="alert alert-danger"> {{ $errors->first() }} </div>
                        @endif

                        @foreach ($all_category as $key => $cate)
                            <tr>
                                <td style="text-align: center;align-content: center"><label class="i-checks m-b-none"><input
                                            type="checkbox" name="post[]"><i></i></label></td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $cate->category_name }}</td>
                                <td style="text-align: center;align-content: center"><span class="text-ellipsis">
                                        <?php
                                        if ($cate->status != 'inactive') {
                                            ?>
                                        <a href="{{ URL::to('/active-category/' . $cate->category_id) }}"><span
                                                class="fa-toggle-styling fa fa-toggle-on"></span></a>;
                                        <?php
                                        } else {
                                            ?>
                                        <a href="{{ URL::to('/inactive-category/' . $cate->category_id) }}"><span
                                                class="fa-toggle-styling fa fa-toggle-off"></span></a>;
                                        <?php
                                        }
                                        ?>
                                    </span></td>
                                <td style="text-align: center; align-content: center;">
                                    <a href="{{ URL::to('edit-category/' . $cate->category_id) }}" class="active"
                                        ui-toggle-class="">
                                        <i
                                            class="fa fa-wrench text-info text-active"style="color: #007bff; font-size: 30px; margin: 0 5px;"></i>
                                    </a>
                                    <a onclick="return confirm ('Bạn có chắc muốn xóa danh mục: {{ $cate->category_name }}?')"
                                        href="{{ URL::to('delete-category/' . $cate->category_id) }}">
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
                            
                            @if ($all_category instanceof \Illuminate\Pagination\LengthAwarePaginator) {{ $all_category->links('pagination::bootstrap-4') }}
                            @endif
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
