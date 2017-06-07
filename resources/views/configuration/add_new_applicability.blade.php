<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 10:34 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content" ng-controller="createApplicability" ng-cloak="" ng-init="@if($applicability_id==0)init()@else;getApplicability(<?php echo $applicability_id?>)@endif">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="addApplicabilityForm" novalidate>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Applicability Name*</label>
                                    <div class="col-sm-4">
                                        <input type="text" ng-model="form.name" name="name" id="name" class="form-control"  ng-disabled="form.used=='1'"required autocomplete="off"/>
                                        <div ng-messages="addApplicabilityForm.name.$error" ng-if="addApplicabilityForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Name is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Type*</label>
                                    <div class="col-sm-4">
                                        <select name="applicability_type" required class="form-control" ng-model="form.type" ng-options="key as value for (key , value) in types">
                                            <option value="">Please select Applicability type</option>
                                        </select>
                                        <div ng-messages="addApplicabilityForm.applicability_type.$error" ng-if="addApplicabilityForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Applicability type is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: none;">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Country*</label>
                                    <div class="col-sm-4">
                                        <select name="country" class="form-control" ng-model="form.country"
                                                ng-options="country.id as country.name for country in countries">
                                            <option value="">Select Country</option>
                                        </select>
                                        <div ng-messages="addApplicabilityForm.country.$error" ng-if="addApplicabilityForm.$submitted"
                                             class="coupon-error">
                                            <p ng-message="required">Country is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Group*</label>
                                    <div class="col-sm-4">
                                        <select name="applicability_type" required class="form-control" ng-model="form.group" ng-options="key as value for (key , value) in groups">
                                            <option value="">Please select Applicability group</option>
                                        </select>
                                        <div ng-messages="addApplicabilityForm.applicability_type.$error" ng-if="addApplicabilityForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Applicability group is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-xs-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right no-padding">
                                    <button ng-show="<?php echo $applicability_id ?>==0" class="btn btn-success" type="submit" ng-click="saveApplicability(addApplicabilityForm.$valid)" ng-disabled="savingApplicability">Save</button>
                                    <button ng-show="<?php echo $applicability_id ?>!=0 && form.used!='1'" class="btn btn-warning" type="submit" ng-click="saveApplicability(addApplicabilityForm.$valid)" ng-disabled="savingReferrer">Update</button>
                                    <a href="/configuration/applicability" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    {!! Html::script('/js/angular/applicabilities.js') !!}
@stop