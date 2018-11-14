<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PublisherAccount;
use App\Models\CpiReport;
use App\Models\Revenue;
use App\Models\CpsOrder;
use App\Models\CpsClick;
use DB;
use Curl;

class CronjobController extends Controller {

    public function everyMinutes() {
        $this->cpi();
        $this->cps(date("Y-m-d", strtotime('-1 days')), date('Y-m-d', strtotime('+1 day')));
    }

    public function daily() {
        $this->cps(date("Y-m-d", strtotime('-60 days')), date('Y-m-d', strtotime('+1 day')));
    }

    public function cpi() {
        $accounts = PublisherAccount::select([
                    'publisher_accounts.account_id',
                    'publisher_accounts.account_username',
                    'publisher_accounts.account_password',
                    'ads_networks.network_id',
                    'ads_networks.network_domain',
                ])
                ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id')
                ->where([
                    'ads_networks.network_status' => 1,
                    'publisher_accounts.account_status' => 1,
                    'ads_networks.network_type' => 1
                ])
                ->get();
        foreach ($accounts as $account) {
            switch ($account->network_domain) {
                case 'pub.ecomobi.com':
                    try {
                        $post_fields = 'username=' . $account->account_username . '&password=' . $account->account_password;
//                    $url_get = 'https://pub.ecomobi.com/reports-detail?view_by=date';
                        $url_get = 'https://pub.ecomobi.com/reports-detail?start_date=2017-10-01&end_date=2018-12-31&view_by=date';
                        $html = $this->curlWithLogin('https://pub.ecomobi.com/login', $post_fields, $url_get);
                        //   dd($html);
                        if ($html) {
                            $reports = $this->handleEcomobiCpi($html);
                            foreach ($reports as $report) {
                                if (!empty($report)) {
                                    try {
                                        $cpi_report = CpiReport::where([
                                                    'report_fd' => $report['fd'],
                                                    'report_network_id' => $account->network_id,
                                                    'report_publisher_account_id' => $account->account_id,
                                                ])
                                                ->first();
                                        if (empty($cpi_report)) {
                                            $cpi_report = new CpiReport;
                                            $cpi_report->report_fm = $report['fm'];
                                            $cpi_report->report_fd = $report['fd'];
                                            $cpi_report->report_network_id = $account->network_id;
                                            $cpi_report->report_publisher_account_id = $account->account_id;
                                            $cpi_report->report_created_at = microtime(true);
                                        } else {
                                            $cpi_report->report_updated_at = microtime(true);
                                        }
                                        $cpi_report->report_clicks = $report['clicks'];
                                        $cpi_report->report_installs = $report['installs'];
                                        $cpi_report->report_revenues = $report['revenues'];
                                        $cpi_report->save();
                                    } catch (Exception $exc) {
                                        dd($exc->getMessage());
                                    }
                                    try {
                                        $revenue = Revenue::where([
                                                    'revenue_fm' => $report['fm'],
                                                    'revenue_fd' => $report['fd'],
                                                    'revenue_type' => 1,
                                                    'revenue_network_id' => $account->network_id,
                                                    'revenue_publisher_account_id' => $account->account_id,
                                                ])->first();
                                        if (empty($revenue)) {
                                            $revenue = new Revenue;
                                            $revenue->revenue_fm = $report['fm'];
                                            $revenue->revenue_fd = $report['fd'];
                                            $revenue->revenue_type = 1;
                                            $revenue->revenue_status = 1;
                                            $revenue->revenue_network_id = $account->network_id;
                                            $revenue->revenue_publisher_account_id = $account->account_id;
                                            $revenue->revenue_created_at = microtime(true);
                                        } else {
                                            $revenue->revenue_updated_at = microtime(true);
                                        }
                                        $revenue->revenue_value = $report['revenues'];
                                        $revenue->save();
                                    } catch (\Exception $exc) {
                                        //   dd($exc->getMessage());
                                    }
                                }
                            }
                        }
                    } catch (\Exception $exc) {
                        //    dd($exc->getMessage());
                    }
                    break;
            }
        }
    }

