@extends('layout') 
@section('style')
{{Html::style('assets/global/plugins/icheck/skins/all.css')}} 
@endsection 
@section('pagecontent')
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase font-dark">Tất cả nguồn traffic</span>
        </div>
        <div class="actions">
            <button type="button" class="btn blue btn-lg uppercase" data-toggle="modal" data-target="#add-source-modal">Thêm mới</button>
        </div>
    </div>
    <div class="portlet-body">
        @if(count($sources) > 0)
        <div class="table-scrollable table-scrollable-borderless">
            <table class="table table-hover table-light">
                <thead>
                    <tr class="uppercase">
                        <th style="width: 70px;">#</th>
                        <th>Tên nguồn traffic</th>
                        <th style="width: 70px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sources as $source)
                    <tr>
                        <td>{!! $source->source_id !!}</td>
                        <td>{!! $source->source_name !!}</td>
                        <td>
                            <button type="button" data-id="{{ $source->source_id }}" class="btn btn-outline green btn-xs btn-loadsource">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" data-id="{{ $source->source_id }}" class="btn btn-outline btn-xs red-soft m-r-0 btn-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-right">
            {!!  $sources->appends(Request::all())->links() !!}
        </div>
        @else
        <h4 class="text-center">Không có dữ liệu</h4> 
        @endif
    </div>
</div>
<div class="modal fade" id="add-source-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase">Thêm nguồn traffic</h4>
            </div>
            {!! Form::open(['action' => 'TrafficSourceController@doAddSource', 'method' => 'POST', 'id'=> 'add-source-form']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Tên nguồn traffic <span class="required"> * </span></label>
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
<div class="modal fade" id="edit-source-modal" role="dialog">
    <div class="modal-dialog" style="margin-top: 5%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title uppercase"><i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu</h4>
            </div>
            {!! Form::open(['action' => 'TrafficSourceController@doEditSource', 'method' => 'POST', 'id'=> 'edit-source-form']) !!}
            <div class="modal-body">
                <input type="hidden" class="form-control" name="txt-id" />
                <div class="form-group">
                    <label class="control-label">Tên nguồn traffic <span class="required"> * </span></label>
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
                <h4 class="modal-title uppercase">Xóa nguồn traffic</h4>
            </div>
            {!! Form::open(['action' => 'TrafficSourceController@doDeleteSource', 'method' => 'POST', 'id' => 'delete-form']) !!}
            <div class="modal-body">
                <input type="hidden" name="txt-id" value="" />
                <div class="font-red-soft">Bạn có chắc chắn muốn xóa nguồn traffic này?</div>
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
        $('#edit-source-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('.modal-title').html('<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý dữ liệu');
            $("#edit-source-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
            modal.find('.modal-body').hide();
            modal.find('.modal-footer').hide();
        });
        $('#add-source-modal').on('hidden.bs.modal', function () {
            var modal = $(this);
            $("#add-source-form").trigger('reset');
            modal.find('.form-group').removeClass('has-error');
            modal.find('.form-group').find('.help-block').remove();
        });
        $('.btn-loadsource').click(function () {
            var source_id = $(this).data('id');
            var modal = $('#edit-source-modal');
            modal.modal('show');
            $.ajax({
                url: "{{URL::action('TrafficSourceController@loadSource')}}",
                type: "GET",
                data: {
                    source_id: source_id
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
                        modal.find('.modal-title').text("Đối tác: " + json_data.data.source_name);
                        modal.find('input[name="txt-id"]').val(json_data.data.source_id);
                        modal.find('input[name="txt-name"]').val(json_data.data.source_name);
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
        $('#add-source-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-name': {
                    required: true
                }
            },
            messages: {
                'txt-name': {
                    required: "Tên nguồn traffic không được để trống"
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
        $('#edit-source-form').validate({
            errorElement: 'span',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                'txt-domain': {
                    required: true
                }
            },
            messages: {
                'txt-domain': {
                    required: "Tên nguồn traffic không được để trống"
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