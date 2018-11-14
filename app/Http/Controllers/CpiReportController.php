<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DateRangeRequest;
use App\Models\CpiReport;
use DateRange;

class CpiReportController extends Controller {

    public function index(DateRangeRequest $request) {
        $response = [
            'title' => "Báo cáo CPI"
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
        $cpi_dates_query = CpiReport::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'cpi_reports.report_publisher_account_id')
                ->whereIn('cpi_reports.report_fd', $dates)
                ->groupBy('cpi_reports.report_fd');
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_dates_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi_dates_query->selectRaw('cpi_reports.report_fd, sum(report_clicks) as total_clicks, sum(report_installs) as total_installs, sum(report_revenues) as total_revenues');
        $cpi_dates_raw = $cpi_dates_query->get();
        $cpi_dates = [];
        foreach ($cpi_dates_raw as $cpi_date) {
            if (!array_key_exists($cpi_date->report_fd, $cpi_dates)) {
                $cpi_dates[$cpi_date->report_fd]['clicks'] = $cpi_date->total_clicks;
                $cpi_dates[$cpi_date->report_fd]['installs'] = $cpi_date->total_installs;
                $cpi_dates[$cpi_date->report_fd]['revenues'] = $cpi_date->total_revenues;
            } else {
                $cpi_dates[$cpi_date->report_fd]['clicks'] += $cpi_date->total_clicks;
                $cpi_dates[$cpi_date->report_fd]['installs'] += $cpi_date->total_installs;
                $cpi_dates[$cpi_date->report_fd]['revenues'] += $cpi_date->total_revenues;
            }
        }
        foreach ($dates as $date) {
            if (!array_key_exists($date, $cpi_dates)) {
                $cpi_dates[$date] = [
                    'clicks' => 0,
                    'installs' => 0,
                    'revenues' => 0
                ];
            }
        }
        krsort($cpi_dates);
        $response['cpi_dates'] = $cpi_dates;

        $cpi_publishers_query = CpiReport::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'cpi_reports.report_publisher_account_id')
                ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id')
                ->whereIn('cpi_reports.report_fd', $dates)
                ->groupBy('cpi_reports.report_publisher_account_id');
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_publishers_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi_publishers_query->selectRaw('ads_networks.network_name, publisher_accounts.account_username, sum(report_clicks) as total_clicks, sum(report_installs) as total_installs, sum(report_revenues) as total_revenues');
        $response['cpi_publishers'] = $cpi_publishers_query->get();

        $cpi_networks_query = CpiReport::join('publisher_accounts', 'publisher_accounts.account_id', '=', 'cpi_reports.report_publisher_account_id')
                ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id')
                ->whereIn('cpi_reports.report_fd', $dates)
                ->groupBy('ads_networks.network_id');
        if ($request->session()->get('user')->user_id != 0) {
            $cpi_networks_query->where('publisher_accounts.account_user_id', $request->session()->get('user')->user_id);
        }
        $cpi_networks_query->selectRaw('ads_networks.network_name, sum(report_clicks) as total_clicks, sum(report_installs) as total_installs, sum(report_revenues) as total_revenues');
        $response['cpi_networks'] = $cpi_networks_query->get();
        return view('cpi.report', $response);
    }

}
