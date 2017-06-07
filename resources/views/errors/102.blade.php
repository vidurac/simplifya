<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/16/2016
 * Time: 8:18 PM
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
    <h1>Sorry!</h1>
    <strong>You are unable to log into Simplifya yet.</strong>
    <p>
        A Simplifya admin must verify and approve your registration. Upon approval, your account will be accessible. Someone from Simplifya may reach out to you at the contact information you provided.
    </p>
    <a href="{{ URL('http://www.simplifya.com') }}" class="btn btn-lg btn-success">Home</a>
</div>

@stop