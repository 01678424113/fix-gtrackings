@extends('layout')
@section('style')
{{Html::style('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css')}}
@endsection
@section('pagecontent')
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a href="{{URL::action('CpiReportController@index')}}">
            <div class="dashboard-stat2 bordered">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span>{!! number_format($cpi['revenues_today'], 0, '', ',') !!}</span>
                        </h3>
                        <small>CPI</small>
                    </div>
                    <div class="icon">
                        <i class="icon-settings"></i>
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress">
                        <span data-toggle="tooltip" data-placement="top" title="{{ $cpi['percent_today_yesterday'] }}%"  style="width: {{ $cpi['percent_today_yesterday'] > 100 ? '100' : $cpi['percent_today_yesterday'] }}%;" class="progress-bar progress-bar-success green-sharp"></span>
                    </div>
                    <div class="status">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="status-title"> Hôm qua </div>
                                <div class="status-number">{!! number_format($cpi['revenues_yesterday'], 0, '', ',') !!}</div>
                            </div>
                            <div class="col-xs-6">
                                <div class="status-title"> Tháng này </div>
                                <div class="status-number">{!! number_format($cpi['revenues_thismonth'], 0, '', ',') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a href="{{URL::action('CpsReportController@index')}}">
            <div class="dashboard-stat2 bordered">
                <div class="display">
                    <div class="number">
                        <h3 class="font-red-haze">
                            <span>{!! number_format($cps['revenues_today'], 0, '', ',') !!}</span>
                        </h3>
                        <small>CPS</small>
                    </div>
                    <div class="icon">
                        <i class="icon-handbag"></i>
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress">
                        <span data-toggle="tooltip" data-placement="top" title="{{ $cps['percent_today_yesterday'] }}%"  style="width: {{ $cps['percent_today_yesterday'] > 100 ? '100' : $cps['percent_today_yesterday'] }}%;" class="progress-bar progress-bar-success red-haze">
                        </span>
                    </div>
                    <div class="status">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="status-title"> Hôm qua </div>
                                <div class="status-number">{!! number_format($cps['revenues_yesterday'], 0, '', ',') !!}</div>
                            </div>
                            <div class="col-xs-6">
                                <div class="status-title"> Tháng này </div>
                                <div class="status-number">{!! number_format($cps['revenues_thismonth'], 0, '', ',') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-blue-sharp">
                        <span>0</span>
                    </h3>
                    <small>CPO</small>
                </div>
                <div class="icon">
                    <i class="icon-basket"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success blue-sharp">
                    </span>
                </div>
                <div class="status">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="status-title"> Hôm qua </div>
                            <div class="status-number"> 0 </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="status-title"> Tháng này </div>
                            <div class="status-number"> 0 </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 bordered">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft">
                        <span>{!! number_format($revenues_thismonth, 0, '', ',') !!}</span>
                    </h3>
                    <small>Tháng này</small>
                </div>
                <div class="icon">
                    <i class="icon-wallet"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 100%;" class="progress-bar progress-bar-success purple-soft">
                    </span>
                </div>
                <div class="status">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="status-title"> Tháng trước </div>
                            <div class="status-number">{!! number_format($revenues_lastmonth, 0, '', ',') !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Biểu đồ doanh thu</span>
        </div>
        <div class="actions">
            <div id="daterange" class="btn default">
                <i class="fa fa-calendar"></i> &nbsp;
                <span></span>
                <b class="fa fa-angle-down"></b>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <div id="dates"></div>
    </div>
</div>
@endsection
@section('script')
{{Html::script('assets/global/plugins/moment.min.js')}}
{{Html::script('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js')}}
{{Html::script('assets/global/plugins/highcharts/js/highcharts.js')}}
<script>
    Highcharts.setOptions({
    lang: {
    decimalPoint: '.',
            thousandsSep: ','
    }
    });
    $(document).ready(function () {
    $('#daterange').daterangepicker({
    opens: 'left',
            showDropdowns: true,
            showWeekNumbers: true,
            format: 'MM/DD/YYYY',
            separator: ' - ',
            startDate: '{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $start) }}',
            maxDate: moment(),
            endDate: '{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $end) }}',
            ranges: {
            'Hôm nay': [moment(), moment()],
                    'Hôm qua': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    '7 ngày trước': [moment().subtract('days', 6), moment()],
                    '30 ngày trước': [moment().subtract('days', 29), moment()],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng trước': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            locale: {
            format: 'DD/MM/YYYY',
                    applyLabel: 'XEM',
                    cancelLabel: 'HỦY BỎ',
                    fromLabel: 'Từ',
                    toLabel: 'Đến',
                    customRangeLabel: 'Tùy chọn',
                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                    firstDay: 1
            }
    }, function (start, end) {
    window.location.href = '{{URL::action("HomeController@index")}}?start=' + start.format('YYMMDD') + '&end=' + end.format('YYMMDD');
    });
    $('#daterange span').html('{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $start) }} - {{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $end) }}');
    $('#dates').highcharts({
    chart: {
    style: {
    fontFamily: '"Open Sans",sans-serif'
    },
            type: 'spline'
    },
            title: {
            text: ''
            },
            subtitle: {
            text: ''
            },
            xAxis: {
            categories: ['{!! implode("', '", array_map(function($date){return preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2" , $date); }, $dates)) !!}']
            },
            yAxis: [{
            labels: {
            format: '{value:,.0f} đ',
                    style: {
                    color: "#D05454"
                    }
            },
                    title: {
                    text: '',
                            style: {
                            color: "#D05454"
                            }
                    },
                    opposite: true
            }, {
            gridLineWidth: 0,
                    title: {
                    text: '',
                            style: {
                            color: "#3598DC"
                            }
                    },
                    labels: {
                    format: '{value:,.0f} %',
                            style: {
                            color: "#3598DC"
                            }
                    }
            }],
            tooltip: {
            crosshairs: true,
                    shared: true
            },
            series: [
            {
            name: 'CPI',
                    color: "#2ab4c0",
                    yAxis: 0,
                    data: [{!! implode(", ", $cpi_dates) !!}],
                    marker: {
                    enabled: false,
                            symbol: 'diamond'
                    },
                    tooltip: {
                    valueSuffix: ' đ'
                    },
            },
            {
            name: 'CPS',
                    color: "#f36a5a",
                    yAxis: 0,
                    data: [{!! implode(", ", $cps_dates) !!}],
                    marker: {
                    enabled: false,
                            symbol: 'diamond'
                    },
                    tooltip: {
                    valueSuffix: ' đ'
                    },
            },
            {
            name: 'CPO',
                    color: "#5C9BD1",
                    yAxis: 0,
                    data: [{!! implode(", ", $cpo_dates) !!}],
                    marker: {
                    enabled: false,
                            symbol: 'diamond'
                    },
                    tooltip: {
                    valueSuffix: ' đ'
                    },
            }
            ],
            credits: {
            enabled: false
            },
    });
    });
</script>
@endsection
