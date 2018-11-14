<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */
$tracking = function() {
    Route::group(['prefix' => 'trackings'], function() {
        Route::group(['prefix' => 'v3'], function() {
            Route::get('/{campaign_id}', 'TrackingController@trackingV3')->where(['campaign_id' => '[0-9]+']);
        });
    });
};
Route::group(['domain' => 'go.gtrackings.com'], $tracking);
Route::group(['domain' => 'go.gclickprice.com'], $tracking);
Route::group(['domain' => 'go.shop-online.sale'], $tracking);
Route::group(['domain' => 'go.gotoweb.us'], $tracking);
Route::group(['domain' => 'go.trackingtop.com'], $tracking);
Route::group(['domain' => 'go.clicktoweb.net'], $tracking);
$base = function() {
    Route::group(['middleware' => 'access'], function() {
        Route::get('/', 'HomeController@index');
        Route::group(['prefix' => 'ads-network'], function() {
            Route::get('/', 'AdsNetworkController@index');
            Route::post('/add', 'AdsNetworkController@doAddNetwork');
            Route::get('/load', 'AdsNetworkController@loadNetwork');
            Route::post('/edit', 'AdsNetworkController@doEditNetwork');
            Route::post('/delete', 'AdsNetworkController@doDeleteNetwork');
        });
        Route::group(['prefix' => 'publisher-account'], function() {
            Route::get('/', 'PublisherAccountController@index');
            Route::post('/add', 'PublisherAccountController@doAddAccount');
            Route::get('/load', 'PublisherAccountController@loadAccount');
            Route::post('/edit', 'PublisherAccountController@doEditAccount');
            Route::post('/delete', 'PublisherAccountController@doDeleteAccount');
        });
        Route::group(['prefix' => 'adwords-account'], function() {
            Route::get('/', 'AdwordsAccountController@index');
            Route::post('/add', 'AdwordsAccountController@doAddAccount');
            Route::get('/load', 'AdwordsAccountController@loadAccount');
            Route::post('/edit', 'AdwordsAccountController@doEditAccount');
            Route::post('/delete', 'AdwordsAccountController@doDeleteAccount');
        });
        Route::group(['prefix' => 'traffic-source'], function() {
            Route::get('/', 'TrafficSourceController@index');
            Route::post('/add', 'TrafficSourceController@doAddSource');
            Route::get('/load', 'TrafficSourceController@loadSource');
            Route::post('/edit', 'TrafficSourceController@doEditSource');
            Route::post('/delete', 'TrafficSourceController@doDeleteSource');
        });
        Route::group(['prefix' => 'cps'], function() {
            Route::group(['prefix' => 'campaign'], function() {
                Route::get('/', 'CpsCampaignController@index');
                Route::post('/add', 'CpsCampaignController@doAddCampaign');
                Route::get('/load', 'CpsCampaignController@loadCampaign');
                Route::post('/edit', 'CpsCampaignController@doEditCampaign');
                Route::post('/update-cost', 'CpsCampaignController@doUpdateCostCampaign');
                Route::get('/load-cost', 'CpsCampaignController@loadCostCampaign');
                Route::post('/delete', 'CpsCampaignController@doDeleteCampaign');
                Route::get('/getlink', 'CpsCampaignController@linkTracking');
            });
            Route::group(['prefix' => 'report'], function() {
                Route::get('/', 'CpsReportController@index');
                Route::get('/click', 'CpsReportController@click');
                Route::get('/order', 'CpsReportController@order');
                Route::get('/revenue', 'CpsReportController@revenue');
                Route::get('/order-detail', 'CpsReportController@orderDetail');
            });
        });
        Route::group(['prefix' => 'cpi'], function() {
            Route::group(['prefix' => 'report'], function() {
                Route::get('/', 'CpiReportController@index');
            });
        });
    });
    Route::group(['prefix' => 'cronjob'], function() {
        Route::group(['prefix' => 'revenue'], function() {
            Route::group(['prefix' => 'cpi'], function() {
                Route::get('/', 'CronjobController@cpi');
            });
        });
    });
    Route::get('/login', 'AccessController@login');
    Route::post('/login', 'AccessController@doLogin');
    Route::get('/logout', 'AccessController@logout');
    Route::get('/cookie', 'HomeController@testCookie');
};
Route::group(['domain' => 'gclickprice.com'], $base);
Route::group(['domain' => 'gtrackings.com'], $base);
//Route::group(['domain' => 'clicktoweb.net'], $base);
