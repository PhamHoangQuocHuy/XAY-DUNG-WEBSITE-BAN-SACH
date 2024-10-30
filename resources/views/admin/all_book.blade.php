@extends('admin_layout')
@section('admin_content')
    <div class="table-agile-info">
        <div class="panel panel-default">
            <div class="panel-heading" style="font-weight: bold">
                LIỆT KÊ SÁCH
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
                            <th style="text-align: center;color: black">TÊN SÁCH</th>
                            <th style="text-align: center;color: black">ISBN</th>
                            <th style="text-align: center;color: black">TÁC GIẢ</th>
                            <th style="text-align: center;color: black">THỂ LOẠI</th>
                            <th style="text-align: center;color: black">NHÀ CUNG CẤP</th>
                            <th style="text-align: center;color: black">NHÀ XUẤT BẢN</th>
                            <th style="text-align: center;color: black">NGÀY XUẤT BẢN</th>
                            <th style="text-align: center;color: black">SỐ LƯỢNG</th>
                            <th style="text-align: center;color: black">GIÁ BÁN</th>
                            <th style="text-align: center;color: black">MÔ TẢ SÁCH</th>
                            <th style="text-align: center;color: black">HÌNH ẢNH</th>
                            <th style="text-align: center;color: black">NGÔN NGỮ</th>
                            <th style="text-align: center;color: black">TỪ KHÓA</th>
                            <th style="text-align: center;color: black">TRẠNG THÁI</th>
                            <th style="text-align: center; width: 100px;color: black">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($all_book as $key => $book)
                            <tr>
                                <td style="text-align: center;align-content: center">
                                    <label class="i-checks m-b-none">
                                        <input type="checkbox" name="post[]"><i></i>
                                    </label>
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $limitWordsFunc($book->book_name, 3) }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ substr($book->isbn, 0, 5) }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->author_name }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->category_name }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->supplier_name }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->publisher }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->publication_date }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->quantity }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $book->formatted_price }} VNĐ
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $limitWordsFunc($book->description, 5) }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    <img src="public/uploads/product/{{ $book->image }}" width="100" height="100"
                                        alt="">
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $limitWordsFunc($book->language, 3) }}
                                </td>
                                <td style="text-align: center;align-content: center; color: black">
                                    {{ $limitWordsFunc($book->tags, 3) }}
                                </td>
                                <td style="text-align: center;align-content: center"><span class="text-ellipsis">
                                    <?php
                                    if ($book->status != 'inactive') {
                                        ?>
                                    <a href="{{ URL::to('/active-book/' . $book->book_id) }}"><span
                                            class="fa-toggle-styling fa fa-toggle-on"></span></a>;
                                    <?php
                                    } else {
                                        ?>
                                    <a href="{{ URL::to('/inactive-book/' . $book->book_id) }}"><span
                                            class="fa-toggle-styling fa fa-toggle-off"></span></a>;
                                    <?php
                                    }
                                    ?>
                                </span></td>
                            <td style="text-align: center; align-content: center;">
                                    <a href="{{ URL::to('edit-book/' . $book->book_id) }}" class="active"
                                        ui-toggle-class="">
                                        <i class="fa fa-wrench text-info text-active"
                                            style="color: #007bff; font-size: 30px; margin: 0 5px;"></i>
                                    </a>
                                    <a onclick="return confirm('Bạn có chắc muốn xóa sách: {{ $book->book_name }}?')"
                                        href="{{ URL::to('delete-book/' . $book->book_id) }}">
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
