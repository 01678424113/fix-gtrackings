<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PublisherAccountAddRequest extends Request {

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
            'txt-username' => "required",
            'sl-network' => "required|alpha_num",
        ];
    }

    public function messages() {
        return [
            'txt-username.required' => "Tên đăng nhập không được để trống",
            'sl-network.required' => "Mạng quảng cáo không hợp lệ",
            'sl-network.alpha_num' => "Mạng quảng cáo không hợp lệ",
        ];
    }

}
