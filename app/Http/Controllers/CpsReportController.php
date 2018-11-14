<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DateRangeRequest;
use App\Models\CpsCampaign;
use App\Models\CpsClick;
use App\Models\CpsDevice;
use App\Models\CpsOrder;
use App\Models\TrafficSource;
use App\Models\AdwordsAccount;
use App\Models\User;
use DateRange;
use Validator;
use DB;

class CpsReportController extends Controller {

    public function index(DateRangeRequest $request) {
        $response = [
            'title' => "Báo cáo CPS"
        ];
        $response['start'] = date('ymd', strtotime('-29 days'));
        $response['end'] = date('ymd');
        if ($request->has('start')) {
            $response['start'] = $request->input('start');
        }
        if ($request->has('end')) {
            $response['end'] = $request->input('end');
        }
        $today = date('ymd');
        $yesterday = date('ymd', strtotime('-1 day'));
        $this_month = date('ym');

        $clicks_today_query = CpsClick::where('click_created_fd', $today);
        if ($request->session()->get('user')->user_id != 0) {
            $clicks_today_query->where('click_user_id', $request->session()->get('user')->user_id);
        }
        $click['today'] = $clicks_today_query->count();

        $clicks_yesterday_query = CpsClick::where('click_created_fd', $yesterday);
        if ($request->session()->get('user')->user_id != 0) {
            $clicks_yesterday_query->where('click_user_id', $request->session()->get('user')->user_id);
        }
        $click['yesterday'] = $clicks_yesterday_query->count();
        if ($click['yesterday'] > 0) {
            $click['percent_today_yesterday'] = round(($click['today'] / $click['yesterday']) * 100, 2);
        } else {
            $click['percent_today_yesterday'] = 0;
        }
        $response['click'] = $click;
        $devices_today_query = CpsDevice::where('device_created_fd', $today);
        if ($request->session()->get('user')->user_id != 0) {
            $devices_today_query->where('device_user_id', $request->session()->get('user')->user_id);
        }
        $response['today_devices_count'] = $devices_today_query->count();
        $orders_today_query = CpsOrder::where('order_bought_fd', $today);
        if ($request->session()->get('user')->user_id != 0) {
            $orders_today_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $order['today_total'] = $orders_today_query->count();
        $order['today_payouts_total'] = $orders_today_query->sum('order_total_payout');
        $orders_today_query->where('order_status', 0);
        $order['today_pending'] = $orders_today_query->count();
        $order['today_payouts_pending'] = $orders_today_query->sum('order_total_payout');

        $orders_today_query = CpsOrder::where('order_bought_fd', $today);
        if ($request->session()->get('user')->user_id != 0) {
            $orders_today_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $orders_today_query->where('order_status', 1);
        $order['today_success'] = $orders_today_query->count();
        $order['today_payouts_success'] = $orders_today_query->sum('order_total_payout');

        $orders_yesterday_query = CpsOrder::where('order_bought_fd', $yesterday);
        if ($request->session()->get('user')->user_id != 0) {
            $orders_yesterday_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $order['yesterday_total'] = $orders_yesterday_query->count();
        $order['yesterday_payouts_total'] = $orders_yesterday_query->sum('order_total_payout');
        if ($order['yesterday_total'] > 0) {
            $order['percent_count_today_yesterday'] = round(($order['today_total'] / $order['yesterday_total']) * 100, 2);
        } else {
            $order['percent_count_today_yesterday'] = 0;
        }
        if ($order['yesterday_payouts_total'] > 0) {
            $order['percent_payouts_today_yesterday'] = round(($order['today_payouts_total'] / $order['yesterday_payouts_total']) * 100, 2);
        } else {
            $order['percent_payouts_today_yesterday'] = 0;
        }
        $response['order'] = $order;

        $orders_thismonth_query = CpsOrder::where('order_bought_fm', $this_month);
        if ($request->session()->get('user')->user_id != 0) {
            $orders_thismonth_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $response['thismonth_orders_payouts_total'] = $orders_thismonth_query->sum('order_total_payout');
        $orders_thismonth_query->where('order_status', 1);
        $response['thismonth_orders_payouts_success'] = $orders_thismonth_query->sum('order_total_payout');
        $orders_thismonth_query = CpsOrder::where('order_bought_fm', $this_month);
        if ($request->session()->get('user')->user_id != 0) {
            $orders_thismonth_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $orders_thismonth_query->where('order_status', -1);
        $response['thismonth_orders_payouts_cancel'] = $orders_thismonth_query->sum('order_total_payout');

        if ($response['thismonth_orders_payouts_success'] > 0) {
            $response['percent_payouts_success'] = round(($response['thismonth_orders_payouts_success'] / $response['thismonth_orders_payouts_total']) * 100, 2);
        } else {
            $response['percent_payouts_success'] = 0;
        }
        $dates = DateRange::get($response['start'], $response['end']);
        $orders_dates_click_query = CpsOrder::whereIn('order_click_fd', $dates)
                ->where('order_status', '<>', -1)
                ->orderBy('order_bought_at', 'DESC');
        if ($request->session()->get('user')->user_id != 0) {
            $orders_dates_click_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $orders_dates_click_raw = $orders_dates_click_query->get();
        $orders_dates_click = [];
        foreach ($orders_dates_click_raw as $order) {
            if (!array_key_exists($order->order_click_fd, $orders_dates_click)) {
                $orders_dates_click[$order->order_click_fd] = [
                    'payouts' => $order->order_total_payout,
                    'prices' => $order->order_total_price
                ];
            } else {
                $orders_dates_click[$order->order_click_fd]['payouts'] += $order->order_total_payout;
                $orders_dates_click[$order->order_click_fd]['prices'] += $order->order_total_price;
            }
        }
        $orders_dates_bought_query = CpsOrder::whereIn('order_bought_fd', $dates)
                ->orderBy('order_bought_at', 'DESC');
        if ($request->session()->get('user')->user_id != 0) {
            $orders_dates_bought_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $orders_dates_bought_raw = $orders_dates_bought_query->get();
        $orders_dates_bought = [];
        foreach ($orders_dates_bought_raw as $order) {
            if (!array_key_exists($order->order_bought_fd, $orders_dates_bought)) {
                $orders_dates_bought[$order->order_bought_fd] = [
                    'payouts' => $order->order_total_payout,
                    'prices' => $order->order_total_price
                ];
            } else {
                $orders_dates_bought[$order->order_bought_fd]['payouts'] += $order->order_total_payout;
                $orders_dates_bought[$order->order_bought_fd]['prices'] += $order->order_total_price;
            }
        }
        $orders_dates_cancel_query = CpsOrder::whereIn('order_bought_fd', $dates)
                ->where('order_status', -1)
                ->orderBy('order_bought_at', 'DESC');
        if ($request->session()->get('user')->user_id != 0) {
            $orders_dates_cancel_query->where('order_user_id', $request->session()->get('user')->user_id);
        }
        $orders_dates_cancel_raw = $orders_dates_cancel_query->get();
        $orders_dates_cancel = [];
        foreach ($orders_dates_cancel_raw as $order) {
            if (!array_key_exists($order->order_bought_fd, $orders_dates_cancel)) {
                $orders_dates_cancel[$order->order_bought_fd] = [
                    'payouts' => $order->order_total_payout,
                    'prices' => $order->order_total_price
                ];
            } else {
                $orders_dates_cancel[$order->order_bought_fd]['payouts'] += $order->order_total_payout;
                $orders_dates_cancel[$order->order_bought_fd]['prices'] += $order->order_total_price;
            }
        }
        foreach ($dates as $date) {
            if (!array_key_exists($date, $orders_dates_click)) {
                $orders_dates_click[$date] = [
                    'payouts' => 0,
                    'prices' => 0
                ];
            }
            if (!array_key_exists($date, $orders_dates_bought)) {
                $orders_dates_bought[$date] = [
                    'payouts' => 0,
                    'prices' => 0
                ];
            }
            if (!array_key_exists($date, $orders_dates_cancel)) {
                $orders_dates_cancel[$date] = [
                    'payouts' => 0,
                    'prices' => 0
                ];
            }
        }
        sort($dates);
        $response['dates'] = $dates;
        ksort($orders_dates_click);
        $response['orders_dates_click'] = $orders_dates_click;
        ksort($orders_dates_bought);
        $response['orders_dates_bought'] = $orders_dates_bought;
        ksort($orders_dates_cancel);
        $response['orders_dates_cancel'] = $orders_dates_cancel;
        $orders_latest_query = CpsOrder::select([
                    'cps_orders.order_id',
                    'cps_orders.order_source_id',
                    'cps_orders.order_click_id',
                    'cps_orders.order_click_at',
                    'cps_orders.order_bought_at',
                    'cps_orders.order_total_price',
                    'cps_orders.order_total_payout',
                    'cps_orders.order_merchant',
                    'cps_orders.order_status',
                    'ads_networks.network_name',
                    'cps_campaigns.campaign_name',
                ])
                ->join('ads_networks', 'ads_networks.network_id', '=', 'cps_orders.order_network_id')
                ->leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id');
        if ($request->session()->get('user')->user_id != 0) {
            $orders_latest_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $orders_latest_query->orderBy('cps_orders.order_bought_at', 'DESC');
        $orders_latest = $orders_latest_query->paginate(env('PAGINATE_ITEM'));
        $response['orders_latest'] = $orders_latest;
        return view('cps.report', $response);
    }

    public function click(DateRangeRequest $request) {
        $response = [
            'title' => "Báo cáo click CPS"
        ];
        $response['start'] = date('ymd');
        $response['end'] = date('ymd');
        if ($request->has('start')) {
            $response['start'] = $request->input('start');
        }
        if ($request->has('end')) {
            $response['end'] = $request->input('end');
        }
        $dates = DateRange::get($response['start'], $response['end']);
        $click_date_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_date_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        $click_date_query->selectRaw('cps_clicks.click_created_fd, count(*) as click_total');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_date_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_date_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_date_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_date_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $response['total_clicks'] = $click_date_query->count();
        $click_date_query->groupBy('cps_clicks.click_created_fd');
        $clicks_date_raw = $click_date_query->get();
        $clicks_date = [];
        foreach ($clicks_date_raw as $click) {
            if (!array_key_exists($click->click_created_fd, $clicks_date)) {
                $clicks_date[$click->click_created_fd] = $click->click_total;
            } else {
                $clicks_date[$click->click_created_fd] += $click->click_total;
            }
        }
        foreach ($dates as $date) {
            if (!array_key_exists($date, $clicks_date)) {
                $clicks_date[$date] = 0;
            }
        }
        krsort($clicks_date);
        $response['clicks_date'] = $clicks_date;

        $click_device_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->leftJoin('cps_devices', 'cps_devices.device_id', '=', 'cps_clicks.click_device_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_device_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_device_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_device_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_device_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_device_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_device = $click_device_query->count();
        $click_device_query->groupBy('cps_devices.device_type')
                ->selectRaw('cps_devices.device_type, count(*) as click_total');
        $clicks_device_raw = $click_device_query->get();
        $clicks_device = [
            'Mobile' => [
                'click' => 0,
                'percent' => 0,
            ],
            'Tablet' => [
                'click' => 0,
                'percent' => 0,
            ],
            'PC' => [
                'click' => 0,
                'percent' => 0,
            ],
            'Không xác định' => [
                'click' => 0,
                'percent' => 0,
            ]
        ];
        foreach ($clicks_device_raw as $click_device) {
            if ($click_device->device_type == 'm') {
                $clicks_device['Mobile']['click'] += $click_device->click_total;
                $clicks_device['Mobile']['percent'] = round(($click_device->click_total / $total_clicks_device) * 100, 2);
            } elseif ($click_device->device_type == 't') {
                $clicks_device['Tablet']['click'] += $click_device->click_total;
                $clicks_device['Tablet']['percent'] = round(($click_device->click_total / $total_clicks_device) * 100, 2);
            } elseif ($click_device->device_type == 'c') {
                $clicks_device['PC']['click'] += $click_device->click_total;
                $clicks_device['PC']['percent'] = round(($click_device->click_total / $total_clicks_device) * 100, 2);
            } else {
                $clicks_device['Không xác định']['click'] += $click_device->click_total;
                $clicks_device['Không xác định']['percent'] = round(($click_device->click_total / $total_clicks_device) * 100, 2);
            }
        }
        $response['clicks_device'] = $clicks_device;

        $click_location_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->leftJoin('criterias', 'criterias.criteria_id', '=', 'cps_clicks.click_criteria_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_location_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_location_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_location_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_location_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_location_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_location = $click_location_query->count();
        $click_location_query->groupBy('criterias.canonical_name')
                ->selectRaw('criterias.canonical_name, count(*) as click_total')
                ->orderBy('click_total', 'DESC');
        $clicks_location = $click_location_query->get();
        foreach ($clicks_location as $click_location) {
            if (is_null($click_location->canonical_name)) {
                $click_location->canonical_name = "Không xác định";
            }
            $click_location->percent = round(($click_location->click_total / $total_clicks_location) * 100, 2);
        }
        $response['clicks_location'] = $clicks_location;

        $click_keyword_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_keyword_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_keyword_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_keyword_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_keyword_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_keyword_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_keyword = $click_keyword_query->count();
        $click_keyword_query->groupBy('cps_clicks.click_keyword')
                ->selectRaw('cps_clicks.click_keyword, count(*) as click_total')
                ->orderBy('click_total', 'DESC');
        $clicks_keyword = $click_keyword_query->get();
        foreach ($clicks_keyword as $click_keyword) {
            if (is_null($click_keyword->click_keyword)) {
                $click_keyword->click_keyword = "Không xác định";
            }
            $click_keyword->percent = round(($click_keyword->click_total / $total_clicks_keyword) * 100, 2);
        }
        $response['clicks_keyword'] = $clicks_keyword;

        $click_aff_sub1_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_aff_sub1_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_aff_sub1_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_aff_sub1_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_aff_sub1_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_aff_sub1_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_aff_sub1 = $click_aff_sub1_query->count();
        $click_aff_sub1_query->groupBy('cps_clicks.click_aff_sub1')
                ->selectRaw('cps_clicks.click_aff_sub1, count(*) as click_total')
                ->orderBy('click_total', 'DESC');
        $clicks_aff_sub1 = $click_aff_sub1_query->get();
        foreach ($clicks_aff_sub1 as $click_aff_sub1) {
            if (is_null($click_aff_sub1->click_aff_sub1)) {
                $click_aff_sub1->click_aff_sub1 = "Không xác định";
            }
            $click_aff_sub1->percent = round(($click_aff_sub1->click_total / $total_clicks_aff_sub1) * 100, 2);
        }
        $response['clicks_aff_sub1'] = $clicks_aff_sub1;

        $click_aff_sub2_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_aff_sub2_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_aff_sub2_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_aff_sub2_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_aff_sub2_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_aff_sub2_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_aff_sub2 = $click_aff_sub2_query->count();
        $click_aff_sub2_query->groupBy('cps_clicks.click_aff_sub2')
                ->selectRaw('cps_clicks.click_aff_sub2, count(*) as click_total')
                ->orderBy('click_total', 'DESC');
        $clicks_aff_sub2 = $click_aff_sub2_query->get();
        foreach ($clicks_aff_sub2 as $click_aff_sub2) {
            if (is_null($click_aff_sub2->click_aff_sub2)) {
                $click_aff_sub2->click_aff_sub2 = "Không xác định";
            }
            $click_aff_sub2->percent = round(($click_aff_sub2->click_total / $total_clicks_aff_sub2) * 100, 2);
        }
        $response['clicks_aff_sub2'] = $clicks_aff_sub2;

        $click_aff_sub3_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_aff_sub3_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_aff_sub3_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_aff_sub3_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_aff_sub3_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_aff_sub3_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_aff_sub3 = $click_aff_sub3_query->count();
        $click_aff_sub3_query->groupBy('cps_clicks.click_aff_sub3')
                ->selectRaw('cps_clicks.click_aff_sub3, count(*) as click_total')
                ->orderBy('click_total', 'DESC');
        $clicks_aff_sub3 = $click_aff_sub3_query->get();
        foreach ($clicks_aff_sub3 as $click_aff_sub3) {
            if (is_null($click_aff_sub3->click_aff_sub3)) {
                $click_aff_sub3->click_aff_sub3 = "Không xác định";
            }
            $click_aff_sub3->percent = round(($click_aff_sub3->click_total / $total_clicks_aff_sub3) * 100, 2);
        }
        $response['clicks_aff_sub3'] = $clicks_aff_sub3;

        $click_aff_sub4_query = CpsClick::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_clicks.click_campaign_id')
                ->whereIn('cps_clicks.click_created_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $click_aff_sub4_query->where('cps_clicks.click_user_id', $request->session()->get('user')->user_id);
        }
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $click_aff_sub4_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $click_aff_sub4_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $click_aff_sub4_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $click_aff_sub4_query->where('cps_clicks.click_user_id', $request->input('user'));
        }
        $total_clicks_aff_sub4 = $click_aff_sub4_query->count();
        $click_aff_sub4_query->groupBy('cps_clicks.click_aff_sub4')
                ->selectRaw('cps_clicks.click_aff_sub4, count(*) as click_total')
                ->orderBy('click_total', 'DESC');
        $clicks_aff_sub4 = $click_aff_sub4_query->get();
        foreach ($clicks_aff_sub4 as $click_aff_sub4) {
            if (is_null($click_aff_sub4->click_aff_sub4)) {
                $click_aff_sub4->click_aff_sub4 = "Không xác định";
            }
            $click_aff_sub4->percent = round(($click_aff_sub4->click_total / $total_clicks_aff_sub4) * 100, 2);
        }
        $response['clicks_aff_sub4'] = $clicks_aff_sub4;

        $response['traffic_sources'] = TrafficSource::select([
                    'source_id',
                    'source_name',
                ])
                ->orderBy('source_name', 'ASC')
                ->get();
        if ($request->has('traffic_source') && $request->input('traffic_source') == 1) {
            $response['adwords_accounts'] = AdwordsAccount::select([
                        'account_id',
                        'account_name'
                    ])
                    ->orderBy('account_name', 'ASC')
                    ->get();
        } else {
            $response['adwords_accounts'] = [];
        }
        $response['campaigns'] = [];
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $response['campaigns'] = CpsCampaign::select([
                        'campaign_id',
                        'campaign_name'
                    ])
                    ->where('campaign_traffic_source_id', $request->input('traffic_source'))
                    ->get();
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account')) && $request->has('traffic_source') && $request->input('traffic_source') == 1) {
            $response['campaigns'] = CpsCampaign::select([
                        'cps_campaigns.campaign_id',
                        'cps_campaigns.campaign_name'
                    ])
                    ->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'))
                    ->get();
        }
        $response['users'] = [];
        if ($request->session()->get('user')->user_id == 0) {
            $response['users'] = User::select([
                        'user_id',
                        'user_name',
                    ])
                    ->orderBy('user_name', 'ASC')
                    ->get();
        }
        return view('cps.reportClick', $response);
    }

    public function revenue(DateRangeRequest $request) {
        $response = [
            'title' => "Báo cáo hoa hồng CPS"
        ];
        $response['start'] = date('ymd');
        $response['end'] = date('ymd');
        if ($request->has('start')) {
            $response['start'] = $request->input('start');
        }
        if ($request->has('end')) {
            $response['end'] = $request->input('end');
        }
        $dates = DateRange::get($response['start'], $response['end']);
        $revenues_date_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_date_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_date_total_query->selectRaw('cps_orders.order_bought_fd, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_date_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_date_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_date_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_date_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $response['total_payout'] = $revenues_date_total_query->sum('cps_orders.order_total_payout');
        $revenues_date_total_query->groupBy('cps_orders.order_bought_fd');
        $revenues_date_total_raw = $revenues_date_total_query->get();
        $revenues_date = [];
        foreach ($revenues_date_total_raw as $revenue) {
            if (!isset($revenues_date[$revenue->order_bought_fd]['payout'])) {
                $revenues_date[$revenue->order_bought_fd]['payout'] = $revenue->total_payout;
            } else {
                $revenues_date[$revenue->order_bought_fd]['payout'] += $revenue->total_payout;
            }
        }

        $revenues_date_success_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->whereIn('cps_orders.order_bought_fd', $dates)
                ->where('cps_orders.order_status', 1);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_date_success_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_date_success_query->selectRaw('cps_orders.order_bought_fd, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_date_success_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_date_success_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_date_success_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_date_success_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $response['total_payout_success'] = $revenues_date_success_query->sum('cps_orders.order_total_payout');
        $revenues_date_success_query->groupBy('cps_orders.order_bought_fd');
        $revenues_date_success_raw = $revenues_date_success_query->get();
        foreach ($revenues_date_success_raw as $revenue) {
            if (!isset($revenues_date[$revenue->order_bought_fd]['payout_success'])) {
                $revenues_date[$revenue->order_bought_fd]['payout_success'] = $revenue->total_payout;
            } else {
                $revenues_date[$revenue->order_bought_fd]['payout_success'] += $revenue->total_payout;
            }
        }

        $revenues_date_cancel_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->whereIn('cps_orders.order_bought_fd', $dates)
                ->where('cps_orders.order_status', -1);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_date_cancel_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_date_cancel_query->selectRaw('cps_orders.order_bought_fd, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_date_cancel_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_date_cancel_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_date_cancel_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_date_cancel_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $response['total_payout_cancel'] = $revenues_date_cancel_query->sum('cps_orders.order_total_payout');
        $revenues_date_cancel_query->groupBy('cps_orders.order_bought_fd');
        $revenues_date_cancel_raw = $revenues_date_cancel_query->get();
        foreach ($revenues_date_cancel_raw as $revenue) {
            if (!isset($revenues_date[$revenue->order_bought_fd]['payout_cancel'])) {
                $revenues_date[$revenue->order_bought_fd]['payout_cancel'] = $revenue->total_payout;
            } else {
                $revenues_date[$revenue->order_bought_fd]['payout_cancel'] += $revenue->total_payout;
            }
        }
        foreach ($dates as $date) {
            if (!isset($revenues_date[$date]['payout'])) {
                $revenues_date[$date]['payout'] = 0;
            }
            if (!isset($revenues_date[$date]['payout_success'])) {
                $revenues_date[$date]['payout_success'] = 0;
            }
            if (!isset($revenues_date[$date]['payout_cancel'])) {
                $revenues_date[$date]['payout_cancel'] = 0;
            }
            if ($revenues_date[$date]['payout'] != 0) {
                $revenues_date[$date]['percent_cancel'] = round(($revenues_date[$date]['payout_cancel'] / $revenues_date[$date]['payout']) * 100, 2);
            } else {
                $revenues_date[$date]['percent_cancel'] = 0;
            }
        }
        krsort($revenues_date);
        $response['revenues_date'] = $revenues_date;

        $revenues_network_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->leftJoin('ads_networks', 'ads_networks.network_id', '=', 'cps_orders.order_network_id')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_network_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_network_total_query->selectRaw('ads_networks.network_name, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_network_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_network_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_network_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_network_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_network_total_query->groupBy('ads_networks.network_name', 'cps_orders.order_status');
        $revenues_network_total_raw = $revenues_network_total_query->get();
        $revenues_network = [];
        foreach ($revenues_network_total_raw as $revenue) {
            if (!isset($revenues_network[$revenue->network_name]['payout'])) {
                $revenues_network[$revenue->network_name]['payout'] = $revenue->total_payout;
            } else {
                $revenues_network[$revenue->network_name]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_network[$revenue->network_name]['payout_success'])) {
                    $revenues_network[$revenue->network_name]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_network[$revenue->network_name]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_network[$revenue->network_name]['payout_cancel'])) {
                    $revenues_network[$revenue->network_name]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_network[$revenue->network_name]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_network as $key => $revenue_network) {
            if (!isset($revenues_network[$key]['payout'])) {
                $revenues_network[$key]['payout'] = 0;
            }
            if (!isset($revenues_network[$key]['payout_success'])) {
                $revenues_network[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_network[$key]['payout_cancel'])) {
                $revenues_network[$key]['payout_cancel'] = 0;
            }
            if ($revenues_network[$key]['payout'] != 0) {
                $revenues_network[$key]['percent_cancel'] = round(($revenues_network[$key]['payout_cancel'] / $revenues_network[$key]['payout']) * 100, 2);
            } else {
                $revenues_network[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_network'] = $revenues_network;

        $revenues_apublish_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->leftJoin('ads_networks', 'ads_networks.network_id', '=', 'cps_orders.order_network_id')
                ->leftJoin('publisher_accounts', 'publisher_accounts.account_id', '=', 'cps_orders.order_publisher_account')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_apublish_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_apublish_total_query->selectRaw('publisher_accounts.account_username, ads_networks.network_name, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_apublish_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_apublish_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_apublish_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_apublish_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_apublish_total_query->groupBy('publisher_accounts.account_id', 'ads_networks.network_id', 'cps_orders.order_status');
        $revenues_apublish_total_raw = $revenues_apublish_total_query->get();
        $revenues_apublish = [];
        foreach ($revenues_apublish_total_raw as $revenue) {
            if (!isset($revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout'])) {
                $revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout'] = $revenue->total_payout;
            } else {
                $revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout_success'])) {
                    $revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout_cancel'])) {
                    $revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_apublish[$revenue->account_username . " - " . $revenue->network_name]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_apublish as $key => $revenue_apublish) {
            if (!isset($revenues_apublish[$key]['payout'])) {
                $revenues_apublish[$key]['payout'] = 0;
            }
            if (!isset($revenues_apublish[$key]['payout_success'])) {
                $revenues_apublish[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_apublish[$key]['payout_cancel'])) {
                $revenues_apublish[$key]['payout_cancel'] = 0;
            }
            if ($revenues_apublish[$key]['payout'] != 0) {
                $revenues_apublish[$key]['percent_cancel'] = round(($revenues_apublish[$key]['payout_cancel'] / $revenues_apublish[$key]['payout']) * 100, 2);
            } else {
                $revenues_apublish[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_apublish'] = $revenues_apublish;

        $revenues_merchant_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->join('cps_merchants', 'cps_merchants.merchant_id', '=', 'cps_campaigns.campaign_merchant_id')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_merchant_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_merchant_total_query->selectRaw('cps_merchants.merchant_domain, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_merchant_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_merchant_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_merchant_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_merchant_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_merchant_total_query->groupBy('cps_merchants.merchant_id', 'cps_orders.order_status');
        $revenues_merchant_total_raw = $revenues_merchant_total_query->get();
        $revenues_merchant = [];
        foreach ($revenues_merchant_total_raw as $revenue) {
            if (!isset($revenues_merchant[$revenue->merchant_domain]['payout'])) {
                $revenues_merchant[$revenue->merchant_domain]['payout'] = $revenue->total_payout;
            } else {
                $revenues_merchant[$revenue->merchant_domain]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_merchant[$revenue->merchant_domain]['payout_success'])) {
                    $revenues_merchant[$revenue->merchant_domain]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_merchant[$revenue->merchant_domain]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_merchant[$revenue->merchant_domain]['payout_cancel'])) {
                    $revenues_merchant[$revenue->merchant_domain]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_merchant[$revenue->merchant_domain]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_merchant as $key => $revenue_network) {
            if (!isset($revenues_merchant[$key]['payout'])) {
                $revenues_merchant[$key]['payout'] = 0;
            }
            if (!isset($revenues_merchant[$key]['payout_success'])) {
                $revenues_merchant[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_merchant[$key]['payout_cancel'])) {
                $revenues_merchant[$key]['payout_cancel'] = 0;
            }
            if ($revenues_merchant[$key]['payout'] != 0) {
                $revenues_merchant[$key]['percent_cancel'] = round(($revenues_merchant[$key]['payout_cancel'] / $revenues_merchant[$key]['payout']) * 100, 2);
            } else {
                $revenues_merchant[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_merchant'] = $revenues_merchant;

        $revenues_sub1_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->join('cps_clicks', 'cps_clicks.click_id', '=', 'cps_orders.order_click_id')
                ->whereNotNull('cps_clicks.click_aff_sub1')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_sub1_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_sub1_total_query->selectRaw('cps_clicks.click_aff_sub1, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_sub1_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_sub1_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_sub1_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_sub1_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_sub1_total_query->groupBy('cps_clicks.click_id', 'cps_orders.order_status');
        $revenues_sub1_total_raw = $revenues_sub1_total_query->get();
        $revenues_sub1 = [];
        foreach ($revenues_sub1_total_raw as $revenue) {
            if (!isset($revenues_sub1[$revenue->click_aff_sub1]['payout'])) {
                $revenues_sub1[$revenue->click_aff_sub1]['payout'] = $revenue->total_payout;
            } else {
                $revenues_sub1[$revenue->click_aff_sub1]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_sub1[$revenue->click_aff_sub1]['payout_success'])) {
                    $revenues_sub1[$revenue->click_aff_sub1]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_sub1[$revenue->click_aff_sub1]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_sub1[$revenue->click_aff_sub1]['payout_cancel'])) {
                    $revenues_sub1[$revenue->click_aff_sub1]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_sub1[$revenue->click_aff_sub1]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_sub1 as $key => $revenue_network) {
            if (!isset($revenues_sub1[$key]['payout'])) {
                $revenues_sub1[$key]['payout'] = 0;
            }
            if (!isset($revenues_sub1[$key]['payout_success'])) {
                $revenues_sub1[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_sub1[$key]['payout_cancel'])) {
                $revenues_sub1[$key]['payout_cancel'] = 0;
            }
            if ($revenues_sub1[$key]['payout'] != 0) {
                $revenues_sub1[$key]['percent_cancel'] = round(($revenues_sub1[$key]['payout_cancel'] / $revenues_sub1[$key]['payout']) * 100, 2);
            } else {
                $revenues_sub1[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_sub1'] = $revenues_sub1;

        $revenues_sub2_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->join('cps_clicks', 'cps_clicks.click_id', '=', 'cps_orders.order_click_id')
                ->whereNotNull('cps_clicks.click_aff_sub2')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_sub2_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_sub2_total_query->selectRaw('cps_clicks.click_aff_sub2, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_sub2_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_sub2_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_sub2_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_sub2_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_sub2_total_query->groupBy('cps_clicks.click_id', 'cps_orders.order_status');
        $revenues_sub2_total_raw = $revenues_sub2_total_query->get();
        $revenues_sub2 = [];
        foreach ($revenues_sub2_total_raw as $revenue) {
            if (!isset($revenues_sub2[$revenue->click_aff_sub2]['payout'])) {
                $revenues_sub2[$revenue->click_aff_sub2]['payout'] = $revenue->total_payout;
            } else {
                $revenues_sub2[$revenue->click_aff_sub2]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_sub2[$revenue->click_aff_sub2]['payout_success'])) {
                    $revenues_sub2[$revenue->click_aff_sub2]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_sub2[$revenue->click_aff_sub2]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_sub2[$revenue->click_aff_sub2]['payout_cancel'])) {
                    $revenues_sub2[$revenue->click_aff_sub2]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_sub2[$revenue->click_aff_sub2]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_sub2 as $key => $revenue_network) {
            if (!isset($revenues_sub2[$key]['payout'])) {
                $revenues_sub2[$key]['payout'] = 0;
            }
            if (!isset($revenues_sub2[$key]['payout_success'])) {
                $revenues_sub2[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_sub2[$key]['payout_cancel'])) {
                $revenues_sub2[$key]['payout_cancel'] = 0;
            }
            if ($revenues_sub2[$key]['payout'] != 0) {
                $revenues_sub2[$key]['percent_cancel'] = round(($revenues_sub2[$key]['payout_cancel'] / $revenues_sub2[$key]['payout']) * 100, 2);
            } else {
                $revenues_sub2[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_sub2'] = $revenues_sub2;

        $revenues_sub3_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->join('cps_clicks', 'cps_clicks.click_id', '=', 'cps_orders.order_click_id')
                ->whereNotNull('cps_clicks.click_aff_sub3')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_sub3_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_sub3_total_query->selectRaw('cps_clicks.click_aff_sub3, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_sub3_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_sub3_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_sub3_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_sub3_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_sub3_total_query->groupBy('cps_clicks.click_id', 'cps_orders.order_status');
        $revenues_sub3_total_raw = $revenues_sub3_total_query->get();
        $revenues_sub3 = [];
        foreach ($revenues_sub3_total_raw as $revenue) {
            if (!isset($revenues_sub3[$revenue->click_aff_sub3]['payout'])) {
                $revenues_sub3[$revenue->click_aff_sub3]['payout'] = $revenue->total_payout;
            } else {
                $revenues_sub3[$revenue->click_aff_sub3]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_sub3[$revenue->click_aff_sub3]['payout_success'])) {
                    $revenues_sub3[$revenue->click_aff_sub3]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_sub3[$revenue->click_aff_sub3]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_sub3[$revenue->click_aff_sub3]['payout_cancel'])) {
                    $revenues_sub3[$revenue->click_aff_sub3]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_sub3[$revenue->click_aff_sub3]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_sub3 as $key => $revenue_network) {
            if (!isset($revenues_sub3[$key]['payout'])) {
                $revenues_sub3[$key]['payout'] = 0;
            }
            if (!isset($revenues_sub3[$key]['payout_success'])) {
                $revenues_sub3[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_sub3[$key]['payout_cancel'])) {
                $revenues_sub3[$key]['payout_cancel'] = 0;
            }
            if ($revenues_sub3[$key]['payout'] != 0) {
                $revenues_sub3[$key]['percent_cancel'] = round(($revenues_sub3[$key]['payout_cancel'] / $revenues_sub3[$key]['payout']) * 100, 2);
            } else {
                $revenues_sub3[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_sub3'] = $revenues_sub3;

        $revenues_sub4_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->join('cps_clicks', 'cps_clicks.click_id', '=', 'cps_orders.order_click_id')
                ->whereNotNull('cps_clicks.click_aff_sub4')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_sub4_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_sub4_total_query->selectRaw('cps_clicks.click_aff_sub4, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_sub4_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_sub4_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_sub4_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_sub4_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_sub4_total_query->groupBy('cps_clicks.click_id', 'cps_orders.order_status');
        $revenues_sub4_total_raw = $revenues_sub4_total_query->get();
        $revenues_sub4 = [];
        foreach ($revenues_sub4_total_raw as $revenue) {
            if (!isset($revenues_sub4[$revenue->click_aff_sub4]['payout'])) {
                $revenues_sub4[$revenue->click_aff_sub4]['payout'] = $revenue->total_payout;
            } else {
                $revenues_sub4[$revenue->click_aff_sub4]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_sub4[$revenue->click_aff_sub4]['payout_success'])) {
                    $revenues_sub4[$revenue->click_aff_sub4]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_sub4[$revenue->click_aff_sub4]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_sub4[$revenue->click_aff_sub4]['payout_cancel'])) {
                    $revenues_sub4[$revenue->click_aff_sub4]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_sub4[$revenue->click_aff_sub4]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_sub4 as $key => $revenue_network) {
            if (!isset($revenues_sub4[$key]['payout'])) {
                $revenues_sub4[$key]['payout'] = 0;
            }
            if (!isset($revenues_sub4[$key]['payout_success'])) {
                $revenues_sub4[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_sub4[$key]['payout_cancel'])) {
                $revenues_sub4[$key]['payout_cancel'] = 0;
            }
            if ($revenues_sub4[$key]['payout'] != 0) {
                $revenues_sub4[$key]['percent_cancel'] = round(($revenues_sub4[$key]['payout_cancel'] / $revenues_sub4[$key]['payout']) * 100, 2);
            } else {
                $revenues_sub4[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_sub4'] = $revenues_sub4;

        $revenues_location_total_query = CpsOrder::leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                ->join('cps_clicks', 'cps_clicks.click_id', '=', 'cps_orders.order_click_id')
                ->leftJoin('criterias', 'criterias.criteria_id', '=', 'cps_clicks.click_criteria_id')
              //  ->whereNotNull('cps_clicks.click_criteria_id')
                ->whereIn('cps_orders.order_bought_fd', $dates);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_location_total_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
        }
        $revenues_location_total_query->selectRaw('criterias.name, cps_orders.order_status, sum(cps_orders.order_total_payout) as total_payout');

        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $revenues_location_total_query->where('cps_campaigns.campaign_traffic_source_id', $request->input('traffic_source'));
        }
        if ($request->has('campaign') && is_numeric($request->input('campaign'))) {
            $revenues_location_total_query->where('cps_campaigns.campaign_id', $request->input('campaign'));
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account'))) {
            $revenues_location_total_query->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'));
        }
        if ($request->has('user') && is_numeric($request->input('user'))) {
            $revenues_location_total_query->where('cps_orders.order_user_id', $request->input('user'));
        }
        $revenues_location_total_query->orderBy('total_payout', 'DESC');
        $revenues_location_total_query->groupBy('criterias.criteria_id', 'cps_orders.order_status');
        $revenues_location_total_raw = $revenues_location_total_query->get();
        $revenues_location = [];
        foreach ($revenues_location_total_raw as $revenue) {
            if (!isset($revenues_location[$revenue->name]['payout'])) {
                $revenues_location[$revenue->name]['payout'] = $revenue->total_payout;
            } else {
                $revenues_location[$revenue->name]['payout'] += $revenue->total_payout;
            }
            if ($revenue->order_status == 1) {
                if (!isset($revenues_location[$revenue->name]['payout_success'])) {
                    $revenues_location[$revenue->name]['payout_success'] = $revenue->total_payout;
                } else {
                    $revenues_location[$revenue->name]['payout_success'] += $revenue->total_payout;
                }
            }
            if ($revenue->order_status == -1) {
                if (!isset($revenues_location[$revenue->name]['payout_cancel'])) {
                    $revenues_location[$revenue->name]['payout_cancel'] = $revenue->total_payout;
                } else {
                    $revenues_location[$revenue->name]['payout_cancel'] += $revenue->total_payout;
                }
            }
        }

        foreach ($revenues_location as $key => $revenue_network) {
            if (!isset($revenues_location[$key]['payout'])) {
                $revenues_location[$key]['payout'] = 0;
            }
            if (!isset($revenues_location[$key]['payout_success'])) {
                $revenues_location[$key]['payout_success'] = 0;
            }
            if (!isset($revenues_location[$key]['payout_cancel'])) {
                $revenues_location[$key]['payout_cancel'] = 0;
            }
            if ($revenues_location[$key]['payout'] != 0) {
                $revenues_location[$key]['percent_cancel'] = round(($revenues_location[$key]['payout_cancel'] / $revenues_location[$key]['payout']) * 100, 2);
            } else {
                $revenues_location[$key]['percent_cancel'] = 0;
            }
        }
        $response['revenues_location'] = $revenues_location;

        $response['traffic_sources'] = TrafficSource::select([
                    'source_id',
                    'source_name',
                ])
                ->orderBy('source_name', 'ASC')
                ->get();
        if ($request->has('traffic_source') && $request->input('traffic_source') == 1) {
            $response['adwords_accounts'] = AdwordsAccount::select([
                        'account_id',
                        'account_name'
                    ])
                    ->orderBy('account_name', 'ASC')
                    ->get();
        } else {
            $response['adwords_accounts'] = [];
        }
        $response['campaigns'] = [];
        if ($request->has('traffic_source') && is_numeric($request->input('traffic_source'))) {
            $response['campaigns'] = CpsCampaign::select([
                        'campaign_id',
                        'campaign_name'
                    ])
                    ->where('campaign_traffic_source_id', $request->input('traffic_source'))
                    ->get();
        }
        if ($request->has('adwords_account') && is_numeric($request->input('adwords_account')) && $request->has('traffic_source') && $request->input('traffic_source') == 1) {
            $response['campaigns'] = CpsCampaign::select([
                        'cps_campaigns.campaign_id',
                        'cps_campaigns.campaign_name'
                    ])
                    ->join('cps_campaigns_adwords', 'cps_campaigns_adwords.campaign_id', '=', 'cps_campaigns.campaign_id')
                    ->where('cps_campaigns_adwords.campaign_adwords_account', $request->input('adwords_account'))
                    ->get();
        }
        $response['users'] = [];
        if ($request->session()->get('user')->user_id == 0) {
            $response['users'] = User::select([
                        'user_id',
                        'user_name',
                    ])
                    ->orderBy('user_name', 'ASC')
                    ->get();
        }
        return view('cps.reportRevenue', $response);
    }
    
    public function order(DateRangeRequest $request) {
        
    }

    public function orderDetail(Request $request) {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                        'order_id' => "required|alpha_num",
                            ], [
                        'order_id.required' => "Đơn hàng không hợp lệ",
                        'order_id.alpha_num' => "Đơn hàng không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $order_query = CpsOrder::select([
                                'cps_orders.order_id',
                                'cps_orders.order_source_id',
                                'cps_orders.order_offer_url',
                                'cps_orders.order_click_id',
                                'cps_orders.order_click_at',
                                'cps_orders.order_bought_at',
                                'cps_orders.order_total_price',
                                'cps_orders.order_total_payout',
                                'cps_orders.order_status',
                                'cps_orders.order_merchant',
                                'ads_networks.network_name',
                                'cps_campaigns.campaign_name',
                                'cps_campaigns.campaign_traffic_source_id',
                                'criterias.canonical_name',
                                'cps_devices.device_type',
                                'cps_devices.device_model',
                                'cps_clicks.click_keyword',
                                'cps_clicks.click_lpurl',
                            ])
                            ->leftJoin('cps_clicks', 'cps_clicks.click_id', '=', 'cps_orders.order_click_id')
                            ->leftJoin('cps_devices', 'cps_devices.device_id', '=', 'cps_clicks.click_device_id')
                            ->leftJoin('criterias', 'criterias.criteria_id', '=', 'cps_clicks.click_criteria_id')
                            ->join('ads_networks', 'ads_networks.network_id', '=', 'cps_orders.order_network_id')
                            ->leftJoin('cps_campaigns', 'cps_campaigns.campaign_id', '=', 'cps_orders.order_campaign_id')
                            ->where('cps_orders.order_id', $request->input('order_id'));
                    if ($request->session()->get('user')->user_id != 0) {
                        $order_query->where('cps_orders.order_user_id', $request->session()->get('user')->user_id);
                    }
                    $order = $order_query->first();
                    if (!empty($order)) {
                        $order->order_click_at = date('H:i:s d/m/Y', $order->order_click_at);
                        $order->order_bought_at = date('H:i:s d/m/Y', $order->order_bought_at);
                        $order_detail = DB::table('cps_order_detail')
                                ->where('order_id', $order->order_id)
                                ->get();
                        $order->order_detail = $order_detail;
                        return response()->json([
                                    "status_code" => 200,
                                    "data" => $order
                        ]);
                    } else {
                        return response()->json([
                                    "status_code" => 404,
                                    "message" => "Đơn hàng không tồn tại",
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

}
