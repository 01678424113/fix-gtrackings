@extends('layout') 
@section('style')
{{Html::style('assets/global/plugins/icheck/skins/all.css')}} 
{{Html::style('assets/global/plugins/select2/css/select2.min.css')}}
{{Html::style('assets/global/plugins/select2/css/select2-bootstrap.min.css')}}
@endsection 
@section('pagecontent')
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Tất cả mạng quảng cáo</span>
        </div>
        <div class="actions">
            <button type="button" class="btn blue uppercase" data-toggle="modal" data-target="#add-network-modal">Thêm mới</button>
        </div>
    </div>
    <div class="portlet-body">
        @if(count($networks) > 0)
        <div class="table-scrollable table-scrollable-borderless">
            <table class="table table-hover table-light">
                <thead>
                    <tr class="uppercase">
                        <th>Tên</th>
                        <th>Loại</th>
                        <th>Link Affiliate</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($networks as $network)
                    <tr>
                        <td>
                            <a href="http://{{ $network->network_domain }}" target="_blank">{!! $network->network_name !!}</a>
                        </td>
                        <td>
                            @if($network->network_type == 1)
                            <span>CPI</span>
                            @elseif($network->network_type == 2)
                            <span>CPS</span>
                            @elseif($network->network_type == 3)
                            <span>CPO</span>
                            @endif
                        </td>
                        <td>{!! $network->network_link_affiliate !!}</td>
                        <td>
                            <button type="button" data-id="{{ $network->network_id }}" class="btn btn-outline green btn-xs btn-loadnetwork">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" data-id="{{ $network->network_id }}" class="btn btn-outline btn-xs red-soft m-r-0 btn-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right">
            {!!  $networks->appends(Request::all())->links() !!}
        </div>
        @else
        <h4 class="text-center">Không có dữ liệu</h4> 
        @endif
    </div>
</div>
<div class="modal fade" id="add-network-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase">Thêm mạng quảng cáo</h4>
            </div>
            {!! Form::open(['action' => 'AdsNetworkController@doAddNetwork', 'method' => 'POST', 'id'=> 'add-network-form']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Tên <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-name" />
                </div>
                <div class="form-group">
                    <label class="control-label">Website <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-domain" />
                </div>
                <div class="form-group">
                    <label class="control-label">Loại <span class="required"> * </span></label>
                    <select class="form-control" name="sl-type">
                        <option value="1">CPI</option>
                        <option value="2">CPS</option>
                        <option value="3">CPO</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Link Affiliate</label>
                    <textarea class="form-control" name="txt-affiliate-link" rows="3"></textarea>
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
<div class="modal fade" id="edit-network-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            {!! Form::open(['action' => 'AdsNetworkController@doEditNetwork', 'method' => 'POST', 'id'=> 'edit-network-form']) !!}
            <div class="modal-body">
                <input type="hidden" class="form-control" name="txt-id" />
                <div class="form-group">
                    <label class="control-label">Tên <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-name" />
                </div>
                <div class="form-group">
                    <label class="control-label">Website <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-domain" />
                </div>
                <div class="form-group">
                    <label class="control-label">Loại <span class="required"> * </span></label>
                    <select class="form-control" name="sl-type">
                        <option value="1">CPI</option>
                        <option value="2">CPS</option>
                        <option value="3">CPO</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Link Affiliate</label>
                    <textarea class="form-control" name="txt-affiliate-link" rows="3"></textarea>
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
<div id="delete-modal" class="modal fade" tabindex="-1" data-keyboard="false">
    <div class="modal-dialog"  style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title uppercase">Xóa mạng quảng cáo</h4>
            </div>
            {!! Form::open(['action' => 'AdsNetworkController@doDeleteNetwork', 'method' => 'POST', 'id' => 'delete-form']) !!}
            <div class="modal-body">
                <input type="hidden" name="txt-id" value="" />
                <div class="font-red-soft">Bạn có chắc chắn muốn xóa mạng quảng cáo này?</div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn blue text-uppercase">Xác nhận</button>
                <button type="button" data-dismiss="modal" class="btn red-soft uppercase">Hủy bỏ</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection 
@section('script') 
{{ Html::script('assets/global/plugins/icheck/icheck.min.js') }} 
{{Html::script('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}
{{Html::script('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}
{{Html::script('assets/global/plugins/select2/js/select2.full.min.js')}}
<script>
    $(document).ready(function () {
        $('#edit-network-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('.modal-title').html('<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu');
            $("#edit-network-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
            modal.find('.modal-body').hide();
            modal.find('.modal-footer').hide();
        });
        $('#add-network-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            $("#add-network-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
        });
        $('#add-network-modal, #edit-network-modal').on('shown.bs.modal', function () {
            $(this).find('select[name="sl-type"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy loại mạng quảng cáo nào";
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
        $('.btn-loadnetwork').click(function () {
            var network_id = $(this).data('id');
            var modal = $('#edit-network-modal');
            modal.modal('show');
            $.ajax({
                url: "{{URL::action('AdsNetworkController@loadNetwork')}}",
                type: "GET",
                data: {
                    network_id: network_id
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
                        modal.find('.modal-title').text(json_data.data.network_name);
                        modal.find('input[name="txt-id"]').val(json_data.data.network_id);
                        modal.find('input[name="txt-name"]').val(json_data.data.network_name);
                        modal.find('input[name="txt-domain"]').val(json_data.data.network_domain);
                        modal.find('select[name="sl-type"]').val(json_data.data.network_type);
                        modal.find('textarea[name="txt-affiliate-link"]').val(json_data.data.network_link_affiliate);
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
        $('#delete-modal').on('hidden.bs.modal', function () {
            $(this).find('#delete-form').trigger('reset');
        });
        $('#add-network-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-name': {
                    required: true
                },
                'txt-domain': {
                    required: true
                },
                'sl-type': {
                    required: true,
                    number: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên mạng quảng cáo không được để trống"
                },
                'txt-domain': {
                    required: "Website mạng quảng cáo không được để trống"
                },
                'sl-type': {
                    required: "Loại mạng quảng cáo không hợp lệ",
                    number: "Loại mạng quảng cáo không hợp lệ"
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
        $('#edit-network-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-name': {
                    required: true
                },
                'txt-domain': {
                    required: true
                },
                'sl-type': {
                    required: true,
                    number: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên mạng quảng cáo không được để trống"
                },
                'txt-domain': {
                    required: "Website mạng quảng cáo không được để trống"
                },
                'sl-type': {
                    required: "Loại mạng quảng cáo không hợp lệ",
                    number: "Loại mạng quảng cáo không hợp lệ"
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
    });
</script>
@endsection