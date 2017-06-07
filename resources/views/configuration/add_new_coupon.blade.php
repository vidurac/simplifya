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
    <div class="content" ng-controller="createCoupon" ng-cloak="">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="addCouponForm" novalidate ng-init="form.mjbEntityType=<?php echo $MJB_entity_type?>;@if($coupon_id==0)init()@else;getCoupon(<?php echo $coupon_id?>)@endif">
                            <input type="hidden"  ng-model="form.mjbEntityType">
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Discount Code*</label>
                                    <div class="col-sm-4">
                                        <input type="text" ng-model="form.code" name="code" autocomplete="off" id="code" class="form-control" ng-maxlength="20" ng-disabled="form.used=='1'"required ng-pattern="/^\S*$/"/>
                                        <div ng-messages="addCouponForm.code.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Code is required.</p>
                                            <p ng-message="maxlength">Code must not be 20 characters long.</p>
                                            <p ng-message="pattern">Do not use spaces</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Description*</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" ng-model="form.description" autocomplete="off" name="description" id="description" ng-disabled="form.used=='1'" required></textarea>
                                        <div ng-messages="addCouponForm.description.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Description is required.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Start Date*</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input type="text" name="start_date" autocomplete="off" class="form-control" valid min-date="minDate" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="form.start_date" ng-disabled="form.used=='1'" is-open="popup1.opened" datepicker-options="dateOptions" show-weeks="false" show-button-bar="false" close-text="Close"  alt-input-formats="altInputFormats" required />

                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" ng-click="open1()" ng-disabled="form.used=='1'"><i class="glyphicon glyphicon-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div ng-messages="addCouponForm.start_date.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Start Date is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">End Date*</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control" autocomplete="off" valid min-date="minDatePop2" cs-date-to-iso uib-datepicker-popup="@{{format}}" name="end_date" ng-model="form.end_date" ng-disabled="form.used=='1' || !form.start_date.length " is-open="popup2.opened" datepicker-options="dateOptions"  show-weeks="false" show-button-bar="false" close-text="Close" alt-input-formats="altInputFormats" required />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" ng-click="open2()" ng-disabled="form.used=='1'"><i class="glyphicon glyphicon-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div ng-messages="addCouponForm.end_date.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">End Date is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Plan*</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" ng-model="form.master_subscription_id" ng-options="plan as plan.name for plan in plans" ng-disabled="form.used=='1'" ng-change="getfields()">
                                            <option value="">Please select a subscription plan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-7 col-lg-offset-3 col-xs-12 form-group coupon-detail m-t">
                                    <div class="col-header col-lg-12 col-xs-12 no-padding">
                                        <div class="col-lg-2 col-xs-3"><label class="control-label"><strong>Month</strong></label></div>
                                        <div class="col-lg-7 col-xs-6"><label class="control-label"><strong>Type</strong></label></div>
                                        <div class="col-lg-2 col-xs-3"><label class="control-label"><strong>Amount</strong></label></div>
                                    </div>

                                    <div class="form-group col-lg-12 col-xs-12" ng-repeat="coupon_detail in form.coupon_details">

                                        <div class="col-lg-2 col-sm-2 col-xs-2">
                                            <label class="control-label">@{{coupon_detail.order}}</label>
                                        </div>
                                    <div class="col-lg-7 col-sm-6 col-xs-6">
                                        <div class="col-sm-6"><label><input type="radio"  value="percentage" ng-model="coupon_detail.type" ng-disabled="form.used=='1'"/> Percentage</label></div>
                                        <div class="col-sm-6"><label><input type="radio"  value="fixed" ng-model="coupon_detail.type" ng-disabled="form.used=='1'"/> Fixed</label></div>

                                    </div>
                                    <div class="col-lg-2 col-sm-3 col-xs-3">
                                        <input type="text" ng-if="coupon_detail.type == 'fixed'" autocomplete="off" discount name="fixed_@{{$index}}" ng-model="coupon_detail.amount" class="input-plans coupon-amnt" ng-disabled="form.used=='1'" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/"/>
                                        <input type="text" ng-if="coupon_detail.type == 'percentage'" autocomplete="off" ng-pattern="/^(100(?:\.0{1,2})?|0*?\.\d{1,2}|\d{1,2}(?:\.\d{1,2})?)$/" name="percentage_@{{$index}}" ng-model="coupon_detail.amount" class="input-plans" ng-disabled="form.used=='1'"/>
                                        <div ng-messages="addCouponForm['fixed_' + $index].$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="pattern">Enter a valid amount</p>
                                            <p ng-message="discount">Discount cannot be greater than $@{{form.master_subscription_id.amount}}</p>
                                        </div>
                                        <div ng-messages="addCouponForm['percentage_' + $index].$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="pattern">Percentage should be 0 to 100</p>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-offset-3 col-lg-7 col-xs-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right no-padding">
                                    <button ng-show="<?php echo $coupon_id ?>==0" class="btn btn-success" type="submit" ng-click="saveCoupon(addCouponForm.$valid)" ng-disabled="savingCoupon">Save</button>
                                    <button ng-show="<?php echo $coupon_id ?>!=0 && form.used!='1'" class="btn btn-warning" type="submit" ng-click="saveCoupon(addCouponForm.$valid)" ng-disabled="savingCoupon">Update</button>
                                    <a href="/configuration/coupons" class="btn btn-default">Cancel</a>
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
    {!! Html::script('/js/angular/coupons.js') !!}
@stop