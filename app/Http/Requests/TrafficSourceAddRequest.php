<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TrafficSourceAddRequest extends Request {

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
            'txt-name' => "required"
        ];
    }

    public function messages() {
        return [
            'txt-name.required' => "Tên nguồn traffic không được để trống",
        ];
    }

}
