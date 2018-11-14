<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CpsCampaignEditRequest extends Request {

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
            'sl-source' => "required|alpha_num",
            'sl-publisher-account' => "required|alpha_num",
            'txt-url' => "url",
            'sl-domain' => "required"
        ];
    }

    public function messages() {
        return [
            'txt-id.required' => "Chiến dịch quảng cáo không hợp lệ",
            'txt-id.alpha_num' => "Chiến dịch quảng cáo không hợp lệ",
            'txt-name.required' => "Tên chiến dịch quảng cáo không được để trống",
            'sl-source.required' => "Nguồn traffic không hợp lệ",
            'sl-source.alpha_num' => "Nguồn traffic không hợp lệ",
            'sl-publisher-account.required' => "Tài khoản publisher không hợp lệ",
            'sl-publisher-account.alpha_num' => "Tài khoản publisher không hợp lệ",
            'txt-url.url' => "URL đích không hợp lệ",
            'sl-domain.required' => "Domain tracking không hợp lệ",
        ];
    }

}
