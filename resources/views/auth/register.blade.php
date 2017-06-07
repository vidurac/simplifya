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
                    <h3>PLEASE REGISTER TO SIMPLIFYA</h3>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <form id="registerForm" role="form" method="POST" action="{{ url('/user/register') }}">
                            <div class="form-group">
                                <label class="control-label" for="username">Username</label>
                                <input type="text" placeholder="example@gmail.com" title="Please enter you username" required="" value="{{$email}}" name="username" id="username" class="form-control" readonly>
                                <input type="hidden" name="user_id" value="{{$id}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Please enter your password" placeholder="******" required="" value="" name="password" id="password" class="form-control">

                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Confirm Password</label>
                                <input type="password" title="Please enter your confirm password" placeholder="******" required="" value="" name="conf_password" id="conf_password" class="form-control">
                            </div>
                            <input type="submit" value="Register"  class="btn btn-success btn-block">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('/js/Users/users.js') !!}
@stop
