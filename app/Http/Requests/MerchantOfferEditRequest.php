<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MerchantOfferEditRequest extends Request {

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
            'txt-domain' => "required",
        ];
    }

    public function messages() {
        return [
            'txt-id.required' => "Website thương mại không hợp lệ",
            'txt-id.alpha_num' => "Website thương mại không hợp lệ",
            'txt-domain.required' => "Website thương mại không được để trống",
        ];
    }

}
