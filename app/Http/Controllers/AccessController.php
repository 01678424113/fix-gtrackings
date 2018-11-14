<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class AccessController extends Controller {

    public function login(Request $request) {
        if (!$request->has('user')) {
            return view('login', [
                'title' => "Đăng nhập"
            ]);
        } else {
            return redirect()->action('HomeController@index');
        }
    }

    public function doLogin(LoginRequest $request) {
        if (!$request->session()->has('user')) {
            try {
                $user = User::where('user_name', $request->input('txt-username'))
                        ->first();
                if (!empty($user)) {
                    if ($user->user_status == 1) {
                        if ($user->user_password == md5($request->input('txt-password') . 'gugitech')) {
                            $request->session()->put('user', $user);
                            return redirect()->action('HomeController@index')->with('success', "Đăng nhập thành công");
                        } else {
                            return redirect()->back()->with('error', "Mật khẩu không đúng");
                        }
                    } else {
                        return redirect()->back()->with('error', "Thành viên này đã bị khóa");
                    }
                } else {
                    return redirect()->back()->with('error', "Thành viên không tồn tại");
                }
            } catch (\Exception $exc) {
                return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
            }
        } else {
            return redirect()->action('HomeController@index');
        }
    }

    public function logout(Request $request) {
        if ($request->session()->has('user')) {
//            $user = $request->session()->get('user');
            $request->session()->forget('user');
            $request->session()->forget('accounts');
        }
        return redirect()->action('AccessController@login');
    }

}
