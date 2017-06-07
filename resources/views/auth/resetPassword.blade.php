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


<div class="login-container"  id="resetPasswordStatus">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>PASSWORD RECOVERY</h3>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                    <p>
                        Enter your registered email address and instructions to reset your password will be sent to you.
                    </p>
                    <form id="resetPasswordForm">
                        <div class="form-group">
                            <label class="control-label" for="username">Email</label>
                            <input type="text" placeholder="example@gmail.com" title="Please enter a valid email address" required="" value="" name="resetPasswordEmail" id="resetPasswordEmail" class="form-control">
                            <span class="help-block small">Your registered email address</span>
                        </div>

                        <button class="btn btn-success btn-block" id="resetPasswordId">Reset password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

{!! Html::script('/js/password/resetPassword.js') !!}

@stop