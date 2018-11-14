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
        {!! Form::open(['action' => ['CpsReportController@revenue'], 'method' => 'GET', 'id' => 'filter-form']) !!}
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
            <div class="col-lg-6 col-md-12 column sortable">
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
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_date as $key=>$revenue_date)
                                    <tr>
                                        <td class="text-left">{!! preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1" , $key) !!}</td>
                                        <td>{!! number_format($revenue_date['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_date['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_date['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_date['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-600">
                                        <td class="uppercase">Tổng</td>
                                        <td>{!! number_format($total_payout, 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($total_payout_success, 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($total_payout_cancel, 0, '', ',')  !!}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if(!empty($revenues_sub1))
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
                                <thead>
                                    <tr class="uppercase">
                                        <th>Aff sub 1</th>
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_sub1 as $key=>$revenue_sub1)
                                    <tr>
                                        <td class="text-left">{!! empty($key) ? 'Không xác định' : $key !!}</td>
                                        <td>{!! number_format($revenue_sub1['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_sub1['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_sub1['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_sub1['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($revenues_sub2))
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
                                <thead>
                                    <tr class="uppercase">
                                        <th>Aff sub 2</th>
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_sub2 as $key=>$revenue_sub2)
                                    <tr>
                                        <td class="text-left">{!! empty($key) ? 'Không xác định' : $key !!}</td>
                                        <td>{!! number_format($revenue_sub2['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_sub2['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_sub2['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_sub2['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($revenues_sub3))
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
                                <thead>
                                    <tr class="uppercase">
                                        <th>Aff sub 3</th>
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_sub3 as $key=>$revenue_sub3)
                                    <tr>
                                        <td class="text-left">{!! empty($key) ? 'Không xác định' : $key !!}</td>
                                        <td>{!! number_format($revenue_sub3['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_sub3['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_sub3['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_sub3['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($revenues_sub4))
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
                                <thead>
                                    <tr class="uppercase">
                                        <th>Aff sub 4</th>
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_sub4 as $key=>$revenue_sub4)
                                    <tr>
                                        <td class="text-left">{!! empty($key) ? 'Không xác định' : $key !!}</td>
                                        <td>{!! number_format($revenue_sub4['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_sub4['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_sub4['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_sub4['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-6 col-md-12 column sortable">
                @if(!empty($revenues_network))
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
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_network as $key=>$revenue_network)
                                    <tr>
                                        <td class="text-left">{!! $key !!}</td>
                                        <td>{!! number_format($revenue_network['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_network['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_network['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_network['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($revenues_apublish))
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
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_apublish as $key=>$revenue_apublish)
                                    <tr>
                                        <td class="text-left">{!! $key !!}</td>
                                        <td>{!! number_format($revenue_apublish['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_apublish['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_apublish['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_apublish['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($revenues_merchant))
                <div class="portlet portlet-sortable light white bordered">
                    <div class="portlet-title m-b-0" style="min-height: 30px;">
                        <div class="caption p-0">
                            <span class="caption-subject uppercase font-14 font-600"> Theo website thương mại </span>
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
                                        <th>Website</th>
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_merchant as $key=>$revenue_merchant)
                                    <tr>
                                        <td class="text-left">{!! empty($key) ? 'Không xác định' : $key !!}</td>
                                        <td>{!! number_format($revenue_merchant['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_merchant['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_merchant['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_merchant['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @if(!empty($revenues_location))
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
                                <thead>
                                    <tr class="uppercase">
                                        <th>Vị trí</th>
                                        <th>Hoa hồng</th>
                                        <th>Đã duyệt</th>
                                        <th>Bị hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenues_location as $key=>$revenue_location)
                                    <tr>
                                        <td class="text-left">{!! empty($key) ? 'Không xác định' : $key !!}</td>
                                        <td>{!! number_format($revenue_location['payout'], 0, '', ',')  !!}</td>
                                        <td class="font-green">{!! number_format($revenue_location['payout_success'], 0, '', ',')  !!}</td>
                                        <td class="font-red-soft">{!! number_format($revenue_location['payout_cancel'], 0, '', ',')  !!}</td>
                                        <td>{!! number_format($revenue_location['percent_cancel'], 0, '', ',')  !!}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
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
            window.location.href = '{{URL::action("CpsReportController@revenue")}}?start=' + start.format('YYMMDD') + '&end=' + end.format('YYMMDD')
                    + '{!! Request::has("traffic_source") && Request::input("traffic_source") !== "" ? "&traffic_source=" . Request::input("traffic_source") : "" !!}'
                    + '{!! Request::has("adwords_account") && Request::input("adwords_account") !== "" ? "&adwords_account=" . Request::input("adwords_account") : "" !!}'
                    + '{!! Request::has("campaign") && Request::input("campaign") !== "" ? "&campaign=" . Request::input("campaign") : "" !!}'
                    + '{!! Request::has("user") && Request::input("user") !== "" ? "&user=" . Request::input("user") : "" !!}';
        });
        $('#daterange input').val('{{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $start) }} - {{ preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$3/$2/20$1", $end) }}');
    });

</script>
@endsection