    public function cps($start_date = "", $end_date = "") {
        if ($start_date == "") {
            $start_date = date('Y-m-d');
        }
        if ($end_date == "") {
            $end_date = date('Y-m-d');
        }
        $publisher_accounts = PublisherAccount::select([
                    'publisher_accounts.account_id',
                    'publisher_accounts.account_affiliate_api_token',
                    'publisher_accounts.account_username',
                    'ads_networks.network_domain',
                    'ads_networks.network_id',
                ])
                ->join('ads_networks', 'ads_networks.network_id', '=', 'publisher_accounts.account_network_id')
                ->where([
                    'ads_networks.network_status' => 1,
                    'publisher_accounts.account_status' => 1
                ])
                ->get();
        foreach ($publisher_accounts as $publisher_account) {
            if (!is_null($publisher_account->account_affiliate_api_token) && $publisher_account->account_affiliate_api_token != "") {
                switch ($publisher_account->network_domain) {
                    case 'pub.accesstrade.vn':
                        $accesstrade_order = $this->getOrdersAccesstrade($publisher_account->account_affiliate_api_token, $start_date, $end_date);
                        if (is_array($accesstrade_order)) {
                            foreach ($accesstrade_order as $order) {
                                $cps_order = CpsOrder::select('order_id')
                                        ->where('order_source_id', $order->order_id)
                                        ->where('order_network_id', $publisher_account->network_id)
                                        ->first();
                                if (empty($cps_order)) {
                                    $cps_order = new CpsOrder;
                                    $click = CpsClick::select([
                                                'click_id',
                                                'click_user_id',
                                                'click_campaign_id',
                                                'click_created_at',
                                                'click_created_fm',
                                                'click_created_fd'
                                            ])->where('click_id', $order->utm_source)
                                            ->first();
                                    if (!empty($click)) {
                                        $cps_order->order_click_id = $order->utm_source;
                                        $cps_order->order_device_id = $order->utm_medium;
                                        $cps_order->order_user_id = $click->click_user_id;
                                        $cps_order->order_campaign_id = $click->click_campaign_id;
                                        $cps_order->order_click_at = $click->click_created_at;
                                        $cps_order->order_click_fm = $click->click_created_fm;
                                        $cps_order->order_click_fd = $click->click_created_fd;
                                    } else {
                                        $cps_order->order_click_id = 0;
                                        $cps_order->order_device_id = 0;
                                        $cps_order->order_user_id = 0;
                                        $cps_order->order_campaign_id = 0;
                                        $cps_order->order_click_at = strtotime($order->click_time);
                                        $cps_order->order_click_fm = date('ym', $cps_order->order_click_at);
                                        $cps_order->order_click_fd = date('ymd', $cps_order->order_click_at);
                                    }
                                    $cps_order->order_network_id = $publisher_account->network_id;
                                    $cps_order->order_source_id = $order->order_id;
                                    $cps_order->order_publisher_account = $publisher_account->account_id;
                                    $cps_order->order_offer_url = $order->at_product_link;
                                    $cps_order->order_bought_at = strtotime($order->sales_time);
                                    $cps_order->order_bought_fm = date('ym', $cps_order->order_bought_at);
                                    $cps_order->order_bought_fd = date('ymd', $cps_order->order_bought_at);
                                    $cps_order->order_total_payout = $order->pub_commission;
                                    $cps_order->order_merchant = $order->merchant;
                                    if ($order->order_success == 1) {
                                        $cps_order->order_status = 1;
                                    } elseif ($order->order_reject == 1) {
                                        $cps_order->order_status = -1;
                                    } else {
                                        $cps_order->order_status = 0;
                                    }
                                    $total_prices = 0;
                                    $products = [];
                                    if (isset($order->products) && is_array($order->products)) {
                                        foreach ($order->products as $product) {
                                            $total_prices += $product->product_price * $product->product_quantity;
                                            $raw_product = $this->getProductAccesstrade($publisher_account->account_affiliate_api_token, $product->product_id, $product->merchant);
                                            array_push($products, [
                                                'product_id' => $product->product_id,
                                                'product_name' => $raw_product->name,
                                                'product_link' => $raw_product->link,
                                                'product_image' => $raw_product->image,
                                                'product_price' => $raw_product->price,
                                                'product_discount' => $raw_product->discount,
                                                'product_payout' => $product->pub_commission,
                                                'product_amount' => $product->product_quantity,
                                            ]);
                                        }
                                    }
                                    $cps_order->order_total_price = $total_prices;
                                    $cps_order->order_product_count = count($products);
                                    try {
                                        $cps_order->save();
                                        try {
                                            $revenue = Revenue::where([
                                                        'revenue_fm' => $cps_order->order_bought_fm,
                                                        'revenue_fd' => $cps_order->order_bought_fd,
                                                        'revenue_type' => 2,
                                                        'revenue_network_id' => $cps_order->order_network_id,
                                                        'revenue_publisher_account_id' => $cps_order->order_publisher_account,
                                                    ])->first();
                                            if (empty($revenue)) {
                                                $revenue = new Revenue;
                                                $revenue->revenue_fm = $cps_order->order_bought_fm;
                                                $revenue->revenue_fd = $cps_order->order_bought_fd;
                                                $revenue->revenue_value = $cps_order->order_total_payout;
                                                $revenue->revenue_type = 2;
                                                $revenue->revenue_status = 0;
                                                $revenue->revenue_network_id = $cps_order->order_network_id;
                                                $revenue->revenue_publisher_account_id = $cps_order->order_publisher_account;
                                                $revenue->revenue_created_at = microtime(true);
                                            } else {
                                                $revenue->revenue_value += $cps_order->order_total_payout;
                                                $revenue->revenue_updated_at = microtime(true);
                                            }

                                            $revenue->save();
                                        } catch (\Exception $exc) {
                                            echo "235:" . $exc->getMessage() . "\n";
                                        }
                                        if (!empty($products)) {
                                            DB::table('cps_order_detail')->insert(array_map(function($product) use (&$cps_order) {
                                                        return [
                                                            'order_id' => $cps_order->order_id,
                                                            'product_id' => $product['product_id'],
                                                            'product_name' => $product['product_name'],
                                                            'product_link' => $product['product_link'],
                                                            'product_image' => $product['product_image'],
                                                            'product_price' => $product['product_price'],
                                                            'product_discount' => $product['product_discount'],
                                                            'product_payout' => $product['product_payout'],
                                                            'product_amount' => $product['product_amount']
                                                        ];
                                                    }, $products));
                                        }
                                    } catch (\Exception $exc) {
                                        echo "253:" . $exc->getMessage() . "\n";
                                    }
                                } else {
                                    if ($order->order_success == 1) {
                                        $cps_order->order_status = 1;
                                    } elseif ($order->order_reject == 1) {
                                        $cps_order->order_status = -1;
                                    } else {
                                        $cps_order->order_status = 0;
                                    }
                                    try {
                                        $cps_order->save();
                                    } catch (\Exception $exc) {
                                        echo "266:" . $exc->getMessage() . "\n";
                                    }
                                }
                            }
                        }
                        break;
                    case 'pub.masoffer.com':
                        $masoffer_order = $this->getOrdersMasOffer($publisher_account->account_username, $publisher_account->account_affiliate_api_token, $start_date, $end_date);
                        if (is_array($masoffer_order)) {
                            foreach ($masoffer_order as $order) {
                                $cps_order = CpsOrder::select('order_id')
                                        ->where('order_source_id', $order->transaction_id)
                                        ->where('order_network_id', $publisher_account->network_id)
                                        ->first();
                                if (empty($cps_order)) {
                                    $cps_order = new CpsOrder;
                                    $click = CpsClick::select([
                                                'click_id',
                                                'click_user_id',
                                                'click_campaign_id',
                                                'click_created_at',
                                                'click_created_fm',
                                                'click_created_fd'
                                            ])->where('click_id', $order->aff_sub1)
                                            ->first();
                                    if (!empty($click)) {
                                        $cps_order->order_user_id = $click->click_user_id;
                                        $cps_order->order_click_id = $order->aff_sub1;
                                        $cps_order->order_device_id = $order->aff_sub2;
                                        $cps_order->order_campaign_id = $click->click_campaign_id;
                                        $cps_order->order_click_at = $click->click_created_at;
                                        $cps_order->order_click_fm = $click->click_created_fm;
                                        $cps_order->order_click_fd = $click->click_created_fd;
                                    } else {
                                        $cps_order->order_user_id = 0;
                                        $cps_order->order_click_id = 0;
                                        $cps_order->order_device_id = 0;
                                        $cps_order->order_campaign_id = 0;
                                        $cps_order->order_click_at = strtotime($order->click_time);
                                        $cps_order->order_click_fm = date('ym', $cps_order->order_click_at);
                                        $cps_order->order_click_fd = date('ymd', $cps_order->order_click_at);
                                    }
                                    $cps_order->order_network_id = $publisher_account->network_id;
                                    $cps_order->order_source_id = $order->transaction_id;
                                    $cps_order->order_publisher_account = $publisher_account->account_id;
                                    $cps_order->order_bought_at = strtotime($order->conversion_time);
                                    $cps_order->order_bought_fm = date('ym', $cps_order->order_bought_at);
                                    $cps_order->order_bought_fd = date('ymd', $cps_order->order_bought_at);
                                    $cps_order->order_total_payout = $order->conversion_publisher_payout;
                                    $cps_order->order_merchant = $order->offer_id;
                                    $cps_order->order_status = $order->conversion_status_code;
                                    $cps_order->order_total_price = $order->conversion_sale_amount;
                                    $products = [];
                                    if (isset($order->products) && is_array($order->products)) {
                                        foreach ($order->products as $product) {
                                            array_push($products, [
                                                'product_id' => $product->product_sku,
                                                'product_name' => isset($product->product_name) ? $product->product_name : '',
                                                'product_link' => isset($product->product_url) ? $product->product_url : '',
                                                'product_price' => $product->conversion_sale_amount,
                                                'product_discount' => 0,
                                                'product_payout' => $product->conversion_publisher_payout,
                                                'product_amount' => 1,
                                            ]);
                                        }
                                    }
                                    $cps_order->order_product_count = count($products);
                                    try {
                                        $cps_order->save();
                                        try {
                                            $revenue = Revenue::where([
                                                        'revenue_fm' => $cps_order->order_bought_fm,
                                                        'revenue_fd' => $cps_order->order_bought_fd,
                                                        'revenue_type' => 2,
                                                        'revenue_network_id' => $cps_order->order_network_id,
                                                        'revenue_publisher_account_id' => $cps_order->order_publisher_account,
                                                    ])->first();
                                            if (empty($revenue)) {
                                                $revenue = new Revenue;
                                                $revenue->revenue_fm = $cps_order->order_bought_fm;
                                                $revenue->revenue_fd = $cps_order->order_bought_fd;
                                                $revenue->revenue_value = $cps_order->order_total_payout;
                                                $revenue->revenue_type = 2;
                                                $revenue->revenue_status = 0;
                                                $revenue->revenue_network_id = $cps_order->order_network_id;
                                                $revenue->revenue_publisher_account_id = $cps_order->order_publisher_account;
                                                $revenue->revenue_created_at = microtime(true);
                                            } else {
                                                $revenue->revenue_value += $cps_order->order_total_payout;
                                                $revenue->revenue_updated_at = microtime(true);
                                            }
                                            $revenue->save();
                                        } catch (\Exception $exc) {
                                            echo "359:" . $exc->getMessage() . "\n";
                                        }
                                        if (!empty($products)) {
                                            DB::table('offer_order_detail')->insert(array_map(function($product) use (&$cps_order) {
                                                        return [
                                                            'order_id' => $cps_order->order_id,
                                                            'product_id' => $product['product_id'],
                                                            'product_name' => $product['product_name'],
                                                            'product_link' => $product['product_link'],
                                                            //  'product_image' => $product['product_image'],
                                                            'product_price' => $product['product_price'],
                                                            'product_discount' => $product['product_discount'],
                                                            'product_payout' => $product['product_payout'],
                                                            'product_amount' => $product['product_amount']
                                                        ];
                                                    }, $products));
                                        }
                                    } catch (\Exception $exc) {
                                        
                                    }
                                } else {
                                    $cps_order->order_status = $order->conversion_status_code;
                                    try {
                                        $cps_order->save();
                                    } catch (\Exception $exc) {
                                        echo "384:" . $exc->getMessage() . "\n";
                                    }
                                }
                            }
                        }
                        break;
                }
            }
        }
    }

