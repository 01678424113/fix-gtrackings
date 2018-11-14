<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TrafficSourceEditRequest extends Request {

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
        ];
    }

    public function messages() {
        return [
            'txt-id.required' => "Nguồn traffic không hợp lệ",
            'txt-id.alpha_num' => "Nguồn traffic không hợp lệ",
            'txt-name.required' => "Tên nguồn traffic không được để trống",
        ];
    }

}
