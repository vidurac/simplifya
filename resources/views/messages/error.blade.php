<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/6/2016
 * Time: 12:12 PM
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
    <i class="pe-7s-way text-danger big-icon"></i>
    <h1>We are Sorry!</h1>
    <strong>Something Went Wrong!</strong>
    <p>
        You have registered to the system. But, you're payment not successfully made. We apologize for the inconvenience.
    </p>
    <a href="{{ URL('/') }}" class="btn btn-lg btn-success">Login</a>
</div>

@stop
