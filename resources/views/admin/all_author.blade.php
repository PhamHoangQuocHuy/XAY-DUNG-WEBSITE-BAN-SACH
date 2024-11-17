@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ TÁC GIẢ
            </div>
            <div class="row w3-res-tb">

                <div class="col-sm-5">
                </div>
                <div class="col-sm-7">
                    <div class="input-group" style="display: flex">
                        <form action="{{ URL::to('/search-author') }}" method="GET">
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
                            <th style="text-align: center;color: black">SỐ THỨ TỰ</th>
                            <th style="text-align: center;color: black">TÊN TÁC GIẢ</th>
                            <th style="text-align: center; width: 100px;color: black">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($errors->any())
                            <div class="alert alert-danger"> {{ $errors->first() }} </div>
                        @endif
                        @foreach ($all_author as $key => $author)
                            <tr>
                                <td style="text-align: center;align-content: center">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox" name="post[]"><i></i>
                                    </label>
                                </td>
                                <td style="text-align: center;align-content: center; color: black">{{ $key + 1 }}</td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $author->author_name }}</td>
                                <td style="text-align: center; align-content: center;">
                                    <a href="{{ URL::to('edit-author/' . $author->author_id) }}" class="active"
                                        ui-toggle-class="">
                                        <i class="fa fa-wrench text-info text-active"
                                            style="color: #007bff; font-size: 30px; margin: 0 5px;"></i>
                                    </a>
                                    <a onclick="return confirm('Bạn có chắc muốn xóa tác giả: {{ $author->author_name }}?')"
                                        href="{{ URL::to('delete-author/' . $author->author_id) }}">
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
                            
                            @if ($all_author instanceof \Illuminate\Pagination\LengthAwarePaginator) {{ $all_author->links('pagination::bootstrap-4') }}
                            @endif
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
@endsection
