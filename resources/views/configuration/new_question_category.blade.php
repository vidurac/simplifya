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
                    <form id="newSubscriptionForm" method="post">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Required*</label>
                                <div class="col-sm-8">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_required" id="is_required" class="i-checks is_req_yes" value="1" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div>
                                        Yes
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_required" id="is_required" class="i-checks is_req_no" value="0" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div> No
                                    </label>
                                    <span id="err-required"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Multi-Select*</label>
                                <div class="col-sm-8">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_multiselect" id="is_multiselect" class="i-checks is_multi_yes" value="1" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div>
                                        Yes
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_multiselect" id="is_multiselect" class="i-checks is_multi_no" value="0" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div> No
                                    </label>
                                    <span id="err-is_multiselect"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Visible To</label>
                                <div class="col-sm-9">
                                    <label class="checkbox-inline"><input type="checkbox" name="visible_to" value="{{ Config::get('simplifya.MARIJUANA_COMPANY_TYPE') }}">MJBusiness</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="visible_to" value="{{ Config::get('simplifya.COMPLIANCE_COMPANY_TYPE') }}">Compliance Company</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="visible_to" value="{{ Config::get('simplifya.GOVERNMENT_ENTITY_TYPE') }}">Government Entity</label>
                                </div>
                                <span id="err-visible_to"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Category Name*</label>
                                <div class="col-sm-9">
                                    <input type="text" name="category_name" id="category_name" value="" class="form-control" />
                                    <span id="err-cat_name"></span>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div id="option_list">
                                <p>
                                    <label for="option_list" class="col-md-12">
                                        <span class="col-md-10">
                                            <input type="text" name="option" class="form-control" value="" placeholder="Option Name" />
                                            <span id="err-option"></span>
                                        </span>
                                        {{--<span class="col-md-5">--}}
                                            {{--<input type="text" name="option_value" class="form-control" value="" placeholder="Option Value" />--}}
                                            {{--<span id="err-option_value"></span>--}}
                                        {{--</span>--}}
                                        <span class="col-md-2"><a href="#" id="addScnt" class="btn btn-info pull-right">Add New Option</a></span>
                                    </label>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right">
                                    <button class="btn btn-success" type="button" id="save-qcategory-btn">Save</button>
                                    <a href="/configuration/qcategories/" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Html::script('js/configuration/questionCategory.js') !!}

@stop


