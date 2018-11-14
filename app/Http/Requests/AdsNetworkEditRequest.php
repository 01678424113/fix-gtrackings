<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AdsNetworkEditRequest extends Request {

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
            'txt-name' => "required",
            'txt-domain' => "required",
            'sl-type' => "required|alpha_num",
        ];
    }

    public function messages() {
        return [
            'txt-id.required' => "Mạng quảng cáo không hợp lệ",
            'txt-id.alpha_num' => "Mạng quảng cáo không hợp lệ",
            'txt-name.required' => "Tên mạng quảng cáo không được để trống",
            'txt-domain.required' => "Website mạng quảng cáo không được để trống",
            'sl-type.required' => "Loại mạng quảng cáo không hợp lệ",
            'sl-type.alpha_num' => "Loại mạng quảng cáo không hợp lệ",
        ];
    }

}
