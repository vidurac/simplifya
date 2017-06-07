<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 2:05 PM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <input type="hidden" name="type_of_sub" id="type_of_sub" value="{{ $subscription_type }}" />
                                <div><a href="/configuration/subscription/{{ $subscription_type }}/new" class="btn btn-info">Create New</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed config-table" id="subscription-table">
                                        <thead>
                                        <tr>
                                            <th>Subscription Plan</th>
                                            <th>Price</th>
                                            <th>Statues</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="subscription-manage-model" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Subscription Edit</h4>
                </div>
                <form name="subscription-edit-form" id="subscription-edit-form" method="post">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12" >
                                <label class="col-sm-4 control-label">Subscription Name* </label>
                                <div class="col-sm-8">
                                    <input type="text" name="subscription_name" id="subscription_name" class="form-control" value="">
                                    <span id="err-subscription_name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12" >
                                <label class="col-sm-4 control-label">Validity Period* </label>
                                <div class="col-sm-8">
                                    <input type="text" name="validity_period_temp" id="validity_period_temp" class="form-control" readonly>

                                    {{--<select name="validity_period" id="validity_period_temp" class="form-control" disabled="disabled">--}}
                                        {{--<option value="">Select</option>--}}
                                        {{--<option value="1">Monthly</option>--}}
                                        {{--<option value="2">Quarterly</option>--}}
                                        {{--<option value="3">Half Yearly</option>--}}
                                        {{--<option value="4">Yearly</option>--}}
                                    {{--</select>--}}
                                    <input type="hidden" name="validity_period" id="validity_period" class="form-control"/>
                                    <input type="hidden" name="company_type" id="company_type" class="form-control"/>

                                    {{--<span id="err-validity_period"></span>--}}
                                </div>
                            </div>
                        </div>


                        {{--<div class="row">--}}
                            {{--<div class="form-group col-lg-12">--}}
                                {{--<label class="col-sm-4 control-label">Company Type* </label>--}}
                                {{--<div class="col-sm-8">--}}
                                    {{--<select name="company_type" id="company_type" class="form-control" disabled>--}}
                                        {{--<option value="">Select</option>--}}
                                        {{--<option value="2">Marijuana Company</option>--}}
                                        {{--<option value="3">Compliance Company</option>--}}
                                        {{--<option value="4">Government Entity</option>--}}
                                    {{--</select>--}}
                                    {{--<span id="err-company_type"></span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Price* </label>
                                <div class="col-sm-8">
                                    <input type="text" name="price" id="price" class="form-control" value="">
                                    <span id="err-price"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="subscription_description" id="subscription_description" class="form-control"></textarea>
                                    <span id="err-subscription_description"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <input type="hidden" id="subscription_id" name="subscription_id" value="">
                                    <a class="btn btn-default close-update-subscription-form">Close</a>
                                    <a class="btn w-xs btn-primary" id="update-subscription-btn">Update</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {!! Html::script('js/configuration/subscription.js') !!}
@stop
