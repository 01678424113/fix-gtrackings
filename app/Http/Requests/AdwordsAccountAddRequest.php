<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AdwordsAccountAddRequest extends Request {

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
            'txt-name' => "required",
            'txt-adwords-id' => "required|alpha_num",
        ];
    }

    public function messages() {
        return [
            'txt-name.required' => "Tên tài khoản adwords không được để trống",
            'txt-adwords-id.required' => "ID tài khoản adwords không được để trống",
            'txt-adwords-id.alpha_num' => "ID tài khoản adwords không hợp lệ",
        ];
    }

}
