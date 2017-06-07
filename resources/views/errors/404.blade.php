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


        <div class="color-line"></div>

        <div class="back-link">
            <a href="/dashboard" class="btn btn-primary">Back to Dashboard</a>
        </div>
        <div class="error-container">
            <i class="pe-7s-way text-success big-icon"></i>
            <h1>404</h1>
            <strong>Page Not Found</strong>
            <p>
                Sorry, but the page you are looking for has not been found.

            </p>
            <a href="/dashboard" class="btn btn-xs btn-success">Go back to dashboard</a>
        </div>


    <!--[if lt IE 7]>
    <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

@stop