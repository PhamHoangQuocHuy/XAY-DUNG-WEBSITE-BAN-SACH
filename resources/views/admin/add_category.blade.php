@extends('admin_layout')
@section('admin_content')
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    THÊM DANH MỤC SÁCH
                </header>
                <div class="panel-body">
                    <div class="position-center">
                        <form role="form">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Tên danh mục sách</label>
                                <input type="email" name="category_name" class="form-control" id="exampleInputEmail1"
                                    placeholder="Điền tên danh mục sách">
                            </div>
                            
                            {{-- <div class="form-group">
                                <label for="exampleInputPassword1">Mô tả danh mục sách</label>
                                <textarea style="resize: none" rows="5" class="form-control" id="exampleInputPassword1" placeholder="Password"></textarea>
                            </div> --}}

                            <div class="form-group">
                                <label for="exampleInputPassword1">Trạng thái</label>
                                <select class="form-control input-sm m-bot15">
                                    <option>Kích hoạt</option>
                                    <option>Không kích hoạt</option>
                                </select>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-info">Thêm</button>
                        </form>
                    </div>

                </div>
            </section>

        </div>
    </div>
@endsection
