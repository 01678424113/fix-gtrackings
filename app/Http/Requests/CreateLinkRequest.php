<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateLinkRequest extends Request {

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
            'txt-url' => "required",
            'sl-type' => "required|alpha_num",
            'sl-campaign' => "required|alpha_num",
        ];
    }

    public function messages() {
        return [
            'txt-url.required' => "URL không được để trống",
            'sl-type.required' => "Loại quảng cáo không hợp lệ",
            'sl-type.alpha_num' => "Loại quảng cáo không hợp lệ",
            'sl-campaign.required' => "Chiến dịch không hợp lệ",
            'sl-campaign.alpha_num' => "Chiến dịch không hợp lệ",
        ];
    }

}