    private function handleEcomobiCpi($html) {
        $html = preg_replace("/(\\n|\\r|\\t)/", "", $html);
        $reports = [];
        if (preg_match("/<table class=\"offers-list common-table table_responsive\">.*?<\/thead>(.*?)<\/table>/", $html, $matches)) {
            $html = $matches[1];
            if (preg_match_all("/<tr>(.*?)<\/tr>/", $html, $matches)) {
                for ($i = 1; $i < count($matches[1]); $i++) {
                    $report = [];
                    if (preg_match("/<a.*?>(\d{2})(\d{2})\-(\d{2})\-(\d{2})<\/a>/", $matches[1][$i], $mt)) {
                        $report['fm'] = $mt[2] . $mt[3];
                        $report['fd'] = $mt[2] . $mt[3] . $mt[4];
                        if (preg_match("/<td class=\"click num\".*?<span class=\"\">(.*?)<\/span>/", $matches[1][$i], $m)) {
                            $report['clicks'] = trim(str_replace([",", "-"], ["", "0"], $m[1]));
                        } else {
                            $report['clicks'] = 0;
                        }
                        if (preg_match("/<td class=\"install num\".*?<span class=\"\">(.*?)<\/span>/", $matches[1][$i], $m)) {
                            $report['installs'] = trim(str_replace([",", "-"], ["", "0"], $m[1]));
                        } else {
                            $report['installs'] = 0;
                        }
                        if (preg_match("/<td class=\"rev num\".*?<span class=\"\">(.*?)<\/span>/", $matches[1][$i], $m)) {
                            $report['revenues'] = trim(str_replace([",", "đ"], "", $m[1]));
                        } else {
                            $report['revenues'] = 0;
                        }
                    }
                    array_push($reports, $report);
                }
            }
        }
        return $reports;
    }

