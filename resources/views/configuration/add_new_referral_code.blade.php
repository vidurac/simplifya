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
    <div class="content" ng-controller="createReferralCode" ng-cloak="" ng-init="maxPlanAmount=<?php echo $maxPlanAmount?>;entityType=<?php echo $MJB_entity_type?>;@if($coupon_id==0)init()@else;initWithCoupon(<?php echo $coupon_id?>)@endif">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="addCouponForm" novalidate ng-submit="saveReferralCode(addCouponForm.$valid)">
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Referral Code*</label>
                                    <div class="col-sm-4">
                                        <input type="text" ng-model="form.code" name="code" id="code" class="form-control" ng-maxlength="20" ng-disabled="form.used=='1'"required autocomplete="off" ng-pattern="/^\S*$/" ng-readonly="<?php echo $read_only ?>"/>
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
                                        <textarea ng-model="form.description" name="description" id="description" autocomplete="off" class="form-control" ng-disabled="form.used=='1'" required style="width: 100%" ng-readonly="<?php echo $read_only ?>"></textarea>
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
                                            <input type="text" name="start_date" class="form-control" autocomplete="off" valid min-date="minDate" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="form.start_date" ng-disabled="form.used=='1' || <?php echo $read_only ?>" is-open="popup1.opened" datepicker-options="dateOptions" show-weeks="false" show-button-bar="false" close-text="Close" alt-input-formats="altInputFormats" required />

                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" ng-click="open1()" ng-disabled="form.used=='1' || <?php echo $read_only ?>"><i class="glyphicon glyphicon-calendar"></i></button>
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
                                            <input type="text" class="form-control" valid min-date="minDatePop2" autocomplete="off" cs-date-to-iso uib-datepicker-popup="@{{format}}" name="end_date" ng-model="form.end_date" ng-disabled="form.used=='1' || !form.start_date.length || <?php echo $read_only ?>" is-open="popup2.opened" datepicker-options="dateOptions" show-weeks="false" show-button-bar="false" close-text="Close" alt-input-formats="altInputFormats" required />

                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" ng-click="open2()" ng-disabled="form.used=='1'|| !form.start_date.length || <?php echo $read_only ?>"><i class="glyphicon glyphicon-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div ng-messages="addCouponForm.end_date.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">End Date is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12  ">
                                    <label class="col-sm-3 control-label">Referrer*</label>
                                    <div class="col-sm-4">
                                        <select required class="form-control" name="master_referral_id" ng-model="form.master_referral_id" ng-disabled="form.id != 0"
                                                ng-options="r.id as r.name for r in referrals">
                                            <option value="">Please select referrer</option>
                                        </select>
                                        <div ng-messages="addCouponForm.master_referral_id.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Referrer is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- commission_period -->
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12  ">
                                    <label class="col-sm-3 control-label">Commission Period*</label>
                                    <div class="col-sm-4">
                                        <select required class="form-control" name="commission_period" ng-model="form.commission_period" ng-disabled="form.id != 0"
                                                ng-options="c.id as c.name for c in commissionPeriod">
                                            <option value="">Please select commission period</option>
                                        </select>
                                        <div ng-messages="addCouponForm.commission_period.$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Commission Period is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <div class="custom-info-alert-panel">
                                        {{--<p class="l">Please consider following subscription plan amounts when considering fixed discounts. Maximum fixed discount amount would be the minimum subscription plan amount which is $@{{ maxPlanAmount }}.</p>--}}
                                        <p class="l">The maximum fixed discount amount cannot exceed the minimum monthly license fee.</p>
                                        <ul>
                                            <li ng-repeat="p in plans">
                                                <p>@{{ p.name }} - $@{{ p.amount }}</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12  ">
                                    <label class="col-sm-3 control-label m-t">Discount*</label>
                                    <div class="col-sm-9">
                                        <div class="coupon-detail">
                                            <div class="col-header col-lg-12 col-xs-12 no-padding">
                                                <div class="col-lg-6 col-xs-6"><label class="control-label"><strong>Type</strong></label></div>
                                                <div class="col-lg-3 col-xs-3"><label class="control-label"><strong>Amount</strong></label></div>
                                            </div>
                                            <div class="form-group col-lg-12 col-xs-12" ng-repeat="coupon_detail in form.coupon_details">
                                                <div class="col-sm-6 col-xs-6">
                                                    <div class="col-sm-6"><label><input type="radio"  value="percentage" ng-model="coupon_detail.type" ng-disabled="form.used=='1' || <?php echo $read_only ?>"/> Percentage</label></div>
                                                    <div class="col-sm-6"><label><input type="radio"  value="fixed" ng-model="coupon_detail.type" ng-disabled="form.used=='1' || <?php echo $read_only ?>"/> Fixed</label></div>
                                                </div>
                                                <div class="col-sm-3 col-xs-3">
                                                    <input type="number" ng-cloak="" ng-if="coupon_detail.type == 'fixed'" name="fixed_amt" ng-model="coupon_detail.amount" class="input-plans" ng-disabled="form.used=='1' || <?php echo $read_only ?>" min="1" max="@{{ maxPlanAmount }}" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/"/>
                                                    <input type="number" ng-cloak="" ng-if="coupon_detail.type == 'percentage'" step="0.01" name="percentage_amt" ng-model="coupon_detail.amount" class="input-plans" ng-disabled="form.used=='1' || <?php echo $read_only ?>" ng-pattern="/^(100(?:\.0{1,2})?|0*?\.\d{1,2}|\d{1,2}(?:\.\d{1,2})?)$/">
                                                    <div ng-messages="addCouponForm['fixed_amt'].$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                                        <p ng-message="pattern">Enter a valid amount</p>
                                                        <p ng-message="min">Discount value must be greater than 0</p>
                                                        <p ng-message="max">Discount value must be less than @{{ maxPlanAmount }}</p>
                                                    </div>
                                                    <div ng-messages="addCouponForm['percentage_amt'].$error" ng-if="addCouponForm.$submitted" class="coupon-error">
                                                        <p ng-message="pattern">Percentage should be 0 to 100</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                            </div>

                            <div class="form-group col-lg-12 col-xs-12" ng-hide="<?php echo $read_only;?>">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right no-padding">
                                    <button ng-show="<?php echo $coupon_id ?>==0" class="btn btn-success" type="submit" ng-click="saveCoupon(addCouponForm.$valid)" ng-disabled="savingCoupon">Save</button>
                                    <button ng-show="<?php echo $coupon_id ?>!=0 && form.used!='1'" class="btn btn-warning" type="submit" ng-click="saveCoupon(addCouponForm.$valid)" ng-disabled="savingCoupon">Update</button>
                                    <a href="{{ URL('configuration/referrals/codes') }}" class="btn btn-default">Cancel</a>
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