<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\AdsNetworkAddRequest;
use App\Http\Requests\AdsNetworkEditRequest;
use App\Models\AdsNetwork;
use Validator;

class AdsNetworkController extends Controller {

    public function index(Request $request) {
        $response = [
            'title' => "Mạng quảng cáo"
        ];
        $networks_query = AdsNetwork::orderBy('network_created_at', 'DESC');
        $response['networks'] = $networks_query->paginate(env('PAGINATE_ITEM', 20));
        return view('adsNetwork.index', $response);
    }

    public function doAddNetwork(AdsNetworkAddRequest $request) {
        try {
            $network = AdsNetwork::select(['network_id'])
                    ->where('network_domain', $request->input('txt-domain'))
                    ->first();
            if (empty($network)) {
                $network = new AdsNetwork;
                $network->network_name = $request->input('txt-name');
                $network->network_domain = $request->input('txt-domain');
                $network->network_type = $request->input('sl-type');
                $network->network_link_affiliate = $request->input('txt-affiliate-link');
                $network->network_status = 1;
                $network->network_created_at = microtime(true);
                $network->network_created_by = $request->session()->get('user')->user_id;
                try {
                    $network->save();
                    return redirect()->back()->with('success', 'Thêm mạng quảng cáo "' . $network->network_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
            } else {
                return redirect()->back()->with('error', "Mạng quảng cáo đã tồn tại");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function loadNetwork(Request $request) {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                        'network_id' => "required|alpha_num",
                            ], [
                        'network_id.required' => "Mạng quảng cáo không hợp lệ",
                        'network_id.alpha_num' => "Mạng quảng cáo không hợp lệ",
            ]);

            if (!$validator->fails()) {
                try {
                    $network = AdsNetwork::where('network_id', $request->input('network_id'))->first();
                    if (!empty($network)) {
                        return response()->json([
                                    "status_code" => 200,
                                    "data" => $network
                        ]);
                    } else {
                        return response()->json([
                                    "status_code" => 404,
                                    "message" => "Mạng quảng cáo không tồn tại",
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

    public function doEditNetwork(AdsNetworkEditRequest $request) {
        try {
            $network = AdsNetwork::where('network_id', $request->input('txt-id'))->first();
            if (!empty($network)) {
                $network_exist = AdsNetwork::select(['network_id'])
                        ->where('network_domain', $request->input('txt-domain'))
                        ->where('network_id', '<>', $network->network_id)
                        ->first();
                if (empty($network_exist)) {
                    $network->network_name = $request->input('txt-name');
                    $network->network_domain = $request->input('txt-domain');
                    $network->network_type = $request->input('sl-type');
                    $network->network_link_affiliate = $request->input('txt-affiliate-link');
                    $network->network_updated_at = microtime(true);
                    $network->network_updated_by = $request->session()->get('user')->user_id;
                    try {
                        $network->save();
                        return redirect()->back()->with('success', 'Sửa mạng quảng cáo "' . $network->network_name . '" thành công');
                    } catch (\Exception $exc) {
                        dd($exc);
                        return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                    }
                } else {
                    return redirect()->back()->with('error', "Mạng quảng cáo đã tồn tại");
                }
            } else {
                return redirect()->back()->with('error', "Mạng quảng cáo không tồn tại");
            }
        } catch (\Exception $exc) {
            dd($exc);
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function doDeleteNetwork(DeleteRequest $request) {
        try {
            $network = AdsNetwork::select(['network_name', 'network_id'])
                            ->where('network_id', $request->input('txt-id'))->first();
            if (!empty($network)) {
                try {
                    $network->delete();
                    return redirect()->back()->with('success', 'Xóa mạng quảng cáo "' . $network->network_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
                }
            } else {
                return redirect()->back()->with('error', 'Mạng quảng cáo không tồn tại');
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
        }
    }

}
