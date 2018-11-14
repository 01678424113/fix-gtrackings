<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{!! $title !!}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
        {{Html::style('http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all')}}
        {{Html::style('assets/global/plugins/font-awesome/css/font-awesome.min.css')}}
        {{Html::style('assets/global/plugins/simple-line-icons/simple-line-icons.min.css')}}
        {{Html::style('assets/global/plugins/bootstrap/css/bootstrap.min.css')}}
        {{Html::style('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}
        {{Html::style('assets/global/css/components.min.css')}}
        {{Html::style('assets/global/css/plugins.min.css')}}
        {{Html::style('assets/pages/css/login.css')}}
        {{Html::favicon(env('APP_FAVICON'))}}
    <body class="login">
        <div class="content" style="margin-top: 5%">
            {!! Form::open(['action' => 'AccessController@doLogin', 'method' => 'POST', 'class'=> 'login-form']) !!}
            <h3 class="form-title font-red-soft uppercase">{!! env('APP_NAME', '') !!}</h3>
            <div id="alerts"></div>
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-user font-blue-dark" style="margin-top: 14px"></i>
                    <input class="form-control" type="text" name="txt-username" placeholder="Tên đăng nhập"/> 
                </div>
            </div>
            <div class="form-group">
                <div class="input-icon">
                    <i class="fa fa-lock font-blue-dark" style="margin-top: 14px"></i>
                    <input class="form-control" type="password" name="txt-password" placeholder="Mật khẩu"/>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-lg blue uppercase btn-block">Đăng nhập</button>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="copyright"> 2017 © {!! env('APP_NAME', '') !!} </div>
        {{Html::script('assets/global/plugins/jquery.min.js')}}
        {{Html::script('assets/global/plugins/bootstrap/js/bootstrap.min.js')}}
        {{Html::script('assets/global/plugins/js.cookie.min.js')}}
        {{Html::script('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}
        {{Html::script('assets/global/plugins/jquery.blockui.min.js')}}
        {{Html::script('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}
        {{Html::script('assets/global/plugins/jquery-validation/js/jquery.validate.min.js')}}
        {{Html::script('assets/global/plugins/jquery-validation/js/additional-methods.min.js')}}
        {{Html::script('assets/global/scripts/app.min.js')}}
        <script type="text/javascript">
            $(document).ready(function () {
                $('.login-form').validate({
                    errorElement: 'span',
                    errorClass: 'help-block',
                    focusInvalid: false,
                    rules: {
                        'txt-username': {
                            required: true,
                            pattern: /^[a-z0-9_]+$/,
                            minlength: 3
                        },
                        'txt-password': {
                            required: true,
                            minlength: 3
                        }
                    },

                    messages: {
                        'txt-username': {
                            required: "Tên đăng nhập không được để trống",
                            pattern: "Tên đăng nhập không hợp lệ",
                            minlength: "Tên đăng nhập phải lớn hơn 3 ký tự"
                        },
                        'txt-password': {
                            required: "Mật khẩu không được để trống",
                            minlength: "Mật khẩu phải lớn hơn 3 ký tự"
                        }
                    },
                    invalidHandler: function (event, validator) {},
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    success: function (label) {
                        label.closest('.form-group').removeClass('has-error');
                        label.remove();
                    },
                    errorPlacement: function (error, element) {
                        error.insertAfter(element.closest('.input-icon'));
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                $('.login-form input').keypress(function (e) {
                    if (e.which === 13) {
                        if ($('.login-form').validate().form()) {
                            $('.login-form').submit();
                        }
                        return false;
                    }
                });
            });

        </script>
        @if(Session::has('error'))
        <script type="text/javascript">
            $(document).ready(function () {
                App.alert({
                    container: "#alerts",
                    place: "append",
                    type: "danger",
                    message: "{!! Session::get('error') !!}",
                    close: true,
                    reset: true,
                    focus: true,
                    closeInSeconds: 3
                });
            });
        </script>
        @elseif(Session::has('success'))
        <script type="text/javascript">
            $(document).ready(function () {
                App.alert({
                    container: "#alerts",
                    place: "append",
                    type: "success",
                    message: "{!! Session::get('success') !!}",
                    close: true,
                    reset: true,
                    focus: true,
                    closeInSeconds: 3
                });
            });
        </script>
        @elseif(Session::has('warning'))
        <script type="text/javascript">
            $(document).ready(function () {
                App.alert({
                    container: "#alerts",
                    place: "append",
                    type: "warning",
                    message: "{!! Session::get('warning') !!}",
                    close: true,
                    reset: true,
                    focus: true,
                    closeInSeconds: 3
                });
            });
        </script>
        @endif
    </body>
</html>