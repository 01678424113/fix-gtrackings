<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeleteRequest;
use App\Models\AdwordsAccount;
use App\Http\Requests\AdwordsAccountAddRequest;
use App\Http\Requests\AdwordsAccountEditRequest;
use Validator;

class AdwordsAccountController extends Controller {

    public function index(Request $request) {
        $response = [
            'title' => "Tài khoản adwords"
        ];
        $account_query = AdwordsAccount::orderBy('account_created_at', 'DESC');
        if ($request->session()->get('user')->user_id != 0) {
            $account_query->where('account_user_id', $request->session()->get('user')->user_id);
        }
        $response['accounts'] = $account_query->paginate(env('PAGINATE_ITEM', 20));
        return view('adwordsAccount.index', $response);
    }

    public function doAddAccount(AdwordsAccountAddRequest $request) {
        try {
            $account = AdwordsAccount::select(['account_id'])
                    ->where('account_id', $request->input('txt-adwords-id'))
                    ->first();
            if (empty($account)) {
                $account = new AdwordsAccount;
                $account->account_name = $request->input('txt-name');
                $account->account_id = $request->input('txt-adwords-id');
                $account->account_user_id = $request->session()->get('user')->user_id;
                $account->account_status = 1;
                $account->account_created_at = microtime(true);
                $account->account_created_by = $request->session()->get('user')->user_id;

                try {
                    $account->save();
                    return redirect()->back()->with('success', 'Thêm tài khoản adwords "' . $account->account_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
            } else {
                return redirect()->back()->with('error', "Tài khoản adwords đã tồn tại");
            }
        } catch (\Exception $exc) {
            dd($exc);
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function loadAccount(Request $request) {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                        'account_id' => "required|alpha_num",
                            ], [
                        'account_id.required' => "Tài khoản adwords không hợp lệ",
                        'account_id.alpha_num' => "Tài khoản adwords không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $account = AdwordsAccount::select([
                                        'account_id',
                                        'account_name'
                                    ])
                                    //     ->where('account_user_id', $request->session()->get('user')->user_id)
                                    ->where('account_id', $request->input('account_id'))->first();
                    if (!empty($account)) {
                        return response()->json([
                                    "status_code" => 200,
                                    "data" => $account
                        ]);
                    } else {
                        return response()->json([
                                    "status_code" => 404,
                                    "message" => "Tài khoản adwords không tồn tại",
                        ]);
                    }
                } catch (\Exception $ex) {
                    return response()->json([
                                "status_code" => 500,
                                "message" => "Lỗi trong quá trình xử lý dữ liệu",
                    ]);
                }
            } else {
                return response()->json([
                            "status_code" => 422,
                            "message" => $validator->errors()->first(),
                ]);
            }
        }
        return redirect()->action('HomeController@index')->with('error', 'Không được truy cập trực tiếp');
    }

    public function doEditAccount(AdwordsAccountEditRequest $request) {
        try {
            $account = AdwordsAccount::where('account_id', $request->input('txt-id'))
                    //    ->where('account_user_id', $request->session()->get('user')->user_id)
                    ->first();
            if (!empty($account)) {
                $account_exist = AdwordsAccount::select(['account_id'])
                        ->where('account_id', $request->input('txt-adwords-id'))
                        ->where('account_id', '<>', $account->account_id)
                        ->first();
                if (empty($account_exist)) {
                    $account->account_name = $request->input('txt-name');
                    $account->account_id = $request->input('txt-adwords-id');
                    $account->account_updated_at = microtime(true);
                    $account->account_updated_by = $request->session()->get('user')->user_id;
                    try {
                        $account->save();
                        return redirect()->back()->with('success', 'Sửa tài khoản adwords "' . $account->account_name . '" thành công');
                    } catch (\Exception $exc) {
                        return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                    }
                } else {
                    return redirect()->back()->with('error', "Tài khoản adwords đã tồn tại");
                }
            } else {
                return redirect()->back()->with('error', "Tài khoản adwords không tồn tại");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function doDeleteAccount(DeleteRequest $request) {
        try {
            $account = AdwordsAccount::select(['account_name', 'account_id'])
                            ->where('account_user_id', $request->session()->get('user')->user_id)
                            ->where('account_id', $request->input('txt-id'))->first();
            if (!empty($account)) {
                try {
                    $account->delete();
                    return redirect()->back()->with('success', 'Xóa tài khoản adwords "' . $account->account_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
                }
            } else {
                return redirect()->back()->with('error', 'Tài khoản adwords không tồn tại');
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
        }
    }

}
