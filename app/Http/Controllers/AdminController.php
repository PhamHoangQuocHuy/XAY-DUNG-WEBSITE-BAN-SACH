<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
session_start();

class AdminController extends Controller
{
    // Kiểm tra đăng nhập không cho truy cập thẳng
    public function AuthLogin(){
        $user_id = Session::get('user_id');
        if($user_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }
    
    public function index(){
        return view('admin_login');
    }
    public function show_dashboard(){
        $this-> AuthLogin();
        return view('admin.dashboard');
    }
    public function dashboard(Request $request){
        $admin_email =$request ->admin_email;
        $admin_password =$request -> admin_password;
        $result = DB ::table('user')
        ->where ('email',$admin_email)
        ->where('password',$admin_password)
        ->where('role','admin')
        ->first();
        if($result) {
            Session::put('username',$result->username);
            Session::put('user_id',$result->user_id);
            return Redirect::to('/dashboard');
        }else{
            Session::put('message','Tài khoản hoặc mật khẩu không đúng');
            return Redirect::to('/admin');
        }
    }
    public function logout(){
        $this-> AuthLogin();
        Session::put('username',null);
        Session::put('user_id',null);
        return Redirect::to('/admin');
}

}
