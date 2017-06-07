<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 2:20 PM
 */
?>

@extends('layout.dashbord')

@section('content')
<div class="content animate-panel">
    <div class="row">
        <div class="col-md-12">
            <div class="hpanel">
                <div class="panel-body">
                    <form action="{{ url('/configuration/subscription/') }}{{ '/'.$subscription_type.'/store' }}" id="newSubscriptionForm" method="post">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Subscription Name*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="subscription_name" id="subscription_name" class="form-control" value=""/>
                                    <span id="err-subscription_name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Validity Period</label>
                                <div class="col-sm-9">
                                    <select name="validity_period" id="validity_period" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1" selected>Monthly</option>
                                        <option value="3">3 Months</option>
                                        <option value="6">6 Months</option>
                                        <option value="12">12 Months</option>
                                    </select>
                                    {{--<input type="hidden" name="validity_period" id="validity_period" class="form-control" value="1"/>--}}
                                    <span id="err-validity_period"></span>
                                </div>
                            </div>
                        </div>

                        @if($subscription_type!="mjb")
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Company Type</label>
                                    <div class="col-sm-9">
                                        <select name="company_type" id="company_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="{{ Config::get('simplifya.COMPLIANCE_COMPANY_TYPE') }}">Compliance Company</option>
                                            <option value="{{ Config::get('simplifya.GOVERNMENT_ENTITY_TYPE') }}">Government Entity</option>
                                        </select>
                                        <input type="hidden" name="sub_type" class="form-control" value="cc_ge"/>
                                        <span id="err-company_type"></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Company Type</label>
                                    <div class="col-sm-9">
                                        <select name="company_type" class="form-control" disabled>
                                            <option value="{{ Config::get('simplifya.MARIJUANA_COMPANY_TYPE') }}" selected>Marijuana Company</option>
                                        </select>
                                        <input type="hidden" name="company_type" value="{{ Config::get('simplifya.MARIJUANA_COMPANY_TYPE') }}"/>
                                        <input type="hidden" name="sub_type" class="form-control" value="mjb"/>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Price($)*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="price" id="price" class="form-control" value=""/>
                                    <span id="err-price"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-9">
                                    <textarea name="subscription_description" id="subscription_description" class="form-control"></textarea>
                                    <span id="err-subscription_description"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right">
                                    <a class="btn btn-success" id="submit_subscription">Save</a>
                                    <a href="/configuration/subscription/{{ $subscription_type }}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Html::script('js/configuration/subscription.js') !!}

@stop


