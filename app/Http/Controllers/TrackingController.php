<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CpsCampaign;
use App\Models\CpsDevice;
use App\Models\CpsClick;
use Validator;
use Agent;

class TrackingController extends Controller {

    public function trackingV3(Request $request, $campaign_id) {
        $validator = Validator::make($request->all(), [
                    'url' => "required|url",
        ]);
        if (!$validator->fails()) {
            try {
                $campaign = CpsCampaign::select([
                            'cps_campaigns.campaign_id',
                            'cps_campaigns.campaign_user_id',
                            'cps_campaigns.campaign_traffic_source_id',
                            'publisher_accounts.account_id',
                            'publisher_accounts.account_affiliate_token',
                            'ads_networks.network_id',
                            'ads_networks.network_link_affiliate'
                        ])
                        ->join('publisher_accounts', 'publisher_accounts.account_id', '=', 'cps_campaigns.campaign_publisher_account_id')
                        ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id')
                        ->where('cps_campaigns.campaign_id', $campaign_id)
                        ->first();
                if (!isset($_COOKIE['tracking_device'])) {
                    $device = new CpsDevice;
                    $device->device_user_id = $campaign->campaign_user_id;
                    $device->device_traffic_source_id = $campaign->campaign_traffic_source_id;
                    $device->device_publisher_account_id = $campaign->campaign_publisher_account_id;
                    $device->device_network_id = $campaign->network_id;
                    $device->device_campaign_id = $campaign->campaign_id;
                    if ($request->has('device')) {
                        $device->device_type = $request->input('device');
                    } elseif (Agent::isMobile()) {
                        $device->device_type = 'm';
                    } elseif (Agent::isTablet()) {
                        $device->device_type = 't';
                    } else {
                        $device->device_type = 'c';
                    }
                    if ($request->has('device_model')) {
                        $device->device_model = $request->input('device_model');
                    } else {
                        $device->device_model = Agent::browser();
                    }
                    $device->device_criteria_id = $request->input('loc');
                    $device->device_ip = $request->ip();
                    $device->device_user_agent = $request->header('User-Agent');
                    $device->device_created_at = microtime(true);
                    $device->device_created_fm = date('ym', $device->device_created_at);
                    $device->device_created_fd = date('ymd', $device->device_created_at);
                    $device->save();
                    $device_id = $device->device_id;
                    setcookie('tracking_device', $device->device_id, time() + (10 * 365 * 24 * 60 * 60), '/');
                } else {
                    $device_id = $_COOKIE['tracking_device'];
                }
                if (!isset($_COOKIE['tracking_click_' . md5($request->input('url'))])) {
                    $click = new CpsClick;
                    $click->click_lpurl = $request->input('url');
                    $click->click_device_id = $device_id;
                    $click->click_user_id = $campaign->campaign_user_id;
                    $click->click_traffic_source_id = $campaign->campaign_traffic_source_id;
                    $click->click_publisher_account_id = $campaign->campaign_publisher_account_id;
                    $click->click_network_id = $campaign->network_id;
                    $click->click_campaign_id = $campaign->campaign_id;
                    $click->click_keyword = $request->input('keyword');
                    $click->click_counts = 1;
                    $click->click_criteria_id = $request->input('loc');
                    $click->click_aff_sub1 = $request->input('aff_sub1');
                    $click->click_aff_sub2 = $request->input('aff_sub2');
                    $click->click_aff_sub3 = $request->input('aff_sub3');
                    $click->click_aff_sub4 = $request->input('aff_sub4');
                    $click->click_created_at = microtime(true);
                    $click->click_created_fm = date('ym', $click->click_created_at);
                    $click->click_created_fd = date('ymd', $click->click_created_at);
                    $click->save();
                    setcookie('tracking_click_' . md5($request->input('url')), $click->click_id, time() + 86400, '/');
                } else {
                    $click = CpsClick::where('click_id', $_COOKIE['tracking_click_' . md5($request->input('url'))])->first();
                    $click->click_counts += 1;
                    $click->save();
                }
                $url = $campaign->network_link_affiliate;
                $url = str_replace('{token}', $campaign->account_affiliate_token, $url);
                $url = str_replace('{click_id}', $click->click_id, $url);
                $url = str_replace('{device_id}', $device_id, $url);
            } catch (\Exception $exc) {
                $url = env('TRACKING_FAIL_URL');
            }
            $url = str_replace('{url}', urlencode($request->input('url')), $url);
            //     return redirect($url);
            return view('tracking', ['url' => $url]);
        }
        abort(404);
    }

}
