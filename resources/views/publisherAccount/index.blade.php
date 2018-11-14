@extends('layout') 
@section('style') 
{{ Html::style('assets/global/plugins/select2/css/select2.min.css') }}
{{ Html::style('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}
{{Html::style('assets/global/plugins/icheck/skins/all.css')}} 
@endsection 
@section('pagecontent')
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Tất cả tài khoản publisher</span>
        </div>
        <div class="actions">
            <button type="button" class="btn blue uppercase" data-toggle="modal" data-target="#add-account-modal">Thêm mới</button>
        </div>
    </div>
    <div class="portlet-body">
        @if(count($accounts) > 0)
        <div class="table-scrollable table-scrollable-borderless">
            <table class="table table-hover table-light">
                <thead>
                    <tr class="uppercase">
                        <th>Tên đăng nhập</th>
                        <th>Mật khẩu</th>
                        <th>Mạng quảng cáo</th>
                        <th>Affiliate token</th>
                        <th>Affiliate API token</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td>{!! $account->account_username !!}</td>
                        <td>{!! $account->account_password !!}</td>
                        <td>
                            <a href="http://{{ $account->network_domain }}" target="_blank">{!! $account->network_name !!}</a>
                        </td>
                        <td>{!! $account->account_affiliate_token !!}</td>
                        <td>{!! $account->account_affiliate_api_token !!}</td>
                        <td>
                            <button type="button" data-id="{{ $account->account_id }}" class="btn btn-outline green btn-xs btn-loadaccount">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" data-id="{{ $account->account_id }}" class="btn btn-outline btn-xs red-soft m-r-0 btn-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right">
            {!!  $accounts->appends(Request::all())->links() !!}
        </div>
        @else
        <h4 class="text-center">Không có dữ liệu</h4> 
        @endif
    </div>
</div>
<div class="modal fade" id="add-account-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase">Thêm tài khoản publisher</h4>
            </div>
            {!! Form::open(['action' => 'PublisherAccountController@doAddAccount', 'method' => 'POST', 'id'=> 'add-account-form']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Tên đăng nhập <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-username" />
                </div>
                <div class="form-group">
                    <label class="control-label">Mật khẩu</label>
                    <input type="text" class="form-control" name="txt-password" />
                </div>
                <div class="form-group">
                    <label class="control-label">Mạng quảng cáo <span class="required"> * </span></label>
                    <select class="form-control" name="sl-network">
                        @foreach($networks as $network)
                        <option value="{{ $network->network_id }}">{!! $network->network_name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Affiliate token</label>
                    <input type="text" class="form-control" name="txt-affiliate-token" />
                </div>
                <div class="form-group">
                    <label class="control-label">Affiliate API token</label>
                    <input type="text" class="form-control" name="txt-affiliate-api-token" />
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
<div class="modal fade" id="edit-account-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            {!! Form::open(['action' => 'PublisherAccountController@doEditAccount', 'method' => 'POST', 'id'=> 'edit-account-form']) !!}
            <div class="modal-body">
                <input type="hidden" class="form-control" name="txt-id" />
                <div class="form-group">
                    <label class="control-label">Tên đăng nhập <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-username" />
                </div>
                <div class="form-group">
                    <label class="control-label">Mật khẩu</label>
                    <input type="text" class="form-control" name="txt-password" />
                </div>
                <div class="form-group">
                    <label class="control-label">Mạng quảng cáo <span class="required"> * </span></label>
                    <select class="form-control" name="sl-network">
                        @foreach($networks as $network)
                        <option value="{{ $network->network_id }}">{!! $network->network_name !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Affiliate token</label>
                    <input type="text" class="form-control" name="txt-affiliate-token" />
                </div>
                <div class="form-group">
                    <label class="control-label">Affiliate API token</label>
                    <input type="text" class="form-control" name="txt-affiliate-api-token" />
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
                <h4 class="modal-title uppercase">Xóa tài khoản publisher</h4>
            </div>
            {!! Form::open(['action' => 'PublisherAccountController@doDeleteAccount', 'method' => 'POST', 'id' => 'delete-form']) !!}
            <div class="modal-body">
                <input type="hidden" name="txt-id" value="" />
                <div class="font-red-soft">Bạn có chắc chắn muốn xóa tài khoản publisher này?</div>
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
{{ Html::script('assets/global/plugins/select2/js/select2.full.min.js') }}
<script>
    $(document).ready(function () {
        $('#edit-account-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('.modal-title').html('<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu');
            $("#edit-account-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
            modal.find('.modal-body').hide();
            modal.find('.modal-footer').hide();
        });
        $('#add-account-modal, #edit-account-modal').on('shown.bs.modal', function () {
            $(this).find('select[name="sl-network"]').select2({
                language: {
                    noResults: function () {
                        return "Không tìm thấy mạng quảng cáo nào";
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
        $('#add-account-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            $("#add-account-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
        });
        $('.btn-loadaccount').click(function () {
            var account_id = $(this).data('id');
            var modal = $('#edit-account-modal');
            modal.modal('show');
            $.ajax({
                url: "{{URL::action('PublisherAccountController@loadAccount')}}",
                type: "GET",
                data: {
                    account_id: account_id
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
                        modal.find('.modal-title').text("Tài khoản offer: " + json_data.data.account_username);
                        modal.find('input[name="txt-id"]').val(json_data.data.account_id);
                        modal.find('input[name="txt-username"]').val(json_data.data.account_username);
                        modal.find('input[name="txt-password"]').val(json_data.data.account_password);
                        modal.find('select[name="sl-network"]').val(json_data.data.account_network_id);
                        modal.find('input[name="txt-affiliate-token"]').val(json_data.data.account_affiliate_token);
                        modal.find('input[name="txt-affiliate-api-token"]').val(json_data.data.account_affiliate_api_token);
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
        $('#add-account-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-username': {
                    required: true
                },
                'sl-network': {
                    required: true,
                    number: true
                }
            },
            messages: {
                'txt-username': {
                    required: "Tên đăng nhập không được để trống"
                },
                'sl-network': {
                    required: "Mạng quảng cáo không hợp lệ",
                    number: "Mạng quảng cáo không hợp lệ"
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
        $('#edit-account-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-username': {
                    required: true
                },
                'sl-network': {
                    required: true,
                    number: true
                }
            },
            messages: {
                'txt-username': {
                    required: "Tên đăng nhập không được để trống"
                },
                'sl-network': {
                    required: "Mạng quảng cáo không hợp lệ",
                    number: "Mạng quảng cáo không hợp lệ"
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