<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/6/2016
 * Time: 11:24 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                @if(Auth::user()->master_user_group_id==2)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        You can request an audit from a 3rd party auditor by selecting from the list of compliance companies registered on Simplifya below, selecting the location to be audited, and entering the preferred date and time of the audit. Upon submitting, a representative from the compliance company will contact you about your appointment.
                    </div>
                @endif
                <div class="hpanel">
                    <div class="panel-body">

                        @if((Auth::user()->master_user_group_id==2) || (Auth::user()->master_user_group_id==3))
                            <ul class="nav nav-tabs">
                                <li class="active" id="req_active"><a href="#" id="mjb-request">Requests</a></li>
                                <li id="audit_active"><a href="#" id="3rd-party-audit">3rd party audit</a></li>
                            </ul>
                        <br>
                            @include('request.3rd-party-audit-table')
                        @else
                            @include('request.request-table')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('js/request/manage.js') !!}
@stop
