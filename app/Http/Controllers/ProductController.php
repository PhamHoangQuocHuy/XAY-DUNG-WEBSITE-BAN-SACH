<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    // Kiểm tra đăng nhập không cho truy cập thẳng
    public function add_book()
    {
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')->get();
        $author_book = DB::table('author')->orderBy('author_id', 'asc')->get();
        $supplier_book = DB::table('supplier')
            ->where('status', 'active')
            ->orderBy('supplier_id', 'asc')->get();
        return view('admin.add_book')
            ->with('category_book', $category_book)
            ->with('author_book', $author_book)
            ->with('supplier_book', $supplier_book);
    }
    // HIỂN THỊ TOÀN BỘ SÁCH
    public function all_book()
    {
        $all_book = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->paginate(6); // Giới hạn 5 sách mỗi trang

        // Hàm giới hạn từ
        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        // Truyền biến vào view
        $manager_book = view('admin.all_book')->with('all_book', $all_book)->with('limitWordsFunc', $limitWordsFunc);
        return view('admin_layout')->with('admin.all_book', $manager_book);
    }

    // THÊM SÁCH
    public function save_book(Request $request)
    {
        // Thêm validation
        $request->validate([
            //'book_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
            'book_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Giới hạn kích thước hình ảnh
            'book_isbn' => 'required|regex:/^[0-9\-]+$/|size:13|unique:book,isbn', // Ràng buộc ISBN có thể chứa chữ số và dấu gạch ngang, đúng 13 ký tự
            'book_publication_date' => 'required|date|before_or_equal:' . now()->format('Y-m-d'), // Ngày xuất bản không lớn hơn ngày hiện tại
            'book_price' => 'required|numeric|min:0', // Ràng buộc giá phải là số và không nhỏ hơn 0
            'book_quantity' => 'required|integer|min:0', // Ràng buộc số lượng phải là số nguyên và không nhỏ hơn 0
            //'book_publisher' => 'required|regex:/^[\p{L}0-9 ]+$/u', // Ràng buộc nhà xuất bản không chứa ký tự đặc biệt
            //'book_description' => 'required|regex:/^[\p{L}0-9 .,?]+$/u', // Ràng buộc mô tả không chứa ký tự đặc biệt
            //'book_language' => 'required|regex:/^[\p{L}0-9 ]+$/u', // Ràng buộc ngôn ngữ không chứa ký tự đặc biệt
            //'book_tags' => 'nullable|regex:/^[\p{L}0-9 ,]+$/u', // Ràng buộc từ khóa không chứa ký tự đặc biệt
        ], [
            //'book_name.regex' => 'Tên sách chỉ được phép chứa chữ cái và khoảng trắng.',
            'book_image.required' => 'Vui lòng thêm hình ảnh cho sách.',
            'book_image.image' => 'Hình ảnh không hợp lệ, vui lòng chọn tệp hình ảnh.',
            'book_image.mimes' => 'Hình ảnh phải là tệp loại: jpeg, png, jpg, hoặc gif.',
            'book_image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'book_isbn.regex' => 'Số ISBN chỉ được phép chứa chữ số và dấu gạch ngang.',
            'book_isbn.size' => 'Số ISBN phải có đúng 13 ký tự.',
            'book_isbn.unique' => 'ISBN đã tồn tại. Vui lòng nhập một ISBN khác.',
            'book_publication_date.before_or_equal' => 'Ngày xuất bản không được lớn hơn ngày hiện tại.',
            'book_price.numeric' => 'Giá sách phải là một số.',
            'book_price.min' => 'Giá sách không được nhỏ hơn 0.',
            'book_quantity.integer' => 'Số lượng phải là một số nguyên.',
            'book_quantity.min' => 'Số lượng không được nhỏ hơn 0.',
            //'book_publisher.regex' => 'Nhà xuất bản không được chứa ký tự đặc biệt.',
            //'book_description.regex' => 'Mô tả không được chứa ký tự đặc biệt.',
            //'book_language.regex' => 'Ngôn ngữ không được chứa ký tự đặc biệt.',
            //'book_tags.regex' => 'Từ khóa không được chứa ký tự đặc biệt.',
        ]);

        $data = array();
        $data['book_name'] = $request->book_name;
        $data['isbn'] = $request->book_isbn;
        $data['author_id'] = $request->book_author;
        $data['category_id'] = $request->book_category;
        $data['supplier_id'] = $request->book_supplier;
        $data['publisher'] = $request->book_publisher;
        $data['publication_date'] = $request->book_publication_date;
        $data['quantity'] = $request->book_quantity;
        $data['price'] = $request->book_price;
        $data['description'] = $request->book_description;
        $data['language'] = $request->book_language;
        $data['tags'] = $request->book_tags;
        $data['status'] = $request->book_status === 'Kích hoạt' ? 'active' : 'inactive';
        $get_image = $request->file('book_image');

        // HÌNH ẢNH
        if ($get_image) {
            $get_name_image = $get_image->getClientOriginalName(); // lấy tên hình ảnh
            $name_image = current(explode('.', $get_name_image)); // phân tách chuỗi hình ảnh -> 1)tên hình ảnh , 2)đuôi hình ảnh như .jpg
            $added_at = now()->format('Ymd_His'); // Định dạng thời gian 

            $new_image = $name_image . '_' . $added_at . '.' . $get_image->getClientOriginalExtension(); // lấy hình ảnh với tên. số rand. đuôi hình ảnh
            $get_image->move('public/uploads/product', $new_image);
            $data['image'] = $new_image;
            DB::table('book')->insert($data);
            Session::put('message', 'Thêm sách thành công');
            return Redirect::to('/add-book');
        }
        // Thêm sách vào cơ sở dữ liệu
        $data['image'] = ''; // đặt giá trị mặc định cho file hình ảnh
        DB::table('book')->insert($data);
        Session::put('message', 'Thêm sách thành công');
        return Redirect::to('all-book');
    }


    // CHỈNH SỬA TRẠNG THÁI
    public function active_book($book_id)
    {
        DB::table('book')->where('book_id', $book_id)->update(['status' => 'inactive']);
        Session::put('message', 'Đã đổi trạng thái thành không kích hoạt');
        return Redirect::to('all-book');
    }

    public function inactive_book($book_id)
    {
        DB::table('book')->where('book_id', $book_id)->update(['status' => 'active']);
        Session::put('message', 'Đã đổi trạng thái thành kích hoạt');
        return Redirect::to('all-book');
    }

    // SỬA SÁCH
    public function edit_book($book_id)
    {
        $category_book = DB::table('category')
            ->orderBy('category_id', 'asc')->get();
        $author_book = DB::table('author')
            ->orderBy('author_id', 'asc')->get();
        $supplier_book = DB::table('supplier')
            ->orderBy('supplier_id', 'asc')->get();

        $edit_book = DB::table('book')->where('book_id', $book_id)->get();
        $manager_book = view('admin.edit_book')
            ->with('edit_book', $edit_book)
            ->with('category_book', $category_book)
            ->with('author_book', $author_book)
            ->with('supplier_book', $supplier_book);
        return view('admin_layout')->with('admin.edit_book', $manager_book);
    }

    public function update_book(Request $request, $book_id)
    {
        // Thêm validation
        $request->validate([
            //'book_name' => 'required|regex:/^[\p{L} ]+$/u', // Chỉ cho phép ký tự chữ và khoảng trắng
            'book_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Giới hạn kích thước hình ảnh
            //'book_isbn' => 'required|regex:/^[0-9\-]+$/|size:13|unique:book,isbn', // Ràng buộc ISBN có thể chứa chữ số và dấu gạch ngang, đúng 13 ký tự
            'book_publication_date' => 'required|date|before_or_equal:' . now()->format('Y-m-d'), // Ngày xuất bản không lớn hơn ngày hiện tại
            'book_price' => 'required|numeric|min:0', // Ràng buộc giá phải là số và không nhỏ hơn 0
            'book_quantity' => 'required|integer|min:0', // Ràng buộc số lượng phải là số nguyên và không nhỏ hơn 0
            //'book_publisher' => 'required|regex:/^[\p{L}0-9 ]+$/u', // Ràng buộc nhà xuất bản không chứa ký tự đặc biệt
            //'book_description' => 'required|regex:/^[\p{L}0-9 .,?]+$/u', // Ràng buộc mô tả không chứa ký tự đặc biệt
            //'book_language' => 'required|regex:/^[\p{L}0-9 ]+$/u', // Ràng buộc ngôn ngữ không chứa ký tự đặc biệt
            'book_tags' => 'nullable|regex:/^[\p{L}0-9 ,]+$/u', // Ràng buộc từ khóa không chứa ký tự đặc biệt
        ], [
            //'book_name.regex' => 'Tên sách chỉ được phép chứa chữ cái và khoảng trắng.',
            //'book_image.required' => 'Vui lòng thêm hình ảnh cho sách.',
            'book_image.image' => 'Hình ảnh không hợp lệ, vui lòng chọn tệp hình ảnh.',
            'book_image.mimes' => 'Hình ảnh phải là tệp loại: jpeg, png, jpg, hoặc gif.',
            'book_image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            //'book_isbn.regex' => 'Số ISBN chỉ được phép chứa chữ số và dấu gạch ngang.',
            //'book_isbn.size' => 'Số ISBN phải có đúng 13 ký tự.',
            //'book_isbn.unique' => 'ISBN đã tồn tại. Vui lòng nhập một ISBN khác.',
            'book_publication_date.before_or_equal' => 'Ngày xuất bản không được lớn hơn ngày hiện tại.',
            'book_price.numeric' => 'Giá sách phải là một số.',
            'book_price.min' => 'Giá sách không được nhỏ hơn 0.',
            'book_quantity.integer' => 'Số lượng phải là một số nguyên.',
            'book_quantity.min' => 'Số lượng không được nhỏ hơn 0.',
            //'book_publisher.regex' => 'Nhà xuất bản không được chứa ký tự đặc biệt.',
            //'book_description.regex' => 'Mô tả không được chứa ký tự đặc biệt.',
            //'book_language.regex' => 'Ngôn ngữ không được chứa ký tự đặc biệt.',
            'book_tags.regex' => 'Từ khóa không được chứa ký tự đặc biệt.',
        ]);

        $data = array();
        $data['book_name'] = $request->book_name;
        $data['isbn'] = $request->book_isbn;
        $data['author_id'] = $request->book_author;
        $data['category_id'] = $request->book_category;
        $data['supplier_id'] = $request->book_supplier;
        $data['publisher'] = $request->book_publisher;
        $data['publication_date'] = $request->book_publication_date;
        $data['quantity'] = $request->book_quantity;
        $data['price'] = $request->book_price;
        $data['description'] = $request->book_description;
        $data['language'] = $request->book_language;
        $data['tags'] = $request->book_tags;


        $get_image = $request->file('book_image');
        // Kiểm tra nếu có file hình ảnh mới
        if ($request->hasFile('book_image')) {
            $get_image = $request->file('book_image');
            $get_name_image = $get_image->getClientOriginalName(); // lấy tên hình ảnh
            $name_image = current(explode('.', $get_name_image)); // phân tách chuỗi hình ảnh -> 1)tên hình ảnh , 2)đuôi hình ảnh như .jpg
            $added_at = now()->format('Ymd_His'); // Định dạng thời gian 

            $new_image = $name_image . '_' . $added_at . '.' . $get_image->getClientOriginalExtension(); // lấy hình ảnh với tên. số rand. đuôi hình ảnh
            $get_image->move('public/uploads/product', $new_image);
            $data['image'] = $new_image;
        } else {
            // Nếu không có hình ảnh mới, giữ nguyên hình ảnh hiện có
            $current_image = DB::table('book')->where('book_id', $book_id)->value('image');
            $data['image'] = $current_image; // giữ nguyên hình ảnh hiện có
        }

        DB::table('book')->where('book_id', $book_id)->update($data);
        Session::put('message', 'Cập nhật sách thành công');
        return Redirect::to('all-book');
    }

    // XÓA SÁCH
    public function delete_book($book_id)
    {
        // Lấy thông tin sách để lấy tên hình ảnh
        $book = DB::table('book')->where('book_id', $book_id)->first();

        // Kiểm tra nếu sách tồn tại và có hình ảnh
        if ($book && !empty($book->image)) {
            $image_path = public_path('uploads/product/' . $book->image);

            // Kiểm tra nếu file hình ảnh tồn tại
            if (file_exists($image_path)) {
                // Xóa file hình ảnh
                unlink($image_path);
            }
        }

        // Xóa sách khỏi cơ sở dữ liệu
        DB::table('book')->where('book_id', $book_id)->delete();

        // Thông báo thành công
        Session::put('message', 'Xóa sách thành công');
        return Redirect::to('/all-book');
    }

    // GIỚI HẠN NỘI DUNG
    function limit_words_with_ellipsis($string, $word_limit)
    {
        $words = explode(' ', $string);
        if (count($words) > $word_limit) {
            return implode(' ', array_splice($words, 0, $word_limit)) . '...';
        }
        return $string;
    }

    //END FUNCTION ADMIN PAGE HERE
    public function show_nxb_home($book_id)
    {

        // NXB
        $nxb_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        // Author
        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        //Category
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();


        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        // Lấy tên nhà xuất bản từ book_id đã chọn
        $nxb_name = DB::table('book')
            ->where('book.book_id', $book_id)
            ->limit(1)
            ->pluck('publisher')
            ->first();

        // Lấy tất cả các sách thuộc nhà xuất bản đó
        $nxb_by_id = DB::table('book')
            ->where('publisher', $nxb_name)
            ->where('status', 'active')
            ->paginate(6);

        return view('pages.nxb.show_nxb')
            ->with('nxb_book', $nxb_book)
            ->with('nxb_by_id', $nxb_by_id)
            ->with('nxb_name', $nxb_name)
            ->with('publisher_list', $publisher_list) // nxb
            ->with('all_book', $all_book) // sách
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('category', $category_book); // thể loại
    }

    // DETAILS PRODUCT
    public function details_product_cate($book_id)
    {
        // code phải có ở các danh mục: thể loại, tác giả, nxb, sách. Chổ nào thiếu thì điền vô
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };
        $reviews = DB::table('review')
            ->join('user', 'review.user_id', '=', 'user.user_id')
            ->select('user.username', 'review.*')
            ->where('book_id', $book_id)
            ->get();

        // CHI TIẾT SẢN PHẨM
        $details_product_category = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('book.book_id', $book_id)
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });
        // SẢN PHẨM LIÊN QUAN   
        foreach ($details_product_category as $key => $value) {
            $category_id = $value->category_id;
        }
        $ralated_product = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('category.category_id', $category_id)
            ->whereNotIn('book.book_id', [$book_id])
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });


        return view('pages.sanpham.show_details_cate')
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('product_details', $details_product_category) // chi tiết sản phẩm
            ->with('relate', $ralated_product) // sản phẩm liên quan
            ->with('reviews', $reviews)
            ->with('limitWordsFunc', $limitWordsFunc);
    }
    public function details_product_author($book_id)
    {

        // code phải có ở các danh mục: thể loại, tác giả, nxb, sách. Chổ nào thiếu thì điền vô
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };
        $reviews = DB::table('review')
            ->join('user', 'review.user_id', '=', 'user.user_id')
            ->select('user.username', 'review.*')
            ->where('book_id', $book_id)
            ->get();
        // CHI TIẾT SẢN PHẨM
        $details_product_author = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('book.book_id', $book_id)
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });
        // SẢN PHẨM LIÊN QUAN ĐẾN AUTHOR
        foreach ($details_product_author as $key => $value) {
            $author_id = $value->author_id;
        }
        $ralated_product_author = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('author.author_id', $author_id)
            ->whereNotIn('book.book_id', [$book_id])
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });


        return view('pages.sanpham.show_details_author')
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('product_details_author', $details_product_author) // chi tiết sản phẩm
            ->with('relate_to_author', $ralated_product_author) // sản phẩm liên quan
            ->with('reviews', $reviews)
            ->with('limitWordsFunc', $limitWordsFunc);
    }

    public function details_product_nxb($book_id)
    {

        // code phải có ở các danh mục: thể loại, tác giả, nxb, sách. Chổ nào thiếu thì điền vô
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get(); // thêm biến tacgia_book

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };
        $reviews = DB::table('review')
            ->join('user', 'review.user_id', '=', 'user.user_id')
            ->select('user.username', 'review.*')
            ->where('book_id', $book_id)
            ->get();

        // CHI TIẾT SẢN PHẨM
        $details_product_nxb = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('book.book_id', $book_id)
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'book.publisher'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });
        // SẢN PHẨM LIÊN QUAN ĐẾN NXB
        //$publisher = null;
        foreach ($details_product_nxb as $key => $value) {
            $publisher = $value->publisher;
        }
        $ralated_product_nxb = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('book.publisher', $publisher)
            ->where('book_id', '!=', $book_id)
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'book.publisher'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });


        return view('pages.sanpham.show_details_nxb')
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('product_details_nxb', $details_product_nxb) // chi tiết sản phẩm
            ->with('relate_to_nxb', $ralated_product_nxb) // sản phẩm liên quan
            ->with('reviews', $reviews)
            ->with('limitWordsFunc', $limitWordsFunc);
    }
    public function details_product_home($book_id)
    {
        // code phải có ở các danh mục: thể loại, tác giả, nxb, sách. Chổ nào thiếu thì điền vô
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get();

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        // CHI TIẾT SẢN PHẨM
        $details_product_home = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('book.book_id', $book_id)
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });
        // SẢN PHẨM LIÊN QUAN   
        foreach ($details_product_home as $key => $value) {
            $category_id = $value->category_id;
        }
        $ralated_product_home = DB::table('book')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->where('category.category_id', $category_id)
            ->whereNotIn('book.book_id', [$book_id])
            ->orderby('book.book_id', 'asc')
            ->select(
                'book.*', // Chọn tất cả các trường từ bảng book
                'category.category_name',
                'author.author_name',
                'supplier.supplier_name',
                'supplier.supplier_phone',
                'supplier.supplier_email',
                'supplier.supplier_address'
            )
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });

        $reviews = DB::table('review')
            ->join('user', 'review.user_id', '=', 'user.user_id')
            ->select('user.username', 'review.*')
            ->where('book_id', $book_id)
            ->get();

        return view('pages.sanpham.show_details_home')
            ->with('category', $category_book) // thể  ại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('product_details_home', $details_product_home) // chi tiết sản phẩm
            ->with('relate_home', $ralated_product_home) // sản phẩm liên quan
            ->with('reviews', $reviews)
            ->with('limitWordsFunc', $limitWordsFunc);
    }

    //REVIEW
    public function show_review($book_id)
    {
        $category_book = DB::table('category')->where('status', 'active')->orderBy('category_id', 'asc')->get();
        $tacgia_book = DB::table('author')->orderBy('author_id', 'asc')->get();
        $all_publishers = DB::table('book')->select('publisher', 'book_id')->where('status', 'active')->orderBy('publisher', 'desc')->limit(4)->get();
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')->where('status', 'active')->orderBy('book_id', 'asc')->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        $reviews = DB::table('review')
            ->join('user', 'review.user_id', '=', 'user.user_id')
            ->select('user.username', 'review.*')
            ->where('book_id', $book_id)
            ->get();

        return view('book.show_details_home')
            ->with('category_book', $category_book)
            ->with('publisher_list', $publisher_list)
            ->with('tacgia_book', $tacgia_book)
            ->with('all_book', $all_book)
            ->with('reviews', $reviews)
            ->with('limitWordsFunc', $limitWordsFunc);
    }
    // THÊM ĐÁNH GIÁ/ BÌNH LUẬN
    public function save_review(Request $request, $book_id)
    {
        $user_id = Session::get('user_id');
        if ($user_id == NULL) {
            return redirect('/login-checkout')->with('message', 'Bạn phải đăng nhập để có thể đánh giá!');
        }

        $request->validate([
            'comment' => 'required|string|min:5',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $data = array();
        $data['book_id'] = $book_id;
        $data['user_id'] = $user_id;
        $data['comment'] = $request->comment;
        $data['rating'] = $request->rating;
        $data['review_date'] = now();

        DB::table('review')->insert($data);

        return redirect()->back()->with('message', 'Đánh giá và bình luận của bạn đã được ghi nhận');
    }
    // XÓA ĐÁNH GIÁ/ BÌNH LUẬN
    public function delete_review($review_id)
    {
        $user_id = Session::get('user_id');
        $review = DB::table('review')->where('review_id', $review_id)->first();

        // Kiểm tra xem người dùng có phải là người đã đăng bình luận hay không
        if ($review && $review->user_id == $user_id) {
            DB::table('review')->where('review_id', $review_id)->delete();
            return redirect()->back()->with('message', 'Bình luận đã được xóa thành công');
        }

        return redirect()->back()->with('message', 'Bạn không có quyền xóa bình luận này');
    }
    // SỬA ĐÁNH GIÁ/ BÌNH LUẬN
    public function edit_review($review_id)
    {
        $category_book = DB::table('category')
            ->where('status', 'active')
            ->orderBy('category_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')
            ->orderBy('author_id', 'asc')
            ->get();

        // Lấy danh sách nhà xuất bản với book_id duy nhất cho mỗi nhà xuất bản
        $all_publishers = DB::table('book')
            ->select('publisher', 'book_id')
            ->where('status', 'active')
            ->orderBy('publisher', 'desc')
            ->limit(4) // lấy 4 nxb
            ->get();
        // Loại bỏ nhà xuất bản trùng lặp
        $publisher_list = $all_publishers->unique('publisher')->values();
        $all_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        $user_id = Session::get('user_id');
        $review = DB::table('review')->where('review_id', $review_id)->first();

        // Kiểm tra xem người dùng có phải là người đã đăng bình luận hay không
        if ($review && $review->user_id == $user_id) {
            return view('pages.sanpham.edit_review')
                ->with('category', $category_book) // thể  ại
                ->with('publisher_list', $publisher_list) // nxb
                ->with('tacgia_book', $tacgia_book) // tác giả
                ->with('all_book', $all_book) // sách
                ->with('review', $review)
                ->with('limitWordsFunc', $limitWordsFunc);
        }

        return redirect()->back()->with('message', 'Bạn không có quyền chỉnh sửa bình luận này');
    }
    public function update_review(Request $request, $review_id)
    {
        $user_id = Session::get('user_id');
        $review = DB::table('review')->where('review_id', $review_id)->first();
        $product_id = $review->book_id;
        // Kiểm tra xem người dùng có phải là người đã đăng bình luận hay không
        if ($review && $review->user_id == $user_id) {
            $request->validate([
                'comment' => 'required|string|min:5',
                'rating' => 'required|integer|min:1|max:5',
            ]);

            DB::table('review')->where('review_id', $review_id)->update([
                'comment' => $request->comment,
                'rating' => $request->rating,
                'review_date' => now(),
            ]);

            return redirect('/chi-tiet-san-pham-theo-trang-chu/' . $product_id)->with('message', 'Bình luận đã được cập nhật thành công');
        }

        return redirect()->back()->with('message', 'Bạn không có quyền chỉnh sửa bình luận này');
    }
    // TÌM KIẾM SÁCH
    public function search_book(Request $request)
    {
        $keywords = $request->input('query');

        // Nếu không có từ khóa, trả về thông báo không tìm thấy
        if (empty($keywords)) {
            return redirect()->back()->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        // Tìm kiếm theo các tiêu chí
        $search_product = DB::table('book')
            ->join('author', 'author.author_id', '=', 'book.author_id')
            ->join('category', 'category.category_id', '=', 'book.category_id')
            ->join('supplier', 'supplier.supplier_id', '=', 'book.supplier_id')
            ->select('book.*', 'author.author_name', 'category.category_name', 'supplier.supplier_name') // Chọn các cột từ các bảng join
            ->where('book.book_name', 'like', '%' . $keywords . '%')
            ->orWhere('book.isbn', 'like', '%' . $keywords . '%')
            ->orWhere('book.publisher', 'like', '%' . $keywords . '%')
            ->orWhere('book.tags', 'like', '%' . $keywords . '%')
            ->orWhere('book.language', 'like', '%' . $keywords . '%')
            ->orWhere('author.author_name', 'like', '%' . $keywords . '%')
            ->orWhere('category.category_name', 'like', '%' . $keywords . '%')
            ->orWhere('supplier.supplier_name', 'like', '%' . $keywords . '%')
            ->paginate(5);

        // Hàm giới hạn từ
        $limitWordsFunc = function ($string, $word_limit) {
            $words = explode(' ', $string);
            if (count($words) > $word_limit) {
                return implode(' ', array_splice($words, 0, $word_limit)) . '...';
            }
            return $string;
        };

        // Kiểm tra nếu không có sản phẩm nào tìm thấy
        if ($search_product->isEmpty()) {
            return view('admin.all_book')
                ->with('all_book', collect()) // gửi danh sách rỗng nếu không tìm thấy sản phẩm nào
                ->with('limitWordsFunc', $limitWordsFunc)
                ->withErrors(['Không tìm thấy kết quả phù hợp với từ khóa: "' . $keywords . '"']);
        }

        return view('admin.all_book')
            ->with('all_book', $search_product)
            ->with('limitWordsFunc', $limitWordsFunc);
    }
}
