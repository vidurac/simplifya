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
                        <form id="masterDataEditForm" name="masterDataForm" method="post">
                            <h5  style="line-height: 35px;">Company Information</h5>
                            <div class="panel-body">
                                <div class="media">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Company Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="company_name" class="form-control" value="{{ $data['company_name']['value'] }}" />
                                                <span id="err-company_name"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="email" class="form-control" value="{{ $data['email']['value'] }}" />
                                                <span id="err-email"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Phone Number</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="phone_no" class="form-control" value="{{ $data['phone']['value'] }}" />
                                                <span id="err-phone_no"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Address Line 1</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="address1" class="form-control" value="{{ $data['address1']['value'] }}" />
                                                <span id="err-address1"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Address Line 2</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="address2" class="form-control" value="{{ $data['address2']['value'] }}" />
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
                                                        <option value="{{$country->id}}" {{ $data['country']['value']==$country->id? 'selected' : '' }}>{{$country->name}}</option>
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
                                                    @foreach($states as $state)
                                                        <option value="{{$state->id}}" {{ $data['state']['value']==$state->id? 'selected' : '' }}>{{$state->name}}</option>
                                                    @endforeach
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
                                                    @foreach($cities as $city)
                                                        <option value="{{$city->id}}" {{ $data['city']['value']==$city->id? 'selected' : '' }}>{{$city->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="err-city"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5  style="line-height: 35px;">Web Configuration</h5>
                            <div class="panel-body" style="padding-top:35px; margin-top: 5px;">
                                <div class="media">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Header</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="header" rows="4">{{ $data['header']['value'] }}</textarea>
                                                <span id="err-header"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Footer</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="footer" rows="4">{{ $data['footer']['value'] }}</textarea>
                                                <span id="err-footer"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Sub Question Levels</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="sub_question_lvls" class="form-control" value="{{ $data['sub_question']['value'] }}" />
                                                <span id="err-sub_question_lvls"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Audit Report Enable Tree View</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['insp_rpt_tree_view']) && $data['insp_rpt_tree_view']['value'] == 1) {?>
                                                <label><input type="radio" name="insp_rpt_tree_view" value="1" checked>Yes</label>
                                                <label><input type="radio" name="insp_rpt_tree_view" value="0">No</label>
                                                <?php }?>
                                                <?php if(isset($data['insp_rpt_tree_view']) && $data['insp_rpt_tree_view']['value'] == 0) {?>
                                                <label><input type="radio" name="insp_rpt_tree_view" value="1">Yes</label>
                                                <label><input type="radio" name="insp_rpt_tree_view" value="0" checked>No</label>
                                                <?php }?>
                                                <?php if(!isset($data['insp_rpt_tree_view'])) {?>
                                                <label><input type="radio" name="insp_rpt_tree_view" value="1">Yes</label>
                                                <label><input type="radio" name="insp_rpt_tree_view" value="0" checked>No</label>
                                                <?php }?>
                                                <span id="err-footer"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">CC/GE Subscription</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['subs_fee']) && $data['subs_fee']['value'] == 1) {?>
                                                <label><input type="radio" name="subs_fee" value="1" checked>Yes</label>
                                                <label><input type="radio" name="subs_fee" value="0">No</label>
                                                <?php }?>
                                                <?php if(isset($data['subs_fee']) && $data['subs_fee']['value'] == 0) {?>
                                                <label><input type="radio" name="subs_fee" value="1">Yes</label>
                                                <label><input type="radio" name="subs_fee" value="0" checked>No</label>
                                                <?php }?>
                                                <span id="err-footer"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">MJB Free Sign Up</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['mjb_free_sign_up']) && $data['mjb_free_sign_up']['value'] == 1): ?>
                                                <label><input type="radio" name="mjb_free_sign_up" value="1" checked>Yes</label>
                                                <label><input type="radio" name="mjb_free_sign_up" value="0">No</label>
                                                <?php endif ?>
                                                <?php if(isset($data['mjb_free_sign_up']) && $data['mjb_free_sign_up']['value'] == 0): ?>
                                                <label><input type="radio" name="mjb_free_sign_up" value="1">Yes</label>
                                                <label><input type="radio" name="mjb_free_sign_up" value="0" checked>No</label>
                                                <?php endif ?>
                                                <?php if(!isset($data['mjb_free_sign_up'])): ?>
                                                <label><input type="radio" name="mjb_free_sign_up" value="1">Yes</label>
                                                <label><input type="radio" name="mjb_free_sign_up" value="0" checked>No</label>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">MJB Free License</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['mjb_free_license']) && $data['mjb_free_license']['value'] == 1): ?>
                                                <label><input type="radio" name="mjb_free_license" value="1" checked>Yes</label>
                                                <label><input type="radio" name="mjb_free_license" value="0">No</label>
                                                <?php endif ?>
                                                <?php if(isset($data['mjb_free_license']) && $data['mjb_free_license']['value'] == 0): ?>
                                                <label><input type="radio" name="mjb_free_license" value="1">Yes</label>
                                                <label><input type="radio" name="mjb_free_license" value="0" checked>No</label>
                                                <?php endif ?>
                                                <?php if(!isset($data['mjb_free_license'])): ?>
                                                <label><input type="radio" name="mjb_free_license" value="1">Yes</label>
                                                <label><input type="radio" name="mjb_free_license" value="0" checked>No</label>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">CC/GE Free Checklist</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['cc_ge_free_checklist']) && $data['cc_ge_free_checklist']['value'] == 1): ?>
                                                <label><input type="radio" name="cc_ge_free_checklist" value="1" checked>Yes</label>
                                                <label><input type="radio" name="cc_ge_free_checklist" value="0">No</label>
                                                <?php endif ?>
                                                <?php if(isset($data['cc_ge_free_checklist']) && $data['cc_ge_free_checklist']['value'] == 0): ?>
                                                <label><input type="radio" name="cc_ge_free_checklist" value="1">Yes</label>
                                                <label><input type="radio" name="cc_ge_free_checklist" value="0" checked>No</label>
                                                <?php endif ?>
                                                <?php if(!isset($data['cc_ge_free_checklist'])): ?>
                                                <label><input type="radio" name="cc_ge_free_checklist" value="1">Yes</label>
                                                <label><input type="radio" name="cc_ge_free_checklist" value="0" checked>No</label>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5 style="line-height: 35px;">Mobile Configuration</h5>
                            <div class="panel-body">
                                <div class="media">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Action Items ON/OFF</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['action_items_on_off']) && $data['action_items_on_off']['value'] == 1): ?>
                                                <label><input type="radio" name="action_items_on_off" value="1" checked>Off</label>
                                                <label><input type="radio" name="action_items_on_off" value="0">On</label>
                                                <?php endif ?>
                                                <?php if(isset($data['action_items_on_off']) && $data['action_items_on_off']['value'] == 0): ?>
                                                <label><input type="radio" name="action_items_on_off" value="1">Off</label>
                                                <label><input type="radio" name="action_items_on_off" value="0" checked>On</label>
                                                <?php endif ?>
                                                <?php if(!isset($data['action_items_on_off'])): ?>
                                                <label><input type="radio" name="action_items_on_off" value="1">Off</label>
                                                <label><input type="radio" name="action_items_on_off" value="0" checked>On</label>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Status Indicator ON/OFF</label>
                                            <div class="col-sm-9">
                                                <?php if(isset($data['status_indicator_on_off']) && $data['status_indicator_on_off']['value'] == 1): ?>
                                                <label><input type="radio" name="status_indicator_on_off" value="1" checked>Off</label>
                                                <label><input type="radio" name="status_indicator_on_off" value="0">On</label>
                                                <?php endif ?>
                                                <?php if(isset($data['status_indicator_on_off']) && $data['status_indicator_on_off']['value'] == 0): ?>
                                                <label><input type="radio" name="status_indicator_on_off" value="1">Off</label>
                                                <label><input type="radio" name="status_indicator_on_off" value="0" checked>On</label>
                                                <?php endif ?>
                                                <?php if(!isset($data['status_indicator_on_off'])): ?>
                                                <label><input type="radio" name="status_indicator_on_off" value="1">Off</label>
                                                <label><input type="radio" name="status_indicator_on_off" value="0" checked>On</label>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">IOS Version</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="ios_version" class="form-control" value="{{ $data['ios_version']['value'] }}" />
                                                <span id="err-ios_version"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-3 control-label">Android Version</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="android_version" class="form-control" value="{{ $data['android_version']['value'] }}" />
                                                <span id="err-android_version"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="media">
                                <div class="">
                                        <div class="text-right">
                                            <a class="btn btn-success" id="update-master-data-btn">Update</a>
                                            <a href="/configuration" class="btn btn-default">Cancel</a>
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

