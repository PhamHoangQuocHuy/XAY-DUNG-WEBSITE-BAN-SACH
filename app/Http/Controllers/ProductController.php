<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    // Kiểm tra đăng nhập không cho truy cập thẳng
    public function AuthLogin()
    {
        $user_id = Session::get('user_id');
        if ($user_id) {
            return Redirect::to('dashboard');
        } else {
            return Redirect::to('admin')->send();
        }
    }
    public function add_book()
    {
        $this->AuthLogin();
        $category_book = DB::table('category')->orderBy('category_id', 'asc')->get();
        $author_book = DB::table('author')->orderBy('author_id', 'asc')->get();
        $supplier_book = DB::table('supplier')->orderBy('supplier_id', 'asc')->get();
        return view('admin.add_book')
            ->with('category_book', $category_book)
            ->with('author_book', $author_book)
            ->with('supplier_book', $supplier_book);
    }
    // HIỂN THỊ TOÀN BỘ SÁCH
    public function all_book()
    {
        $this->AuthLogin();
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
            ->get()
            ->map(function ($book) {
                $book->formatted_price = number_format($book->price, 0, ',', '.'); // Định dạng khi hiển thị
                return $book;
            });
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
        dd($all_book); // Kiểm tra dữ liệu

    }

    // THÊM SÁCH
    public function save_book(Request $request)
    {
        $this->AuthLogin();
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
        $this->AuthLogin();
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
        $this->AuthLogin();
        $category_book = DB::table('category')->orderBy('category_id', 'asc')->get();
        $author_book = DB::table('author')->orderBy('author_id', 'asc')->get();
        $supplier_book = DB::table('supplier')->orderBy('supplier_id', 'asc')->get();

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
        $this->AuthLogin();
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
        $this->AuthLogin();
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
        $this->AuthLogin();
        $words = explode(' ', $string);
        if (count($words) > $word_limit) {
            return implode(' ', array_splice($words, 0, $word_limit)) . '...';
        }
        return $string;
    }

    //END FUNCTION ADMIN PAGE HERE
    public function show_nxb_home($book_id)
    {
        $nxb_book = DB::table('book')
            ->where('status', 'active')
            ->orderBy('book_id', 'asc')
            ->get();

        $tacgia_book = DB::table('author')->orderBy('author_id', 'asc')->get(); // thêm biến tacgia_book
        $category_book = DB::table('category')->where('status', 'active')->orderBy('category_id', 'asc')->get();
        $all_book = DB::table('book')
        ->where('status', 'active')
        ->orderBy('book_id', 'asc')
        ->take(3) // chỉ lấy 3 nxb
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
            ->get();

        return view('pages.nxb.show_nxb')
            ->with('nxb_book', $nxb_book)
            ->with('nxb_by_id', $nxb_by_id)
            ->with('nxb_name', $nxb_name)
            ->with('all_book', $all_book) // truyền thêm biến tacgia_book
            ->with('tacgia_book', $tacgia_book) // truyền thêm biến tacgia_book
            ->with('category', $category_book);
    }
}
