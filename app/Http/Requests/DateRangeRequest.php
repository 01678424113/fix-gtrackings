<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DateRangeRequest extends Request {

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
            'start' => "regex:/^(\d{2})(\d{2})(\d{2})$/",
            'end' => "regex:/^(\d{2})(\d{2})(\d{2})$/"
        ];
    }

    public function messages() {
        return [
            'start.required' => "Ngày bắt đầu không hợp lệ",
            'start.regex' => "Ngày bắt đầu không hợp lệ",
            'end.required' => "Ngày kết thúc không hợp lệ",
        ];
    }

}