    private function curlWithLogin($url_login, $post_fields, $url_get) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_login);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiename=0');
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
            curl_exec($ch);
            curl_setopt($ch, CURLOPT_URL, $url_get);
            $html = curl_exec($ch);
            curl_close($ch);
            return $html;
        } catch (\Exception $exc) {
            return false;
        }
    }

    private function getOrdersMasOffer($pub_id, $api_token, $start_date, $end_date, $status = "") {
        $url = 'http://api.masoffer.com/v1/transactions?pub_id=' . $pub_id . '&token=' . $api_token . '&limit=500&date_from=' . str_replace("-", '', $start_date) . '&date_to=' . str_replace("-", '', $end_date) . '';
        if ($status != "") {
            $url .= "&conversion_status_code=" . $status;
        }
        $json = Curl::to($url)->get();
        $orders = json_decode($json);
        // dd($orders->data->item);
        if (isset($orders->data->item)) {
            $orders = $orders->data->item;
            foreach ($orders as $order) {
                $item_json = Curl::to('http://api.masoffer.com/v1/transaction/' . $order->offer_id . '/' . $order->transaction_id . '/detail?pub_id=' . $pub_id . '&token=' . $api_token)->get();
                $products = json_decode($item_json);
                $order->products = $products->data;
            }
            //   dd($orders);
            return $orders;
        } else {
            return [];
        }
    }

    private function getOrdersAccesstrade($api_token, $start_date, $end_date, $status = "") {
        $url = 'https://api.accesstrade.vn/v1/orders?since=' . $start_date . 'T00:00:00Z&until=' . $end_date . 'T00:00:00Z&limit=99999999';
        if ($status != "") {
            $url .= "&status=" . $status;
        }
        $json = Curl::to($url)
                ->withHeader('Authorization: Token ' . $api_token)
                ->withContentType('application/json')
                ->get();
        $orders = json_decode($json);
        return $orders->data;
    }

    private function getProductAccesstrade($api_token, $product_id, $merchant) {
        $json = Curl::to('https://api.accesstrade.vn/v1/product_detail?merchant=' . $merchant . '&product_id=' . $product_id)
                ->withHeader('Authorization: Token ' . $api_token)
                ->withContentType('application/json')
                ->get();
        //  dd(json_decode($json));
        return json_decode($json);
    }

}
