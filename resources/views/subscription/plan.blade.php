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
    <div class="content" ng-controller="SubscriptionPlanCtrl">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info" ng-cloak ng-if="currentPlan && isSubscriptionDataLoaded">
                    <i class="fa fa-info-circle"></i>
                    Current Subscription Plan: @{{currentPlan.plan_name}}. Started on @{{currentPlan.plan_start_date}} to @{{currentPlan.plan_end_date}}.
                    <span ng-if="nextPlan">
                    Your next subscription plan (@{{nextPlan.plan_name}}) will be started on @{{nextPlan.plan_start_date}}.
                    </span>
                    <span>
                    Your next subscription charge scheduled on @{{currentPlan.plan_next_due_date}}.
                    </span>
                </div>
                <div class="alert alert-warning" ng-cloak ng-if="undefined == currentPlan && isSubscriptionDataLoaded" >
                    <i class="fa fa-info-circle"></i>
                    <span>Please update your subscription plan
                    </span>
                </div>
                <div class="hpanel">
                    <div class="panel-heading hbuilt">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        </div>
                        Plan Details
                    </div>
                    <div class="panel-body">
                        <form name="subscription" class="subscription_plan" novalidate ng-submit="updatePlan(subscription.$valid)">
                            <div class="form-group col-lg-10 col-sm-offset-2">
                                <label class="col-sm-4 control-label">Your subscription plan</label>
                                <div class="col-sm-6">
                                    <select required class="form-control" name="selectedPlan" ng-model="subscriptionForm.selectedPlan"
                                            ng-options="plan.id as plan.name for plan in plans" ng-change="validateCoupon()">
                                        <option value="">Please select plan</option>
                                    </select>
                                    <div ng-messages="subscription.selectedPlan.$error" ng-if="subscription.$submitted || !subscription.selectedPlan.$pristine">
                                        <p ng-message="required">Subscription plan is required.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-10 col-sm-offset-2" ng-if="couponReferralId == '0'">
                                <label class="col-sm-4 control-label">Referral Code</label>
                                <div class="col-sm-6">
                                    <input  type="text" class="form-control coupon" placeholder="Referral Code" ng-model="subscriptionForm.coupon_code" name="coupon_code" id="coupon_code" ng-blur="validateCoupon()">
                                    <span class="coupon_validation " id="coupon_check_msg"></span>

                                    <div ng-messages="subscription.coupon_code.$error" ng-if="subscription.$submitted || !subscription.coupon_code.$pristine ">
                                        <p ng-message="required">Referral Code is required.</p>
                                    </div>
                                    <input type="hidden" ng-model="is_valid_coupon" id="is_valid_coupon" value="0">
                                </div>
                            </div>

                            <div class="form-group col-lg-10 col-sm-offset-2">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn w-xs btn-primary">Update Plan</button>
                                </div>
                            </div>

                            <div class="form-group col-lg-10 col-sm-offset-2" ng-if="!hideCancelPlan">
                                <label class="col-sm-4 control-label">Want to cancel your plan?</label>
                                <div class="col-sm-6">
                                </div>
                            </div>
                        </form>

                        <div ng-if="currentPlan">
                            <div class="form-group col-lg-10 col-sm-offset-2">
                                <div class="col-sm-12">
                                    <p ng-if="cancelFee != 0 && isSubscriptionDataLoaded && !hideCancelPlan">
                                        In order to cancel your subscription you will be charged $@{{ cancelFee }}.
                                    </p>
                                </div>
                            </div>

                            <div class="form-group col-lg-10 col-sm-offset-2">
                                <div class="col-sm-4" ng-if="isSubscriptionDataLoaded && !hideCancelPlan">
                                    <button type="button" class="btn w-xs btn-warning" ng-click="cancelPlan()">Cancel Plan</button>
                                </div>
                                <div class="col-sm-6"></div>
                            </div>
                        </div>
                    <div>
                </div>


            </div>

        </div>
                </div>
            </div>
        <div class="row">

        </div>
    </div>

@stop
@section('scripts')
    {!! Html::script('/js/angular/subscription.js') !!}
@stop