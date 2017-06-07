<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 11:33 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form id="masterDataForm" name="masterDataForm" method="post">
                            <div class="panel-body">
                                <div class="media">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Company Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="company_name" class="form-control" value="" />
                                                <span id="err-company_name"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="email" class="form-control" value="" />
                                                <span id="err-email"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Phone Number</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="phone_no" class="form-control" value="" />
                                                <span id="err-phone_no"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Address Line 1</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="address1" class="form-control" value="" />
                                                <span id="err-address1"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Address Line 2</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="address2" class="form-control" value="" />
                                                <span id="err-address2"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Country</label>
                                            <div class="col-sm-9">
                                                <select class="form-control country valid" name="country_id" id="country" aria-required="true" aria-invalid="false">
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="err-country_id"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">State</label>
                                            <div class="col-sm-9">
                                                <select class="form-control state_id valid" name="state_id" id="state_id" aria-required="true" aria-invalid="false">
                                                    <option value="">Select State</option>
                                                </select>
                                                <span id="err-state_id"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">City</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="city" id="city">
                                                    <option value="">Select City</option>
                                                </select>
                                                <span id="err-city"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="media">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Header</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="header" rows="4"></textarea>
                                                <span id="err-header"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Footer</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="footer" rows="4"></textarea>
                                                <span id="err-footer"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">CC/GE monthly subscription</label>
                                            <div class="col-sm-9">
                                                <label><input type="radio" name="subs_fee" value="1" checked>Yes</label>
                                                <label><input type="radio" name="subs_fee" value="0">No</label>
                                                <span id="err-footer"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Sub Question Levels*</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="sub_question_lvls" class="form-control" value="" />
                                                <span id="err-sub_question_lvls"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <div class="col-sm-7"></div>
                                            <div class="col-sm-5 text-right">
                                                <a class="btn btn-success" id="save-master-data-btn">Save</a>
                                                <a href="/configuration" class="btn btn-default">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('js/configuration/masterData.js') !!}
@stop

