<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryProduct extends Controller
{
    public function add_category(){
        return view('/admin.add_category');
    }
    public function all_category(){
        return view('/admin.all_category');
    }
}
