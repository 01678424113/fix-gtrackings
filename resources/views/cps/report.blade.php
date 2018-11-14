@extends('layout')
@section('style')
{{Html::style('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css')}}
@endsection
@section('pagecontent')
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a href="{{ URL::action('CpsReportController@click') }}">
            <div class="dashboard-stat2 bordered">
                <div class="display">
                    <div class="number">
                        <h3 class="font-green-sharp">
                            <span>{!! number_format($click['today'], 0, '', ',') !!}</span>
                        </h3>
                        <small>Click</small>
                    </div>
                    <div class="icon">
                        <i class="icon-mouse"></i>
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress">
                        <span data-toggle="tooltip" data-placement="top" title="{{ $click['percent_today_yesterday'] }}%"  style="width: {{ $click['percent_today_yesterday'] > 100 ? '100' : $click['percent_today_yesterday'] }}%;" class="progress-bar progress-bar-success green-sharp"></span>
                    </div>
                    <div class="status">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="status-title"> Device </div>
                                <div class="status-number">{!! number_format($today_devices_count, 0, '', ',') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a href="{{ URL::action('CpsReportController@order') }}">
            <div class="dashboard-stat2 bordered">
                <div class="display">
                    <div class="number">
                        <h3 class="font-red-haze">
                            <span>{!! number_format($order['today_total'], 0, '', ',') !!}</span>
                        </h3>
                        <small>Đơn hàng</small>
                    </div>
                    <div class="icon">
                        <i class="icon-basket"></i>
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress">
                        <span data-toggle="tooltip" data-placement="top" title="{{ $order['percent_count_today_yesterday'] }}%"  style="width: {{ $order['percent_count_today_yesterday'] > 100 ? '100' : $order['percent_count_today_yesterday'] }}%;" class="progress-bar progress-bar-success red-haze">
                        </span>
                    </div>
                    <div class="status">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="status-title"> Chờ duyệt </div>
                                <div class="status-number">{!! number_format($order['today_pending'], 0, '', ',') !!}</div>
                            </div>
                            <div class="col-xs-6">
                                <div class="status-title"> Đã duyệt </div>
                                <div class="status-number">{!! number_format($order['today_success'], 0, '', ',') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a href="{{ URL::action('CpsReportController@revenue') }}">
            <div class="dashboard-stat2 bordered">
                <div class="display">
                    <div class="number">
                        <h3 class="font-blue-sharp">
                            <span>{!! number_format($order['today_payouts_total'], 0, '', ',') !!}</span>
                        </h3>
                        <small>Hoa hồng</small>
                    </div>
                    <div class="icon">
                        <i class="icon-diamond"></i>
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress">
                        <span data-toggle="tooltip" data-placement="top" title="{{ $order['percent_payouts_today_yesterday'] }}%"  style="width: {{ $order['percent_payouts_today_yesterday'] > 100 ? '100' : $order['percent_payouts_today_yesterday'] }}%;" class="progress-bar progress-bar-success blue-sharp">
                        </span>
                    </div>
                    <div class="status">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="status-title"> Chưa duyệt </div>
                                <div class="status-number">{!! number_format($order['today_payouts_pending'], 0, '', ',') !!}</div>
                            </div>
                            <div class="col-xs-6">
                                <div class="status-title"> Đã duyệt </div>
                                <div class="status-number">{!! number_format($order['today_payouts_success'], 0, '', ',') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a href="{{ URL::action('CpsReportController@revenue', ['start' => date('ymd', strtotime('first day of this month')), 'end' => date('ymd')]) }}">
            <div class="dashboard-stat2 bordered">
                <div class="display">
                    <div class="number">
                        <h3 class="font-purple-soft">
                            <span>{!! number_format($thismonth_orders_payouts_total, 0, '', ',') !!}</span>
                        </h3>
                        <small>Tháng này</small>
                    </div>
                    <div class="icon">
                        <i class="icon-wallet"></i>
                    </div>
                </div>
                <div class="progress-info">
                    <div class="progress">
                        <span data-toggle="tooltip" data-placement="top" title="{{ $percent_payouts_success }}%"  style="width: {{ $percent_payouts_success > 100 ? '100' : $percent_payouts_success }}%;" class="progress-bar progress-bar-success purple-soft">
                        </span>
                    </div>
                    <div class="status">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="status-title"> Đã duyệt </div>
                                <div class="status-number">{!! number_format($thismonth_orders_payouts_success, 0, '', ',') !!}</div>
                            </div>
                            <div class="col-xs-6">
                                <div class="status-title"> Bị hủy </div>
                                <div class="status-number">{!! number_format($thismonth_orders_payouts_cancel, 0, '', ',') !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Biểu đồ hoa hồng</span>
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
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Đơn hàng mới phát sinh</span>
        </div>
    </div>
    <div class="portlet-body">
        @if(count($orders_latest) > 0)
        <div class="table-scrollable table-scrollable-borderless">
            <table class="table table-hover table-light" id="orders">
                <thead>
                    <tr class="uppercase">

                        <th>Mạng quảng cáo</th>
<!--                        <th>ID đơn hàng</th>-->
                        <th>Chiến dịch</th>
                        <th>Click</th>
                        <th>Thời gian click</th>
                        <th>Thời gian mua</th>
                        <th>Shop</th>
                        <th>Giá trị</th>
                        <th>Hoa hồng</th>
                        <th style="width: 100px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders_latest as $order)
                    <tr data-id="{{ $order->order_id }}" 
                        @if(date('dmy', $order->order_click_at) == date('dmy', $order->order_bought_at))
                        class="font-green-jungle"
                        @else
                        class=" font-red"
                        @endif
                        >
                        <td>{!! $order->network_name !!}</td>
<!--                        <td>{!! $order->order_source_id !!}</td>-->
                        <td>{!! $order->campaign_name !!}</td>
                        <td>{!! $order->order_click_id !!}</td>
                        <td>{!! date('H:i:s d/m/Y', $order->order_click_at) !!}</td>
                        <td>{!! date('H:i:s d/m/Y', $order->order_bought_at) !!}</td>
                        <td>{!! $order->order_merchant !!}</td>
                        <td>{!! number_format($order->order_total_price, 0, '', ',')  !!}</td>
                        <td>{!! number_format($order->order_total_payout, 0, '', ',')  !!}</td>
                        <td class="text-center">
                            @if($order->order_status == 1)
                            <span class="badge badge-success badge-roundless"> Đã duyệt </span>
                            @elseif($order->order_status == 0)
                            <span class="badge badge-warning badge-roundless"> Chờ duyệt </span>
                            @else
                            <span class="badge badge-danger badge-roundless"> Bị hủy </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right">
            {!!  $orders_latest->appends(Request::all())->links() !!}
        </div>
        @else
        <h4 class="text-center">Không có dữ liệu</h4> 
        @endif
    </div>
</div>
<div class="modal fade" id="detail-order-modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            <div class="modal-body" style="display: none">
                <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                    <table class="table table-hover table-light">
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td class="font-600">Mạng quảng cáo</td>
                                <td id="network_name"></td>
                            </tr>
                            <tr>
                                <td class="font-600">ID đơn hàng</td>
                                <td id="order_id"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Chiến dịch</td>
                                <td id="campaign_name"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Thời gian click</td>
                                <td id="order_click_at"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Thời gian mua</td>
                                <td id="order_bought_at"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Shop</td>
                                <td id="order_merchant"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Giá trị</td>
                                <td id="order_total_price"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Hoa hồng</td>
                                <td id="order_total_payout"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Click ID</td>
                                <td id="order_click_id"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Click URL</td>
                                <td id="click_lpurl" class="font-12"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Từ khóa</td>
                                <td id="click_keyword"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Vị trí</td>
                                <td id="canonical_name"></td>
                            </tr>
                            <tr>
                                <td class="font-600">Thiết bị</td>
                                <td id="device_model"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <table class="table table-bordered table-hover m-t-10 td-middle" id="products">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá bán</th>
                            <th>Hoa hồng</th>
                            <th style="width: 25px">SL</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="display: none">
                <button type="button" class="btn default uppercase" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
{{Html::script('assets/global/plugins/moment.min.js')}}
{{Html::script('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js')}}
{{Html::script('assets/global/plugins/highcharts/js/highcharts.js')}}
<script type="text/javascript">
    $(document).ready(function () {
    $('#detail-order-modal').on('hidden.bs.modal', function(){
    $(this).find('td[id]').empty();
    $(this).find('#products').find('tbody').empty();
    });
    $('#orders').find('tbody tr').click(function () {
    var order_id = $(this).data('id');
    var modal = $('#detail-order-modal');
    modal.modal('show');
    $.ajax({
    url: "{{URL::action('CpsReportController@orderDetail')}}",
            type: "GET",
            data: {
            order_id: order_id
            },
            dataType: "text",
            timeout: 30000,
            error: function (jqXHR, textStatus, errorThrow) {
            modal.modal('hide');
            toastr['error']('Lỗi trong quá trình xử lý dữ liệu');
            },
            success: function (data) {
            var json_data = $.parseJSON(data);
            console.log(json_data);
            if (json_data.status_code === 200) {
            modal.find('.modal-title').text("Đơn hàng: " + json_data.data.order_source_id + " - " + json_data.data.network_name);
            modal.find('#network_name').text(json_data.data.network_name);
            modal.find('#order_id').text(json_data.data.order_source_id);
            if (json_data.data.campaign_type == 1){
            json_data.data.campaign_name += ' (Adwords)';
            }
            if (json_data.data.campaign_name !== null) {
            modal.find('#campaign_name').text(json_data.data.campaign_name);
            } else {
            modal.find('#campaign_name').text("");
            }
            modal.find('#order_click_id').text(json_data.data.order_click_id);
            modal.find('#order_click_at').text(json_data.data.order_click_at);
            modal.find('#order_bought_at').text(json_data.data.order_bought_at);
            modal.find('#order_merchant').text(json_data.data.order_merchant);
            modal.find('#order_total_price').text(json_data.data.order_total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' đ');
            modal.find('#order_total_payout').text(json_data.data.order_total_payout.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' đ');
            if (json_data.data.click_lpurl !== null) {
            modal.find('#click_lpurl').text(json_data.data.click_lpurl);
            } else {
            modal.find('#click_lpurl').text("");
            }
            if (json_data.data.click_keyword !== null) {
            modal.find('#click_keyword').text(json_data.data.click_keyword);
            }
            if (json_data.data.canonical_name !== null) {
            modal.find('#canonical_name').text(json_data.data.canonical_name);
            }
            if (json_data.data.device_type === 'm') {
            json_data.data.device_model += ' - Mobile';
            } else if (json_data.data.device_type === 't') {
            json_data.data.device_model += ' - Tablet';
            } else if (json_data.data.device_type === 'c') {
            json_data.data.device_model += '- PC';
            }
            if (json_data.data.device_model !== null) {
            modal.find('#device_model').text(json_data.data.device_model);
            } else {
            modal.find('#device_model').text("");
            }
            var products = "";
            $.each(json_data.data.order_detail, function(key, product){
            products += '<tr>';
            products += '<td>' + (key + 1) + '</td>';
            products += '<td><a href="' + product.product_link + '" target="_blank">' + product.product_name + '</a></td>';
            products += '<td>' + product.product_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' đ</td>';
            products += '<td>' + product.product_payout.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' đ</td>';
            products += '<td>' + product.product_amount + '</td>';
            products += '</tr>';
            });
            modal.find('#products').find('tbody').html(products);
            modal.find('.modal-body').show();
            modal.find('.modal-footer').show();
            } else {
            modal.modal('hide');
            toastr['error'](json_data.message);
            }
            }
    });
    });
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
    window.location.href = '{{URL::action("CpsReportController@index")}}?start=' + start.format('YYMMDD') + '&end=' + end.format('YYMMDD');
    });
    $('#daterange span').html('{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $start) }} - {{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $end) }}');
    Highcharts.setOptions({
    lang: {
    decimalPoint: '.',
            thousandsSep: ','
    }
    });
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
            name: 'Ngày mua',
                    color: "#32C5D2",
                    yAxis: 0,
                    data: [{!! implode(", ", array_map(function($order){return $order["payouts"]; }, $orders_dates_bought)) !!}],
                    marker: {
                    enabled: false,
                            symbol: 'diamond'
                    },
                    tooltip: {
                    valueSuffix: ' đ'
                    },
            },
            {
            name: 'Ngày click',
                    color: "#8E44AD",
                    yAxis: 0,
                    data: [{!! implode(", ", array_map(function($order){return $order["payouts"]; }, $orders_dates_click)) !!}],
                    marker: {
                    enabled: false,
                            symbol: 'diamond'
                    },
                    tooltip: {
                    valueSuffix: ' đ'
                    },
                    visible: false
            },
            {
            name: 'Bị hủy',
                    color: "#D05454",
                    yAxis: 0,
                    data: [{!! implode(", ", array_map(function($order){return $order["payouts"]; }, $orders_dates_cancel)) !!}],
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