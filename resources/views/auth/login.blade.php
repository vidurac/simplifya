<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/6/2016
 * Time: 11:24 AM
 */
?>

@extends('layout.default')

@section('content')

    @if (isset($message))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>{{$message}}</strong>
        </div>
    @endif

<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                {{--<img src="http://nexttestsite.com/simplifyaweb/wp-content/uploads/2016/06/simplifya_logo.png" alt="Simplifya">--}}
                <img src="https://app.simplifya.com/images/simplifya_logo-login.png" alt="Simplifya">
            </div>
            <div class="hpanel">
                <div class="panel-body">
                    <form id="loginForm" role="form" method="POST" action="{{ url('/auth/login') }}">
                        <div class="form-group">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <label class="control-label" for="username">Email Address</label>
                            <input type="text" placeholder="example@gmail.com" title="Please enter you username" value="" name="email" id="email" class="form-control">
                            @if ($errors->has('email'))<label class="error">{!!$errors->first('email')!!}</label>@endif
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="password">Password</label>
                            <input type="password" title="Please enter your password" placeholder="******" value="" name="password" id="password" class="form-control">
                            @if ($errors->has('password'))<label class="error">{!!$errors->first('password')!!}</label>@endif
                        </div>
                        <div class="checkbox">
                            {{--<input type="checkbox" class="i-checks" checked>--}}
                            {{--Remember login--}}
                        </div>
                        <input type="submit" value="Log In"  class="btn btn-success btn-block">
                        <a class="btn btn-default btn-block" href="/company/registration">Sign Up</a>
                    </form>

                    <br />
                    {{--<a href="/company/registration/1" type="submit"  style="color: #00b3ee; text-decoration: underline"> Sign Up </a>--}}
                    <a href="/resetPassword" class="pull-right" style="color: #00b3ee; text-decoration: underline"> Forgot password?</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{--<strong>HOMER</strong> - AngularJS Responsive WebApp <br/> 2015 Copyright Company Name--}}
        </div>
    </div>
</div>

{!! Html::script('js/login-validation.js') !!}
<script>
    // Toastr options
    toastr.options = {
        "debug": false,
        "newestOnTop": false,
        "positionClass": "toast-top-center",
        "closeButton": true,
        "toastClass": "animated fadeInDown",
    };

    var msg = '<?php if(Session::has('message')) { $has_error = true; echo Session::get('message');}else{ $has_error = false;}?>';
    var has_error = '<?php echo $has_error?>';
    if(has_error) {
        toastr.success(msg);
    }

</script>
@stop