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

        </div>
    </div>
    <div class="portlet-body">
        {!! Form::open(['action' => ['CpsReportController@click'], 'method' => 'GET', 'id' => 'filter-form']) !!}
        <div class="row">
            @if(Request::has('start'))
            <input type="hidden" name="start" value="{{ Request::input('start') }}" />
            @endif
            @if(Request::has('end'))
            <input type="hidden" name="end" value="{{ Request::input('end') }}" />
            @endif
            <div class="col-md-2">
                <div class="form-group">
                    <select name="traffic_source" class="form-control">
                        <option value="">Chọn nguồn traffic</option>
                        @foreach($traffic_sources as $traffic_source)
                        <option value="{{ $traffic_source->source_id }}" {{ Request::has('traffic_source') && Request::input('traffic_source') == $traffic_source->source_id ? 'selected' : '' }}>
                                {!! $traffic_source->source_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <select name="adwords_account" class="form-control" {{ Request::has('traffic_source') && Request::input('traffic_source') == 1 ? '' : 'disabled' }}>
                            <option value="">Chọn tài khoản Adwords</option>
                        @foreach($adwords_accounts as $adwords_account)
                        <option value="{{ $adwords_account->account_id }}" {{ Request::has('adwords_account') && Request::input('adwords_account') == $adwords_account->account_id ? 'selected' : '' }}>
                                {!! $adwords_account->account_id . ' - ' .$adwords_account->account_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <select name="campaign" class="form-control" {{ Request::has('traffic_source') ? '' : 'disabled' }}>
                            <option value="">Chọn chiến dịch quảng cáo</option>
                        @foreach($campaigns as $campaign)
                        <option value="{{ $campaign->campaign_id }}" {{ Request::has('campaign') && Request::input('campaign') == $campaign->campaign_id ? 'selected' : '' }}>
                                {!! $campaign->campaign_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
          
            <div class="col-md-2">
                <div class="form-group">
                    <select name="user" class="form-control" {{ Session::get('user')->user_id == 0 ? '' : 'disabled' }}>
                            <option value="">Chọn thành viên</option>
                        @foreach($users as $user)
                        <option value="{{ $user->user_id }}" {{ Request::has('user') && Request::input('user') == $user->user_id ? 'selected' : '' }}>
                                {!! $user->user_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <div class="input-group" id="daterange">
                        <input type="text" class="form-control" readonly>
                        <span class="input-group-btn">
                            <button class="btn default date-range-toggle" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <div class="row" id="sortable_portlets">
            <div class="col-md-4 column sortable">
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
                                <tbody>
                                    @foreach($clicks_date as $key=>$click_date)
                                    <tr>
                                        <td class="text-left">{!! preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1" , $key) !!}</td>
                                        <td class="text-right">{!! number_format($click_date, 0, '', ',')  !!}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-600">
                                        <td class="uppercase">Tổng</td>
                                        <td class="text-right">{!! number_format($total_clicks, 0, '', ',')  !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo thiết bị </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_device as $key=>$click_device)
                                    <tr>
                                        <td class="text-left">{!! $key !!}</td>
                                        <td class="text-cênter">{!! number_format($click_device['click'], 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_device['percent'], 2, '.', ',')  !!}%</td>
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
                            <span class="caption-subject uppercase font-14 font-600"> Theo aff sub 1 </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_aff_sub1 as $click_sub1)
                                    <tr>
                                        <td class="text-left">{!! $click_sub1->click_aff_sub1 !!}</td>
                                        <td class="text-cênter">{!! number_format($click_sub1->click_total, 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_sub1->percent, 2, '.', ',')  !!}%</td>
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
                            <span class="caption-subject uppercase font-14 font-600"> Theo aff sub 2 </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_aff_sub2 as $click_sub2)
                                    <tr>
                                        <td class="text-left">{!! $click_sub2->click_aff_sub2 !!}</td>
                                        <td class="text-cênter">{!! number_format($click_sub2->click_total, 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_sub2->percent, 2, '.', ',')  !!}%</td>
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
                            <span class="caption-subject uppercase font-14 font-600"> Theo aff sub 3 </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_aff_sub3 as $click_sub3)
                                    <tr>
                                        <td class="text-left">{!! $click_sub3->click_aff_sub3 !!}</td>
                                        <td class="text-cênter">{!! number_format($click_sub3->click_total, 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_sub3->percent, 2, '.', ',')  !!}%</td>
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
                            <span class="caption-subject uppercase font-14 font-600"> Theo aff sub 4 </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_aff_sub4 as $click_sub4)
                                    <tr>
                                        <td class="text-left">{!! $click_sub4->click_aff_sub4 !!}</td>
                                        <td class="text-cênter">{!! number_format($click_sub4->click_total, 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_sub4->percent, 2, '.', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 column sortable">
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo từ khóa </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_keyword as $click_keyword)
                                    <tr>
                                        <td class="text-left">{!! $click_keyword->click_keyword !!}</td>
                                        <td class="text-cênter">{!! number_format($click_keyword->click_total, 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_keyword->percent, 2, '.', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 column sortable">
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo vị trí </span>
                        </div>
                        <div class="tools p-0 m-0">
                            <a role="button" class="collapse"></a>
                        </div>
                    </div>
                    <div class="portlet-body p-0">
                        <div class="table-scrollable table-scrollable-borderless table-responsive m-0">
                            <table class="table table-hover table-light">
                                <tbody>
                                    @foreach($clicks_location as $click_location)
                                    <tr>
                                        <td class="text-left">{!! str_replace(',', ', ', $click_location->canonical_name) !!}</td>
                                        <td class="text-cênter">{!! number_format($click_location->click_total, 0, '', ',')  !!}</td>
                                        <td class="text-right">{!! number_format($click_location->percent, 2, '.', ',')  !!}%</td>
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
        $('#filter-form').find('select').change(function () {
            $('#filter-form').submit();
        });
        $('#filter-form').find('input, textarea').keyup(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#filter-form').submit();
            }
        });
        $('#filter-form').submit(function () {
            $(this).find('select, input, textarea').each(function () {
                if ($.trim($(this).val()) === "") {
                    $(this).prop('disabled', true);
                }
            });
        });
        $('#filter-form').find('select[name="traffic_source"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy nguồn traffic nào";
                }
            }
        });
        $('#filter-form').find('select[name="user"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy thành viên nào";
                }
            }
        });
        $('#filter-form').find('select[name="adwords_account"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy tài khoản Adwords nào";
                }
            }
        });
        $('#filter-form').find('select[name="campaign"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy chiến dịch quảng cáo nào";
                }
            }
        });
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
            window.location.href = '{{URL::action("CpsReportController@click")}}?start=' + start.format('YYMMDD') + '&end=' + end.format('YYMMDD')
                    + '{!! Request::has("traffic_source") && Request::input("traffic_source") !== "" ? "&traffic_source=" . Request::input("traffic_source") : "" !!}'
                    + '{!! Request::has("adwords_account") && Request::input("adwords_account") !== "" ? "&adwords_account=" . Request::input("adwords_account") : "" !!}'
                    + '{!! Request::has("campaign") && Request::input("campaign") !== "" ? "&campaign=" . Request::input("campaign") : "" !!}'
                    + '{!! Request::has("user") && Request::input("user") !== "" ? "&user=" . Request::input("user") : "" !!}';
        });
        $('#daterange input').val('{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $start) }} - {{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $end) }}');
    });

</script>
@endsection
