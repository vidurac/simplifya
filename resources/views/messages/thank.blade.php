<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/6/2016
 * Time: 11:39 AM
 */
        ?>
@extends('layout.default')

@section('content')

    <!-- Simple splash screen-->
    <div class="splash">
        <div class="color-line"></div>
        <div class="splash-title">
            <h1></h1>
            <p></p>
            <div class="spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
    </div>
    <!--[if lt IE 7]>
    <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <div class="error-container">
        <i class="pe-7s-like2 text-success big-icon"></i>
        <h1>Thanks!</h1>

        @if(Session::has('entity_type'))
            @if(Session::get('entity_type') != 2)
                <strong>You successfully registered your business!</strong>
                <p id="msg">{{Session('reg_message')}}</p>
                <p>{!!Session('reg_button')!!}</p>
            @else
                <strong>Log in to start auditing your business!</strong>
                <br><br>
                <p>{!!Session('reg_button')!!}</p>
            @endif
        @endif
        {{--<a href="http://simplifya.com/" class="btn btn-lg btn-success">HOME</a>--}}
    </div>

@stop

