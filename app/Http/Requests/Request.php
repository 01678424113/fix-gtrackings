<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

abstract class Request extends FormRequest {

    protected function formatErrors(Validator $validator) {
        return $validator->errors()->all();
    }

    public function response(array $errors) {
        if (($this->ajax() && !$this->pjax()) || $this->wantsJson()) {
            return response()->json([
                        "status_code" => 422,
                        "message" => $errors[0],
            ]);
        }
        return $this->redirector->to($this->getRedirectUrl())
                        ->withInput($this->except($this->dontFlash))
                        ->with('error', $errors[0]);
    }

}
