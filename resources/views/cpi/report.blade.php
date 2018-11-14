@extends('layout')
@section('style')
{{Html::style('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css')}}
{{ Html::style('assets/global/plugins/select2/css/select2.min.css') }}
{{ Html::style('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}
@endsection
@section('pagecontent')
<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">{!! $title !!}</span>
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
        <div class="row" id="sortable_portlets">
            <div class="col-md-6 column sortable">
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo ngày </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <thead>
                                    <tr class="uppercase">
                                        <th>Ngày</th>
                                        <th>Click</th>
                                        <th>Cài đặt</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total_clicks = 0;
                                    $total_intall = 0;
                                    $total_revenues = 0;
                                    @endphp
                                    @foreach($cpi_dates as $key => $cpi_date)
                                    @php
                                    $total_clicks += $cpi_date['clicks'];
                                    $total_intall += $cpi_date['installs'];
                                    $total_revenues += $cpi_date['revenues']
                                    @endphp
                                    <tr>
                                        <td>{!! preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1" , $key) !!}</td>
                                        <td>{!! number_format($cpi_date['clicks'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($cpi_date['installs'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($cpi_date['revenues'], 0, '', ',')  !!}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-600">
                                        <td class="uppercase">Tổng</td>
                                        <td>{!! number_format($total_clicks, 0, '', ',')  !!}</td>
                                        <td>{!! number_format($total_intall, 0, '', ',')  !!}</td>
                                        <td>{!! number_format($total_revenues, 0, '', ',')  !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-6 column sortable">
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo tài khoản publisher </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <thead>
                                    <tr class="uppercase">
                                        <th>Tên tài khoản</th>
                                        <th>Click</th>
                                        <th>Cài đặt</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cpi_publishers as $key => $cpi_publisher)
                                    <tr>
                                        <td>{!! $cpi_publisher->account_username . ' - ' . $cpi_publisher->network_name !!}</td>
                                        <td>{!! number_format($cpi_publisher->total_clicks, 0, '', ',')  !!}</td>
                                        <td>{!! number_format($cpi_publisher->total_installs, 0, '', ',')  !!}</td>
                                        <td>{!! number_format($cpi_publisher->total_revenues, 0, '', ',')  !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo mạng quảng cáo </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <thead>
                                    <tr class="uppercase">
                                        <th>Mạng quảng cáo</th>
                                        <th>Click</th>
                                        <th>Cài đặt</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cpi_networks as $key => $cpi_network)
                                    <tr>
                                        <td>{!! $cpi_network->network_name !!}</td>
                                        <td>{!! number_format($cpi_network->total_clicks, 0, '', ',')  !!}</td>
                                        <td>{!! number_format($cpi_network->total_installs, 0, '', ',')  !!}</td>
                                        <td>{!! number_format($cpi_network->total_revenues, 0, '', ',')  !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
{{Html::script('assets/global/plugins/moment.min.js')}}
{{Html::script('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js')}}
{{Html::script('assets/global/plugins/jquery-ui/jquery-ui.min.js')}}
{{ Html::script('assets/global/plugins/select2/js/select2.full.min.js') }}
<script type="text/javascript">
    $(document).ready(function () {
        $("#sortable_portlets").sortable({
            connectWith: ".portlet",
            items: ".portlet",
            opacity: 0.8,
            handle: '.portlet-title',
            coneHelperSize: true,
            placeholder: 'portlet-sortable-placeholder',
            forcePlaceholderSize: true,
            tolerance: "pointer",
            helper: "clone",
            cancel: ".portlet-sortable-empty, .portlet-fullscreen",
            revert: 250,
            update: function (b, c) {
                if (c.item.prev().hasClass("portlet-sortable-empty")) {
                    c.item.prev().before(c.item);
                }
            }
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
            window.location.href = '{{URL::action("CpiReportController@index")}}?start=' + start.format('YYMMDD') + '&end=' + end.format('YYMMDD');
        });
        $('#daterange span').html('{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $start) }} - {{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $end) }}');
    });

</script>
@endsection
