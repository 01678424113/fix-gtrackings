@extends('layout') 
@section('style') 
{{ Html::style('assets/global/plugins/select2/css/select2.min.css') }}
{{ Html::style('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}
{{Html::style('assets/global/plugins/icheck/skins/all.css')}} 
{{Html::style('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}} 
@endsection 
@section('pagecontent')
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Tất cả chiến dịch quảng cáo</span>
        </div>
        <div class="actions">
            <button type="button" class="btn blue uppercase" data-toggle="modal" data-target="#add-campaign-modal">Thêm mới</button>
        </div>
    </div>
    <div class="portlet-body">
        {!! Form::open(['action' => 'CpsCampaignController@index', 'method' => 'GET', 'id' => 'filter-form']) !!}
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Tìm theo tên chiến dịch" value="{{ Request::has('name') ? Request::input('name') : '' }}" />
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select name="source" class="form-control">
                        <option value="">Chọn nguồn traffic</option>
                        @foreach($traffic_sources as $traffic_source)
                        <option value="{{ $traffic_source->source_id }}" 
                                {{ Request::has('source') && Request::input('source') == $traffic_source->source_id ? 'selected' : '' }}
                                >{!! $traffic_source->source_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select class="form-control" name="a_account">
                            <option value="">Chọn tài khoản adwords</option>
                        @foreach($adwords_accounts as $adwords_account)
                        <option value="{{ $adwords_account->account_id }}"
                                {{ Request::has('a_account') && Request::input('a_account') == $adwords_account->account_id ? 'selected' : '' }}
                                >{!! $adwords_account->account_id . ' - ' . $adwords_account->account_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select name="network" class="form-control">
                        <option value="">Chọn mạng quảng cáo</option>
                        @foreach($ads_networks as $ads_network)
                        <option value="{{$ads_network->network_id}}"
                                {{ Request::has('network') && Request::input('network') == $ads_network->network_id ? 'selected' : '' }}
                                >{!! $ads_network->network_name !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select name="p_account" class="form-control">
                        <option value="">Chọn tài khoản publisher</option>
                        @foreach($publisher_accounts as $publisher_account)
                        <option value="{{$publisher_account->account_id}}"
                                {{ Request::has('p_account') && Request::input('p_account') == $publisher_account->account_id ? 'selected' : '' }}
                                >{!! $publisher_account->account_username !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <select name="merchant" class="form-control">
                        <option value="">Chọn website thương mại</option>
                        @foreach($cps_merchants as $cps_merchant)
                        <option value="{{$cps_merchant->merchant_id}}" 
                                {{ Request::has('merchant') && Request::input('merchant') == $cps_merchant->merchant_id ? 'selected' : '' }}
                                >{!! $cps_merchant->merchant_domain !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        @if(count($campaigns) > 0)
        <div class="table-scrollable table-scrollable-borderless">
            <table class="table table-hover table-light">
                <thead>
                    <tr class="uppercase">
                        <th>ID</th>
                        <th>Tên chiến dịch</th>
                        <th>Nguồn traffic</th>
                        <th>TK adwords</th>
                        <th>Mạng quảng cáo</th>
                        <th>TK publisher</th>
                        <th>Website thương mại</th>
                        <th style="width: 100px;">Trạng thái</th>
                        <th style="width: 131px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                    <tr>
                        <td>{!! $campaign->campaign_id !!}</td>
                        <td class="font-13">{!! $campaign->campaign_name !!}</td>
                        <td>{!! $campaign->source_name !!}</td>
                        <td>{!! $campaign->account_name !!}</td>
                        <td>{!! $campaign->network_name !!}</td>
                        <td>{!! $campaign->account_username !!}</td>
                        <td>{!! $campaign->merchant_domain !!}</td>
                        <td class="text-center font-13">
                            @if($campaign->campaign_status == 1)
                            <span class="font-green-jungle">Bật</span>
                            @elseif($campaign->campaign_status == 0)
                            <span class="font-yellow-lemon">Chờ duyệt</span>
                            @else 
                            <span class="font-red-thunderbird">Tắt</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" data-id="{{ $campaign->campaign_id }}" class="btn btn-outline blue btn-xs btn-getlink">
                                <i class="fa fa-link"></i>
                            </button>
                            <button type="button" data-id="{{ $campaign->campaign_id }}" class="btn btn-outline green btn-xs btn-loadcampaign">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" data-id="{{ $campaign->campaign_id }}" class="btn btn-outline purple btn-xs btn-cost">
                                <i class="fa fa-dollar" style="margin: 0 1px;"></i>
                            </button>
                            <button type="button" data-id="{{ $campaign->campaign_id }}" class="btn btn-outline btn-xs red-soft m-r-0 btn-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right">
            {!!  $campaigns->appends(Request::all())->links() !!}
        </div>
        @else
        <h4 class="text-center">Không có dữ liệu</h4> 
        @endif
    </div>
</div>
<div class="modal fade" id="add-campaign-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 3%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase">Thêm chiến dịch quảng cáo</h4>
            </div>
            {!! Form::open(['action' => 'CpsCampaignController@doAddCampaign', 'method' => 'POST', 'id'=> 'add-campaign-form']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Tên chiến dịch <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-name" />
                </div>
                <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <div class="input-group">
                        <div class="icheck-inline">
                            <label class="control-label" role="button">
                                <input checked type="radio" name="rd-status" class="icheck" value="1"
                                       data-radio="iradio_minimal-green"/>
                                <span class="font-green-jungle">Bật</span>
                            </label>
                            <label class="control-label" role="button">
                                <input  type="radio" name="rd-status" class="icheck" value="0"
                                        data-radio="iradio_minimal-green"/>
                                <span class="font-yellow-lemon">Chờ duyệt</span>
                            </label>
                            <label class="control-label" role="button">
                                <input type="radio" name="rd-status" class="icheck" value="-1"
                                       data-radio="iradio_minimal-green"/>
                                <span class="font-red-thunderbird">Tắt</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Nguồn traffic <span class="required"> * </span></label>
                    <select class="form-control" name="sl-source">
                        <option value="">Chọn nguồn traffic</option>
                        @foreach($traffic_sources as $traffic_source)
                        <option value="{{ $traffic_source->source_id }}">{!! $traffic_source->source_name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Tài khoản adwords <span class="required"> * </span></label>
                    <select class="form-control" name="sl-adwords-account" >
                        <option value="">Chọn tài khoản adwords</option>
                        @foreach($adwords_accounts as $adwords_account)
                        <option value="{{ $adwords_account->account_id }}">{!! $adwords_account->account_id . ' - ' . $adwords_account->account_name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Tài khoản publisher <span class="required"> * </span></label>
                    <select class="form-control" name="sl-publisher-account">
                        <option value="">Chọn tài khoản publisher</option>
                        @foreach($publisher_accounts as $publisher_account)
                        <option value="{{ $publisher_account->account_id }}">{!!  $publisher_account->network_name . ' - ' . $publisher_account->account_username !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">URL đích</label>
                    <input type="text" name="txt-url" class="form-control" />
                </div>
                <div class="form-group">
                    <label class="control-label">Domain tracking <span class="required"> * </span></label>
                    <select class="form-control" name="sl-domain">
                        <option value="go.gtrackings.com">go.gtrackings.com</option>
                        <option value="go.gclickprice.com">go.gclickprice.com</option>
                        <option value="go.shop-online.sale">go.shop-online.sale</option>
                        <option value="go.gotoweb.us">go.gotoweb.us</option>
                        <option value="go.trackingtop.com">go.trackingtop.com</option>
                        <option value="go.clicktoweb.net">go.clicktoweb.net</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn blue uppercase">Thêm mới</button>
                <button type="button" class="btn red-soft uppercase" data-dismiss="modal">Hủy bỏ</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<div class="modal fade" id="edit-campaign-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 3%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            {!! Form::open(['action' => 'CpsCampaignController@doEditCampaign', 'method' => 'POST', 'id'=> 'edit-campaign-form']) !!}
            <div class="modal-body">
                <input type="hidden" class="form-control" name="txt-id" />
                <div class="form-group">
                    <label class="control-label">Tên chiến dịch <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-name" />
                </div>
                <div class="form-group">
                    <label class="control-label">Trạng thái</label>
                    <div class="input-group">
                        <div class="icheck-inline">
                            <label class="control-label" role="button">
                                <input type="radio" name="rd-status" class="icheck" value="1"
                                       data-radio="iradio_minimal-green"/>
                                <span class="font-green-jungle">Bật</span>
                            </label>
                            <label class="control-label" role="button">
                                <input type="radio" name="rd-status" class="icheck" value="0"
                                       data-radio="iradio_minimal-green"/>
                                <span class="font-yellow-lemon">Chờ duyệt</span>
                            </label>
                            <label class="control-label" role="button">
                                <input type="radio" name="rd-status" class="icheck" value="-1"
                                       data-radio="iradio_minimal-green"/>
                                <span class="font-red-thunderbird">Tắt</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Nguồn traffic <span class="required"> * </span></label>
                    <select class="form-control" name="sl-source">
                        <option value="">Chọn nguồn traffic</option>
                        @foreach($traffic_sources as $traffic_source)
                        <option value="{{ $traffic_source->source_id }}">{!! $traffic_source->source_name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Tài khoản adwords <span class="required"> * </span></label>
                    <select class="form-control" name="sl-adwords-account" disabled>
                        <option value="">Chọn tài khoản adwords</option>
                        @foreach($adwords_accounts as $adwords_account)
                        <option value="{{ $adwords_account->account_id }}">{!! $adwords_account->account_id . ' - ' . $adwords_account->account_name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Tài khoản publisher <span class="required"> * </span></label>
                    <select class="form-control" name="sl-publisher-account">
                        <option value="">Chọn tài khoản publisher</option>
                        @foreach($publisher_accounts as $publisher_account)
                        <option value="{{ $publisher_account->account_id }}">{!!  $publisher_account->network_name . ' - ' . $publisher_account->account_username !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">URL đích</label>
                    <input type="text" name="txt-url" class="form-control" />
                </div>
                <div class="form-group">
                    <label class="control-label">Domain tracking <span class="required"> * </span></label>
                    <select class="form-control" name="sl-domain">
                        <option value="go.gtrackings.com">go.gtrackings.com</option>
                        <option value="go.gclickprice.com">go.gclickprice.com</option>
                        <option value="go.shop-online.sale">go.shop-online.sale</option>
                        <option value="go.gotoweb.us">go.gotoweb.us</option>
                        <option value="go.trackingtop.com">go.trackingtop.com</option>
                        <option value="go.clicktoweb.net">go.clicktoweb.net</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-update" class="btn blue uppercase">Lưu chỉnh sửa</button>
                <button type="button" class="btn red-soft uppercase" data-dismiss="modal">Hủy bỏ</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<div class="modal fade" id="link-tracking-modal" role="dialog">
    <div class="modal-dialog modal-lg" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <a role="button" id="cp-final" class="btn btn-xs green pull-right uppercase">
                        <i class="fa fa-copy font-11"></i> Copy
                    </a>
                    <label class="control-label">Link đích</label>
                    <input type="text" class="form-control" name="txt-final" />
                </div>
                <div class="form-group">
                    <label class="control-label">Domain tracking</label>
                    <select class="form-control" name="sl-domain">
                        <option value="go.gtrackings.com">go.gtrackings.com</option>
                        <option value="go.gclickprice.com">go.gclickprice.com</option>
                        <option value="go.shop-online.sale">go.shop-online.sale</option>
                        <option value="go.gotoweb.us">go.gotoweb.us</option>
                        <option value="go.trackingtop.com">go.trackingtop.com</option>
                        <option value="go.clicktoweb.net">go.clicktoweb.net</option>
                    </select>
                </div>
                <div class="form-group">
                    <a role="button" id="cp-tracking" class="btn btn-xs green pull-right uppercase">
                        <i class="fa fa-copy font-11"></i> Copy
                    </a>
                    <label class="control-label">Link tracking</label>
                    <textarea name="txt-tracking" class="form-control" rows="3"></textarea>
                </div>
                <div class="font-12" id="alerts"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn red-soft uppercase" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<div id="delete-modal" class="modal fade" tabindex="-1" data-keyboard="false">
    <div class="modal-dialog"  style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title uppercase">Xóa chiến dịch quảng cáo</h4>
            </div>
            {!! Form::open(['action' => 'CpsCampaignController@doDeleteCampaign', 'method' => 'POST', 'id' => 'delete-form']) !!}
            <div class="modal-body">
                <input type="hidden" name="txt-id" value="" />
                <div class="font-red-soft">Bạn có chắc chắn muốn xóa chiến dịch quảng cáo này?</div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn blue text-uppercase">Xác nhận</button>
                <button type="button" data-dismiss="modal" class="btn red-soft uppercase">Hủy bỏ</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div class="modal fade" id="cost-campaign-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            {!! Form::open(['action' => 'CpsCampaignController@doUpdateCostCampaign', 'method' => 'POST', 'id'=> 'cost-campaign-form']) !!}
            <div class="modal-body">
                <input type="hidden" class="form-control" name="txt-id" />
                <div class="form-group">
                    <label class="control-label">Ngày <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-date"/>
                </div>
                <div class="form-group">
                    <label class="control-label">Chi phí <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-cost" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-update" class="btn blue uppercase">Cập nhật</button>
                <button type="button" class="btn red-soft uppercase" data-dismiss="modal">Hủy bỏ</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection 
@section('script') 
{{ Html::script('assets/global/plugins/icheck/icheck.min.js') }} 
{{Html::script('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}
{{Html::script('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}
{{ Html::script('assets/global/plugins/select2/js/select2.full.min.js') }}
{{ Html::script('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}
{{ Html::script('assets/global/plugins/moment.min.js') }}
<script>
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
        $('#filter-form').find('select[name="merchant"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy website thương mại nào";
                }
            }
        });
        $('#filter-form').find('select[name="source"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy nguồn traffic nào";
                }
            }
        });
        $('#filter-form').find('select[name="network"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy ads network nào";
                }
            }
        });
        $('#filter-form').find('select[name="a_account"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy tài khoản adword nào";
                }
            }
        });
        $('#filter-form').find('select[name="p_account"]').select2({
            language: {
                noResults: function () {
                    return "Không tìm thấy tài khoản publisher nào";
                }
            }
        });
        $('#edit-campaign-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('.modal-title').html('<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu');
            $("#edit-campaign-form").trigger('reset');
            modal.find('select[name="sl-adwords-account"]').prop('disabled', true);
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
            modal.find('.modal-body').hide();
            modal.find('.modal-footer').hide();
        });
        $('#add-campaign-modal, #edit-campaign-modal').on('shown.bs.modal', function () {
            $(this).find('select[name="sl-domain"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy domain nào";
                    }
                }
            }).on('change', function () {

            });
            $(this).find('select[name="sl-source"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy nguồn traffic nào";
                    }
                }
            }).on('change', function () {
                var value = $.trim($(this).val());
                if (value !== "") {
                    var parent = $(this).closest('.form-group');
                    parent.removeClass('has-error');
                    parent.find('.help-block').remove();
                }
                var sl_adwords = $(this).closest('form').find('select[name="sl-adwords-account"]');
                if (value === "1") {
                    sl_adwords.prop('disabled', false);
                } else {
                    sl_adwords.val("").trigger('change');
                    sl_adwords.prop('disabled', true);
                }
            });
            $(this).find('select[name="sl-adwords-account"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy tài khoản adwords nào";
                    }
                }
            }).on('change', function () {
                var value = $.trim($(this).val());
                if (value !== "") {
                    var parent = $(this).closest('.form-group');
                    parent.removeClass('has-error');
                    parent.find('.help-block').remove();
                }
            });
            $(this).find('select[name="sl-publisher-account"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy tài khoản publisher nào";
                    }
                }
            }).on('change', function () {
                var value = $.trim($(this).val());
                if (value !== "") {
                    var parent = $(this).closest('.form-group');
                    parent.removeClass('has-error');
                    parent.find('.help-block').remove();
                }
            });
        });
        $('#add-campaign-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            $("#add-campaign-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
        });
        $('.btn-loadcampaign').click(function () {
            var campaign_id = $(this).data('id');
            var modal = $('#edit-campaign-modal');
            modal.modal('show');
            $.ajax({
                url: "{{URL::action('CpsCampaignController@loadCampaign')}}",
                type: "GET",
                data: {
                    campaign_id: campaign_id
                },
                dataType: "text",
                timeout: 30000,
                error: function (jqXHR, textStatus, errorThrow) {
                    modal.modal('hide');
                    toastr['error']('Lỗi trong quá trình xử lý dữ liệu');
                },
                success: function (data) {
                    var json_data = $.parseJSON(data);
                    if (json_data.status_code === 200) {
                        modal.find('.modal-title').text(json_data.data.campaign_name);
                        modal.find('input[name="txt-id"]').val(json_data.data.campaign_id);
                        modal.find('input[name="txt-name"]').val(json_data.data.campaign_name);
                        modal.find('input[name="rd-status"][value="' + json_data.data.campaign_status + '"]').iCheck('check');
                        modal.find('select[name="sl-source"]').val(json_data.data.campaign_traffic_source_id);
                        console.log(json_data.data);
                        if (json_data.data.campaign_traffic_source_id === 1) {
                            modal.find('select[name="sl-adwords-account"]').prop('disabled', false);
                            modal.find('select[name="sl-adwords-account"]').val(json_data.data.campaign_adwords_account).trigger('change');
                        } else {
                            modal.find('select[name="sl-adwords-account"]').prop('disabled', true);
                        }
                        modal.find('input[name="txt-url"]').val(json_data.data.campaign_url);
                        modal.find('select[name="sl-domain"]').val(json_data.data.campaign_domain_tracking);
                        modal.find('select[name="sl-publisher-account"]').val(json_data.data.campaign_publisher_account_id);
                        modal.find('.modal-body').show();
                        modal.find('.modal-footer').show();
                    } else {
                        modal.modal('hide');
                        toastr['error'](json_data.message);
                    }
                }
            });
        });
        $('.btn-delete').click(function () {
            var id = $.trim($(this).data('id'));
            if (id !== "") {
                $('#delete-modal').find('input[name="txt-id"]').val(id);
                $('#delete-modal').modal('show');
            }
        });
        $('.btn-cost').click(function () {
            var campaign_id = $(this).data('id');
            var modal = $('#cost-campaign-modal');
            modal.modal('show');
            modal.find('input[name="txt-id"]').val(campaign_id);
            modal.find('input[name="txt-date"]').datepicker('setDate', moment().format('DD/MM/YYYY'));
        });
        $('#cost-campaign-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('.modal-title').html('<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu');
            $("#cost-campaign-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
            modal.find('.modal-body').hide();
            modal.find('.modal-footer').hide();
        });
        $('#cost-campaign-modal').find('input[name="txt-date"]').datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true,
            format: "dd/mm/yyyy",
            todayHighlight: true,
            endDate: moment().format('DD/MM/YYYY')
        }).on('change', function () {
            var modal = $('#cost-campaign-modal');
            var campaign_id = modal.find('input[name="txt-id"]').val();
            $.ajax({
                url: "{{URL::action('CpsCampaignController@loadCostCampaign')}}",
                type: "GET",
                data: {
                    campaign_id: campaign_id,
                    date: $(this).val().replace(/^(\d{2})\/(\d{2})\/.*?(\d{2})$/, "$3$2$1")
                },
                dataType: "text",
                timeout: 30000,
                error: function (jqXHR, textStatus, errorThrow) {
                    modal.modal('hide');
                    toastr['error']('Lỗi trong quá trình xử lý dữ liệu');
                },
                success: function (data) {
                    console.log(data);
                    var json_data = $.parseJSON(data);
                    if (json_data.status_code === 200) {
                        modal.find('.modal-title').text(json_data.data.campaign_name);
                        modal.find('input[name="txt-cost"]').val(json_data.data.cost);
                        modal.find('.modal-body').show();
                        modal.find('.modal-footer').show();
                    } else {
                        modal.modal('hide');
                        toastr['error'](json_data.message);
                    }
                }
            });
        });
        $('#delete-modal').on('hidden.bs.modal', function () {
            $(this).find('#delete-form').trigger('reset');
        });
        $('.btn-getlink').click(function () {
            var campaign_id = $(this).data('id');
            var modal = $('#link-tracking-modal');
            modal.modal('show');
            $.ajax({
                url: "{{URL::action('CpsCampaignController@linkTracking')}}",
                type: "GET",
                data: {
                    campaign_id: campaign_id
                },
                dataType: "text",
                timeout: 30000,
                error: function (jqXHR, textStatus, errorThrow) {
                    modal.modal('hide');
                    toastr['error']('Lỗi trong quá trình xử lý dữ liệu');
                },
                success: function (data) {
                    var json_data = $.parseJSON(data);
                    if (json_data.status_code === 200) {
                        modal.find('.modal-title').text(json_data.data.campaign_name);
                        modal.find('input[name="txt-final"]').val(json_data.data.final);
                        modal.find('textarea[name="txt-tracking"]').val(json_data.data.tracking);
                        modal.find('select[name="sl-domain"]').val(json_data.data.domain_tracking);
                        modal.find('.modal-body').show();
                        modal.find('.modal-footer').show();
                    } else {
                        modal.modal('hide');
                        toastr['error'](json_data.message);
                    }
                }
            });
        });
        $('#cp-final').click(function () {
            var temp = $("<input>");
            $("body").append(temp);
            temp.val($('#link-tracking-modal').find('input[name="txt-final"]').val()).select();
            document.execCommand("copy");
            App.alert({
                container: "#alerts",
                place: "append",
                type: "success",
                message: "Đã sao chép link đích!",
                close: true,
                reset: true,
                focus: true,
                closeInSeconds: 3
            });
            temp.remove();
        });
        $('#cp-tracking').click(function () {
            var temp = $("<input>");
            $("body").append(temp);
            temp.val($('#link-tracking-modal').find('textarea[name="txt-tracking"]').val()).select();
            document.execCommand("copy");
            App.alert({
                container: "#alerts",
                place: "append",
                type: "success",
                message: "Đã sao chép link tracking!",
                close: true,
                reset: true,
                focus: true,
                closeInSeconds: 3
            });
            temp.remove();
        });
        $('#link-tracking-modal').on('shown.bs.modal', function () {
            var modal = $(this);
            $(this).find('select[name="sl-domain"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy domain nào";
                    }
                }
            }).on('change', function () {
                var domain = $.trim($(this).val());
                var tracking = modal.find('textarea[name="txt-tracking"]').val();
                tracking = tracking.replace(/^http(s?):\/\/.*?\//, 'http://' + domain + '/');
                modal.find('textarea[name="txt-tracking"]').val(tracking);
            });
            /* $(this).find('input[name="txt-final"]').focusout(function () {
             var url = $.trim($(this).val());
             if (url !== "") {
             var link = modal.find('textarea[name="txt-tracking"]').val();
             link = link.replace(/\?url=.*?(\&)?/, '?url=' + encodeURIComponent(url) + '$1');
             modal.find('textarea[name="txt-tracking"]').val(link);
             }
             }); */
        });
        $('#add-campaign-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-name': {
                    required: true
                },
                'sl-source': {
                    required: true,
                    number: true
                },
                'txt-url': {
                    url: true
                },
                'sl-adwords-account': {
                    required: true,
                    number: true
                },
                'sl-publisher-account': {
                    required: true,
                    number: true
                },
                'sl-domain': {
                    required: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên chiến dịch không được để trống"
                },
                'sl-source': {
                    required: "Nguồn traffic không hợp lệ",
                    number: "Nguồn traffic không hợp lệ"
                },
                'txt-url': {
                    url: "URL đích không hợp lệ"
                },
                'sl-adwords-account': {
                    required: "Tài khoản adwords không hợp lệ",
                    number: "Tài khoản adwords không hợp lệ"
                },
                'sl-publisher-account': {
                    required: "Tài khoản publisher không hợp lệ",
                    number: "Tài khoản publisher không hợp lệ"
                },
                'sl-domain': {
                    required: "Domain tracking không được để trống"
                }
            },
            invalidHandler: function (event, validator) {
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                element.closest('.form-group').append(error);
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $('#edit-campaign-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-name': {
                    required: true
                },
                'sl-source': {
                    required: true,
                    number: true
                },
                'txt-url': {
                    url: true
                },
                'sl-adwords-account': {
                    required: true,
                    number: true
                },
                'sl-publisher-account': {
                    required: true,
                    number: true
                },
                'sl-domain': {
                    required: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên chiến dịch không được để trống"
                },
                'sl-source': {
                    required: "Đối tác quảng cáo không hợp lệ",
                    number: "Đối tác quảng cáo không hợp lệ"
                },
                'txt-url': {
                    url: "URL đích không hợp lệ"
                },
                'sl-adwords-account': {
                    required: "Tài khoản adwords không hợp lệ",
                    number: "Tài khoản adwords không hợp lệ"
                },
                'sl-publisher-account': {
                    required: "Tài khoản publisher không hợp lệ",
                    number: "Tài khoản publisher không hợp lệ"
                },
                'sl-domain': {
                    required: "Domain tracking không được để trống"
                }
            },
            invalidHandler: function (event, validator) {
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                element.closest('.form-group').append(error);
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $('#cost-campaign-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-date': {
                    required: true,
                    pattern: /^(\d{2})\/(\d{2})\/(\d{4})$/
                },
                'txt-cost': {
                    required: true,
                    number: true,
                    min: 0
                }
            },
            messages: {
                'txt-date': {
                    required: "Ngày không được để trống hợp lệ",
                    pattern: "Ngày không hợp lệ"
                },
                'txt-cost': {
                    required: "Chi phí không được để trống",
                    number: "Chi phí không hợp lệ",
                    min: "Chi phí không hợp lệ"
                }
            },
            invalidHandler: function (event, validator) {
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            errorPlacement: function (error, element) {
                element.closest('.form-group').append(error);
            },
            submitHandler: function (form) {
                var form_data = new FormData(form);
                $.ajax({
                    url: "{{ URL::action('CpsCampaignController@doUpdateCostCampaign') }}",
                    type: "POST",
                    data: form_data,
                    dataType: "text",
                    timeout: 30000,
                    contentType: false,
                    processData: false,
                    error: function (jqXHR, textStatus, errorThrow) {
                        toastr['error']('Lỗi trong quá trình xử lý dữ liệu');
                    },
                    success: function (data) {
                        var json_data = $.parseJSON(data);
                        if (json_data.status_code === 200) {
                            toastr['success'](json_data.message);
                        } else {
                            toastr['error'](json_data.message);
                        }
                    }
                });
                return false;
            }
        });
    });
</script>
@endsection