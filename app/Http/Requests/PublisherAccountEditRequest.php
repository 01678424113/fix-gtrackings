<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PublisherAccountEditRequest extends Request {

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
            'txt-id' => "required|alpha_num",
            'txt-username' => "required",
            'sl-network' => "required|alpha_num",
        ];
    }

    public function messages() {
        return [
            'txt-id.required' => "Tài khoản publisher không hợp lệ",
            'txt-id.alpha_num' => "Tài khoản publisher không hợp lệ",
            'txt-username.required' => "Tên đăng nhập không được để trống",
            'sl-network.required' => "Mạng quảng cáo không hợp lệ",
            'sl-network.alpha_num' => "Mạng quảng cáo không hợp lệ",
        ];
    }

}
