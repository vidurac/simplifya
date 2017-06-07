<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/6/2016
 * Time: 11:24 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content" ng-controller="createNonMjb" ng-cloak="">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="addNonMjbForm" novalidate ng-init="@if($company_id==0)init()@else;getTempMjb(<?php echo $company_id?>)@endif">
                            <input type="hidden" ng-model="form.mjbEntityType">
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Name of Business / Entity*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <input type="text" ng-model="form.name_of_business" autocomplete="off"
                                               name="name_of_business" id="name_of_business" class="form-control"
                                               placeholder="Name of Business / Entity" ng-disabled="form.used=='1'"
                                               required/>
                                        <div ng-messages="addNonMjbForm.name_of_business.$error"
                                             ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Name of Business / Entity is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Address*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <input type="text" ng-model="form.add_line_1" autocomplete="off"
                                               name="add_line_1" id="add_line_1" class="form-control"
                                               placeholder="Address Line 1" ng-disabled="form.used=='1'" required/>
                                        <div ng-messages="addNonMjbForm.add_line_1.$error"
                                             ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Address Line 1 is required.</p>
                                        </div>
                                        </br>
                                        <input type="text" ng-model="form.add_line_2" autocomplete="off"
                                               name="add_line_2" id="add_line_2" class="form-control"
                                               placeholder="Address Line 2" ng-disabled="form.used=='1'"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Country*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <select name="country" required class="form-control" ng-model="form.country"
                                                ng-options="country.id as country.name for country in countries"
                                                ng-change="getStatesMain()">
                                            <option value="">Select Country</option>
                                        </select>
                                        <div ng-messages="addNonMjbForm.country.$error" ng-if="addNonMjbForm.$submitted"
                                             class="coupon-error">
                                            <p ng-message="required">Country is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">State*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <select name="state" required class="form-control" ng-model="form.state"
                                                ng-options="state.id as state.name for state in states"
                                                ng-change="getCitiesAndLicensesMain()">
                                            <option value="">Select State</option>
                                        </select>
                                        <div ng-messages="addNonMjbForm.state.$error" ng-if="addNonMjbForm.$submitted"
                                             class="coupon-error">
                                            <p ng-message="required">State is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Citi(es)*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <select name="city" required class="form-control" ng-model="form.city"
                                                ng-options="city.id as city.name for city in cities">
                                            <option value="">Select Cities</option>
                                        </select>
                                        <div ng-messages="addNonMjbForm.city.$error" ng-if="addNonMjbForm.$submitted"
                                             class="coupon-error">
                                            <p ng-message="required">City is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Zip Code*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <input type="text" ng-model="form.zip_code" autocomplete="off" name="zip_code"
                                               id="zip_code" class="form-control" placeholder="Zip Code"
                                               ng-disabled="form.used=='1'" required ng-pattern="/^\d{5}(?:-\d{4})?$/"/>
                                        <div ng-messages="addNonMjbForm.zip_code.$error"
                                             ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Zip Code is required.</p>
                                            <p ng-message="pattern">Enter a valid zip code.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Phone Number*</label>
                                    <div class="col-sm-9 col-md-6">
                                        <input type="text" ng-model="form.phone_no" autocomplete="off" name="phone_no"
                                               id="phone_no" class="form-control" ng-pattern="/[0-9 -()+]+$/" placeholder="Phone Number"
                                               ng-disabled="form.used=='1'" required/>
                                        <div ng-messages="addNonMjbForm.phone_no.$error"
                                             ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Phone Number is required.</p>
                                            <p ng-message="pattern">Enter a valid phone no.</p>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Contact Person</label>
                                    <div class="col-sm-9 col-md-6">
                                        <input type="text" ng-model="form.contact_person" autocomplete="off" name="contact_person"
                                               id="contact_person" class="form-control" placeholder="Contact Person"
                                               ng-disabled="form.used=='1'" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Contact Person Email</label>
                                    <div class="col-sm-9 col-md-6">
                                        <input type="email" ng-model="form.contact_email" autocomplete="off" name="contact_email"
                                               id="contact_email" class="form-control" placeholder="Contact Person Email"
                                               ng-disabled="form.used=='1'"/>
                                        <div ng-messages="addNonMjbForm.contact_email.$error"
                                             ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                            <p ng-message="email">Enter a valid email.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 col-md-3 control-label">License*</label>
                                    <div class="col-sm-9 col-md-9">

                                        <div class="form-group " ng-repeat="choice in form.choices">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-5">
                                                    <select name="licen_type_@{{$index}}" required class="form-control"
                                                            ng-model="choice.license"
                                                            ng-disabled="choice.license!=undefined && form.choices.length != '1'"
                                                            ng-change="enableAddmore()"
                                                            ng-options="license.id as license.name for license in choice.licenses">
                                                        <option value="">Select License Type</option>
                                                    </select>
                                                    <div ng-messages="addNonMjbForm['licen_type_' + $index].$error"
                                                         ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                                        <p ng-message="required">License Type is required.</p>
                                                    </div>


                                                </div>
                                                <div class="col-sm-9 col-md-3">
                                                    <input type="text" ng-model="choice.licen_no" autocomplete="off"
                                                           name="licen_no_@{{$index}}" id="licen_no"
                                                           class="form-control" placeholder="License Number"
                                                           ng-disabled="form.used=='1'" required/>
                                                    <div ng-messages="addNonMjbForm['licen_no_' + $index].$error"
                                                         ng-if="addNonMjbForm.$submitted" class="coupon-error">
                                                        <p ng-message="required">License Number is required.</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1">
                                                    <a class="remove btn btn-danger" ng-show="!$first"
                                                       style="border-radius: 0px;"
                                                       ng-click="removeChoice($index,choice)">-</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">

                                                <button class="btn btn-default" type="button"
                                                        ng-hide="(form.choices.length)==licenses.length"
                                                        ng-disabled="disableAddmore" style="border: none;color: #337ab7"
                                                        ng-click="addNewChoice()"><i class="fa fa-plus"></i>&nbsp;Add
                                                    another
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group col-lg-12 col-xs-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right no-padding">
                                    <button class="btn btn-success" type="submit"
                                            ng-click="saveNonMjb(addNonMjbForm.$valid)" ng-disabled="savingReferrer">
                                        Next
                                    </button>
                                    <a href="/appointment" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
    {!! Html::script('/js/angular/nonMjb.js') !!}
@stop
