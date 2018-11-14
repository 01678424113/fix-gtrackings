<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LoginRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'txt-username' => "required|regex:/^[a-z0-9\_]+$/|min:3",
            'txt-password' => "required|min:3"
        ];
    }

    public function messages() {
        return [
            'txt-username.required' => "Tên đăng nhập không được để trống",
            'txt-username.regex' => "Tên đăng nhập không hợp lệ",
            'txt-username.min' => "Tên đăng nhập phải lớn hơn 3 ký tự",
            'txt-password.required' => "Mật khẩu không được để trống",
            'txt-password.min' => "Mật khẩu phải lớn hơn 3 ký tự"
        ];
    }

}
