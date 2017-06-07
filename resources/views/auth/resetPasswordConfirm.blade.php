<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 7/7/2016
 * Time: 11:38 AM
 */?>
@extends('layout.default')

@section('content')
    <div class="login-container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    <h3>RESET PASSWORD</h3>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <form id="resetPasswordFromEmailForm" role="form" method="POST" action="{{ url('/resetPassword/newpassword') }}">
                            <div class="form-group">
                                <label class="control-label" for="username">Username</label>
                                <input type="text" placeholder="example@gmail.com" title="Please enter you username" required="" value="{{$email}}" name="email" id="email" class="form-control" readonly>
                                <input type="hidden" name="user_id" value="{{$user_id}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Please enter your password" placeholder="******" required="" value="" name="password" id="password" class="form-control">

                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Confirm Password</label>
                                <input type="password" title="Please enter your confirm password" placeholder="******" required="" value="" name="conf_password" id="conf_password" class="form-control">
                            </div>
                            <input type="submit" value="Reset Password"  class="btn btn-success btn-block" id="resetPasswordFromEmail">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('/js/password/resetPassword.js') !!}
@stop
