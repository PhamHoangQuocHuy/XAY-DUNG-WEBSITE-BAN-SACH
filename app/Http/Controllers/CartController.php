<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{

    public function save_cart(Request $request)
    {
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

        $productID = $request->product_id_hidden;
        $quantity = $request->qty;

        $product_info = DB::table('book')
            ->where('book_id', $productID)
            ->first();
        if ($product_info) {
            $data['id'] = $product_info->book_id;
            $data['qty'] = $quantity;
            $data['name'] = $product_info->book_name;
            $data['price'] = $product_info->price;
            $data['options']['image'] = $product_info->image;
            $data['options']['isbn'] = $product_info->isbn;
            Cart::add($data);
            // Cart::destroy();
            return Redirect::to('show-cart');
        } else {
            return Redirect::to('show-cart')->with('error', 'Sản phẩm không tồn tại.');
        }
    }
    public function show_cart()
    {
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
        return view('pages.cart.show_cart')
            ->with('category', $category_book) // thể loại
            ->with('publisher_list', $publisher_list) // nxb
            ->with('tacgia_book', $tacgia_book) // tác giả
            ->with('all_book', $all_book) // sách
            ->with('limitWordsFunc', $limitWordsFunc);
    }
    public function delete_to_cart($rowId)
    {
        Cart::remove($rowId);

        if (request()->ajax()) {
            $totalAmount = Cart::subtotal(0, ',', '.');
            $finalAmount = Cart::subtotal(0, ',', '.');

            return response()->json([
                'success' => true,
                'totalAmount' => $totalAmount,
                'finalAmount' => $finalAmount
            ]);
        }

        return Redirect::to('show-cart');
    }
    public function update_cart_quantity(Request $request)
    {
        $rowId = $request->rowId_cart;
        $qty = $request->cart_quantity;
        Cart::update($rowId, $qty);

        if ($request->ajax()) {
            $item = Cart::get($rowId);
            $itemTotal = number_format($item->price * $item->qty, 0, ',', '.');
            $totalAmount = Cart::subtotal(0, ',', '.');
            $finalAmount = Cart::subtotal(0, ',', '.');

            return response()->json([
                'success' => true,
                'itemTotal' => $itemTotal,
                'totalAmount' => $totalAmount,
                'finalAmount' => $finalAmount
            ]);
        }

        return Redirect::to('show-cart');
    }
}
