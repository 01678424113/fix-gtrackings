<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CpsCampaignAddRequest;
use App\Http\Requests\CpsCampaignEditRequest;
use App\Http\Requests\DeleteRequest;
use App\Models\AdsNetwork;
use App\Models\PublisherAccount;
use App\Models\TrafficSource;
use App\Models\AdwordsAccount;
use App\Models\CpsMerchant;
use App\Models\CpsCampaign;
use Validator;
use DB;

class CpsCampaignController extends Controller
{

    public function index(Request $request)
    {
        $response = [
            'title' => "Chiến dịch quảng cáo"
        ];
        $campaign_query = CpsCampaign::select([
            'cps_campaigns.campaign_id',
            'cps_campaigns.campaign_name',
            'cps_campaigns.campaign_status',
            'traffic_sources.source_name',
            'ads_networks.network_name',
            'publisher_accounts.account_username',
            'adwords_accounts.account_name',
            'cps_merchants.merchant_domain',
        ])
            ->join('traffic_sources', 'traffic_sources.source_id', '=', 'cps_campaigns.campaign_traffic_source_id')
            ->join('publisher_accounts', 'publisher_accounts.account_id', '=', 'cps_campaigns.campaign_publisher_account_id')
            ->leftJoin('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
            ->leftJoin('adwords_accounts', 'adwords_accounts.account_id', '=', 'cps_campaigns_adwords.campaign_adwords_account')
            ->join('cps_merchants', 'cps_merchants.merchant_id', '=', 'cps_campaigns.campaign_merchant_id')
            ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id');
        if ($request->session()->get('user')->user_id != 0) {
            $campaign_query->where('cps_campaigns.campaign_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('name') && $request->input('name') != "") {
            $campaign_query->where('cps_campaigns.campaign_name', 'LIKE', '%' . $request->input('name') . '%');
        }
        if ($request->has('source') && is_numeric($request->input('source'))) {
            $campaign_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('source'));
        }
        if ($request->has('merchant') && is_numeric($request->input('merchant'))) {
            $campaign_query->where('cps_campaigns.campaign_merchant_id', $request->input('merchant'));
        }
        if ($request->has('network') && is_numeric($request->input('network'))) {
            $campaign_query->where('ads_networks.network_id', $request->input('network'));
        }
        if ($request->has('p_account') && is_numeric($request->input('p_account'))) {
            $campaign_query->where('publisher_accounts.campaign_publisher_account_id', $request->input('p_account'));
        }
        if ($request->has('a_account') && is_numeric($request->input('a_account'))) {
            $campaign_query->where('adwords_accounts.account_id', $request->input('a_account'));
        }
        $campaign_query->orderBy('cps_campaigns.campaign_created_at', 'DESC');
        $response['campaigns'] = $campaign_query->paginate(env('PAGINATE_ITEM', 20));

        $response['traffic_sources'] = TrafficSource::orderBy('source_name', 'ASC')->get();
        $response['adwords_accounts'] = AdwordsAccount::select([
            'account_id',
            'account_name'
        ])
            ->get();
        $response['cps_merchants'] = CpsMerchant::orderBy('merchant_domain', 'ASC')->get();
        $response['ads_networks'] = AdsNetwork::orderBy('network_name', 'ASC')
            ->where('network_type', 2)->get();
        $response['publisher_accounts'] = PublisherAccount::select([
            'publisher_accounts.account_id',
            'publisher_accounts.account_username',
            'ads_networks.network_name'
        ])
            ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id')
            //    ->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id)
            ->orderBy('ads_networks.network_name', 'ASC')
            ->get();
        return view('cps.campaign', $response);
    }

    public function doAddCampaign(CpsCampaignAddRequest $request)
    {
        try {
            $campaign = new CpsCampaign;
            $campaign->campaign_name = trim($request->input('txt-name'));
            $campaign->campaign_url = trim($request->input('txt-url'));
            $campaign->campaign_domain_tracking = $request->input('sl-domain');
            try {
                $parse_url = parse_url($campaign->campaign_url);
                if (isset($parse_url['host'])) {
                    $merchant = CpsMerchant::where('merchant_domain', $parse_url['host'])->first();
                    if (empty($merchant)) {
                        $merchant = new CpsMerchant;
                        $merchant->merchant_domain = $parse_url['host'];
                        $merchant->merchant_status = 1;
                        $merchant->merchant_created_at = microtime(true);
                        $merchant->merchant_created_by = $request->session()->get('user')->user_id;
                        $merchant->save();
                    }
                    $campaign->campaign_merchant_id = $merchant->merchant_id;
                }
            } catch (\Exception $exc) {
                return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
            }
            $campaign->campaign_publisher_account_id = $request->input('sl-publisher-account');
            $campaign->campaign_user_id = $request->session()->get('user')->user_id;
            $campaign->campaign_traffic_source_id = $request->input('sl-source');
            $campaign->campaign_status = $request->input('rd-status');
            $campaign->campaign_created_at = microtime(true);
            $campaign->campaign_created_by = $request->session()->get('user')->user_id;
            try {
                $campaign->save();
                try {
                    if ($campaign->campaign_traffic_source_id == 1) {
                        DB::table('cps_campaigns_adwords')->insert([
                            'campaign_id' => $campaign->campaign_id,
                            'campaign_adwords_account' => $request->input('sl-adwords-account'),
                            'campaign_adwords_id' => $request->input('txt-adwords-id')
                        ]);
                    }
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
                return redirect()->back()->with('success', 'Thêm chiến dịch quảng cáo "' . $campaign->campaign_name . '" thành công');
            } catch (\Exception $exc) {
                return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function loadCampaign(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'campaign_id' => "required|alpha_num",
            ], [
                'campaign_id.required' => "Chiến dịch quảng cáo không hợp lệ",
                'campaign_id.alpha_num' => "Chiến dịch quảng cáo không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $campaign_query = CpsCampaign::select([
                        'cps_campaigns.campaign_id',
                        'cps_campaigns.campaign_name',
                        'cps_campaigns.campaign_status',
                        'cps_campaigns.campaign_traffic_source_id',
                        'cps_campaigns.campaign_publisher_account_id',
                        'cps_campaigns.campaign_merchant_id',
                        'cps_campaigns_adwords.campaign_adwords_account',
                        'cps_campaigns_adwords.campaign_adwords_id',
                        'cps_campaigns.campaign_url',
                        'cps_campaigns.campaign_domain_tracking',
                    ])
                        ->leftJoin('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                        ->where('cps_campaigns.campaign_id', $request->input('campaign_id'));
                    if ($request->session()->get('user')->user_id != 0) {
                        $campaign_query->where('cps_campaigns.campaign_user_id', $request->session()->get('user')->user_id);
                    }
                    $campaign = $campaign_query->first();
                    if (!empty($campaign)) {
                        return response()->json([
                            "status_code" => 200,
                            "data" => $campaign
                        ]);
                    } else {
                        return response()->json([
                            "status_code" => 404,
                            "message" => "Chiến dịch quảng cáo không tồn tại",
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

    public function doEditCampaign(CpsCampaignEditRequest $request)
    {
        try {
            $campaign_query = CpsCampaign::where('campaign_id', $request->input('txt-id'));
            if ($request->session()->get('user')->user_id != 0) {
                $campaign_query->where('cps_campaigns.campaign_user_id', $request->session()->get('user')->user_id);
            }
            $campaign = $campaign_query->first();
            if (!empty($campaign)) {
                $campaign->campaign_name = trim($request->input('txt-name'));
                $campaign->campaign_url = trim($request->input('txt-url'));
                $campaign->campaign_domain_tracking = $request->input('sl-domain');
                try {
                    $parse_url = parse_url($campaign->campaign_url);
                    if (isset($parse_url['host'])) {
                        $merchant = CpsMerchant::where('merchant_domain', $parse_url['host'])->first();
                        if (empty($merchant)) {
                            $merchant = new CpsMerchant;
                            $merchant->merchant_domain = $parse_url['host'];
                            $merchant->merchant_status = 1;
                            $merchant->merchant_created_at = microtime(true);
                            $merchant->merchant_created_by = $request->session()->get('user')->user_id;
                            $merchant->save();
                        }
                        $campaign->campaign_merchant_id = $merchant->merchant_id;
                    }
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
                $campaign->campaign_publisher_account_id = $request->input('sl-publisher-account');
                $campaign->campaign_user_id = $request->session()->get('user')->user_id;
                $campaign->campaign_traffic_source_id = $request->input('sl-source');
                $campaign->campaign_status = $request->input('rd-status');
                $campaign->campaign_updated_at = microtime(true);
                $campaign->campaign_updated_by = $request->session()->get('user')->user_id;
                try {
                    $campaign->save();
                    try {
                        DB::table('cps_campaigns_adwords')->where('campaign_id', $campaign->campaign_id)->update([
                            'campaign_adwords_account' => $request->input('sl-adwords-account'),
                            'campaign_adwords_id' => $request->input('txt-adwords-id')
                        ]);
                    } catch (\Exception $exc) {
                        return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                    }
                    return redirect()->back()->with('success', 'Sửa chiến dịch quảng cáo "' . $campaign->account_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
            } else {
                return redirect()->back()->with('error', "Chiến dịch quảng cáo không tồn tại");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function loadCostCampaign(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'campaign_id' => "required|alpha_num",
                'date' => "required|regex:/^(\d{2})(\d{2})(\d{2})$/",
            ], [
                'campaign_id.required' => "Chiến dịch quảng cáo không hợp lệ",
                'campaign_id.alpha_num' => "Chiến dịch quảng cáo không hợp lệ",
                'date.required' => "Ngày không hợp lệ",
                'date.regex' => "Ngày không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $campaign_query = CpsCampaign::select([
                        'campaign_id',
                        'campaign_name'
                    ])->where('campaign_id', $request->input('campaign_id'));
                    if ($request->session()->get('user')->user_id != 0) {
                        $campaign_query->where('campaign_user_id', $request->session()->get('user')->user_id);
                    }
                    $campaign = $campaign_query->first();
                    if (!empty($campaign)) {
                        $campaign->date = $request->input('date');
                        $cost = DB::table('cps_campaign_cost')->where([
                            'campaign_id' => $campaign->campaign_id,
                            'cost_fd' => $request->input('date')
                        ])->first();
                        if (!empty($cost)) {
                            $campaign->cost = $cost->cost_value;
                        } else {
                            $campaign->cost = "";
                        }
                        return response()->json([
                            "status_code" => 200,
                            "data" => $campaign
                        ]);
                    } else {
                        return response()->json([
                            "status_code" => 404,
                            "message" => "Chiến dịch quảng cáo không tồn tại",
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

    public function doUpdateCostCampaign(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'txt-id' => "required|alpha_num",
                'txt-date' => "required|regex:/^(\d{2})\/(\d{2})\/(\d{4})$/",
                'txt-cost' => "required|alpha_num|min:0",
            ], [
                'txt-id.required' => "Chiến dịch quảng cáo không hợp lệ",
                'txt-id.alpha_num' => "Chiến dịch quảng cáo không hợp lệ",
                'txt-date.required' => "Ngày không được để trống",
                'txt-date.regex' => "Ngày không hợp lệ",
                'txt-cost.required' => "Chi phí không được để trống",
                'txt-cost.alpha_num' => "Chi phí không hợp lệ",
                'txt-cost.min' => "Chi phí không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $campaign_query = CpsCampaign::select(['campaign_id', 'campaign_name'])
                        ->where('campaign_id', $request->input('txt-id'));
                    if ($request->session()->get('user')->user_id != 0) {
                        $campaign_query->where('campaign_user_id', $request->session()->get('user')->user_id);
                    }
                    $campaign = $campaign_query->first();
                    $fd = preg_replace("/^(\d{2})\/(\d{2})\/.*?(\d{2})$/", "$3$2$1", $request->input('txt-date'));
                    $fm = preg_replace("/^(\d{2})\/(\d{2})\/.*?(\d{2})$/", "$3$2", $request->input('txt-date'));
                    if (!empty($campaign)) {
                        $campaign_cost = DB::table('cps_campaign_cost')
                            ->where('campaign_id', $campaign->campaign_id)
                            ->where('cost_fd', $fd)
                            ->first();
                        if (empty($campaign_cost)) {
                            DB::table('cps_campaign_cost')->insert([
                                'campaign_id' => $campaign->campaign_id,
                                'cost_fd' => $fd,
                                'cost_fm' => $fm,
                                'cost_value' => $request->input('txt-cost'),
                            ]);
                        } else {
                            DB::table('cps_campaign_cost')
                                ->where('cost_fd', $fd)
                                ->where('campaign_id', $campaign->campaign_id)
                                ->update([
                                    'cost_value' => $request->input('txt-cost')
                                ]);
                        }
                        return response()->json([
                            "status_code" => 200,
                            "message" => 'Cập nhật chi phí cho chiến dịch "' . $campaign->campaign_name . '" thành công',
                        ]);
                    } else {
                        return response()->json([
                            "status_code" => 404,
                            "message" => "Chiến dịch quảng cáo không tồn tại",
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

    public function doDeleteCampaign(DeleteRequest $request)
    {
        try {
            $campaign_query = CpsCampaign::select(['campaign_name', 'campaign_id'])
                ->where('campaign_id', $request->input('txt-id'));
            if ($request->session()->get('user')->user_id != 0) {
                $campaign_query->where('cps_campaigns.campaign_user_id', $request->session()->get('user')->user_id);
            }
            $campaign = $campaign_query->first();
            if (!empty($campaign)) {
                try {
                    $campaign->delete();
                    return redirect()->back()->with('success', 'Xóa chiến dịch quảng cáo "' . $campaign->campaign_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
                }
            } else {
                return redirect()->back()->with('error', 'Chiến dịch quảng cáo không tồn tại');
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
        }
    }

    public function linkTracking(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'campaign_id' => "required|alpha_num",
            ], [
                'campaign_id.required' => "Chiến dịch quảng cáo không hợp lệ",
                'campaign_id.alpha_num' => "Chiến dịch quảng cáo không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $campaign = CpsCampaign::select([
                        'cps_campaigns.campaign_id',
                        'cps_campaigns.campaign_name',
                        'cps_campaigns.campaign_url',
                        'cps_campaigns.campaign_domain_tracking',
                        'cps_campaigns.campaign_traffic_source_id',
                        'publisher_accounts.account_network_id'
                    ])
                        ->join('publisher_accounts', 'publisher_accounts.account_id', '=', 'cps_campaigns.campaign_publisher_account_id')
                        ->where('cps_campaigns.campaign_id', $request->input('campaign_id'))
                        ->first();
                    if (!empty($campaign)) {
                        $final = $campaign->campaign_url;
                        $tracking = 'http://' . $campaign->campaign_domain_tracking . '/trackings/v3/';
                        $tracking .= $campaign->campaign_id . '?';
                        if ($campaign->campaign_traffic_source_id == 1) {
                            $final .= '?{ignore}';
                            $parse_url = parse_url($campaign->campaign_url);
                            if (isset($parse_url['host'])) {
                                if (preg_match("/lazada/", $parse_url['host'])) {
                                    switch ($campaign->account_network_id) {
                                        case 1:
                                            $final .= "offer_name&affiliate_id&offer_id&transaction_id&affiliate_name&aff_source";
                                            break;
                                        case 2:
                                            $final .= "offer_id&affiliate_id&offer_name&affiliate_name&transaction_id&aff_source";
                                            break;
                                    }
                                } else if (preg_match("/fptshop/", $parse_url['host'])) {
                                    switch ($campaign->account_network_id) {
                                        case 1:
                                            $final .= "utm_source&aff_sid";
                                            break;
                                        case 2:
                                            $final .= "utm_source&traffic_id";
                                            break;
                                    }
                                } else if (preg_match("/adayroi/", $parse_url['host'])) {
                                    switch ($campaign->account_network_id) {
                                        case 1:
                                            $final .= "utm_source&traffic_id";
                                            break;
                                        case 2:
                                            $final .= "utm_source&utm_medium&traffic_id";
                                            break;
                                    }
                                } else if (preg_match("/lotte/", $parse_url['host'])) {
                                    switch ($campaign->account_network_id) {
                                        case 1:
                                            $final .= "utm_campaign&utm_medium&utm_source";
                                            break;
                                        case 2:
                                            $final .= "utm_source&ref&utm_medium&utm_content";
                                            break;
                                    }
                                }
                            }
                            $tracking .= 'url={escapedlpurl}';
                            $tracking .= '&loc={loc_physical_ms}';
                            $tracking .= '&device={device}';
                            $tracking .= '&device_model={devicemodel}';
                            $tracking .= '&cid={campaignid}';
                            $tracking .= '&gid={adgroupid}';
                            $tracking .= '&keyword={keyword}';
                        } else {
                            $tracking .= 'url=' . urlencode($campaign->campaign_url);
                            $tracking .= '&loc=';
                            $tracking .= '&device=';
                            $tracking .= '&device_model=';
                            $tracking .= '&cid=';
                            $tracking .= '&gid=';
                            $tracking .= '&keyword=';
                        }
                        $tracking .= '&aff_sub1=';
                        $tracking .= '&aff_sub2=';
                        $tracking .= '&aff_sub3=';
                        $tracking .= '&aff_sub4=';
                        return response()->json([
                            "status_code" => 200,
                            "data" => [
                                'campaign_name' => $campaign->campaign_name,
                                'domain_tracking' => $campaign->campaign_domain_tracking,
                                'final' => $final,
                                'tracking' => $tracking
                            ]
                        ]);
                    } else {
                        return response()->json([
                            "status_code" => 404,
                            "message" => "Chiến dịch quảng cáo không tồn tại",
                        ]);
                    }
                } catch (\Exception $exc) {
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

}
