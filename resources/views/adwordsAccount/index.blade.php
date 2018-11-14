@extends('layout') 
@section('style')
{{Html::style('assets/global/plugins/icheck/skins/all.css')}} 
@endsection 
@section('pagecontent')
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Tất cả tài khoản adwords</span>
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
                        <th style="width: 220px;">ID tài khoản</th>
                        <th>Tên tài khoản</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td>{!! $account->account_id !!}</td>
                        <td>{!! $account->account_name !!}</td>
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
                <h4 class="modal-title uppercase">Thêm tài khoản adwords</h4>
            </div>
            {!! Form::open(['action' => 'AdwordsAccountController@doAddAccount', 'method' => 'POST', 'id'=> 'add-account-form']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">ID tài khoản <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-adwords-id" />
                </div>
                <div class="form-group">
                    <label class="control-label">Tên tài khoản <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-name" />
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
            {!! Form::open(['action' => 'AdwordsAccountController@doEditAccount', 'method' => 'POST', 'id'=> 'edit-account-form']) !!}
            <div class="modal-body">
                <input type="hidden" class="form-control" name="txt-id" />
                <div class="form-group">
                    <label class="control-label">ID tài khoản <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-adwords-id" />
                </div>
                <div class="form-group">
                    <label class="control-label">Tên tài khoản <span class="required"> * </span></label>
                    <input type="text" class="form-control" name="txt-name" />
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
                <h4 class="modal-title uppercase">Xóa tài khoản adwords</h4>
            </div>
            {!! Form::open(['action' => 'AdwordsAccountController@doDeleteAccount', 'method' => 'POST', 'id' => 'delete-form']) !!}
            <div class="modal-body">
                <input type="hidden" name="txt-id" value="" />
                <div class="font-red-soft">Bạn có chắc chắn muốn xóa tài khoản adwords này?</div>
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
                url: "{{URL::action('AdwordsAccountController@loadAccount')}}",
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
                        modal.find('.modal-title').text("Tài khoản: " + json_data.data.account_name);
                        modal.find('input[name="txt-id"]').val(json_data.data.account_id);
                        modal.find('input[name="txt-name"]').val(json_data.data.account_name);
                        modal.find('input[name="txt-adwords-id"]').val(json_data.data.account_id);
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
                'txt-name': {
                    required: true
                },
                'txt-adwords-id': {
                    required: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên tài khoản không được để trống"
                },
                'txt-adwords-id': {
                    required: "ID tài khoản không được để trống"
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
                'txt-name': {
                    required: true
                },
                'txt-adwords-id': {
                    required: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên tài khoản không được để trống"
                },
                'txt-adwords-id': {
                    required: "ID tài khoản không được để trống"
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