<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DateRangeRequest;
use App\Models\Revenue;
use DateRange;

class HomeController extends Controller {

    public function index(DateRangeRequest $request) {
        $response = [
            'title' => 'Trang chủ'
        ];
        $response['start'] = date('ymd', strtotime('first day of this month'));
        $response['end'] = date('ymd');
        if ($request->has('start')) {
            $response['start'] = $request->input('start');
        }
        if ($request->has('end')) {
            $response['end'] = $request->input('end');
        }
        $dates = DateRange::get($response['start'], $response['end']);
        $today = date('ymd');
        $yesterday = date('ymd', strtotime('-1 day'));
        $this_month = date('ym');
        $last_month = date('ym', strtotime('last month'));
        $cpi = [];

        $cpi_revenues_today_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_type', 1)
                ->where('revenues.revenue_status', 1)
                ->where('revenues.revenue_fd', $today);
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_revenues_today_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi['revenues_today'] = $cpi_revenues_today_query->sum('revenue_value');

        $cpi_revenues_yesterday_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_type', 1)
                ->where('revenues.revenue_status', 1)
                ->where('revenues.revenue_fd', $yesterday);
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_revenues_today_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi['revenues_yesterday'] = $cpi_revenues_yesterday_query->sum('revenue_value');
        if ($cpi['revenues_yesterday'] != 0) {
            $cpi['percent_today_yesterday'] = round(($cpi['revenues_today'] / $cpi['revenues_yesterday']) * 100, 2);
        } else {
            $cpi['percent_today_yesterday'] = 0;
        }
        $cpi_revenues_thismonth_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_type', 1)
                ->where('revenues.revenue_status', 1)
                ->where('revenues.revenue_fm', $this_month);
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_revenues_thismonth_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi['revenues_thismonth'] = $cpi_revenues_thismonth_query->sum('revenue_value');

        $response['cpi'] = $cpi;

        $cps = [];
        $cps_revenues_today_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_type', 2)
                //    ->whereIn('revenues.revenue_status', [-1,0,1])
                ->where('revenues.revenue_fd', $today);
        if ($request->session()->get('user')->user_id != 0) {
            $cps_revenues_today_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cps['revenues_today'] = $cps_revenues_today_query->sum('revenue_value');

        $cps_revenues_yesterday_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_type', 2)
                //   ->where('revenues.revenue_status', 1)
                ->where('revenues.revenue_fd', $yesterday);
        if ($request->session()->get('user')->user_id != 0) {
            $cps_revenues_today_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cps['revenues_yesterday'] = $cps_revenues_yesterday_query->sum('revenue_value');
        if ($cps['revenues_yesterday'] != 0) {
            $cps['percent_today_yesterday'] = round(($cps['revenues_today'] / $cps['revenues_yesterday']) * 100, 2);
        } else {
            $cps['percent_today_yesterday'] = 0;
        }
        $cps_revenues_thismonth_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_type', 2)
                //   ->where('revenues.revenue_status', 1)
                ->where('revenues.revenue_fm', $this_month);
        if ($request->session()->get('user')->user_id != 0) {
            $cps_revenues_thismonth_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cps['revenues_thismonth'] = $cps_revenues_thismonth_query->sum('revenue_value');

        $response['cps'] = $cps;


        $revenues_thismonth_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_fm', $this_month);
        if ($request->session()->get('user')->user_id != 0) {
            $cps_revenues_thismonth_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $response['revenues_thismonth'] = $revenues_thismonth_query->sum('revenue_value');
        $revenues_lastmonth_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->where('revenues.revenue_fm', $last_month);
        if ($request->session()->get('user')->user_id != 0) {
            $revenues_lastmonth_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $response['revenues_lastmonth'] = $revenues_lastmonth_query->sum('revenue_value');

        $cpi_dates_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->whereIn('revenues.revenue_fd', $dates)
                ->where('revenues.revenue_type', 1)
                ->orderBy('revenues.revenue_fd', 'DESC');
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_dates_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi_dates_raw = $cpi_dates_query->get();
        $cpi_dates = [];
        foreach ($cpi_dates_raw as $cpi_date) {
            if (!isset($cpi_dates[$cpi_date->revenue_fd])) {
                $cpi_dates[$cpi_date->revenue_fd] = $cpi_date->revenue_value;
            } else {
                $cpi_dates[$cpi_date->revenue_fd] += $cpi_date->revenue_value;
            }
        }

        $cps_dates_query = Revenue::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'revenues.revenue_publisher_account_id')
                ->whereIn('revenues.revenue_fd', $dates)
                ->where('revenues.revenue_type', 2)
                ->orderBy('revenues.revenue_fd', 'DESC');
        if ($request->session()->get('user')->user_id != 0) {
            $cps_dates_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cps_dates_raw = $cps_dates_query->get();
        $cps_dates = [];

        foreach ($cps_dates_raw as $cps_date) {
            if (!isset($cps_dates[$cps_date->revenue_fd])) {
                $cps_dates[$cps_date->revenue_fd] = $cps_date->revenue_value;
            } else {
                $cps_dates[$cps_date->revenue_fd] += $cps_date->revenue_value;
            }
        }
        $cpo_dates = [];
        foreach ($dates as $date) {
            if (!array_key_exists($date, $cpi_dates)) {
                $cpi_dates[$date] = 0;
            }
            if (!array_key_exists($date, $cps_dates)) {
                $cps_dates[$date] = 0;
            }
            $cpo_dates[$date] = 0;
        }
        sort($dates);
        $response['dates'] = $dates;
        ksort($cpi_dates);
        $response['cpi_dates'] = $cpi_dates;
        ksort($cps_dates);
        $response['cps_dates'] = $cps_dates;
        $response['cpo_dates'] = $cpo_dates;
        return view('home', $response);
    }

}
