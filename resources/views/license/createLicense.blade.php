@extends('layout.dashbord')

@section('content')
    <div class="content" ng-controller="createLicense" ng-cloak="" ng-init="@if($license_id==0)init()@else;initWithLicense(<?php echo $license_id?>)@endif" ng-show="!isLoading">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="LicenseForm" class="form-horizontal" novalidate ng-submit="saveLicense(LicenseForm.$valid)">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">License Name*</label>
                                <div class="col-sm-5">
                                    <input class="form-control" type="text" placeholder="License Name" name="license_name" id="license_name" ng-model="licenseForm.license_name" required ng-maxlength="255">
                                    <div ng-messages="LicenseForm['license_name'].$error" ng-if="LicenseForm.$submitted" class="coupon-error">
                                        <p ng-message="required">License name is required</p>
                                        <p ng-message="maxlength">License name must be less than 255 characters</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Select Type*</label>
                                <div class="col-sm-5">
                                    <select required class="form-control" name="type" ng-model="licenseForm.type"
                                            ng-options="key as value for (key , value) in types" ng-change="getApplicabilityDetails()">
                                        <option value="">Select Type</option>
                                    </select>
                                    <div ng-messages="LicenseForm['type'].$error" ng-if="LicenseForm.$submitted" class="coupon-error">
                                        <p ng-message="required">State is required</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Select Country*</label>
                                <div class="col-sm-5">
                                    <select required class="form-control" name="country" ng-model="licenseForm.country"
                                            ng-options="c.id as c.name for c in countries" ng-change="getStates()">
                                        <option value="">Select Country</option>
                                    </select>
                                    <div ng-messages="LicenseForm['country'].$error" ng-if="LicenseForm.$submitted" class="coupon-error">
                                        <p ng-message="required">Country is required</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Select State*</label>
                                <div class="col-sm-5">
                                    <select required class="form-control" name="state" ng-model="licenseForm.state"
                                            ng-options="s.id as s.name for s in states">
                                        <option value="">Select State</option>
                                    </select>
                                    <div ng-messages="LicenseForm['state'].$error" ng-if="LicenseForm.$submitted" class="coupon-error">
                                        <p ng-message="required">State is required</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Checklist Fee*</label>
                                <div class="col-sm-5">
                                    <input class="form-control" ng-model="licenseForm.checklist_fee" step="0.01" type="number" placeholder="Check List Fee" name="checklist_Fee" id="checklist_Fee" min="1" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" required>
                                    <small class="text-muted">Amount CCs/GEs will be charged to generate a checklist for this license.</small>
                                    <div ng-messages="LicenseForm['checklist_Fee'].$error" ng-if="LicenseForm.$submitted" class="coupon-error">
                                        <p ng-message="pattern">Enter a valid amount</p>
                                        <p ng-message="min">Discount value must be greater than 0</p>
                                        <p ng-message="required">Checklist fee is required</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" ng-show="applicabilities">
                                <label class="col-sm-2 control-label">Applicabilties</label>
                                <div class="col-sm-5">
                                    <ul style="padding-top:5px;" ng-repeat="(key, value) in applicabilities | groupBy: 'group'" class="list-unstyled">
                                        <li><h5>@{{ key }}</h5></li>
                                        <div ng-repeat="p in value">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" ng-model="p.checked">@{{ p.name }}
                                                </label>
                                            </div>
                                        </div>
                                    </ul>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 pull-right">
                                    <button ng-show="<?php echo $license_id ?>==0" class="create_parent_question btn w-xs btn-success pull-right" type="submit" style="margin-left: 2%" ng-disabled="isSubmitted"><strong>Save</strong></button>
                                    <button ng-show="<?php echo $license_id ?>!=0" class="create_parent_question btn w-xs btn-success pull-right" type="submit" style="margin-left: 2%" ng-disabled="isSubmitted"><strong>Update</strong></button>
                                    <a href="{{URL('configuration/licenses')}}" class="btn w-xs btn w-xs btn-default pull-right"><strong>Cancel</strong></a>
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
    {!! Html::script('/js/angular/licenses.js') !!}
@stop