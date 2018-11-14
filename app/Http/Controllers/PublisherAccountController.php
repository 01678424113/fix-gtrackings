<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeleteRequest;
use App\Models\PublisherAccount;
use App\Models\AdsNetwork;
use App\Http\Requests\PublisherAccountAddRequest;
use App\Http\Requests\PublisherAccountEditRequest;
use Validator;

class PublisherAccountController extends Controller {

    public function index(Request $request) {
        $response = [
            'title' => "Tài khoản publisher"
        ];
        $account_query = PublisherAccount::select([
                    'publisher_accounts.account_id',
                    'publisher_accounts.account_username',
                    'publisher_accounts.account_password',
                    'publisher_accounts.account_affiliate_token',
                    'publisher_accounts.account_affiliate_api_token',
                    'ads_networks.network_name',
                    'ads_networks.network_domain',
                ])
                ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id');
        if ($request->session()->get('user')->user_id != 0) {
            $account_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $account_query->orderBy('publisher_accounts.account_created_at', 'DESC');
        $response['accounts'] = $account_query->paginate(env('PAGINATE_ITEM', 20));
        $response['networks'] = AdsNetwork::orderBy('network_name', 'ASC')->get();
        return view('publisherAccount.index', $response);
    }

    public function doAddAccount(PublisherAccountAddRequest $request) {
        try {
            $account = PublisherAccount::select(['account_id'])
                    ->where([
                        'account_username' => trim($request->input('txt-username')),
                        'account_network_id' => $request->input('sl-network')
                    ])
                    ->first();
            if (empty($account)) {
                $account = new PublisherAccount;
                $account->account_user_id = $request->session()->get('user')->user_id;
                $account->account_username = trim($request->input('txt-username'));
                $account->account_password = $request->input('txt-password');
                $account->account_network_id = $request->input('sl-network');
                $account->account_affiliate_token = trim($request->input('txt-affiliate-token'));
                $account->account_affiliate_api_token = trim($request->input('txt-affiliate-api-token'));
                $account->account_status = 1;
                $account->account_created_at = microtime(true);
                $account->account_created_by = $request->session()->get('user')->user_id;
                try {
                    $account->save();
                    return redirect()->back()->with('success', 'Thêm tài khoản publisher "' . $account->account_username . '" thành công');
                } catch (\Exception $exc) {
                    dd($exc);
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
            } else {
                return redirect()->back()->with('error', "Tài khoản publisher đã tồn tại");
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
                        'account_id.required' => "Tài khoản publisher không hợp lệ",
                        'account_id.alpha_num' => "Tài khoản publisher không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $account_query = PublisherAccount::where('account_id', $request->input('account_id'));
                    if ($request->session()->get('user')->user_id != 0) {
                        $account_query->where('account_user_id', $request->session()->get('user')->user_id);
                    }
                    $account = $account_query->first();
                    if (!empty($account)) {
                        return response()->json([
                                    "status_code" => 200,
                                    "data" => $account
                        ]);
                    } else {
                        return response()->json([
                                    "status_code" => 404,
                                    "message" => "Tài khoản publisher không tồn tại",
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

    public function doEditAccount(PublisherAccountEditRequest $request) {
        try {
            $account_query = PublisherAccount::where('account_id', $request->input('txt-id'));
            if ($request->session()->get('user')->user_id != 0) {
                $account_query->where('account_user_id', $request->session()->get('user')->user_id);
            }
            $account = $account_query->first();
            if (!empty($account)) {
                $account_exist = PublisherAccount::select(['account_id'])
                        ->where([
                            'account_username' => trim($request->input('txt-username')),
                            'account_network_id' => $request->input('sl-network')
                        ])
                        ->where('account_id', '<>', $account->account_id)
                        ->first();
                if (empty($account_exist)) {
                    $account->account_username = trim($request->input('txt-username'));
                    $account->account_password = $request->input('txt-password');
                    $account->account_network_id = $request->input('sl-network');
                    $account->account_affiliate_token = trim($request->input('txt-affiliate-token'));
                    $account->account_affiliate_api_token = trim($request->input('txt-affiliate-api-token'));
                    $account->account_updated_at = microtime(true);
                    $account->account_updated_by = $request->session()->get('user')->user_id;
                    try {
                        $account->save();
                        return redirect()->back()->with('success', 'Sửa tài khoản publisher "' . $account->account_username . '" thành công');
                    } catch (\Exception $exc) {
                        return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                    }
                } else {
                    return redirect()->back()->with('error', "Tài khoản publisher đã tồn tại");
                }
            } else {
                return redirect()->back()->with('error', "Tài khoản publisher không tồn tại");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function doDeleteAccount(DeleteRequest $request) {
        try {
            $account_query = PublisherAccount::select(['account_username', 'account_id']);
            if ($request->session()->get('user')->user_id != 0) {
                $account_query->where('account_user_id', $request->session()->get('user')->user_id);
            }
            $account_query->where('account_id', $request->input('txt-id'));
            $account = $account_query->first();
            if (!empty($account)) {
                try {
                    $account->delete();
                    return redirect()->back()->with('success', 'Xóa tài khoản publisher "' . $account->account_username . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
                }
            } else {
                return redirect()->back()->with('error', 'Tài khoản publisher không tồn tại');
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
        }
    }

}
